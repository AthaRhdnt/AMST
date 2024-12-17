<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stok;
use App\Models\Outlets;
use App\Models\Transaksi;
use App\Models\StokOutlet;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
	{
		$user = auth()->user();
        $isKaryawan = $user->role->nama_role === 'Karyawan';

        $outlet = $user->outlets->first();
        $outletId = $isKaryawan ? $outlet->id_outlet : null;
        $outletName = $isKaryawan ? $outlet->user->nama_user : 'Master';

        $outlets = Outlets::all();
        $totalOutlets = $outlets->count();

        $totalSales = Transaksi::whereYear('tanggal_transaksi', Carbon::now()->year)
			->where('kode_transaksi', 'LIKE', 'ORD-%')
            ->when($outletId, fn($query) => $query->where('id_outlet', $outletId))
            ->sum('total_transaksi');

		$transactionsThisMonth = Transaksi::whereMonth('tanggal_transaksi', Carbon::now()->month)
			->when($outletId, fn($query) => $query->where('id_outlet', $outletId))
			->where('kode_transaksi', 'LIKE', 'ORD-%')
			->sum('total_transaksi'); 

		$lowStock = StokOutlet::with(['stok', 'outlet'])
			->when($outletId, fn($query) => $query->where('id_outlet', $outletId))
			->get()
			->filter(function($item) use ($outletId, $user) {
				// Determine the stock status for each item
				$stok = $item->stok;
				$stokMinimum = $stok->minimum ?? 0; // Use the minimum stock threshold from the stok table
				$stokJumlah = $item->jumlah;

				// Check if item status should be 'Habis' or 'Sekarat'
				$isLowStock = false;
				
				// If user is Pemilik (Owner), show all outlets' stocks
				if ($user->role->nama_role === 'Pemilik' || $outletId == '') {
					if ($stokJumlah == 0 || $stokJumlah <= $stokMinimum) {
						$isLowStock = true; // This item is "low stock" (either Habis or Sekarat)
					}
				} else {
					// If the user is not Pemilik, show stock for their specific outlet
					if ($stokJumlah == 0 || $stokJumlah <= $stokMinimum) {
						$isLowStock = true; // This item is "low stock" (either Habis or Sekarat)
					}
				}

				return $isLowStock; // Only count items that are low stock
			});

		$lowStockCount = $lowStock->count();

		$topSellingItems = Transaksi::join('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
		->join('menu', 'menu.id_menu', '=', 'detail_transaksi.id_menu')
		->select('menu.nama_menu', \DB::raw('SUM(detail_transaksi.jumlah) as sales_count'))
		->when($outletId, fn($query) => $query->where('transaksi.id_outlet', $outletId))
		->whereDate('transaksi.tanggal_transaksi', '<=', today())
		->groupBy('menu.nama_menu')
		->orderByDesc('sales_count')
		->get();
		// ->paginate(5, ['*'], 'top_selling_page');

        $recentTransactions = Transaksi::select('id_outlet', \DB::raw('SUM(total_transaksi) as total_today'))
            ->whereDate('created_at', Carbon::today())
            ->when($outletId, fn($query) => $query->where('id_outlet', $outletId))
            ->groupBy('id_outlet')
            ->get();
            // ->paginate(5, ['*'], 'recent_transactions_page');

		$todayTransactions = Transaksi::with('detailTransaksi.menu')
			->when($outletId, fn($query) => $query->where('id_outlet', $outletId))
			->where(function($query) {
				$query->where('kode_transaksi', 'LIKE', 'ORD-%')
					->whereDate('tanggal_transaksi', today());
			})
			->orderByRaw("FIELD(status, 'proses', 'selesai')")
			->orderBy('id_transaksi', 'asc')
			->get();
			// ->paginate(5);

		return view('pages.dashboard.dashboard', compact(
			'totalSales',
			'transactionsThisMonth',
			'lowStock',
			'lowStockCount',
			'outlets',
			'totalOutlets',
			'topSellingItems',
			'recentTransactions',
			'todayTransactions',
			'outletName',
		));
	}
}