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
    public function getTransaksiData($outletId, $startDate, $endDate, $kode)
    {
        $query = Transaksi::with(['detailTransaksi', 'detailPembelian']);

        if ($outletId) {
            $query->where('id_outlet', $outletId);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
        } elseif ($endDate) {
            $query->where('tanggal_transaksi', '<=', $endDate);
        }
        if (!empty($kode)) {
            $query->where('kode_transaksi', 'LIKE', $kode . '%');
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
            $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
        } elseif ($endDate) {
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
                            SELECT rs.stok_awal
                            FROM riwayat_stok rs
                            JOIN transaksi t ON rs.id_transaksi = t.id_transaksi
                            WHERE rs.id_barang = stok.id_barang
                            AND t.tanggal_transaksi = '{$startDate}'
                            AND t.id_outlet = '{$outletId}'
                            AND rs.id_riwayat_stok = (
                                SELECT MAX(rs_inner.id_riwayat_stok)
                                FROM riwayat_stok rs_inner
                                JOIN transaksi t_inner ON rs_inner.id_transaksi = t_inner.id_transaksi
                                WHERE rs_inner.id_barang = rs.id_barang
                                AND t_inner.id_outlet = t.id_outlet
                                AND t_inner.tanggal_transaksi = t.tanggal_transaksi
                            )
                        ) as stok_awal,
                        (
                            SELECT SUM(rs.stok_awal) AS total_stok_awal
                            FROM riwayat_stok rs
                            JOIN transaksi t ON rs.id_transaksi = t.id_transaksi
                            WHERE rs.id_barang = stok.id_barang
                            AND t.tanggal_transaksi = '{$startDate}'
                            AND rs.id_transaksi = (
                                SELECT MIN(rs_inner.id_transaksi)
                                FROM riwayat_stok rs_inner
                                JOIN transaksi t_inner ON rs_inner.id_transaksi = t_inner.id_transaksi
                                WHERE rs_inner.id_barang = rs.id_barang
                                AND t_inner.id_outlet = t.id_outlet
                                AND t_inner.tanggal_transaksi = '{$startDate}'
                                AND rs_inner.created_at = (
                                    SELECT MIN(rs_sub.created_at)
                                    FROM riwayat_stok rs_sub
                                    JOIN transaksi t_sub ON rs_sub.id_transaksi = t_sub.id_transaksi
                                    WHERE rs_sub.id_barang = rs.id_barang
                                    AND t_sub.id_outlet = t.id_outlet
                                    AND t_sub.tanggal_transaksi = t.tanggal_transaksi
                                )
                            )
                        ) as sum_stok_awal,
                        SUM(CASE WHEN riwayat_stok.keterangan = 'Update Tambah' THEN riwayat_stok.jumlah_pakai ELSE 0 END) as jumlah_tambah,
                        SUM(CASE WHEN riwayat_stok.keterangan = 'Update Kurang' THEN riwayat_stok.jumlah_pakai ELSE 0 END) as jumlah_kurang,
                        SUM(CASE WHEN riwayat_stok.keterangan = 'Pembelian' THEN riwayat_stok.jumlah_pakai ELSE 0 END) as jumlah_beli,
                        SUM(CASE WHEN riwayat_stok.keterangan = 'Penjualan' THEN riwayat_stok.jumlah_pakai ELSE 0 END) as jumlah_pakai,
                        (
                            SELECT rs.stok_akhir
                            FROM riwayat_stok rs
                            JOIN transaksi t ON rs.id_transaksi = t.id_transaksi
                            WHERE rs.id_barang = stok.id_barang
                            AND t.tanggal_transaksi BETWEEN '{$startDate}' AND '{$endDate}'
                            AND t.id_outlet = '{$outletId}'
                            AND rs.id_riwayat_stok = (
                                SELECT MAX(rs_inner.id_riwayat_stok)
                                FROM riwayat_stok rs_inner
                                JOIN transaksi t_inner ON rs_inner.id_transaksi = t_inner.id_transaksi
                                WHERE rs_inner.id_barang = rs.id_barang
                                AND t_inner.id_outlet = t.id_outlet
                                AND t_inner.tanggal_transaksi BETWEEN '{$startDate}' AND '{$endDate}'
                            )
                        ) as stok_akhir,
                        (
                            SELECT SUM(rs.stok_akhir) AS total_stok_akhir
                            FROM riwayat_stok rs
                            JOIN transaksi t ON rs.id_transaksi = t.id_transaksi
                            WHERE rs.id_barang = stok.id_barang
                            AND t.tanggal_transaksi BETWEEN '{$startDate}' AND '{$endDate}'
                            AND rs.id_riwayat_stok = (
                                SELECT MAX(rs_inner.id_riwayat_stok)
                                FROM riwayat_stok rs_inner
                                JOIN transaksi t_inner ON rs_inner.id_transaksi = t_inner.id_transaksi
                                WHERE rs_inner.id_barang = rs.id_barang
                                AND t_inner.id_outlet = t.id_outlet
                                AND t_inner.tanggal_transaksi BETWEEN '{$startDate}' AND '{$endDate}'
                                AND rs_inner.created_at = (
                                    SELECT MAX(rs_sub.created_at)
                                    FROM riwayat_stok rs_sub
                                    JOIN transaksi t_sub ON rs_sub.id_transaksi = t_sub.id_transaksi
                                    WHERE rs_sub.id_barang = rs.id_barang
                                    AND t_sub.id_outlet = t.id_outlet
                                    AND t_sub.tanggal_transaksi = t_inner.tanggal_transaksi
                                )
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
        $isKaryawan = $user->role->nama_role === 'Karyawan';

        if ($isKaryawan && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        $startDate = session('transaksi_start_date', now()->toDateString());
        $endDate = session('transaksi_end_date', now()->toDateString());
        $entries = session('laporan_transaksi_entries', 5); 
        $outletId = session('outlet_id');
        $kode = session('kode_transaksi');
    
        if ($request->input('start_date')) {
            $startDate = $request->input('start_date');
            session(['transaksi_start_date' => $startDate]);
        }
        if ($request->input('end_date')) {
            $endDate = $request->input('end_date');
            session(['transaksi_end_date' => $endDate]); 
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['laporan_transaksi_entries' => $entries]); 
        }
        if ($request->has('outlet_id')) {
            $outletId = $request->input('outlet_id');
            if ($outletId === '') {
                session()->forget('outlet_id');
                $outletId = null;
            } else {
                session(['outlet_id' => $outletId]);
            }
        }
        if ($request->has('kode_transaksi')) {
            $kode = $request->input('kode_transaksi');
            session(['kode_transaksi' => $kode]); 
        }
        if ($request->has('reset')) {
            session()->forget(['transaksi_start_date', 'transaksi_end_date']);
            return redirect()->route('laporan.index.transaksi');
        }

        $outlets = Outlets::all();
        $outletName = $isKaryawan ? $user->outlets->first()->user->nama_user : 'Master';
        
        $query = $this->getTransaksiData($outletId, $startDate, $endDate, $kode)
                ->where(function($query) {
                    $query->where('kode_transaksi', 'LIKE', 'BUY-%')
                        ->orWhere('kode_transaksi', 'LIKE', 'ORD-%');
                })
                ->orderBy('id_transaksi', 'desc');

        $transaksi = $query->paginate($entries);

        return view('pages.laporan.index-transaksi', compact('transaksi', 'startDate', 'endDate', 'entries', 'kode', 'outlets', 'outletName'));
    }

    public function indexFinansial(Request $request)
    {
        $user = auth()->user();
        $isKaryawan = $user->role->nama_role === 'Karyawan';

        if ($isKaryawan && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        $startDate = session('finansial_start_date', now()->toDateString());
        $endDate = session('finansial_end_date', now()->toDateString());
        $entries = session('laporan_finansial_entries', 5); 
        $outletId = session('outlet_id');
    
        if ($request->input('start_date')) {
            $startDate = $request->input('start_date');
            session(['finansial_start_date' => $startDate]);
        }
        if ($request->input('end_date')) {
            $endDate = $request->input('end_date');
            session(['finansial_end_date' => $endDate]); 
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['laporan_finansial_entries' => $entries]);  
        }
        if ($request->has('outlet_id')) {
            $outletId = $request->input('outlet_id');
            if ($outletId === '') {
                session()->forget('outlet_id');
                $outletId = null;
            } else {
                session(['outlet_id' => $outletId]);
            }
        }
        if ($request->has('reset')) {
            session()->forget(['finansial_start_date', 'finansial_end_date']);
            return redirect()->route('laporan.index.finansial');
        }
    
        $outlets = Outlets::all();
        $outletName = $isKaryawan ? $user->outlets->first()->user->nama_user : 'Master';

        $query = $this->getFinansialData($outletId, $startDate, $endDate);
        $finansial = $query->paginate($entries);

        return view('pages.laporan.index-finansial', compact('finansial', 'startDate', 'endDate', 'entries', 'outlets', 'outletName'));
    }

    public function indexStok(Request $request)
    {
        $user = auth()->user();
        $isKaryawan = $user->role->nama_role === 'Karyawan';

        if ($isKaryawan && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        $startDate = session('l_stok_start_date', now()->toDateString());
        $endDate = session('l_stok_end_date', now()->toDateString());
        $search = session('laporan_stok_search', '');
        $entries = session('laporan_stok_entries', 5);
        $outletId = session('outlet_id');

        if ($request->has('start_date')) {
            $startDate = $request->input('start_date');
            session(['l_stok_start_date' => $startDate]);
        }
        if ($request->has('end_date')) {
            $endDate = $request->input('end_date');
            session(['l_stok_end_date' => $endDate]);
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
            session()->forget(['l_stok_start_date', 'l_stok_end_date']);
            return redirect()->route('laporan.index.stok');
        }

        $outlets = Outlets::all();
        $outletName = $isKaryawan ? $user->outlets->first()->user->nama_user : 'Master';

        $query = $this->getStokData($outletId, $startDate, $endDate);
        
        if ($search) {
            $query->when($search, function ($q, $search) {
                $q->where('stok.nama_barang', 'like', '%' . $search . '%');
            });
        }

        $stok = $query->paginate($entries);

        return view('pages.laporan.index-stok', compact('stok', 'search', 'entries', 'startDate', 'endDate', 'outlets', 'outletName'));
    }

    public function printOrPreview(Transaksi $transaksi, $action = null)
    {
        $transaksi = Transaksi::with('detailTransaksi.menu', 'detailTransaksi.menu.stok')->find($transaksi->id_transaksi);

        if (!$transaksi) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan');
        }

        $totalTransaksi = $transaksi->total_transaksi;
        $details = $transaksi->detailTransaksi;

        if ($action === 'print') {
            $currentDateTime = now()->setTimezone('Asia/Jakarta')->format('dmy_His');
            $pdf = Pdf::loadView('pages.print.struk', compact('transaksi', 'details', 'totalTransaksi'));
            $fileName = 'Struk_' . $currentDateTime . '.pdf';
            return $pdf->download($fileName);
        } else {
            return view('pages.print.preview', compact('transaksi', 'details', 'totalTransaksi'));
        }
    }

    public function downloadPdfTransaksi(Request $request)
    {
        $startDate = session('transaksi_start_date');
        $endDate = session('transaksi_end_date', now()->toDateString());
        $outletId = session('outlet_id');
        $kode = session('kode_transaksi');

        $query = $this->getTransaksiData($outletId, $startDate, $endDate, $kode)
        ->where(function($query) {
            $query->where('kode_transaksi', 'LIKE', 'BUY-%')
                ->orWhere('kode_transaksi', 'LIKE', 'ORD-%');
        })
        ->orderBy('id_transaksi', 'desc');

        $transaksi = $query->get();

        // return view('pages.print.pdf-transaksi', compact('transaksi', 'startDate', 'endDate', 'outletId', 'kode'));

        $currentDateTime = now()->setTimezone('Asia/Jakarta')->format('dmy_His');
        $pdf = Pdf::loadView('pages.print.pdf-transaksi', compact('transaksi', 'startDate', 'endDate', 'outletId' , 'kode'))
                ->setPaper('A4', 'landscape');
        $fileName = 'Transaksi_' . $currentDateTime . '.pdf';
        return $pdf->download($fileName);
    }

    public function downloadPdfFinansial(Request $request)
    {
        $startDate = session('finansial_start_date');
        $endDate = session('finansial_end_date', now()->toDateString());
        $outletId = session('outlet_id');

        $query = $this->getFinansialData($outletId, $startDate, $endDate);
        $finansial = $query->get();

        // return view('pages.print.pdf-finansial', compact('finansial', 'startDate', 'endDate', 'outletId'));

        $currentDateTime = now()->setTimezone('Asia/Jakarta')->format('dmy_His');
        $pdf = Pdf::loadView('pages.print.pdf-finansial', compact('finansial', 'startDate', 'endDate', 'outletId'))
                ->setPaper('A4', 'landscape');
        $fileName = 'Finansial_' . $currentDateTime . '.pdf';
        return $pdf->download($fileName);
    }

    public function downloadkPdfStok(Request $request)
    {
        $user = auth()->user();
        $isKaryawan = $user->role->nama_role === 'Karyawan';

        if ($isKaryawan && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        $outletName = $isKaryawan ? $user->outlets->first()->user->nama_user : 'Keseluruhan';

        $startDate = session('l_stok_start_date');
        $endDate = session('l_stok_end_date', now()->toDateString());
        $outletId = session('outlet_id');

        $query = $this->getStokData($outletId, $startDate, $endDate);
        $stok = $query->get();

        // return view('pages.print.pdf-stok', compact('stok', 'startDate', 'endDate', 'outletId', 'outletName'));

        $currentDateTime = now()->setTimezone('Asia/Jakarta')->format('dmy_His');
        $pdf = Pdf::loadView('pages.print.pdf-stok', compact('stok', 'startDate', 'endDate', 'outletId', 'outletName'))
                ->setPaper('A4', 'landscape');
        $fileName = 'Stok_' . $currentDateTime . '.pdf';
        return $pdf->download($fileName);
    }

}
