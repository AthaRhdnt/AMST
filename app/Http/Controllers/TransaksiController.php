<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\Laporan;
use App\Models\Outlets;
use App\Models\Transaksi;
use App\Models\StokOutlet;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
            $id_outlet = $request->input('id_outlet');

            $timestamp = Transaksi::getTransactionTimestamp()->getTimestamp();
            $hexTimestamp = strtoupper(dechex($timestamp * 1000));

            // Check if a transaction already exists for that outlet and day
            $existingTransaction = Transaksi::transactionExistsForToday($id_outlet, $timestamp);
            
            if (!$existingTransaction) {
                $systemTransaction = Transaksi::createSystemTransaction($request, $timestamp, $hexTimestamp, $id_outlet);
            }

            $timestamp = Transaksi::getTransactionTimestamp()->getTimestamp();
            // Create the transaction
            $transaksi = Transaksi::create([
                'id_outlet' => $request->input('id_outlet'),
                'kode_transaksi' => $request->input('kode_transaksi'),
                'tanggal_transaksi' => $timestamp,
                'total_transaksi' => $request->input('total_transaksi')
            ]);

            // Variable to track the total sales
            $totalPenjualan = 0;
            $shortages = [];
            $totalNeeded = [];

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
                    $requiredTotal = $pivotData->jumlah * $detail['jumlah'];

                    if ($stokOutlet && $stokOutlet->jumlah >= $requiredTotal) {
                        // Deduct the stock from the StokOutlet (based on the pivot quantity)
                        $stokOutlet->jumlah -= $requiredTotal;  // Decrease stock
                        $stokOutlet->save();  // Save the updated stock

                        // Fetch the most recent RiwayatStok for this item
                        $previousRiwayatStok = RiwayatStok::where('id_barang', $stok->id_barang)
                            ->whereHas('transaksi', function ($query) use ($transaksi) {
                                $query->where('id_outlet', $transaksi->id_outlet)
                                    ->whereDate('tanggal_transaksi', '<', $transaksi->tanggal_transaksi);
                            })
                            ->orderBy('created_at', 'desc')
                            ->first();

                        // Determine stok_awal and stok_akhir
                        $stokAwal = $previousRiwayatStok && $previousRiwayatStok->transaksi->tanggal_transaksi->isSameDay($transaksi->tanggal_transaksi)
                            ? $previousRiwayatStok->stok_awal
                            : ($previousRiwayatStok->stok_akhir ?? $stok->jumlah);

                        $stokAkhir = $stokOutlet->jumlah;
                    
                        // Log the stock usage in RiwayatStok
                        $riwayatStok = RiwayatStok::create([
                            'id_transaksi' => $transaksi->id_transaksi,
                            'id_menu' => $detail['id_menu'],
                            'id_barang' => $stok->id_barang,
                            'stok_awal' => $stokAwal,
                            'jumlah_pakai' => '-' . $requiredTotal,  // Use quantity from pivot
                            'stok_akhir' => $stokAkhir,
                            'keterangan' => 'Penjualan',
                        ]);
                    } else {
                        // Collect shortage information
                        $shortages[] = "Not enough stock for menu item '{$detailTransaksi->menu->nama_menu}' (Ingredient: {$stok->nama_barang}). Available: " . 
                                        ($stokOutlet ? $stokOutlet->jumlah : 0) . 
                                        ", Required: {$requiredTotal}";

                        // Add to the total needed for the ingredient
                        if (isset($totalNeeded[$stok->nama_barang])) {
                            $totalNeeded[$stok->nama_barang] += $requiredTotal;  // Accumulate the total for this ingredient
                        } else {
                            $totalNeeded[$stok->nama_barang] = $requiredTotal;  // Initialize the total for this ingredient
                        }
                    }
                }
            }

            // If there are shortages, roll back and return the list
            if (!empty($shortages)) {
                // Prepare the total needed message
                $totalNeededMessage = "\nTotal Needed:\n";
                foreach ($totalNeeded as $ingredient => $amount) {
                    $totalNeededMessage .= "{$ingredient}: {$amount}\n";
                }

                // Combine both the shortages and the total needed message
                $errorMessage = implode("\n", $shortages) . $totalNeededMessage;

                throw new \Exception($errorMessage);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaction recorded successfully',
                'transaction_id' => $transaksi->id_transaksi,
                'print_url' => route('transaksi.print', $transaksi->id_transaksi)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error for debugging
            \Log::error('Transaction failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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

    public function print(Transaksi $transaksi)
    {
        // Retrieve the transaction with its details
        $transaksi = Transaksi::with('detailTransaksi.menu', 'detailTransaksi.menu.stok')->find($transaksi->id_transaksi);

        if (!$transaksi) {
            return redirect()->route('transaksi.index')->with('error', 'Transaksi tidak ditemukan');
        }

        // Optionally, you can format the data for printing (e.g., subtotal, taxes, total)
        $totalTransaksi = $transaksi->total_transaksi;
        $details = $transaksi->detailTransaksi;  // All details for the transaction

        return view('pages.transaksi.struk', compact('transaksi', 'details', 'totalTransaksi'));
    }
}
