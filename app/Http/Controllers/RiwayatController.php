<?php

namespace App\Http\Controllers;

use App\Models\Outlets;
use App\Models\Transaksi;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RiwayatController extends Controller
{
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

        $startDate = session('start_date', now()->toDateString());
        $endDate = session('end_date', now()->toDateString());
        $search = session('riwayat_transaksi_search', '');
        $entries = session('riwayat_transaksi_entries', 5);
        $outletId = session('outlet_id');
        $kode = session('kode_transaksi');

        if ($request->input('start_date')) {
            $startDate = $request->input('start_date');
            session(['start_date' => $startDate]);
        }
        if ($request->input('end_date')) {
            $endDate = $request->input('end_date');
            session(['end_date' => $endDate]); 
        }
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['riwayat_transaksi_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['riwayat_transaksi_entries' => $entries]);
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
            session()->forget(['start_date', 'end_date']);
            return redirect()->route('riwayat.index.transaksi');
        }

        $outlets = Outlets::where('status', 'active')->get();
        $outletName = $isKaryawan ? $user->outlets->first()->user->nama_user : 'Master';
        
        $query = Transaksi::with(['detailTransaksi.menu', 'detailPembelian'])
            ->where(function($query) {
                $query->where('kode_transaksi', 'LIKE', 'BUY-%')
                    ->orWhere('kode_transaksi', 'LIKE', 'ORD-%');
            })
            ->orderBy('id_transaksi', 'desc');

        if ($outletId) {
            $query->where('id_outlet', $outletId);
            
        }
        if ($startDate && $endDate) {
            $query->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            });
        }
        if ($search) {
            $query->whereHas('detailTransaksi.menu', function ($query) use ($search) {
                $query->where('nama_menu', 'LIKE', '%' . $search . '%');
            });
        }
        if (!empty($kode)) {
            $query->where('kode_transaksi', 'LIKE', $kode . '%');
        }

        $rawTransaksi = $query->get();

        foreach ($rawTransaksi as $data) {
            $data->detailTransaksi = $data->detailTransaksi->filter(function ($detail) use ($search) {
                return stripos($detail->menu->nama_menu, $search) !== false;
            });
        }

        $details = [];
        foreach ($rawTransaksi as $data) {
            foreach ($data->detailTransaksi as $penjualan) {
                $details[] = [
                    'tanggal_transaksi' => $data->tanggal_transaksi,
                    'created_at' => $data->created_at,
                    'kode_transaksi' => $data->kode_transaksi,
                    'nama_user' => $data->outlet->user->nama_user ?? null,
                    'nama_item' => $penjualan->menu->nama_menu,
                    'jumlah' => $penjualan->jumlah,
                    'subtotal' => $penjualan->subtotal,
                    'status' => $data->status,
                ];
            }

            foreach ($data->detailPembelian as $pembelian) {
                $details[] = [
                    'tanggal_transaksi' => $data->tanggal_transaksi,
                    'created_at' => $data->created_at,
                    'kode_transaksi' => $data->kode_transaksi,
                    'nama_user' => $data->outlet->user->nama_user ?? null,
                    'nama_item' => $pembelian->stok->nama_barang,
                    'jumlah' => $pembelian->jumlah,
                    'subtotal' => $pembelian->subtotal,
                    'status' => $data->status,
                ];
            }
        }
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $entries;
        $currentItems = array_slice($details, ($currentPage - 1) * $perPage, $perPage);

        $transaksi = new LengthAwarePaginator(
            $currentItems,
            count($details),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('pages.riwayat.index-transaksi', compact('transaksi', 'search', 'entries', 'startDate', 'endDate', 'outlets', 'outletName'));
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

        $startDate = session('start_date', now()->toDateString());
        $endDate = session('end_date', now()->toDateString());
        $search = session('riwayat_stok_search', '');
        $entries = session('riwayat_stok_entries', 5);
        $outletId = session('outlet_id');

        if ($request->input('start_date')) {
            $startDate = $request->input('start_date');
            session(['start_date' => $startDate]);
        }
        if ($request->input('end_date')) {
            $endDate = $request->input('end_date');
            session(['end_date' => $endDate]); 
        }
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['riwayat_stok_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['riwayat_stok_entries' => $entries]);
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
            session()->forget(['start_date', 'end_date']);
            return redirect()->route('riwayat.index.stok');
        }

        $outlets = Outlets::where('status', 'active')->get();
        $outletName = $isKaryawan ? $user->outlets->first()->user->nama_user : 'Master';

        $query = RiwayatStok::with('transaksi')
                ->join('transaksi', 'riwayat_stok.id_transaksi', '=', 'transaksi.id_transaksi')
                ->with('transaksi.outlet')
                ->select('riwayat_stok.*')
                ->orderBy('riwayat_stok.id_riwayat_stok', 'desc')
                ->orderBy('transaksi.tanggal_transaksi', 'asc') 
                ->orderBy('transaksi.created_at', 'asc')
                ;

        if ($outletId) {
            $query->whereHas('transaksi', function ($q) use ($outletId) {
                $q->where('id_outlet', $outletId);
            });
        }
        if ($startDate && $endDate) {
            $query->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate]);
            });
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('stok', function ($query) use ($search) {
                    $query->where('nama_barang', 'LIKE', '%' . $search . '%');
                });
            });
        }

        $riwayat = $query->paginate($entries);

        return view('pages.riwayat.index-stok', compact('riwayat', 'search', 'entries', 'startDate', 'endDate', 'outlets', 'outletName'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RiwayatStok $riwayat)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RiwayatStok $riwayat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatStok $riwayat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatStok $riwayat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatStok $riwayat)
    {
        //
    }
}
