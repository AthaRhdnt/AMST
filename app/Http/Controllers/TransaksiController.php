<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaksi;
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

        if ($request->input('date_range')) {
            [$startDate, $endDate] = explode(' to ', $request->input('date_range'));
        }

        if ($request->has('entries')) {
            $entries  = $request->input('entries');
            session(['transaksi_entries' => $entries]); // Update session with the request value
        }

        $query = Transaksi::query();

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

        return view('pages.transaksi.index', compact('transaksi', 'startDate', 'endDate', 'entries'));
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

        return view('pages.transaksi.create', compact('menuItems', 'search'));
    }

    public function resetDateFilters(Request $request)
    {
        $request->session()->forget(['start_date', 'end_date']);

        return redirect()->route('transaksi.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $transaksi = Transaksi::create([
                'id_outlet' => $request->input('id_outlet'),
                'kode_transaksi' => $request->input('kode_transaksi'),
                'tanggal_transaksi' => now(),
                'total_transaksi' => $request->input('total_transaksi')
            ]);

            foreach ($request->input('details') as $detail) {
                $detailTransaksi = DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_menu' => $detail['id_menu'],
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $detail['subtotal']
                ]);

                foreach ($detailTransaksi->menu->stok as $stok) {
                    $stok->decrement('jumlah_barang', $detail['jumlah']);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transaction recorded successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
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
}
