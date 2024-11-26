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
        // // Ensure outlet_id is set for Kasir users on the first request
        $user = auth()->user();
        $isKasir = $user->role->nama_role === 'Kasir';

        if ($isKasir && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        // Retrieve session values or set default values
        $startDate = session('stok_start_date', now()->toDateString());
        $endDate = session('stok_end_date', now()->toDateString());
        $search = session('stok_search', '');
        $entries = session('stok_entries', 5);
        $outletId = session('outlet_id');
        \Log::info('Start Log');
        \Log::info('Start Date Stok:', [$startDate]);
        \Log::info('End Date Stok:', [$endDate]);
        \Log::info('Outlet ID Stok:', [$outletId]);

        if ($request->has('start_date')) {
            $startDate = $request->input('start_date');
            session(['stok_start_date' => $startDate]);
        }

        if ($request->has('end_date')) {
            $endDate = $request->input('end_date');
            session(['stok_end_date' => $endDate]);
        }

        // Update session values if new values are provided
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['stok_search' => $search]);
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
        ->orderBy('stok_akhir', 'asc');

        // Filter by selected outlet if provided
        if ($outletId) {
            $query->where('transaksi.id_outlet', $outletId);
        }

        if ($search) {
            $query->where('stok.nama_barang', 'like', '%' . $search . '%');
        }

        if ($startDate && $endDate) {
            $query->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate]);
        }
    
        // Paginate the results
        $stok = $query->paginate($entries);
        $outlets = Outlets::all();
        $outletName = $isKasir ? $user->outlets->first()->user->nama_user : 'Master';

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
            'minimum' => 'required|integer|min:1',
        ]);

        // Step 1: Create the Stok entry (in the Stok table)
        $stok = Stok::create([
            'nama_barang' => $request->input('nama_barang'),
            'minimum' => $request->input('minimum'), // Initial quantity is 0, as it's only added to outlets next
        ]);

        // Step 2: Retrieve all outlets
        $outlets = Outlets::all();

        // Step 3: Loop through each outlet and create a StokOutlet entry for each one
        foreach ($outlets as $outlet) {
            $stokOutlet= StokOutlet::create([
                'id_outlet' => $outlet->id_outlet, // Outlet ID
                'id_barang' => $stok->id_barang,   // Stok ID (link to the Stok model)
                'jumlah' => $request->input('jumlah_barang'), // Stock quantity for the outlet
            ]);

            $timestamp = Transaksi::getTransactionTimestamp();
            $hexTimestamp = strtoupper(dechex($timestamp->getTimestamp() * 1000));

            $newStok = Transaksi::create([
                'id_outlet' => $outlet->id_outlet,
                'kode_transaksi' => 'ADD-' . $hexTimestamp,
                'tanggal_transaksi' => $timestamp->getTimestamp(),
                'total_transaksi' => 0,
                'created_at' => now(),
            ]);

            // Create RiwayatStok record
            RiwayatStok::create([
                'id_transaksi' => $newStok->id_transaksi,
                'id_menu' => 97, // Adjust this to the correct menu ID
                'id_barang' => $stok->id_barang,
                'stok_awal' => 0,
                'jumlah_pakai' => $request->input('jumlah_barang'),
                'stok_akhir' => $request->input('jumlah_barang'),
                'keterangan' => 'Update Tambah',
                'created_at' => now() ,
            ]);
        }

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
            'minimum' => 'required|integer|min:1',
            'jumlah_barang' => 'required|array', // Use array for outlet-specific quantities
            'jumlah_barang.*' => 'required|integer|min:1', // Ensure each outlet has a valid quantity
        ]);

        // Step 1: Update the base Stok entry (in the Stok table)
        $stok->update([
            'nama_barang' => $request->input('nama_barang'),
            'minimum' => $request->input('minimum'),
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

                // $timestamp = Transaksi::getTransactionTimestamp()->getTimestamp();
                // $hexTimestamp = strtoupper(dechex($timestamp * 1000));

                // // Check if a transaction already exists for that outlet and day
                // $existingTransaction = Transaksi::transactionExistsForToday($id_outlet, $timestamp);
                
                // if (!$existingTransaction) {
                //     $systemTransaction = Transaksi::createSystemTransaction($request, $timestamp, $hexTimestamp, $id_outlet);
                // }

                $timestamp = Transaksi::getTransactionTimestamp();
                $hexTimestamp = strtoupper(dechex($timestamp->getTimestamp() * 1000));

                // Check if a transaction already exists for that outlet and day
                $lastTransaction = Transaksi::getLastTransaction($id_outlet);
                $startDateTransaction = $lastTransaction 
                    ? $lastTransaction->tanggal_transaksi->addDay() // Day after the last transaction
                    : $timestamp->copy()->startOfDay();

                $endDateTransaction = $timestamp->copy()->endOfDay();
                $currentDate = $startDateTransaction->copy();
                while ($currentDate->lessThanOrEqualTo($endDateTransaction)) {
                    // Check if a transaction exists for the current date
                    $transactionExists = Transaksi::transactionExistsForToday($id_outlet, $currentDate);
                    // Create a system transaction if one doesn't exist for the current day
                    if (!$transactionExists) {
                        $hexCurrentTimestamp = strtoupper(dechex($currentDate->getTimestamp() * 1000));
                        Transaksi::createSystemTransaction($request, $currentDate, $hexCurrentTimestamp, $id_outlet);
                    }
                    // Move to the next day
                    $currentDate->addDay();
                }

                $update = Transaksi::create([
                    'id_outlet' => $outlet->id_outlet,
                    'kode_transaksi' => 'UPD-' . $hexTimestamp,
                    'tanggal_transaksi' => $timestamp->getTimestamp(),
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
    public function destroy(Request $request, StokOutlet $stok)
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