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
        $startDate = session('stok_start_date', now()->subDay()->toDateString());
        $endDate = session('stok_end_date', now()->toDateString());
        $search = session('stok_search', '');
        $entries = session('stok_entries', 5);
        $outletId = session('outlet_id');

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
        
        $outlets = Outlets::all();
        $outletName = $isKasir ? $user->outlets->first()->user->nama_user : 'Master';

        $query = StokOutlet::with(['stok', 'outlet']);

        if ($outletId) {
            // If outletId is provided, apply the filtering logic for that outlet
            $query->whereHas('outlet', function ($q) use ($outletId) {
                $q->where('id_outlet', $outletId);
            });
        } else {
            // Aggregation for when no outletId is set
            $query->selectRaw('stok_outlet.id_barang, SUM(stok_outlet.jumlah) as total_jumlah, SUM(stok.minimum) as total_minimum')
                ->join('stok', 'stok_outlet.id_barang', '=', 'stok.id_barang')
                ->groupBy('stok_outlet.id_barang')
                ->orderByRaw('SUM(stok_outlet.jumlah) ASC')  // Correct usage of orderByRaw with aggregation
                ->orderBy('stok_outlet.id_barang', 'asc'); // Order by id_barang as a tie-breaker
        }

        if ($search) {
            $query->whereHas('stok', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%'.$search.'%');
            });
        }

        // Paginate the results
        $stok = $query->orderBy('jumlah', 'asc')
                    ->orderBy('id_barang', 'asc')
                    ->paginate($entries);

        $stok->getCollection()->transform(function ($item) {
            // Initialize the status variable to 'Aman' as the default
            $itemStatus = 'Aman';
            $outletStatuses = []; // Array to store the statuses from each outlet
            
            // If outlet_id is empty (meaning all outlets), check across all outlets for this item
            if (session('outlet_id') == '') {
                // Log the item being processed
                \Log::info('Processing item: ' . $item->id_barang);
        
                // Loop through all the StokOutlets for the current item
                $stokOutlets = StokOutlet::where('id_barang', $item->id_barang)->get();
                
                // Check the stock status for each outlet
                foreach ($stokOutlets as $stokOutlet) {
                    $stokJumlah = $stokOutlet->jumlah; // Quantity for this item at this outlet
                    $stokMinimum = $stokOutlet->stok->minimum ?? 0; // Minimum stock for this outlet
        
                    // Log the details for each outlet
                    \Log::info('Outlet ID: ' . $stokOutlet->id_outlet . ' | Jumlah: ' . $stokJumlah . ' | Minimum: ' . $stokMinimum);
        
                    // Determine the status for this outlet
                    if ($stokJumlah == 0) {
                        $outletStatuses[] = 'Habis'; // If any outlet has 'Habis', mark it
                    } elseif ($stokJumlah > 0 && $stokJumlah <= $stokMinimum) {
                        $outletStatuses[] = 'Sekarat'; // If any outlet has 'Sekarat', mark it
                    } else {
                        $outletStatuses[] = 'Aman'; // If the outlet is 'Aman', keep it as 'Aman'
                    }
                }
        
                // Log the collected outlet statuses for the item
                \Log::info('Outlet statuses for item ' . $item->id_barang . ': ' . implode(', ', $outletStatuses));
        
                // Now, determine the overall status for the item
                if (in_array('Habis', $outletStatuses)) {
                    $itemStatus = 'Habis'; // If any outlet is 'Habis', the overall status is 'Habis'
                } elseif (in_array('Sekarat', $outletStatuses) && !in_array('Habis', $outletStatuses)) {
                    $itemStatus = 'Sekarat'; // If no 'Habis', but any outlet is 'Sekarat', the overall status is 'Sekarat'
                } else {
                    $itemStatus = 'Aman'; // If neither 'Habis' nor 'Sekarat', the status remains 'Aman'
                }
        
                // Log the final status for the item
                \Log::info('Final status for item ' . $item->id_barang . ': ' . $itemStatus);
            } else {
                // If a specific outlet is selected, check only that outlet's stock
                $stok = $item->stok;  // Access the individual stock information for this item
                $minimum = $stok->minimum ?? 0; // Get the minimum stock value for the item
                $jumlah = $item->jumlah; // Get the current quantity of this item in the selected outlet
        
                // Log the selected outlet data
                \Log::info('Selected outlet: ' . session('outlet_id') . ' | Jumlah: ' . $jumlah . ' | Minimum: ' . $minimum);
        
                // Determine the status based on the quantity and minimum stock for the selected outlet
                if ($jumlah == 0) {
                    $itemStatus = 'Habis'; // If quantity is 0, the status is 'Habis'
                } elseif ($jumlah > 0 && $jumlah <= $minimum) {
                    $itemStatus = 'Sekarat'; // If quantity is between 0 and minimum, the status is 'Sekarat'
                } else {
                    $itemStatus = 'Aman'; // If quantity is above minimum, the status is 'Aman'
                }
        
                // Log the final status for the selected outlet
                \Log::info('Final status for selected outlet: ' . $itemStatus);
            }
        
            // Set the calculated status as an attribute of the item
            $item->status = $itemStatus;
        
            // Log the final status of the item
            \Log::info('Item ' . $item->id_barang . ' status set to: ' . $itemStatus);
        
            return $item;
        });

        // $stok->getCollection()->transform(function ($item) {
        //     // Initialize the status variable to 'Aman' as the default
        //     $itemStatus = 'Aman';
        //     $outletStatuses = []; // Array to store the statuses from each outlet
        //     $totalJumlah = 0; // Total quantity for the item across all outlets
        //     $totalMinimum = 0; // Total minimum stock for the item across all outlets
        
        //     // If outlet_id is empty (meaning all outlets), check across all outlets for this item
        //     if (session('outlet_id') == '') {
        //         // Log the item being processed
        //         \Log::info('Processing item: ' . $item->id_barang);
        
        //         // Loop through all the StokOutlets for the current item
        //         $stokOutlets = StokOutlet::where('id_barang', $item->id_barang)->get();
        
        //         // Check the stock status for each outlet
        //         foreach ($stokOutlets as $stokOutlet) {
        //             $stokJumlah = $stokOutlet->jumlah; // Quantity for this item at this outlet
        //             $stokMinimum = $stokOutlet->stok->minimum ?? 0; // Minimum stock for this outlet
        
        //             // Log the details for each outlet
        //             \Log::info('Outlet ID: ' . $stokOutlet->id_outlet . ' | Jumlah: ' . $stokJumlah . ' | Minimum: ' . $stokMinimum);
        
        //             // Aggregate the total quantity and total minimum across all outlets
        //             $totalJumlah += $stokJumlah;
        //             $totalMinimum += $stokMinimum;
        
        //             // Determine the status for this outlet
        //             if ($stokJumlah < $stokMinimum) {
        //                 $outletStatuses[] = 'Habis'; // If any outlet has 'Habis', mark it
        //             } elseif ($stokJumlah > 0 && $stokJumlah <= $stokMinimum) {
        //                 $outletStatuses[] = 'Sekarat'; // If any outlet has 'Sekarat', mark it
        //             } else {
        //                 $outletStatuses[] = 'Aman'; // If the outlet is 'Aman', keep it as 'Aman'
        //             }
        //         }
        
        //         // Log the collected outlet statuses for the item
        //         \Log::info('Outlet statuses for item ' . $item->id_barang . ': ' . implode(', ', $outletStatuses));
        
        //         // Determine the overall status based on the total quantity and minimum
        //         if ($totalJumlah == 0) {
        //             // "Grave" status (priority #1)
        //             $itemStatus = 'Grave'; // If total quantity is 0, mark it as 'Grave'
        //         } elseif ($totalJumlah > 0 && $totalJumlah < $totalMinimum) {
        //             // "Death" status (priority #2)
        //             $itemStatus = 'Death'; // If total quantity < total minimum, mark it as 'Death'
        //         } elseif (in_array('Habis', $outletStatuses)) {
        //             // "Habis" status (priority #3)
        //             $itemStatus = 'Habis'; // If any outlet is 'Habis', the overall status is 'Habis'
        //         } elseif (in_array('Sekarat', $outletStatuses) && !in_array('Habis', $outletStatuses)) {
        //             // "Sekarat" status (priority #4)
        //             $itemStatus = 'Sekarat'; // If no 'Habis', but any outlet is 'Sekarat', the overall status is 'Sekarat'
        //         } else {
        //             // "Aman" status (priority #5)
        //             $itemStatus = 'Aman'; // If neither of the above, keep status as 'Aman'
        //         }
        
        //         // Log the final status for the item
        //         \Log::info('Final status for item ' . $item->id_barang . ': ' . $itemStatus);
        //     } else {
        //         // If a specific outlet is selected, check only that outlet's stock
        //         $stok = $item->stok;  // Access the individual stock information for this item
        //         $minimum = $stok->minimum ?? 0; // Get the minimum stock value for the item
        //         $jumlah = $item->jumlah; // Get the current quantity of this item in the selected outlet
        
        //         // Log the selected outlet data
        //         \Log::info('Selected outlet: ' . session('outlet_id') . ' | Jumlah: ' . $jumlah . ' | Minimum: ' . $minimum);
        
        //         // Determine the status based on the quantity and minimum stock for the selected outlet
        //         if ($jumlah == 0) {
        //             $itemStatus = 'Habis'; // If quantity is 0, the status is 'Habis'
        //         } elseif ($jumlah > 0 && $jumlah <= $minimum) {
        //             $itemStatus = 'Sekarat'; // If quantity is between 0 and minimum, the status is 'Sekarat'
        //         } elseif ($jumlah < $minimum) {
        //             $itemStatus = 'Habis'; // If quantity is less than minimum, the status is 'Habis'
        //         } else {
        //             $itemStatus = 'Aman'; // If quantity is above minimum, the status is 'Aman'
        //         }
        
        //         // Log the final status for the selected outlet
        //         \Log::info('Final status for selected outlet: ' . $itemStatus);
        //     }
        
        //     // Set the calculated status as an attribute of the item
        //     $item->status = $itemStatus;
        
        //     // Log the final status of the item
        //     \Log::info('Item ' . $item->id_barang . ' status set to: ' . $itemStatus);
        
        //     return $item;
        // });

        // $outlets = Outlets::all();
        // $outletName = $isKasir ? $user->outlets->first()->user->nama_user : 'Master';
        // $overallStatus = 'green'; // Default to green
        // $outletStatuses = [];

        // $query = StokOutlet::with(['stok', 'outlet'])
        //                     ->orderBy('jumlah', 'asc');

        // if ($outletId) {
        //     // If outletId is provided, filter by outlet
        //     $query->whereHas('outlet', function ($q) use ($outletId) {
        //         $q->where('id_outlet', $outletId);
        //     });
        // } else {
        //     // If outletId is not provided (empty), sum each id_barang across all outlets
        //     $query->selectRaw('stok_outlet.id_barang, SUM(stok_outlet.jumlah) as total_jumlah, SUM(stok.minimum) as total_minimum')
        //     ->join('stok', 'stok_outlet.id_barang', '=', 'stok.id_barang') // Join stok table to get the minimum value
        //     ->groupBy('stok_outlet.id_barang');  // Only group by id_barang for total sum across all outlets
        // }

        // if ($search) {
        //     $query->whereHas('stok', function ($q) use ($search) {
        //         $q->where('nama_barang', 'like', '%'.$search.'%');
        //     });
        // }

        // // Paginate the results
        // $stok = $query->paginate($entries);

        // $stok->getCollection()->transform(function ($item) use (&$overallStatus, &$outletStatuses, $outletId) {
        //     $minimum = $item->stok->minimum ?? 0; // Assuming 'minimum' is in the stok relation
        //     $jumlah = $item->jumlah;

        //     // Determine the status of each item
        //     if ($jumlah == 0) {
        //         $item->status = 'Habis';
        //     } elseif ($jumlah > 0 && $jumlah <= $minimum) {
        //         $item->status = 'Sekarat';
        //     } else {
        //         $item->status = 'Aman';
        //     }

        //     return $item;
        // });

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