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
use Illuminate\Pagination\Paginator;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexTransaksi(Request $request)
    {
        // Retrieve session values
        $startDate = session('start_date');
        $endDate = session('end_date', now()->toDateString());
        $entries = session('lapooran_transaksi_entries', 5); // Default value if not set
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
            session(['lapooran_transaksi_entries' => $entries]); // Update session with the request value
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
    
        $user = auth()->user();
        $outletName = 'Master';  // Default label for pemilik and admin
    
        if ($user->role->nama_role === 'Kasir') {
            $outlet = $user->outlets->first();
            $outletId = $outlet->id_outlet;
            $outletName = $outlet->user->nama_user;
        }
    
        // Aggregating purchases (Pembelian) and sales (Transaksi) per date
        $query = Transaksi::selectRaw('id_outlet, DATE(tanggal_transaksi) as tanggal, SUM(total_transaksi) as total_penjualan')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            })
            ->when($outletId, function ($query) use ($outletId) {
                $query->where('id_outlet', $outletId);
            })
            ->groupBy('id_outlet', 'tanggal')
            ->orderBy('tanggal', 'desc');
    
        // Adding the total pembelian for each outlet and date
        $transaksi = $query->paginate($entries)->through(function ($item) use ($startDate, $endDate, $outletId) {
            $item->total_pembelian = Pembelian::where('id_outlet', $item->id_outlet)
                ->whereDate('created_at', $item->tanggal)
                ->sum('total_harga');
            return $item;
        });
    
        $outlets = Outlets::all();
    
        return view('pages.laporan.index-transaksi', compact('transaksi', 'startDate', 'endDate', 'entries', 'outlets', 'outletName'));
    }

    public function indexStok(Request $request)
    {
        // Retrieve session values or set default values
        $startDate = session('start_date');
        $endDate = session('end_date', now()->toDateString());
        $search = session('lapooran_stok_search', '');
        $entries = session('lapooran_stok_entries', 5);
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
            session(['lapooran_stok_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['lapooran_stok_entries' => $entries]);
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
    
        $query = StokOutlet::with(['stok', 'outlet']);
    
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
            $query->where('id_outlet', $outletId);
        }
    
        if ($startDate && $endDate) {
            // Filter between the start and end dates using the 'tanggal_transaksi' from the 'transaksi' table
            $query->whereHas('outlet.transaksi', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            });
        } elseif ($endDate) {
            // If only the end date is provided, filter up to that date
            $query->whereHas('outlet.transaksi', function ($q) use ($endDate) {
                $q->where('tanggal_transaksi', '<=', $endDate);
            });
        }
    
        if ($search) {
            $query->whereHas('stok', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%');
            });
        }
    
        // Fetch the stock items (grouped by outlet)
        $stok = $query->paginate($entries);

        foreach ($stok as $item) {
            // Calculate total usage (penjualan) for this item at this outlet
            $totalPemakaian = RiwayatStok::where('id_barang', $item->id_barang)
                ->whereHas('transaksi', function ($query) use ($item) {
                    // Ensure the RiwayatStok is related to the correct outlet through Transaksi
                    $query->where('id_outlet', $item->id_outlet); // Filter by outlet from the related transaksi
                })
                ->sum('jumlah_pakai');  // Sum of all usage (penjualan)
        
            // Calculate total purchases (pembelian) for this item at this outlet
            $totalPembelian = Pembelian::whereHas('detailPembelian', function ($query) use ($item) {
                $query->where('id_barang', $item->id_barang);
            })
            ->join('detail_pembelian', 'pembelian.id_pembelian', '=', 'detail_pembelian.id_pembelian')
            ->where('pembelian.id_outlet', $item->id_outlet) // Ensure purchases are for the specific outlet
            ->sum('detail_pembelian.jumlah'); // Sum of all purchases (pembelian)
        
            // Store the calculated values in the item for view display
            $item->total_pembelian = $totalPembelian;
            $item->total_pemakaian = $totalPemakaian;
        }

        return view('pages.laporan.index-stok', compact('stok', 'search', 'entries', 'startDate', 'endDate', 'outlets', 'outletName'));
    }

    public function resetDateFilters(Request $request)
    {
        $request->session()->forget(['start_date', 'end_date']);

        return redirect()->back();
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Laporan $laporan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laporan $laporan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laporan $laporan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laporan $laporan)
    {
        //
    }
}
