<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stok;
use App\Models\Outlets;
use App\Models\Pembelian;
use App\Models\Transaksi;
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
        // Ensure outlet_id is set for Kasir users on the first request
        $user = auth()->user();
        $isKasir = $user->role->nama_role === 'Kasir';
        
        if ($isKasir && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        // Retrieve session values or set default values
        $search = session('stok_search', '');
        $entries = session('stok_entries', 5);
        $outletId = session('outlet_id');
        
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
            session(['outlet_id' => $outletId]);
        }
    
        // Initialize query using StokOutlet model
        $query = StokOutlet::with(['stok', 'outlet']); // Eager load 'stok' and 'outlet' relationships
        $outlets = Outlets::all();

        $user = auth()->user();
        $outletName = 'Master'; // Default for Pemilik

        // If the user is 'Kasir', set outlet based on user's outlet, else default to outlet_id 1
        $outletName = 'Master';
        if ($isKasir) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                $outletId = $outlet->id_outlet;
                // session(['outlet_id' => $outletId]);
                $outletName = $outlet->user->nama_user;
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
            $jumlahPembelian = Transaksi::whereHas('detailPembelian', function ($query) use ($item) {
                    $query->where('id_barang', $item->id_barang);
                })
                ->join('detail_pembelian', 'transaksi.id_transaksi', '=', 'detail_pembelian.id_transaksi')
                ->whereDate('transaksi.created_at', now()->toDateString())
                ->where('transaksi.id_outlet', $item->id_outlet) // Ensure purchases are for the specific outlet
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
        $outlets = Outlets::all();
        return view('pages.stok.edit', compact('stok', 'outlets'));
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
            $jumlah = $request->input("jumlah_barang.{$outlet->id_outlet}");

            // Find the specific StokOutlet for this outlet
            $stokOutlet = StokOutlet::where('id_outlet', $outlet->id_outlet)
                ->where('id_barang', $stok->id_barang)
                ->first();

            // Only proceed if the stock quantity has changed
            if ($stokOutlet && $stokOutlet->jumlah != $jumlah) {
                $jumlah_update = $jumlah - $stokOutlet->jumlah;
                $stokKeterangan = $jumlah_update >= 0 ? 'Update Tambah' : 'Update Kurang';

                // Update the StokOutlet quantity
                $stokOutlet->update([
                    'jumlah' => $jumlah,
                ]);

                $id_outlet =  $outlet->id_outlet;

                $timestamp = Transaksi::getTransactionTimestamp()->getTimestamp();
                $hexTimestamp = strtoupper(dechex($timestamp * 1000));

                // Check if a transaction already exists for that outlet and day
                $existingTransaction = Transaksi::transactionExistsForToday($id_outlet, $timestamp);
                
                if (!$existingTransaction) {
                    $systemTransaction = Transaksi::createSystemTransaction($request, $timestamp, $hexTimestamp, $id_outlet);
                }

                $update = Transaksi::create([
                    'id_outlet' => $outlet->id_outlet,
                    'kode_transaksi' => 'UPD-' . $hexTimestamp,
                    'tanggal_transaksi' => $timestamp,
                    'total_transaksi' => 0,
                    'created_at' => now(),
                ]);

                // Fetch the most recent RiwayatStok for this item
                $previousRiwayatStok = RiwayatStok::where('id_barang', $stok->id_barang)
                    ->whereHas('transaksi', function ($query) use ($update) {
                        $query->where('id_outlet', $update->id_outlet)
                            ->whereDate('tanggal_transaksi', '<', $update->tanggal_transaksi);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Determine stok_awal and stok_akhir
                $stokAwal = $previousRiwayatStok && $previousRiwayatStok->transaksi->tanggal_transaksi->isSameDay($update->tanggal_transaksi)
                    ? $previousRiwayatStok->stok_awal
                    : ($previousRiwayatStok->stok_akhir ?? $stok->jumlah);

                $stokAkhir = $stokOutlet->jumlah;

                // Create a RiwayatStok record
                RiwayatStok::create([
                    'id_transaksi' => $update->id_transaksi,
                    'id_menu' => 98, // Adjust this based on your business logic
                    'id_barang' => $stok->id_barang,
                    'stok_awal' => $stokAwal,
                    'jumlah_pakai' => $jumlah_update,
                    'stok_akhir' => $stokAkhir,
                    'keterangan' => $stokKeterangan,
                    'created_at' => now(),
                ]);
            } elseif (!$stokOutlet) {
                // If no StokOutlet exists for this outlet, create a new one
                StokOutlet::create([
                    'id_outlet' => $outlet->id_outlet,
                    'id_barang' => $stok->id_barang,
                    'jumlah' => $jumlah,
                ]);
            }
        }

        return redirect()->route('stok.index')->with('success', 'Stok berhasil diubah untuk outlet yang relevan.');
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