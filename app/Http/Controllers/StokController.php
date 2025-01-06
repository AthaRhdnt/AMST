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
        $user = auth()->user();
        $isKaryawan = $user->role->nama_role === 'Karyawan';

        if ($isKaryawan && !session()->has('outlet_id')) {
            $outlet = $user->outlets->first();
            if ($outlet) {
                session(['outlet_id' => $outlet->id_outlet]);
            }
        }

        $startDate = session('stok_start_date', now()->subDay()->toDateString());
        $endDate = session('stok_end_date', now()->toDateString());
        $search = session('stok_search', '');
        $entries = session('stok_entries', 5);
        $outletId = session('outlet_id');
        $status = session('stok_status', 'active');

        if ($request->has('start_date')) {
            $startDate = $request->input('start_date');
            session(['stok_start_date' => $startDate]);
        }
        if ($request->has('end_date')) {
            $endDate = $request->input('end_date');
            session(['stok_end_date' => $endDate]);
        }
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
        if ($request->has('status')) {
            $status = $request->input('status');
            session(['stok_status' => $status]);
        }
        
        $outlets = Outlets::where('status', 'active')->get();
        $outletName = $isKaryawan ? $user->outlets->first()->user->nama_user : 'Master';

        $query = StokOutlet::with(['stok', 'outlet']);

        if ($outletId) {
            $query->orderBy('jumlah', 'asc')
                ->orderBy('id_barang', 'asc')
                ->whereHas('outlet', function ($q) use ($outletId) {
                    $q->where('id_outlet', $outletId);
                })
                ->whereHas('stok', function ($q) use ($status) {
                    $q->where('status', $status);
                });
        } else {
            $query->selectRaw('stok_outlet.id_barang, SUM(stok_outlet.jumlah) as total_jumlah, SUM(stok.minimum) as total_minimum')
                ->join('stok', 'stok_outlet.id_barang', '=', 'stok.id_barang')
                ->groupBy('stok_outlet.id_barang')
                ->where(function ($q) use ($status) {
                    if ($status) {
                        $q->where('stok.status', $status);
                    }
                })
                ->orderByRaw('MIN(stok_outlet.jumlah) ASC')
                ->orderBy('stok_outlet.id_barang', 'asc');
        }        
        if ($search) {
            $query->whereHas('stok', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%'.$search.'%');
            });
        }

        $stok = $query->paginate($entries);

        $stok->getCollection()->transform(function ($item) {
            $itemStatus = 'Aman';
            $outletStatuses = []; 
            
            if (session('outlet_id') == '') {
                $stokOutlets = StokOutlet::where('id_barang', $item->id_barang)->get();
                
                foreach ($stokOutlets as $stokOutlet) {
                    $stokJumlah = $stokOutlet->jumlah; 
                    $stokMinimum = $stokOutlet->stok->minimum ?? 0; 
        
                    if ($stokJumlah <= 0) {
                        $outletStatuses[] = 'Habis'; 
                    } elseif ($stokJumlah > 0 && $stokJumlah <= $stokMinimum) {
                        $outletStatuses[] = 'Sekarat'; 
                    } else {
                        $outletStatuses[] = 'Aman'; 
                    }
                }
        
                if (in_array('Habis', $outletStatuses)) {
                    $itemStatus = 'Habis'; 
                } elseif (in_array('Sekarat', $outletStatuses) && !in_array('Habis', $outletStatuses)) {
                    $itemStatus = 'Sekarat'; 
                } else {
                    $itemStatus = 'Aman'; 
                }
                
                \Log::info('Item status:', [
                    'Nama Barang' => $item->stok->nama_barang,
                    'outlet_statuses' => $outletStatuses,
                    'item_status' => $itemStatus,
                ]);

            } else {
                $stok = $item->stok;  
                $minimum = $stok->minimum ?? 0;
                $jumlah = $item->jumlah; 
        
                if ($jumlah <= 0) {
                    $itemStatus = 'Habis'; 
                } elseif ($jumlah > 0 && $jumlah <= $minimum) {
                    $itemStatus = 'Sekarat'; 
                } else {
                    $itemStatus = 'Aman'; 
                }
            }
        
            $item->status = $itemStatus;

            return $item;
        });

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

        $stok = Stok::create([
            'nama_barang' => $request->input('nama_barang'),
            'minimum' => $request->input('minimum'), 
            'status' => 'active',
        ]);

        $outlets = Outlets::all();

        foreach ($outlets as $outlet) {
            $stokOutlet= StokOutlet::create([
                'id_outlet' => $outlet->id_outlet, 
                'id_barang' => $stok->id_barang,   
                'jumlah' => $request->input('jumlah_barang'), 
            ]);

            $timestamp = Transaksi::getTransactionTimestamp()->subDay();
            
            $newStok = Transaksi::create([
                'id_outlet' => $outlet->id_outlet,
                'kode_transaksi' => 'SYS-' . $timestamp->format('dmy'),
                'tanggal_transaksi' => $timestamp->getTimestamp(),
                'total_transaksi' => 0,
                'created_at' => $timestamp->getTimestamp(),
                'updated_at' => $timestamp->getTimestamp(),
            ]);

            RiwayatStok::create([
                'id_transaksi' => $newStok->id_transaksi,
                'id_menu' => 97, 
                'id_barang' => $stok->id_barang,
                'stok_awal' => $request->input('jumlah_barang'),
                'jumlah_pakai' => 0,
                'stok_akhir' => $request->input('jumlah_barang'),
                'keterangan' => 'Stok Baru',
                'created_at' => $timestamp->getTimestamp(),
                'updated_at' => $timestamp->getTimestamp(),
            ]);
        }

        return redirect()->route('stok.index')->with('success', 'Stok berhasil ditambahkan ke semua outlet.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stok $stok)
    {
        $outlets = Outlets::where('status', 'active')->get();
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
            'status' => 'required|in:active,inactive',
            'jumlah_barang' => 'required|array', 
            'jumlah_barang.*' => 'required|integer|min:1',
        ]);

        $stok->update([
            'nama_barang' => $request->input('nama_barang'),
            'minimum' => $request->input('minimum'),
        ]);
        $stok->status = $request->status;
        $stok->save();

        $outlets = Outlets::all();

        foreach ($outlets as $outlet) {
            $jumlah = $request->input("jumlah_barang.{$outlet->id_outlet}");

            $stokOutlet = StokOutlet::where('id_outlet', $outlet->id_outlet)
                ->where('id_barang', $stok->id_barang)
                ->first();

            if ($stokOutlet && $stokOutlet->jumlah != $jumlah) {
                $jumlah_update = $jumlah - $stokOutlet->jumlah;
                $stokKeterangan = $jumlah_update >= 0 ? 'Update Tambah' : 'Update Kurang';

                $stokOutlet->update([
                    'jumlah' => $jumlah,
                ]);

                $id_outlet =  $outlet->id_outlet;

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

                $transactionCode = Transaksi::generateTransactionCode('UPD', $id_outlet, $timestamp);

                $update = Transaksi::create([
                    'id_outlet' => $outlet->id_outlet,
                    'kode_transaksi' => $transactionCode,
                    'tanggal_transaksi' => $timestamp->getTimestamp(),
                    'total_transaksi' => 0,
                    'status' => 'selesai',
                    'created_at' => $timestamp->getTimestamp(),
                    'updated_at' => $timestamp->getTimestamp(),
                ]);

                $previousRiwayatStok = RiwayatStok::where('id_barang', $stok->id_barang)
                    ->whereHas('transaksi', function ($query) use ($update) {
                        $query->where('id_outlet', $update->id_outlet)
                            ->whereDate('tanggal_transaksi', '<', $update->tanggal_transaksi);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();

                $stokAwal = $previousRiwayatStok && $previousRiwayatStok->transaksi->tanggal_transaksi->isSameDay($update->tanggal_transaksi)
                    ? $previousRiwayatStok->stok_awal
                    : ($previousRiwayatStok->stok_akhir ?? $stok->jumlah);

                $stokAkhir = $stokOutlet->jumlah;

                RiwayatStok::create([
                    'id_transaksi' => $update->id_transaksi,
                    'id_menu' => 98, 
                    'id_barang' => $stok->id_barang,
                    'stok_awal' => $stokAwal,
                    'jumlah_pakai' => $jumlah_update,
                    'stok_akhir' => $stokAkhir,
                    'keterangan' => $stokKeterangan,
                    'created_at' => $timestamp->getTimestamp(),
                    'updated_at' => $timestamp->getTimestamp(),
                ]);
            }
        }

        return redirect()->route('stok.index')->with('success', 'Stok berhasil diubah untuk outlet yang relevan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Stok $stok, StokOutlet $stokOulet)
    {
        $adminPassword = $request->input('admin_password');

        if ($adminPassword && !Hash::check($adminPassword, auth()->user()->password)) {
            return back()->withErrors(['admin_password' => 'Password admin tidak valid.'])
            ->with(['id_barang' => $stok->id_barang, 'nama_barang' => $stok->nama_barang]);
        }

        if ($stok->riwayatStok()->exists()) {
            $stok->status = 'inactive';
            $stok->save();
            return redirect()->route('stok.index')->with('success', 'Stok ditandai inactive.');
        }

        $stok->delete();
        $stokOulet->delete();

        return redirect()->route('stok.index')->with('success', 'Stok berhasil dihapus');
    }
}