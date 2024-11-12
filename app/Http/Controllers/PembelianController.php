<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\Pembelian;
use App\Models\StokOutlet;
use Illuminate\Http\Request;
use App\Models\DetailPembelian;

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
    public function store(Request $request, StokOutlet $stok)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'visiblePrice' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
        ]);
    
        try {
            // Get the id_outlet from the request
            $id_outlet = $request->input('id_outlet');
    
            // Retrieve the specific StokOutlet record that matches both the id_outlet and id_barang
            $stokOutlet = StokOutlet::where('id_outlet', $id_outlet)
                ->where('id_barang', $stok->id_barang)
                ->first();
    
            // If no matching record is found, return an error
            if (!$stokOutlet) {
                return redirect()->back()->withErrors(['error' => 'Stok untuk outlet ini tidak ditemukan.']);
            }
    
            // Create a new Pembelian record
            $pembelian = Pembelian::create([
                'id_outlet' => $id_outlet,
                'id_barang' => $stok->id_barang,
                'total_harga' => $validated['total_harga'],
            ]);
    
            // Update the jumlah in the correct StokOutlet record
            $stokOutlet->jumlah += $validated['quantity'];
            $stokOutlet->save();
    
            DetailPembelian::create([
                'id_pembelian' => $pembelian->id_pembelian,
                'id_barang' => $stok->id_barang,
                'jumlah' => $validated['quantity'],
                'subtotal' => $validated['total_harga'],
            ]);
    
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
