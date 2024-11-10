<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\Pembelian;
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
    public function store(Request $request, Stok $stok)
    {
        // Log incoming request data
        \Log::info('Request Data:', $request->all());

        // Get outlet_id from session or the request
        $outletId = session('outlet_id');
        
        if ($request->has('outlet_id')) {
            $outletId = $request->input('outlet_id');
            if ($outletId === '') {
                session()->forget('outlet_id');
                $outletId = null;
            } else {
                session(['outlet_id' => $outletId]);
            }
        }

        // Log outlet_id and session status
        \Log::info('Outlet ID:', [$outletId]);

        // Ensure the outlet_id exists before proceeding
        if (!$outletId) {
            \Log::error('Outlet ID is missing');
            return redirect()->back()->withErrors(['outlet_id' => 'Outlet ID is required.']);
        }

        // Validate the request inputs for quantity, price, and total price
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'visiblePrice' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
        ]);

        // Log the validated data
        \Log::info('Validated Data:', $validated);

        $user = auth()->user();
        $outletName = 'Master';

        // If the user is a Kasir, use the outlet associated with the Kasir
        if ($user->role->nama_role === 'Kasir') {
            $outlet = $user->outlets->first();
            $outletId = $outlet->id_outlet;
            $outletName = $outlet->user->nama_user;
        }

        // Log user and outlet data
        \Log::info('User Info:', [$user->id, $user->role->nama_role, $outletName]);

        try {
            // Create a new Pembelian record
            $pembelian = Pembelian::create([
                'id_outlet' => $outletId,
                'id_barang' => $stok->id_barang,
                'total_harga' => $validated['total_harga'],
            ]);

            // Log the created Pembelian
            \Log::info('Pembelian Created:', $pembelian->toArray());

            // Update the stock quantity
            $stok->jumlah_barang += $validated['quantity'];
            $stok->save();

            // Log stock update
            \Log::info('Updated Stock:', ['id_barang' => $stok->id_barang, 'jumlah_barang' => $stok->jumlah_barang]);

            // Create a DetailPembelian record
            DetailPembelian::create([
                'id_pembelian' => $pembelian->id_pembelian,
                'id_barang' => $stok->id_barang,
                'jumlah' => $validated['quantity'],
                'subtotal' => $validated['total_harga'],
            ]);

            // Log DetailPembelian creation
            \Log::info('Detail Pembelian Created');

            // Redirect with success message
            return redirect()->route('stok.index')->with('success', 'Pembelian berhasil!');
        } catch (\Exception $e) {
            // Log any exceptions
            \Log::error('Error during purchase process: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while processing the purchase.']);
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
