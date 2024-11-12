<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Laporan;
use App\Models\Outlets;
use App\Models\Transaksi;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\StokOutlet;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve session values
        $startDate = session('start_date');
        $endDate = session('end_date', now()->toDateString());
        $entries  = session('transaksi_entries', 5); // Default value if not set
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
            $entries  = $request->input('entries');
            session(['transaksi_entries' => $entries]); // Update session with the request value
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

        $query = Transaksi::with('outlet');
        $outlets = Outlets::all();

        // Role-based filtering
        $user = auth()->user();
        $outletName = 'Master';  // Default label for pemilik and admin

        if ($user->role->nama_role === 'Kasir') {
            $outlet = $user->outlets->first();
            $query->where('id_outlet', $outlet->id_outlet);
            $outletName = $outlet->user->nama_user;
        }

        // Filter by selected outlet if provided
        if ($outletId) {
            $query->where('id_outlet', $outletId);
        }

        if ($startDate && $endDate) {
            // If both dates are provided, filter between the two dates
            $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
        } elseif ($endDate) {
            // If only the end date is provided, filter up to that specific date
            $query->where('tanggal_transaksi', '<=', $endDate);
        }

        $transaksi = $query->orderBy('tanggal_transaksi', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate($entries);

        return view('pages.transaksi.index', compact('transaksi', 'startDate', 'endDate', 'entries', 'outlets', 'outletName'));
    }

    public function resetDateFilters(Request $request)
    {
        $request->session()->forget(['start_date', 'end_date']);

        return redirect()->route('transaksi.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Retrieve session value for search
        $search = session('transaksi_search', '');

        // Update session values if new values are provided
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['transaksi_search' => $search]);
        }
        
        $query = Menu::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', '%'.$search.'%');
            });
        }

        $menuItems = $query->paginate(9);
        $user = auth()->user();
        if ($user->role->nama_role === 'Pemilik') {
            $idOutlet = 1;
        } else {
            $idOutlet = $user->outlets->first()->id_outlet;
        }

        return view('pages.transaksi.create', compact('menuItems', 'search', 'idOutlet'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (empty($request->input('details'))) {
            return response()->json([
                'success' => false,
                'message' => 'No items in the cart'
            ]);
        }
        
        DB::beginTransaction();
    
        try {
            // Create the transaction
            $transaksi = Transaksi::create([
                'id_outlet' => $request->input('id_outlet'),
                'kode_transaksi' => $request->input('kode_transaksi'),
                'tanggal_transaksi' => now(),
                'total_transaksi' => $request->input('total_transaksi')
            ]);
    
            // Variable to track the total sales
            $totalPenjualan = 0;
    
            // Loop through the transaction details (menu items)
            foreach ($request->input('details') as $detail) {
                $detailTransaksi = DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_menu' => $detail['id_menu'],
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $detail['subtotal']
                ]);
    
                // Add to the total sales
                $totalPenjualan += $detail['subtotal'];
    
                // Get related stocks for the menu item
                $menuStocks = $detailTransaksi->menu->stok;
    
                foreach ($menuStocks as $stok) {
                    $pivotData = $stok->pivot;
                
                    // Access related StokOutlet to get the available stock for this outlet
                    $stokOutlet = StokOutlet::where('id_outlet', $transaksi->id_outlet)
                                            ->where('id_barang', $stok->id_barang)
                                            ->first();  // Ensure you're using both outlet and barang to get the correct stokOutlet
                
                    // Ensure enough stock is available based on the pivot quantity
                    if ($stokOutlet && $stokOutlet->jumlah >= $pivotData->jumlah) {
                        // Deduct the stock from the StokOutlet (based on the pivot quantity)
                        $stokOutlet->jumlah -= $pivotData->jumlah;  // Decrease stock
                        $stokOutlet->save();  // Save the updated stock
                
                        // Log the stock usage in RiwayatStok
                        $riwayatStok = RiwayatStok::create([
                            'id_transaksi' => $transaksi->id_transaksi,
                            'id_menu' => $detail['id_menu'],
                            'id_barang' => $stok->id_barang,
                            'jumlah_pakai' => $pivotData->jumlah,  // Use quantity from pivot
                        ]);
                    } else {
                        // Not enough stock available, throw an exception
                        throw new \Exception("Not enough stock for item {$stok->nama_barang}. Available: {$stokOutlet->jumlah}, Required: {$pivotData->jumlah}");
                    }
                }
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaction recorded successfully',
                'transaction_id' => $transaksi->id_transaksi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error for debugging
            \Log::error('Transaction failed:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaksi $transaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $transaksi)
    {
        //
    }

    // public function print($id)
    // {
    //     $penjualan = Transaksi::with('detailTransaksi')->find($id);
    //     $setting = Setting::first();

    //     if (!$penjualan) {
    //         return redirect()->route('jual.index')->with('error', 'Transaksi tidak ditemukan');
    //     }

    //     return view('jual.cetak', compact('penjualan' , 'setting'));
    // }
}
