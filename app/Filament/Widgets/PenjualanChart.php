<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PenjualanChart extends ChartWidget
{
    // Tanpa static
    protected ?string $heading = 'Grafik Pesanan 7 Hari Terakhir';
    protected ?string $maxHeight = '300px';
    protected static ?int $sort = 2; // Biar di bawah angka-angka
    protected int | string | array $columnSpan = 'full';
    
    protected function getData(): array
    {
        // Mengambil data pesanan 7 hari terakhir secara dinamis
        $data = collect(range(6, 0))->map(function ($days) {
            return Pesanan::whereDate('created_at', Carbon::now()->subDays($days))->count();
        });

        $labels = collect(range(6, 0))->map(function ($days) {
            return Carbon::now()->subDays($days)->format('D'); 
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pesanan',
                    'data' => $data->toArray(),
                    'borderColor' => '#fbbf24',
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}