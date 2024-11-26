<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\Laporan;
use App\Models\Outlets;
use App\Models\Pembelian;
use App\Models\Transaksi;
use App\Models\StokOutlet;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanController extends Controller
{
    protected function applyDateOutletFilter($query, $startDate, $endDate, $outletId, $dateColumn)
    {
        if ($startDate && $endDate) {
            // Filter between start and end dates
            $query->whereBetween($dateColumn, [$startDate, $endDate]);
        } elseif ($endDate) {
            // Filter up to the end date if only the end date is provided
            $query->where($dateColumn, '<=', $endDate);
        }

        // Apply outlet filter if provided
        if ($outletId) {
            $query->where('id_outlet', $outletId);
        }

        return $query;
    }

    public function getTransaksiData($startDate, $endDate, $outletId)
    {
        // Apply filters separately to each query using the helper function
        $transaksiDates = Transaksi::selectRaw('id_outlet, DATE(tanggal_transaksi) as tanggal')
            ->groupBy('id_outlet', 'tanggal');
        $transaksiDates = $this->applyDateOutletFilter($transaksiDates, $startDate, $endDate, $outletId, 'tanggal_transaksi');

        $pembelianDates = Pembelian::selectRaw('id_outlet, DATE(created_at) as tanggal')
            ->groupBy('id_outlet', 'tanggal');
        $pembelianDates = $this->applyDateOutletFilter($pembelianDates, $startDate, $endDate, $outletId, 'created_at');

        // Union the dates from both sources
        $dates = $transaksiDates->union($pembelianDates)->get();

        // Build final report combining data from both sources
        $laporanTransaksi = $dates->map(function ($date) {
            // Retrieve total_penjualan for each date and outlet, or zero if not present
            $total_penjualan = Transaksi::where('id_outlet', $date->id_outlet)
                ->whereDate('tanggal_transaksi', $date->tanggal)
                ->sum('total_transaksi') ?? 0;

            // Retrieve total_pembelian for each date and outlet, or zero if not present
            $total_pembelian = Pembelian::where('id_outlet', $date->id_outlet)
                ->whereDate('created_at', $date->tanggal)
                ->sum('total_harga') ?? 0;

            // Load outlet information
            $outlet = Outlets::find($date->id_outlet);

            // Return the combined data
            return (object) [
                'id_outlet' => $date->id_outlet,
                'tanggal' => $date->tanggal,
                'total_penjualan' => $total_penjualan,
                'total_pembelian' => $total_pembelian,
                'outlet' => $outlet,
            ];
        });

        return $laporanTransaksi;
    }

    public function indexTransaksi(Request $request)
    {
        if ($request->has('reset')) {
            session()->forget(['start_date', 'end_date']);
        }

        $startDate = session('start_date');
        $endDate = session('end_date', now()->toDateString());
        $entries = session('laporan_transaksi_entries', 5); // Default value if not set
        $outletId = session('outlet_id');
    
        if ($request->input('start_date')) {
            $startDate = $request->input('start_date');
            session(['start_date' => $startDate]);
        }
    
        if ($request->input('end_date')) {
            $endDate = $request->input('end_date');
            session(['end_date' => $endDate]); // Save end_date to session
        }
    
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['laporan_transaksi_entries' => $entries]); // Update session with the request value
        }
    
        if ($request->has('outlet_id')) {
            $outletId = $request->input('outlet_id');
            if ($outletId === '') {
                // Clear session if "All Outlets" is selected (empty value)
                session()->forget('outlet_id');
                $outletId = null;
            } else {
                // Save specific outlet_id to session
                session(['outlet_id' => $outletId]);
            }
        }

        $laporanTransaksi = $this->getTransaksiData($startDate, $endDate, $outletId);
    
        // Get the list of outlets for the filter dropdown
        $outlets = Outlets::all();
        $user = auth()->user();
        $outletName = 'Master';  // Default label for pemilik and admin
    
        if ($user->role->nama_role === 'Kasir') {
            $outlet = $user->outlets->first();
            $outletId = $outlet->id_outlet;
            $outletName = $outlet->user->nama_user;
        }

        // Paginate the combined data
        $transaksi = new LengthAwarePaginator(
			$laporanTransaksi->forPage($request->page, $entries), // Items for the current page
			count($laporanTransaksi), // Total items
			$entries, // Items per page
			$request->page, // Current page
			[
				'path' => $request->url(), // URL for pagination links
				'query' => $request->query(), // Query parameters
			]
		);
    
        return view('pages.laporan.index-transaksi', compact('transaksi', 'startDate', 'endDate', 'entries', 'outlets', 'outletName'));
    }

    public function downloadTransaksiPdf(Request $request)
    {
        // Fetch data as in the `indexTransaksi` method
        $startDate = session('start_date');
        $endDate = session('end_date', now()->toDateString());
        $entries = session('laporan_transaksi_entries', 5);
        $outletId = session('outlet_id');

        $query = $this->getTransaksiData($startDate, $endDate, $outletId);

        // Get current date and time in GMT+7 timezone
        $currentDateTime = now()->setTimezone('Asia/Jakarta')->format('dmy_His');

        // Load PDF view
        $pdf = Pdf::loadView('pages.laporan.pdf-transaksi', compact('query', 'startDate', 'endDate', 'outletId'));

        // Dynamically set the file name with today's date and time
        $fileName = 'Transaksi_' . $currentDateTime . '.pdf';

        return $pdf->download($fileName);
    }

    public function getStokData($startDate, $endDate, $outletId)
    {
        // Get dates from both Transaksi and Pembelian tables
        $transaksiDates = Transaksi::selectRaw('id_outlet, DATE(tanggal_transaksi) as tanggal')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            })
            ->when($outletId, function ($query) use ($outletId) {
                $query->where('id_outlet', $outletId);
            })
            ->groupBy('id_outlet', 'tanggal');

        $pembelianDates = Pembelian::selectRaw('id_outlet, DATE(created_at) as tanggal')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($outletId, function ($query) use ($outletId) {
                $query->where('id_outlet', $outletId);
            })
            ->groupBy('id_outlet', 'tanggal');

        // Union the dates from both tables
        $dates = $transaksiDates->union($pembelianDates)->get();

        // Generate report by combining data
        $laporanStok = $dates->map(function ($date) {
            // Total stock usage (pemakaian)
            $total_pemakaian = RiwayatStok::with('transaksi')
                ->whereHas('transaksi', function ($query) use ($date) {
                    $query->whereDate('tanggal_transaksi', $date->tanggal)
                        ->where('id_outlet', $date->id_outlet);
                })
                ->sum('jumlah_pakai') ?? 0;

            // Retrieve total additions (pembelian) for each date and outlet
            $total_pembelian = Pembelian::with('detailPembelian')
                ->where('id_outlet', $date->id_outlet)
                ->whereDate('created_at', $date->tanggal)
                ->get()
                ->sum(function ($pembelian) {
                    return $pembelian->detailPembelian->sum('jumlah');
                }) ?? 0;

            // Get outlet info
            $outlet = Outlets::find($date->id_outlet);

            // Get current stock in the outlet with stock names
            $stokOutlet = StokOutlet::where('id_outlet', $date->id_outlet)
                ->with('stok') // Assuming StokOutlet has a relationship to Stok model
                ->get()
                ->map(function ($stokOutletItem) {
                    return [
                        'nama_barang' => $stokOutletItem->stok->nama_barang,  // Assuming Stok model has 'nama_barang' field
                        'jumlah' => $stokOutletItem->jumlah,         // Assuming quantity in StokOutlet is 'jumlah'
                    ];
                });

            return (object) [
                'id_outlet' => $date->id_outlet,
                'tanggal' => $date->tanggal,
                'total_pemakaian' => $total_pemakaian,
                'total_pembelian' => $total_pembelian,
                'outlet' => $outlet,
                'stokOutlet' => $stokOutlet,  // Correct relationship name here
            ];
        });

        return $laporanStok;
    }

    public function indexStok(Request $request)
    {
        $user = auth()->user();
        $isKasir = $user->role->nama_role === 'Kasir';

        if ($isKasir && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        $startDate = session('start_date', now()->toDateString());
        $endDate = session('end_date', now()->toDateString());
        $search = session('laporan_stok_search', '');
        $entries = session('laporan_stok_entries', 5);
        $outletId = session('outlet_id');
        \Log::info('Start Date Laporan:', [$startDate]);
        \Log::info('End Date Laporan:', [$endDate]);
        \Log::info('Outlet ID Laporan:', [$outletId]);
        \Log::info('End Log');

        if ($request->has('start_date')) {
            $startDate = $request->input('start_date');
            session(['start_date' => $startDate]);
        }

        if ($request->has('end_date')) {
            $endDate = $request->input('end_date');
            session(['end_date' => $endDate]);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            session(['laporan_stok_search' => $search]);
        }

        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['laporan_stok_entries' => $entries]);
        }

        if ($request->has('outlet_id')) {
            $outletId = $request->input('outlet_id');
            session(['outlet_id' => $outletId]);
        }

        $query = RiwayatStok::join('transaksi', 'riwayat_stok.id_transaksi', '=', 'transaksi.id_transaksi')
        ->join('stok', 'riwayat_stok.id_barang', '=', 'stok.id_barang')
        ->leftJoin('outlet', 'transaksi.id_outlet', '=', 'outlet.id_outlet')
        ->leftJoin('users', 'outlet.id_user', '=', 'users.id_user')
        ->select(
            'stok.id_barang',
            'stok.nama_barang',
            DB::raw("
                (
                    SELECT riwayat_stok.stok_awal
                    FROM riwayat_stok
                    JOIN transaksi AS t ON riwayat_stok.id_transaksi = t.id_transaksi
                    WHERE
                        riwayat_stok.id_barang = stok.id_barang
                        AND t.tanggal_transaksi = '{$startDate}'
                        " . (!empty($outletId) ? "AND t.id_outlet = '{$outletId}'" : "") . "
                    ORDER BY riwayat_stok.created_at DESC
                    LIMIT 1
                ) as stok_awal,
                (
                    SELECT
                        SUM(rs.stok_awal) AS total_stok_awal
                    FROM
                        riwayat_stok rs
                    JOIN
                        transaksi t ON rs.id_transaksi = t.id_transaksi
                    LEFT JOIN
                        outlet o ON t.id_outlet = o.id_outlet
                    WHERE
                        rs.id_barang = stok.id_barang
                        AND t.tanggal_transaksi = '{$startDate}'
                        AND rs.created_at = (
                            SELECT MAX(rs_inner.created_at)
                            FROM riwayat_stok rs_inner
                            JOIN transaksi t_inner ON rs_inner.id_transaksi = t_inner.id_transaksi
                            WHERE
                                rs_inner.id_barang = rs.id_barang
                                AND t_inner.tanggal_transaksi = t.tanggal_transaksi
                                AND t_inner.id_outlet = t.id_outlet
                        )
                ) as sum_stok_awal,
                SUM(CASE WHEN riwayat_stok.keterangan = 'Update Tambah' THEN riwayat_stok.jumlah_pakai ELSE 0 END) as jumlah_tambah,
                SUM(CASE WHEN riwayat_stok.keterangan = 'Update Kurang' THEN riwayat_stok.jumlah_pakai ELSE 0 END) as jumlah_kurang,
                SUM(CASE WHEN riwayat_stok.keterangan = 'Pembelian' THEN riwayat_stok.jumlah_pakai ELSE 0 END) as jumlah_beli,
                SUM(CASE WHEN riwayat_stok.keterangan = 'Penjualan' THEN riwayat_stok.jumlah_pakai ELSE 0 END) as jumlah_pakai,
                (
                    SELECT riwayat_stok.stok_akhir
                    FROM riwayat_stok
                    JOIN transaksi AS t ON riwayat_stok.id_transaksi = t.id_transaksi
                    WHERE
                        riwayat_stok.id_barang = stok.id_barang
                        AND t.tanggal_transaksi BETWEEN '{$startDate}' AND '{$endDate}'
                        " . (!empty($outletId) ? "AND t.id_outlet = '{$outletId}'" : "") . "
                    ORDER BY riwayat_stok.created_at DESC
                    LIMIT 1
                ) as stok_akhir,
                (
                    SELECT
                        SUM(rs.stok_akhir) AS total_stok_akhir
                    FROM
                        riwayat_stok rs
                    JOIN
                        transaksi t ON rs.id_transaksi = t.id_transaksi
                    LEFT JOIN
                        outlet o ON t.id_outlet = o.id_outlet
                    WHERE
                        rs.id_barang = stok.id_barang
                        AND t.tanggal_transaksi = '{$endDate}'
                        AND rs.created_at = (
                            SELECT MAX(rs_inner.created_at)
                            FROM riwayat_stok rs_inner
                            JOIN transaksi t_inner ON rs_inner.id_transaksi = t_inner.id_transaksi
                            WHERE
                                rs_inner.id_barang = rs.id_barang
                                AND t_inner.tanggal_transaksi = t.tanggal_transaksi
                                AND t_inner.id_outlet = t.id_outlet
                        )
                ) as sum_stok_akhir
            ")
        )
        ->groupBy(
            'stok.id_barang',
            'stok.nama_barang',
            'riwayat_stok.id_barang'
        )
        ->orderBy('stok.id_barang', 'asc');

        if ($outletId) {
            $query->where('transaksi.id_outlet', $outletId);
        }

        if ($search) {
            $query->where('stok.nama_barang', 'like', '%' . $search . '%');
        }

        if ($startDate && $endDate) {
            $query->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate]);
        }

        $stok = $query->paginate($entries);
        $outlets = Outlets::all();
        $outletName = $isKasir ? $user->outlets->first()->user->nama_user : 'Master';

        return view('pages.laporan.index-stok', compact('stok', 'search', 'entries', 'startDate', 'endDate', 'outlets', 'outletName'));
    }

    // public function downloadStokPdf(Request $request)
    // {
    //     // Fetch data as in the `indexTransaksi` method
    //     $startDate = session('start_date');
    //     $endDate = session('end_date', now()->toDateString());
    //     $entries = session('laporan_transaksi_entries', 5);
    //     $outletId = session('outlet_id');

    //     $query = $this->getTransaksiData($startDate, $endDate, $outletId);

    //     // Get current date and time in GMT+7 timezone
    //     $currentDateTime = now()->setTimezone('Asia/Jakarta')->format('dmy_His');

    //     // Load PDF view
    //     $pdf = Pdf::loadView('pages.laporan.pdf-transaksi', compact('query', 'startDate', 'endDate', 'outletId'));

    //     // Dynamically set the file name with today's date and time
    //     $fileName = 'Transaksi_' . $currentDateTime . '.pdf';

    //     return $pdf->download($fileName);
    // }

    public function resetDateFilters(Request $request)
    {
        $request->session()->forget(['start_date', 'end_date']);

        return redirect()->route('laporan.index.stok');
    }
}
