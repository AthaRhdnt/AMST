<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stok;
use App\Models\Pembelian;
use App\Models\Transaksi;
use App\Models\StokOutlet;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use App\Models\DetailPembelian;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, Stok $stok)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'visiblePrice' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
    
        try {
            $id_outlet = $request->input('id_outlet');
    
            $stokOutlet = StokOutlet::where('id_outlet', $id_outlet)
                ->where('id_barang', $stok->id_barang)
                ->first();
    
            if (!$stokOutlet) {
                return redirect()->back()->withErrors(['error' => 'Stok untuk outlet ini tidak ditemukan.']);
            }

            $timestamp = Transaksi::getTransactionTimestamp();
            $lastTransaction = Transaksi::getLastTransaction($id_outlet);

            $startDateTransaction = $lastTransaction 
                ? $lastTransaction->tanggal_transaksi->addDay() 
                : $timestamp->copy()->startOfDay();

            $endDateTransaction = $timestamp->copy()->endOfDay();
            $currentDate = $startDateTransaction->copy();

            while ($currentDate->lessThanOrEqualTo($endDateTransaction)) {
                $transactionExists = Transaksi::transactionExistsForToday($id_outlet, $currentDate);

                if (!$transactionExists) {
                    Transaksi::createSystemTransaction($currentDate, $id_outlet);
                }

                $currentDate->addDay();
            }

            $transactionCode = Transaksi::generateTransactionCode('BUY', $id_outlet, $timestamp);
            
            $pembelian = Transaksi::create([
                'id_outlet' => $id_outlet,
                'kode_transaksi' => $transactionCode,
                'tanggal_transaksi' => $timestamp->getTimestamp(),
                'total_transaksi' => $validated['total_harga'],
                'status' => 'selesai',
                'created_at' => $timestamp->getTimestamp(),
                'updated_at' => $timestamp->getTimestamp(),
            ]);
    
            $stokOutlet->jumlah += $validated['quantity'];
            $stokOutlet->save();
    
            DetailPembelian::create([
                'id_transaksi' => $pembelian->id_transaksi,
                'id_barang' => $stok->id_barang,
                'jumlah' => $validated['quantity'],
                'subtotal' => $validated['total_harga'],
            ]);

            $previousRiwayatStok = RiwayatStok::where('id_barang', $stok->id_barang)
                                ->whereHas('transaksi', function ($query) use ($pembelian) {
                                    $query->where('id_outlet', $pembelian->id_outlet)
                                        ->whereDate('tanggal_transaksi', '<', $pembelian->tanggal_transaksi);
                                })
                                ->orderBy('created_at', 'desc')
                                ->first();

            $stokAwal = $previousRiwayatStok && $previousRiwayatStok->transaksi->tanggal_transaksi->isSameDay($pembelian->tanggal_transaksi)
                ? $previousRiwayatStok->stok_awal
                : ($previousRiwayatStok->stok_akhir ?? $stok->jumlah);

            $stokAkhir = $stokOutlet->jumlah;
        
            $riwayatStok = RiwayatStok::create([
                'id_transaksi' => $pembelian->id_transaksi,
                'id_menu' => 99,
                'id_barang' => $stok->id_barang,
                'stok_awal' => $stokAwal,
                'jumlah_pakai' => $validated['quantity'],  
                'stok_akhir' => $stokAkhir,
                'keterangan' => 'Pembelian',
                'created_at' => $timestamp->getTimestamp(),
                'updated_at' => $timestamp->getTimestamp(),
            ]);
            
            DB::commit();
            return redirect()->route('stok.index')->with('success', 'Pembelian berhasil!');
        } catch (\Exception $e) {
            \Log::error('Error during purchase process: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to process purchase.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembelian $pembelian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembelian $pembelian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembelian $pembelian)
    {
        //
    }
}
