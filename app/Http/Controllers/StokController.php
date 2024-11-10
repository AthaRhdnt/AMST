<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\Outlets;
use App\Models\Pembelian;
use App\Models\StokOutlet;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve session values or set default values
        $search = session('stok_search', '');
        $entries = session('stok_entries', 5);
        $outletId = session('outlet_id');

        // Log session values for debugging
        \Log::info('Session Data:', [
            'stok_search' => $search,
            'stok_entries' => $entries,
            'outlet_id' => $outletId
        ]);
        
        // Update session values if new values are provided
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['stok_search' => $search]);
        } else {
            session()->forget('stok_search');
        }
    
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['stok_entries' => $entries]);
        }
    
        if ($request->has('outlet_id')) {
            $outletId = $request->input('outlet_id');
            if ($outletId === '') {
                session()->forget('outlet_id');
                $outletId = null;
            } else {
                session(['outlet_id' => $outletId]);
                \Log::info('Outlet ID Set in Session:', ['outlet_id' => $outletId]);
            }
        }
    
        // Initialize query using StokOutlet model
        $query = StokOutlet::with(['stok', 'outlet']); // Eager load 'stok' and 'outlet' relationships
        $outlets = Outlets::all();

        $user = auth()->user();
        $outletName = 'Master'; // Default for Pemilik

        // Filter for 'Kasir' role
        if ($user->role->nama_role === 'Kasir') {
            $outlet = $user->outlets->first();

            if ($outlet) {
                $query->where('id_outlet', $outlet->id_outlet); // Filter by outlet for Kasir
                $outletName = $outlet->user->nama_user;
                session(['outlet_id' => $outlet->id_outlet]);
            } else {
                $query->whereNull('id_outlet'); // Handle no outlet associated with Kasir
            }
        }

        // Additional filter by selected outlet
        if ($outletId) {
            $query->where('id_outlet', $outletId);
        }
    
        // Search filter (if search term is provided)
        if ($search) {
            $query->whereHas('stok', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%'.$search.'%');
            });
        }
    
        // Paginate the results
        $stok = $query->paginate($entries);

        // Calculate additional stock data for each item
        foreach ($stok as $item) {
            // Calculate today's usage for this specific item at this outlet
            $usedToday = RiwayatStok::where('id_barang', $item->id_barang)
                ->whereHas('transaksi', function ($query) use ($item) {
                    // Ensure the RiwayatStok is related to the correct outlet through Transaksi
                    $query->where('id_outlet', $item->id_outlet); // Filter by outlet from the related transaksi
                })
                ->whereDate('created_at', now()->toDateString())
                ->sum('jumlah_pakai');

            $stokAwal = $item->jumlah + $usedToday; // Starting stock for the item at this outlet (from StokOutlet)
                
            // Calculate today's purchases for this specific item at this outlet
            $jumlahPembelian = Pembelian::whereHas('detailPembelian', function ($query) use ($item) {
                $query->where('id_barang', $item->id_barang);
            })
            ->join('detail_pembelian', 'pembelian.id_pembelian', '=', 'detail_pembelian.id_pembelian')
            ->whereDate('pembelian.created_at', now()->toDateString())
            ->where('pembelian.id_outlet', $item->id_outlet) // Ensure purchases are for the specific outlet
            ->sum('detail_pembelian.jumlah');
        
            // Calculate the final stock for the item at this outlet
            $stokAkhir = $stokAwal - $usedToday + $jumlahPembelian;
        
            // Add calculated values to the item
            $item->stok_awal = $stokAwal == 0 ? "N/A ({$stokAwal})" : $stokAwal;
            $item->stok_akhir = $stokAkhir;
            $item->used_today = $usedToday;
            $item->jumlah_pembelian = $jumlahPembelian;
        }

        return view('pages.stok.index', compact('stok', 'search', 'entries', 'outletName', 'outletId', 'outlets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.stok.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah_barang' => 'required|integer|min:1',
        ]);

        // Step 1: Create the Stok entry (in the Stok table)
        $stok = Stok::create([
            'nama_barang' => $request->input('nama_barang'),
            'jumlah_barang' => 0, // Initial quantity is 0, as it's only added to outlets next
        ]);

        // Step 2: Retrieve all outlets
        $outlets = Outlets::all();

        // Step 3: Loop through each outlet and create a StokOutlet entry for each one
        foreach ($outlets as $outlet) {
            StokOutlet::create([
                'id_outlet' => $outlet->id_outlet, // Outlet ID
                'id_barang' => $stok->id_barang,   // Stok ID (link to the Stok model)
                'jumlah' => $request->input('jumlah_barang'), // Stock quantity for the outlet
            ]);
        }

        Stok::updateJumlahBarang($stok->id_barang);

        return redirect()->route('stok.index')->with('success', 'Stok berhasil ditambahkan ke semua outlet.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stok $stok)
    {
        return view('pages.stok.edit', compact('stok'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stok $stok)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah_barang' => 'required|array', // Use array for outlet-specific quantities
            'jumlah_barang.*' => 'required|integer|min:1', // Ensure each outlet has a valid quantity
        ]);
    
        // Step 1: Update the base Stok entry (in the Stok table)
        $stok->update([
            'nama_barang' => $request->input('nama_barang'),
        ]);
    
        // Step 2: Update the quantity (jumlah) for each outlet
        $outlets = Outlets::all();
    
        foreach ($outlets as $outlet) {
            // Get the quantity for the specific outlet
            $jumlah = $request->input("jumlah_barang.{$outlet->id_outlet}");
    
            // Find the specific StokOutlet for this outlet
            $stokOutlet = StokOutlet::where('id_outlet', $outlet->id_outlet)
                ->where('id_barang', $stok->id_barang)
                ->first();
    
            // If the StokOutlet exists, update the quantity
            if ($stokOutlet) {
                $stokOutlet->update([
                    'jumlah' => $jumlah, // Update the quantity for this outlet
                ]);
            } else {
                // If no StokOutlet exists for this outlet, create a new one with the specified quantity
                StokOutlet::create([
                    'id_outlet' => $outlet->id_outlet,
                    'id_barang' => $stok->id_barang,
                    'jumlah' => $jumlah,
                ]);
            }
        }
        Stok::updateJumlahBarang($stok->id_barang);

        return redirect()->route('stok.index')->with('success', 'Stok berhasil diubah ke semua outlet.'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Stok $stok)
    {
        $adminPassword = $request->input('admin_password');
        
        if ($adminPassword && Hash::check($adminPassword, auth()->user()->password)) {
            // Delete the StokOutlet entry
            $stok->delete();

            return redirect()->route('stok.index')->with('success', 'Stok berhasil dihapus.');
        }

        return back()->withErrors(['admin_password' => 'Password tidak valid.']);
    }
}