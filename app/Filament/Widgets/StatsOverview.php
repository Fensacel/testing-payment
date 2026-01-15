<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Atur waktu refresh otomatis (opsional, misal tiap 15 detik)
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // Hitung Total Pendapatan (Hanya yang status 'success')
        $income = Order::where('status', 'success')->sum('total_price');

        // Hitung Order Baru (Status 'pending')
        $pendingOrders = Order::where('status', 'pending')->count();

        // Hitung Total Produk Aktif
        $totalProducts = Product::count();

        return [
            // Kartu 1: Total Pendapatan
            Stat::make('Total Pendapatan', 'Rp ' . number_format($income, 0, ',', '.'))
                ->description('Pemasukan bersih dari pesanan sukses')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success') // Warna Hijau
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Grafik mini hiasan

            // Kartu 2: Order Pending
            Stat::make('Menunggu Pembayaran', $pendingOrders)
                ->description('Pesanan yang belum dibayar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'), // Warna Kuning

            // Kartu 3: Total Produk
            Stat::make('Total Produk', $totalProducts)
                ->description('Item digital yang tersedia')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'), // Warna Biru
        ];
    }
}