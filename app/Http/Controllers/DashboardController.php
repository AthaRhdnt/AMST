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
		$isKasir = $user->role->nama_role === 'Kasir';
		$outletId = null;

		// If the user is a Kasir, ensure the outlet ID is set in the session
		if ($isKasir) {
			if (!session()->has('outlet_id')) {
				$outlet = $user->outlets->first();
				if ($outlet) {
					session(['outlet_id' => $outlet->id_outlet]);
				}
			}
			$outletId = session('outlet_id');
		}

		// If outlet_id is provided in the request (like a filter), update session
		if ($request->has('outlet_id')) {
			$outletId = $request->input('outlet_id');
			session(['outlet_id' => $outletId]);
		}

		// Set outlet name for display
		$outletName = $isKasir && $outletId ? $user->outlets->first()->user->nama_user : 'Master';

		// Now apply outlet-based filtering if needed
		$totalSales = Transaksi::whereYear('created_at', Carbon::now()->year)  // Filter by the current year
			->when($outletId, fn($query) => $query->where('id_outlet', $outletId))
			->sum('total_transaksi');

		$transactionsThisMonth = Transaksi::whereMonth('created_at', Carbon::now()->month)  // Filter by the current month
			->when($outletId, fn($query) => $query->where('id_outlet', $outletId))
			->sum('total_transaksi'); 

		$lowStockCount = StokOutlet::when($outletId, fn($query) => $query->where('id_outlet', $outletId))
			->whereHas('stok.menu', function($query) {
				$query->whereHas('stok', function($query) {
					$query->whereHas('menu', function($query) {
						$query->whereHas('stok', function($query) {
							$query->whereRaw('stok_outlet.jumlah < (menu_stok.jumlah * 2)');
						});
					});
				});
			})
			->count();

		$outlets = $user->role->nama_role === 'Pemilik' ? Outlets::with('user')->get() : null;
		$totalOutlets = Outlets::count();

		$topSellingItems = Transaksi::join('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
			->join('menu', 'menu.id_menu', '=', 'detail_transaksi.id_menu')
			->select('menu.nama_menu', \DB::raw('SUM(detail_transaksi.jumlah) as sales_count'))
			->when($outletId, fn($query) => $query->where('transaksi.id_outlet', $outletId))
			->groupBy('menu.nama_menu')
			->orderByDesc('sales_count')
			->paginate(5, ['*'], 'top_selling_page');

        $recentTransactions = Transaksi::select('id_outlet', \DB::raw('SUM(total_transaksi) as total_today'))
            ->whereDate('created_at', Carbon::today())
            ->when($outletId, fn($query) => $query->where('id_outlet', $outletId))
            ->groupBy('id_outlet')
            ->paginate(5, ['*'], 'recent_transactions_page');

		return view('pages.dashboard.dashboard', compact(
			'totalSales',
			'transactionsThisMonth',
			'lowStockCount',
			'outlets',
			'totalOutlets',
			'topSellingItems',
			'recentTransactions',
			'outletName'
		));
	}
}