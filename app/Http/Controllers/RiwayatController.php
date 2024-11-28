<?php

namespace App\Http\Controllers;

use App\Models\Outlets;
use App\Models\Transaksi;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;

class RiwayatController extends Controller
{
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

        // Retrieve session values or set default values
        $startDate = session('start_date', now()->toDateString());
        $endDate = session('end_date', now()->toDateString());
        $search = session('riwayat_transaksi_search', '');
        $entries = session('riwayat_transaksi_entries', 5);
        $outletId = session('outlet_id');

        // Update session values if new values are provided
        if ($request->input('start_date')) {
            $startDate = $request->input('start_date');
            session(['start_date' => $startDate]);
        }
    
        if ($request->input('end_date')) {
            $endDate = $request->input('end_date');
            session(['end_date' => $endDate]); // Save end_date to session
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

        $query = DetailTransaksi::with(['transaksi', 'menu'])
        ->whereHas('transaksi', function($query) {
            $query->where('kode_transaksi', 'LIKE', 'BUY-%')
                ->orWhere('kode_transaksi', 'LIKE', 'ORD-%');
        })
        ->orderBy('id_transaksi', 'desc');

        // Filter by selected outlet if provided
        if ($outletId) {
            $query->whereHas('transaksi', function ($query) use ($outletId) {
                $query->where('id_outlet', $outletId);
            });
        }

        if ($startDate && $endDate) {
            // Filter records between the two dates
            $query->whereHas('transaksi', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            });
        } elseif ($endDate) {
            // Filter records up to the specified end date
            $query->whereHas('transaksi', function ($query) use ($endDate) {
                $query->where('tanggal_transaksi', '<=', $endDate);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('menu', function ($query) use($search) {
                    $query->where('nama_menu', 'LIKE', '%'.$search.'%');
                });
            });
        }

        $transaksi = $query->paginate($entries);

        return view('pages.riwayat.index-transaksi', compact('transaksi', 'search', 'entries', 'startDate', 'endDate', 'outlets', 'outletName'));
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

        // Retrieve session values or set default values
        $startDate = session('start_date', now()->toDateString());
        $endDate = session('end_date', now()->toDateString());
        $search = session('riwayat_stok_search', '');
        $entries = session('riwayat_stok_entries', 5);
        $outletId = session('outlet_id');

        // Update session values if new values are provided
        if ($request->input('start_date')) {
            $startDate = $request->input('start_date');
            session(['start_date' => $startDate]);
        }
    
        if ($request->input('end_date')) {
            $endDate = $request->input('end_date');
            session(['end_date' => $endDate]); // Save end_date to session
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

        // Query logic remains the same
        $query = RiwayatStok::with('transaksi')
                        ->join('transaksi', 'riwayat_stok.id_transaksi', '=', 'transaksi.id_transaksi')
                        ->with('transaksi.outlet')
                        ->select('riwayat_stok.*')
                        ->orderByRaw('CASE WHEN riwayat_stok.keterangan IS NOT NULL THEN 0 ELSE 1 END')
                        ->orderBy('transaksi.tanggal_transaksi', 'desc') // Sort by transaction date in descending order
                        ->orderBy('transaksi.created_at', 'desc')
                        ->orderBy('riwayat_stok.id_riwayat_stok', 'desc')
                        ->orderBy('riwayat_stok.keterangan', 'desc');

        $outlets = Outlets::all();

        // Role-based filtering
        $user = auth()->user();
        $outletName = 'Master';  // Default label for pemilik and admin

        if ($user->role->nama_role === 'Kasir') {
            $outlet = $user->outlets->first();
            $query->where('id_outlet', $outlet->id_outlet);
            $outletName = $outlet->user->nama_user; // Assuming the outlet model has a `name` attribute
        }

        // Filter by selected outlet if provided
        if ($outletId) {
            $query->whereHas('transaksi', function ($q) use ($outletId) {
                $q->where('id_outlet', $outletId);
            });
        }

        if ($startDate && $endDate) {
            // Filter between the start and end dates using the 'tanggal_transaksi' from the 'transaksi' table
            $query->whereHas('transaksi', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            });
        } elseif ($endDate) {
            // If only the end date is provided, filter up to that date
            $query->whereHas('transaksi', function ($q) use ($endDate) {
                $q->where('tanggal_transaksi', '<=', $endDate);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('stok', function ($query) use($search) {
                    $query->where('nama_barang', 'LIKE', '%'.$search.'%');
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
