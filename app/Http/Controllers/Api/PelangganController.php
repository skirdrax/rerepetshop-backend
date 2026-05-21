<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = $request->user(); 
        $pelanggan = Pelanggan::where('email', $user->email)->first();

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan!'
            ], 404);
        }

        $request->validate([
            'nama'     => 'sometimes|string|max:255',
            'no_hp'    => 'sometimes|string|max:15',
            'password' => 'sometimes|nullable|min:6|confirmed',
            'alamat' => 'sometimes|string',
        ]);

        return DB::transaction(function () use ($request, $user, $pelanggan) {
            if ($request->has('nama')) {
                $pelanggan->nama = $request->nama;
                $user->name = $request->nama;
            }

            if ($request->has('no_hp')) {
                $pelanggan->no_hp = $request->no_hp;
            }

            if ($request->filled('password')) {
                $hash = Hash::make($request->password);
                $pelanggan->password = $hash;
                $user->password = $hash;
            }

            if ($request->has('alamat')) {
                $pelanggan->alamat = $request->alamat;
            }

            $pelanggan->save();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Data profil Rere Petshop berhasil diperbarui!',
                'data' => [
                    'user' => $user, 
                    'pelanggan' => $pelanggan,
                ]
            ]);
        });
    }
}