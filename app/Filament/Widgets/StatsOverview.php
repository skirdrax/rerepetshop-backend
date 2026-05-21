<?php

namespace App\Filament\Widgets;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;
    protected function getStats(): array
{
    return [
        // 1. Total Produk (Existing)
        Stat::make('Total Produk', Produk::count())
            ->description('Variasi produk di katalog')
            ->descriptionIcon('heroicon-m-shopping-bag')
            ->color('info'),

        // 2. Stok Menipis (Logika: Stok di bawah 10)
        Stat::make('Stok Menipis', Produk::where('stok', '<', 5)->count())
            ->description('Produk yang hampir habis')
            ->descriptionIcon('heroicon-m-exclamation-triangle')
            ->color('danger'),

        // 3. Pelanggan (Existing)
        Stat::make('Pelanggan Aktif', Pelanggan::count())
            ->description('Pecinta anabul terdaftar')
            ->descriptionIcon('heroicon-m-users')
            ->color('success'),

        // 4. Total Pesanan (Pakai kolom yang pasti ada di database kamu)
       Stat::make('Total Pesanan', Pesanan::count())
    ->description('Semua transaksi masuk')
    ->descriptionIcon('heroicon-m-clipboard-document-list')
    ->chart([7, 2, 10, 3, 15, 4, 17]) // Ini bakal bikin garis grafik naik turun
    ->color('warning'),
    ];
}
}