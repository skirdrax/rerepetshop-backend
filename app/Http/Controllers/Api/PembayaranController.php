<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PembayaranController extends Controller
{
    public function __construct()
    {
        // Konfigurasi khusus Sandbox
        Config::$serverKey = config('midtrans.server_key'); 
        Config::$isProduction = false; // Wajib false untuk Sandbox
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

   protected function resolvePelanggan(Request $request): ?Pelanggan
{
    $user = $request->user();

    if (!$user) {
        return null;
    }

    if ($user->pelanggan_id) {
        return Pelanggan::find($user->pelanggan_id);
    }

    return Pelanggan::where('email', $user->email)->first();
}

    protected function formatPaymentMethod(string $paymentType, ?string $bank = null, ?string $store = null): string
    {
        return match ($paymentType) {
            'bank_transfer' => trim(($bank ? strtoupper($bank) . ' ' : '') . 'Virtual Account'),
            'echannel' => 'Mandiri Bill Payment',
            'qris' => 'QRIS',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'cstore' => $store ? ucfirst($store) : 'Convenience Store',
            'akulaku' => 'Akulaku',
            'credit_card' => 'Kartu Kredit',
            'bca_klikpay' => 'BCA KlikPay',
            'bca_klikbca' => 'KlikBCA',
            'bri_epay' => 'BRI e-Pay',
            'cimb_clicks' => 'CIMB Clicks',
            'danamon_online' => 'Danamon Online',
            default => ucwords(str_replace('_', ' ', $paymentType)),
        };
    }

    protected function resolveMidtransMethodFromData(?string $paymentType, ?array $vaNumbers = null, ?string $store = null): ?string
    {
        if (! $paymentType) {
            return null;
        }

        $bank = $vaNumbers[0]['bank'] ?? $vaNumbers[0]->bank ?? null;

        if ($paymentType === 'echannel') {
            $bank = 'mandiri';
        }

        return $this->formatPaymentMethod($paymentType, $bank, $store);
    }

    protected function mapTransactionStatusToPaymentStatus(?string $transactionStatus, ?string $fraudStatus = null): ?string
    {
        return match ($transactionStatus) {
            'capture' => $fraudStatus === 'challenge' ? 'pending' : 'paid',
            'settlement' => 'paid',
            'cancel', 'deny', 'expire' => 'failed',
            'pending' => 'pending',
            default => null,
        };
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pesanan' => 'required|integer',
            'metode_bayar' => 'required|string|max:50',
        ]);

        $pelanggan = $this->resolvePelanggan($request);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan',
            ], 404);
        }

        $pesanan = Pesanan::with('details.produk')
            ->where('id_pesanan', $request->id_pesanan)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        // Cek apakah payment sudah ada
        $existingPayment = Pembayaran::where('id_pesanan', $pesanan->id_pesanan)->first();
        if ($existingPayment && $existingPayment->status_bayar === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah dibayar.',
            ], 400);
        }

        if ($existingPayment && $existingPayment->status_bayar === 'pending' && $existingPayment->snap_token) {
            return response()->json([
                'success' => true,
                'snap_token' => $existingPayment->snap_token,
                'ref_gateway' => $existingPayment->ref_gateway,
                'is_existing' => true,
            ]);
        }

        // Siapkan data transaksi untuk Midtrans
        $transactionDetails = [
            'order_id'     => 'ORDER-' . $pesanan->id_pesanan . '-' . time(),
            'gross_amount' => (int) $pesanan->total,
        ];

        $customerDetails = [
            'first_name' => $pelanggan->nama,
            'email'      => $pelanggan->email,
            'phone'      => $pelanggan->no_hp ?? '',
        ];

        $itemDetails = $pesanan->details->map(function ($detail) {
            return [
                'id'       => $detail->id_detail,
                'price'    => (int) $detail->harga_satuan,
                'quantity' => (int) $detail->qty,
                'name'     => $detail->produk->nama_produk ?? 'Produk',
            ];
        })->toArray();

        $params = [
            'transaction_details' => $transactionDetails,
            'customer_details'    => $customerDetails,
            'item_details'        => $itemDetails,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            // Simpan atau update data pembayaran
            Pembayaran::updateOrCreate(
                ['id_pesanan' => $pesanan->id_pesanan],
                [
                    'metode_bayar'  => 'Menunggu Pilihan Metode',
                    'status_bayar'  => 'pending',
                    'jumlah_bayar'  => (int) $pesanan->total,
                    'waktu_bayar'  => null,
                    'ref_gateway'   => $transactionDetails['order_id'],
                    'snap_token'    => $snapToken,
                ]
            );

            // Update status pesanan
            $pesanan->update(['status_pesanan' => 'baru']);

            return response()->json([
                'success'    => true,
                'snap_token' => $snapToken,
                'ref_gateway' => $transactionDetails['order_id'],
                'is_existing' => false,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Midtrans error: ' . $e->getMessage(),
            ], 500);
        }
    }

   public function webhook(Request $request)
{
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = false;

    try {
        $notif = new Notification();

        // PERBAIKAN 1: Gunakan null coalescing operator untuk mencegah undefined property
        $orderId = $notif->order_id ?? null;
        $transactionStatus = $notif->transaction_status ?? null;
        $fraudStatus = $notif->fraud_status ?? null;
        
        $resolvedMethod = $this->resolveMidtransMethodFromData(
            $notif->payment_type ?? null,
            $notif->va_numbers ?? null,
            $notif->store ?? null,
        );

        // PERBAIKAN 2: Validasi data wajib sebelum diproses
        if (!$orderId) {
            return response()->json([
                'message' => 'Notifikasi tidak valid: order_id tidak ditemukan'
            ], 400);
        }

        $idPesanan = explode('-', $orderId)[1] ?? null;

        if (!$idPesanan) {
            return response()->json([
                'message' => 'Format order_id tidak valid'
            ], 400);
        }

        $pembayaran = Pembayaran::where('id_pesanan', $idPesanan)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        if ($resolvedMethod) {
            $pembayaran->metode_bayar = $resolvedMethod;
        }

        $resolvedStatus = $this->mapTransactionStatusToPaymentStatus($transactionStatus, $fraudStatus);

        if ($resolvedStatus) {
            $pembayaran->status_bayar = $resolvedStatus;
        }

        if ($resolvedStatus === 'paid') {
            $pembayaran->waktu_bayar = now();
        }

        $pembayaran->save();

        return response()->json(['message' => 'Webhook berhasil diproses']);

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

    // ⬇️ INI METHOD SHOW YANG SUDAH DIPERBAIKI (TANPA AUTH)
    public function show($id)
    {
        // Langsung cari pembayaran berdasarkan id_pesanan
        $pembayaran = Pembayaran::where('id_pesanan', $id)->first();

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pembayaran
        ]);
    }

    public function sync(Request $request, $id)
    {
        $pelanggan = $this->resolvePelanggan($request);

        if (! $pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'payment_type' => 'nullable|string|max:50',
            'transaction_status' => 'nullable|string|max:50',
            'fraud_status' => 'nullable|string|max:50',
            'store' => 'nullable|string|max:50',
            'va_numbers' => 'nullable|array',
            'va_numbers.*.bank' => 'nullable|string|max:50',
        ]);

        $pembayaran = Pembayaran::where('id_pesanan', $id)
            ->whereHas('pesanan', function ($query) use ($pelanggan) {
                $query->where('id_pelanggan', $pelanggan->id_pelanggan);
            })
            ->first();

        if (! $pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Data pembayaran belum ada',
            ], 404);
        }

        $resolvedMethod = $this->resolveMidtransMethodFromData(
            $validated['payment_type'] ?? null,
            $validated['va_numbers'] ?? null,
            $validated['store'] ?? null,
        );
        $resolvedStatus = $this->mapTransactionStatusToPaymentStatus(
            $validated['transaction_status'] ?? null,
            $validated['fraud_status'] ?? null,
        );

        if ($resolvedMethod) {
            $pembayaran->metode_bayar = $resolvedMethod;
        }

        if ($resolvedStatus) {
            $pembayaran->status_bayar = $resolvedStatus;
        }

        if ($resolvedStatus === 'paid') {
            $pembayaran->waktu_bayar = now();
        }

        $pembayaran->save();

        return response()->json([
            'success' => true,
            'data' => $pembayaran,
        ]);
    }
}