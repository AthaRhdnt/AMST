<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Stok;
use App\Models\Outlets;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Sales This Month
        $totalSales = Transaksi::whereMonth('created_at', Carbon::now()->month)
            ->sum('total_transaksi');

        // Transactions Today
        $transactionsToday = Transaksi::whereDate('created_at', Carbon::today())
            ->count();

        // Low Stock Items (Assume threshold of 10)
        $lowStockCount = Stok::where('jumlah_barang', '<', 10)->count();

        // Total Outlets
        $totalOutlets = Outlets::count();

        // Top-Selling Items with corrected column names
        $topSellingItems = Transaksi::join('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
        ->join('menu', 'menu.id_menu', '=', 'detail_transaksi.id_menu')
        ->select('menu.nama_menu', \DB::raw('SUM(detail_transaksi.jumlah) as sales_count'))
        ->groupBy('menu.nama_menu')
        ->orderByDesc('sales_count')
        ->take(5)
        ->get();

        // Recent Transactions
        $recentTransactions = Transaksi::orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('pages.dashboard.dashboard', compact(
            'totalSales',
            'transactionsToday',
            'lowStockCount',
            'totalOutlets',
            'topSellingItems',
            'recentTransactions'
        ));
    }
}