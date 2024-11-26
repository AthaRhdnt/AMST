<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\User;
use App\Models\Outlets;
use App\Models\Transaksi;
use App\Models\StokOutlet;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve session values or set default values
        $search = session('outlet_search', '');
        $entries = session('outlet_entries', 5);
        $status = session('outlet_status', 'active');

        // Update session values if new values are provided
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['outlet_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['outlet_entries' => $entries]);
        } 
        if ($request->has('status')) {
            $status = $request->input('status');
            session(['outlet_status' => $status]);
        }

        $query = Outlets::with('user')->where('status', $status);

        // If status is not empty or null, filter by status
        if (($status)) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama_user', 'like', '%' . $search . '%');
            });
        }

        $outlets = $query->paginate($entries);

        return view('pages.outlet.index', compact('outlets', 'search', 'entries', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.outlet.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_user' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:2',
            'alamat_outlet' => 'required|string|max:255',
            'admin_password' => 'required|string',
        ]);
    
        // Verify the admin password
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return back()->withErrors(['admin_password' => 'Password admin tidak valid.']);
        }
    
        // Create the user for the outlet
        $user = User::create([
            'nama_user' => $request->nama_user,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_role' => 2,
        ]);
    
        // Create the outlet associated with the user
        $outlet = Outlets::create([
            'id_user' => $user->id_user,
            'alamat_outlet' => $request->alamat_outlet,
            'status' => 'active',
        ]);

        // Add stock items to the new outlet
        // Retrieve all stock items
        $stokItems = Stok::all(); // Assuming you want to associate all available stock items

        foreach ($stokItems as $stokItem) {
            // Insert into stok_outlet table for each stock item
            $stokOutlet = StokOutlet::create([
                'id_outlet' => $outlet->id_outlet,
                'id_barang' => $stokItem->id_barang,
                'jumlah' => 9000, // Default quantity set to 1000 for new outlets
            ]);
            $timestamp = Transaksi::getTransactionTimestamp();
            $hexTimestamp = strtoupper(dechex($timestamp->getTimestamp() * 1000));

            $newStok = Transaksi::create([
                'id_outlet' => $stokOutlet->id_outlet,
                'kode_transaksi' => 'ADD-' . $hexTimestamp,
                'tanggal_transaksi' => $timestamp->getTimestamp(),
                'total_transaksi' => 0,
                'created_at' => now(),
            ]);

            // Create RiwayatStok record
            RiwayatStok::create([
                'id_transaksi' => $newStok->id_transaksi,
                'id_menu' => 97, // Adjust this to the correct menu ID
                'id_barang' => $stokItem->id_barang,
                'stok_awal' => 0,
                'jumlah_pakai' => $stokOutlet->jumlah,
                'stok_akhir' => $stokOutlet->jumlah,
                'keterangan' => 'Update Tambah',
                'created_at' => now() ,
            ]);
        }
    
        return redirect()->route('outlets.index')->with('success', 'Outlet created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Outlets $outlets)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Outlets $outlets)
    {
        return view('pages.outlet.edit', compact('outlets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Outlets $outlets)
    {
        $request->validate([
            'nama_user' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $outlets->user->id_user . ',id_user', // Exclude the current user
            'password' => 'nullable|string|min:2|confirmed',
            'alamat_outlet' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'admin_password' => 'required|string',
        ]);
    
        // Verify the admin password
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return back()->withErrors(['admin_password' => 'Password admin tidak valid.']);
        }
    
        // Update the user for the outlet
        $outlets->user->update([
            'nama_user' => $request->nama_user,
            'username' => $request->username,
            'password' => $request->password ? Hash::make($request->password) : $outlets->user->password,
        ]);
    
        // Update the outlet
        $outlets->update([
            'alamat_outlet' => $request->alamat_outlet,
            'status' => $request->status,
        ]);
    
        return redirect()->route('outlets.index')->with('success', 'Outlet updated successfully.');
    }

    public function reset(Request $request, Outlets $outlets)
    {
        $request->validate([
            'password' => 'nullable|string|min:2|confirmed',
            'admin_password' => 'required|string',
        ]);
    
        // Verify the admin password
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return back()->withErrors(['admin_password' => 'Password admin tidak valid.']);
        }
    
        // Update the user for the outlet
        $outlets->user->update([
            'password' => Hash::make('123'),
        ]);
    
        // Update the outlet
        $outlets->update();
    
        return redirect()->route('outlets.index')->with('success', 'Outlet password reset successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Outlets $outlets)
    {
        $adminPassword = $request->input('admin_password');
    
        // Validate admin password
        if (!Hash::check($adminPassword, auth()->user()->password)) {
            return back()->withErrors(['admin_password' => 'Password admin tidak valid.']);
        }
    
        // Check if the outlet has associated transactions
        if ($outlets->transaksi()->exists()) {
            $outlets->update(['status' => 'inactive']);
            return redirect()->route('outlets.index')->with('success', 'Outlet marked as inactive due to existing transactions.');
        }

        $outlets->user()->delete();
        $outlets->delete();
    
        return redirect()->route('outlets.index')->with('success', 'Outlet deleted successfully.');
    }
}
