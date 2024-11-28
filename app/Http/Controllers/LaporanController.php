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
use App\Models\DetailPembelian;
use App\Models\DetailTransaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanController extends Controller
{
    public function getTransaksiData($outletId, $startDate, $endDate)
    {
        $query = Transaksi::with(['detailTransaksi', 'detailPembelian']);

        // Filter by selected outlet if provided
        if ($outletId) {
            $query->where('id_outlet', $outletId);
        }

        if ($startDate && $endDate) {
            // If both dates are provided, filter between the two dates
            $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
        } elseif ($endDate) {
            // If only the end date is provided, filter up to that specific date
            $query->where('tanggal_transaksi', '<=', $endDate);
        }

        return $query;
    }

    public function getFinansialData($outletId, $startDate, $endDate)
    {
        $query = Transaksi::with(['detailTransaksi.stok', 'detailPembelian.stok', 'outlet.user'])
        ->where(function($query) {
            $query->where('kode_transaksi', 'LIKE', 'BUY-%')
                ->orWhere('kode_transaksi', 'LIKE', 'ORD-%');
        })
        ->selectRaw('id_outlet, tanggal_transaksi,
                    SUM(CASE WHEN kode_transaksi LIKE "BUY-%" THEN total_transaksi ELSE 0 END) as total_pembelian,
                    SUM(CASE WHEN kode_transaksi LIKE "ORD-%" THEN total_transaksi ELSE 0 END) as total_penjualan')
        ->groupBy('id_outlet', 'tanggal_transaksi')
        ->orderBy('id_transaksi', 'desc');

        if ($outletId) {
            $query->where('id_outlet', $outletId);
        }

        if ($startDate && $endDate) {
            // If both dates are provided, filter between the two dates
            $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
        } elseif ($endDate) {
            // If only the end date is provided, filter up to that specific date
            $query->where('tanggal_transaksi', '<=', $endDate);
        }

        return $query;
    }

    public function getStokData($outletId, $startDate, $endDate)
    {
        $query = RiwayatStok::join('transaksi', 'riwayat_stok.id_transaksi', '=', 'transaksi.id_transaksi')
        ->join('stok', 'riwayat_stok.id_barang', '=', 'stok.id_barang')
        ->leftJoin('outlet', 'transaksi.id_outlet', '=', 'outlet.id_outlet')
        ->leftJoin('users', 'outlet.id_user', '=', 'users.id_user')
        ->select(
            'stok.id_barang',
            'stok.nama_barang',
            'stok.minimum',
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
                ) as sum_stok_akhir,
                SUM(stok.minimum) as sum_minimum
            ")
        )
        ->groupBy(
            'stok.id_barang',
            'stok.nama_barang',
            'riwayat_stok.id_barang',
            'stok.minimum',
        )
        ->orderBy('stok.id_barang', 'asc');

        if ($outletId) {
            $query->when($outletId, function ($q, $outletId) {
                $q->where('transaksi.id_outlet', $outletId);
            });
        }

        if ($startDate && $endDate) {
            $query->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate]);
            });
        }

        return $query;
    }

    public function indexTransaksi(Request $request)
    {
        $user = auth()->user();
        $isKasir = $user->role->nama_role === 'Kasir';

        if ($isKasir && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
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

        if ($request->has('reset')) {
            session()->forget(['start_date', 'end_date']);
        }

        $outlets = Outlets::all();
        $outletName = $isKasir ? $user->outlets->first()->user->nama_user : 'Master';
        
        $query = $this->getTransaksiData($outletId, $startDate, $endDate)
        ->where(function($query) {
            $query->where('kode_transaksi', 'LIKE', 'BUY-%')
                ->orWhere('kode_transaksi', 'LIKE', 'ORD-%');
        })
        ->orderBy('id_transaksi', 'desc');

        $transaksi = $query->paginate($entries);

        return view('pages.laporan.index-transaksi', compact('transaksi', 'startDate', 'endDate', 'entries', 'outlets', 'outletName'));
    }

    public function indexFinansial(Request $request)
    {
        $user = auth()->user();
        $isKasir = $user->role->nama_role === 'Kasir';

        if ($isKasir && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        $startDate = session('start_date');
        $endDate = session('end_date', now()->toDateString());
        $entries = session('laporan_finansial_entries', 5); // Default value if not set
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
            session(['laporan_finansial_entries' => $entries]); // Update session with the request value
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

        if ($request->has('reset')) {
            session()->forget(['start_date', 'end_date']);
        }
    
        $outlets = Outlets::all();
        $outletName = $isKasir ? $user->outlets->first()->user->nama_user : 'Master';

        $query = $this->getFinansialData($outletId, $startDate, $endDate);
        $finansial = $query->paginate($entries);

        return view('pages.laporan.index-finansial', compact('finansial', 'startDate', 'endDate', 'entries', 'outlets', 'outletName'));
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

        if ($request->has('reset')) {
            session()->forget(['start_date', 'end_date']);
        }

        $outlets = Outlets::all();
        $outletName = $isKasir ? $user->outlets->first()->user->nama_user : 'Master';

        $query = $this->getStokData($outletId, $startDate, $endDate);
        
        if ($search) {
            $query->when($search, function ($q, $search) {
                $q->where('stok.nama_barang', 'like', '%' . $search . '%');
            });
        }

        $stok = $query->paginate($entries);

        return view('pages.laporan.index-stok', compact('stok', 'search', 'entries', 'startDate', 'endDate', 'outlets', 'outletName'));
    }

    public function print(Transaksi $transaksi)
    {
        // Retrieve the transaction with its details
        $transaksi = Transaksi::with('detailTransaksi.menu', 'detailTransaksi.menu.stok')->find($transaksi->id_transaksi);

        if (!$transaksi) {
            return redirect()->route('laporan.index.transaksi')->with('error', 'Transaksi tidak ditemukan');
        }

        // Optionally, you can format the data for printing (e.g., subtotal, taxes, total)
        $totalTransaksi = $transaksi->total_transaksi;
        $details = $transaksi->detailTransaksi;  // All details for the transaction

        return view('pages.print.struk', compact('transaksi', 'details', 'totalTransaksi'));
    }

    public function downloadPdfTransaksi(Request $request)
    {
        $startDate = session('start_date');
        $endDate = session('end_date', now()->toDateString());
        $outletId = session('outlet_id');

        $query = Transaksi::with(['detailTransaksi.stok', 'detailPembelian.stok', 'outlet.user'])
            ->leftJoin('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
            ->leftJoin('detail_pembelian', 'transaksi.id_transaksi', '=', 'detail_pembelian.id_transaksi')
            ->where(function ($query) {
                $query->where('kode_transaksi', 'LIKE', 'BUY-%')
                    ->orWhere('kode_transaksi', 'LIKE', 'ORD-%');
            })
            ->selectRaw('
                transaksi.id_outlet, 
                transaksi.tanggal_transaksi,
                transaksi.kode_transaksi,
                SUM(CASE WHEN transaksi.kode_transaksi LIKE "BUY-%" THEN detail_pembelian.jumlah ELSE 0 END) as jumlah_beli,
                SUM(CASE WHEN transaksi.kode_transaksi LIKE "ORD-%" THEN detail_transaksi.jumlah ELSE 0 END) as jumlah_jual
            ')
            ->groupBy(
                'transaksi.id_outlet', 
                'transaksi.tanggal_transaksi',
                'transaksi.kode_transaksi'
            )
            ->orderBy('transaksi.id_transaksi', 'desc');

        // Log the raw SQL query for debugging
        \Log::info('Generated SQL Query: ', ['query' => $query->toSql()]);

        // Execute the query and fetch results
        $transaksi = $query->get();

        // Log the fetched data for debugging
        \Log::info('Fetched Transactions Data: ', ['data' => $transaksi]);

        // return $transaksi;

        // Load PDF view (debug return view here for now)
        return view('pages.print.pdf-transaksi', compact('transaksi', 'startDate', 'endDate', 'outletId'));

        // $currentDateTime = now()->setTimezone('Asia/Jakarta')->format('dmy_His');
        // $pdf = Pdf::loadView('pages.print.pdf-transaksi', compact('transaksi', 'startDate', 'endDate', 'outletId'));
        // $fileName = 'Transaksi_' . $currentDateTime . '.pdf';
        // return $pdf->download($fileName);
    }

    public function downloadPdfFinansial(Request $request)
    {
        $startDate = session('start_date');
        $endDate = session('end_date', now()->toDateString());
        $outletId = session('outlet_id');

        $query = $this->getFinansialData($outletId, $startDate, $endDate);
        $finansial = $query->get();

        $currentDateTime = now()->setTimezone('Asia/Jakarta')->format('dmy_His');
        $pdf = Pdf::loadView('pages.print.pdf-finansial', compact('finansial', 'startDate', 'endDate', 'outletId'));
        $fileName = 'Finansial_' . $currentDateTime . '.pdf';
        return $pdf->download($fileName);
    }

    public function downloadkPdfStok(Request $request)
    {
        // Fetch data as in the `indexTransaksi` method
        $startDate = session('start_date');
        $endDate = session('end_date', now()->toDateString());
        $outletId = session('outlet_id');

        $query = $this->getStokData($outletId, $startDate, $endDate);
        $stok = $query->get();

        return view('pages.print.pdf-stok', compact('stok', 'startDate', 'endDate', 'outletId'));


        // $currentDateTime = now()->setTimezone('Asia/Jakarta')->format('dmy_His');
        // $pdf = Pdf::loadView('pages.print.pdf-stok', compact('stok', 'startDate', 'endDate', 'outletId'));
        // $fileName = 'Stok_' . $currentDateTime . '.pdf';
        // return $pdf->download($fileName);
    }

}
