<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        // Retrieve session values or set default values
        $search = session('stok_search', '');
        $entries = session('stok_entries', 5);

        // Update session values if new values are provided
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['stok_search' => $search]);
        } else {
            session()->forget('stok_search');
        }

        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['stok_entries' => $entries]);
        }

        $query = Stok::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%'.$search.'%');
            });
        }

        $stok = $query->paginate($entries);

        return view('pages.stok.index', compact('stok', 'search', 'entries'));
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
            'jumlah_barang' => 'required|integer|min:1',
        ]);

        // Create the category
        Stok::create([
            'nama_barang' => $request->input('nama_barang'),
            'jumlah_barang' => $request->input('jumlah_barang'),
        ]);

        return redirect()->route('stok.index')->with('success', 'Stok berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stok $stok)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stok $stok)
    {
        return view('pages.stok.edit', compact('stok'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stok $stok)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah_barang' => 'required|integer|min:1',
        ]);

        // Update the category
        $stok->update([
            'nama_barang' => $request->input('nama_barang'),
            'jumlah_barang' => $request->input('jumlah_barang'),
        ]);

        return redirect()->route('stok.index')->with('success', 'Stok berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Stok $stok)
    {
        // Check if the admin password is provided
        $adminPassword = $request->input('admin_password');
        
        if ($adminPassword && Hash::check($adminPassword, auth()->user()->password)) {
            // Delete the category
            $stok->delete();
            return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
        }

        return back()->withErrors(['admin_password' => 'Password tidak valid.']);
    }
}
