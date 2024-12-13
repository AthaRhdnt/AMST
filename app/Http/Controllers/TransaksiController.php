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
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $isKaryawan = $user->role->nama_role === 'Karyawan';

        if ($isKaryawan && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        $search = session('transaksi_search', '');
        $outletId = session('outlet_id');

        if ($request->has('search')) {
            $search = $request->input('search');
            session(['transaksi_search' => $search]);
        }
        if ($request->has('outlet_id')) {
            $outletId = $request->input('outlet_id');
            session(['outlet_id' => $outletId]);
        }
        
        $query = Menu::query()->whereNotIn('id_menu', [97, 98, 99]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', '%'.$search.'%');
            });
        }

        $menuItems = $query->paginate(9);

        return view('pages.transaksi.create', compact('menuItems', 'search', 'outletId'));
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

            $timestamp = Transaksi::getTransactionTimestamp();
            $hexTimestamp = strtoupper(dechex($timestamp->getTimestamp() * 1000));

            $lastTransaction = Transaksi::getLastTransaction($id_outlet);

            $startDateTransaction = $lastTransaction 
                ? $lastTransaction->tanggal_transaksi->addDay() 
                : $timestamp->copy()->startOfDay();

            $endDateTransaction = $timestamp->copy()->endOfDay();
            $currentDate = $startDateTransaction->copy();

            while ($currentDate->lessThanOrEqualTo($endDateTransaction)) {
                $transactionExists = Transaksi::transactionExistsForToday($id_outlet, $currentDate);

                if (!$transactionExists) {
                    $hexCurrentTimestamp = strtoupper(dechex($currentDate->getTimestamp() * 1000));
                    Transaksi::createSystemTransaction($request, $currentDate, $hexCurrentTimestamp, $id_outlet);
                }

                $currentDate->addDay();
            }

            $transaksi = Transaksi::create([
                'id_outlet' => $request->input('id_outlet'),
                'kode_transaksi' => $request->input('kode_transaksi'),
                'tanggal_transaksi' => $timestamp->getTimestamp(),
                'total_transaksi' => $request->input('total_transaksi')
            ]);

            $totalPenjualan = 0;
            $shortages = [];
            $totalNeeded = [];

            foreach ($request->input('details') as $detail) {
                $detailTransaksi = DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_menu' => $detail['id_menu'],
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $detail['subtotal']
                ]);

                $totalPenjualan += $detail['subtotal'];
                $menuStocks = $detailTransaksi->menu->stok;

                foreach ($menuStocks as $stok) {
                    $pivotData = $stok->pivot;

                    $stokOutlet = StokOutlet::where('id_outlet', $transaksi->id_outlet)
                            ->where('id_barang', $stok->id_barang)
                            ->first(); 
                
                    $requiredTotal = $pivotData->jumlah * $detail['jumlah'];

                    if ($stokOutlet && $stokOutlet->jumlah >= $requiredTotal) {
                        $stokOutlet->jumlah -= $requiredTotal;  
                        $stokOutlet->save();  

                        $previousRiwayatStok = RiwayatStok::where('id_barang', $stok->id_barang)
                                            ->whereHas('transaksi', function ($query) use ($transaksi) {
                                                $query->where('id_outlet', $transaksi->id_outlet)
                                                    ->whereDate('tanggal_transaksi', '<', $transaksi->tanggal_transaksi);
                                            })
                                            ->orderBy('created_at', 'desc')
                                            ->first();

                        $stokAwal = $previousRiwayatStok && $previousRiwayatStok->transaksi->tanggal_transaksi->isSameDay($transaksi->tanggal_transaksi)
                            ? $previousRiwayatStok->stok_awal
                            : ($previousRiwayatStok->stok_akhir ?? $stok->jumlah);

                        $stokAkhir = $stokOutlet->jumlah;
                    
                        $riwayatStok = RiwayatStok::create([
                            'id_transaksi' => $transaksi->id_transaksi,
                            'id_menu' => $detail['id_menu'],
                            'id_barang' => $stok->id_barang,
                            'stok_awal' => $stokAwal,
                            'jumlah_pakai' => '-' . $requiredTotal,  
                            'stok_akhir' => $stokAkhir,
                            'keterangan' => 'Penjualan',
                        ]);
                    } else {
                        $shortages[] = "Not enough stock for menu item '{$detailTransaksi->menu->nama_menu}' (Ingredient: {$stok->nama_barang}). Available: " . 
                                        ($stokOutlet ? $stokOutlet->jumlah : 0) . 
                                        ", Required: {$requiredTotal}";

                        if (isset($totalNeeded[$stok->nama_barang])) {
                            $totalNeeded[$stok->nama_barang] += $requiredTotal;  
                        } else {
                            $totalNeeded[$stok->nama_barang] = $requiredTotal; 
                        }
                    }
                }
            }

            if (!empty($shortages)) {
                $totalNeededMessage = "\nTotal Needed:\n";
                foreach ($totalNeeded as $ingredient => $amount) {
                    $totalNeededMessage .= "{$ingredient}: {$amount}\n";
                }

                $errorMessage = implode("\n", $shortages) . $totalNeededMessage;

                throw new \Exception($errorMessage);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction recorded successfully',
                'transaction_id' => $transaksi->id_transaksi,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function statusUpdate(Request $request, Transaksi $transaksi)
    {
        //
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
}
