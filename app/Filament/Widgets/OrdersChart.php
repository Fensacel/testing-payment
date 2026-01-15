<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Statistik Order (7 Hari Terakhir)';

    // Urutkan agar grafik muncul di bawah statistik
    protected static ?int $sort = 2; 

    protected function getData(): array
    {
        // Ambil data 7 hari terakhir secara manual untuk contoh simpel
        // (Untuk produksi yang lebih kompleks bisa pakai package 'flowframe/laravel-trend')

        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M'); // Label Tanggal (Contoh: 15 Jan)

            // Hitung jumlah order sukses pada tanggal tersebut
            $data[] = Order::whereDate('created_at', $date->format('Y-m-d'))
                           ->where('status', 'success')
                           ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Order Sukses',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6', // Warna Biru
                    'borderColor' => '#2563eb',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa diganti 'line' jika ingin grafik garis
    }
}