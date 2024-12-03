<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = session('kategori_search', '');
        $entries = session('kategori_entries', 5);

        if ($request->has('search')) {
            $search = $request->input('search');
            session(['kategori_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['kategori_entries' => $entries]);
        }

        $query = Kategori::where('id_kategori', '!=', 99);

        if ($search) {
            $query->where('nama_kategori', 'like', '%' . $search . '%');
        }

        $kategori = $query->paginate($entries);

        return view('pages.kategori.index', compact('kategori', 'search', 'entries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Kategori::create([
            'nama_kategori' => $request->input('nama_kategori'),
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kategori $kategori)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        return view('pages.kategori.edit', compact('kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori->update([
            'nama_kategori' => $request->input('nama_kategori'),
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Kategori $kategori)
    {
        $adminPassword = $request->input('admin_password');

        if ($adminPassword && Hash::check($adminPassword, auth()->user()->password)) {
            $kategori->delete();
            return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
        }

        return back()->withErrors(['admin_password' => 'Password tidak valid.']);
    }
}
