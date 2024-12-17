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
        $status = session('kategori_status', 'active');

        if ($request->has('search')) {
            $search = $request->input('search');
            session(['kategori_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['kategori_entries' => $entries]);
        }
        if ($request->has('status')) {
            $status = $request->input('status');
            session(['kategori_status' => $status]);
        }

        $query = Kategori::where('id_kategori', '!=', 99);

        if ($status) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->where('nama_kategori', 'like', '%' . $search . '%');
        }

        $kategori = $query->paginate($entries);

        return view('pages.kategori.index', compact('kategori', 'search', 'entries', 'status'));
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
            'status' => 'required|in:active,inactive',
        ]);

        Kategori::create([
            'nama_kategori' => $request->input('nama_kategori'),
            'status' => 'active',
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
            'status' => 'required|in:active,inactive',
        ]);

        $kategori->update([
            'nama_kategori' => $request->input('nama_kategori'),
        ]);
        $kategori->status = $request->status;
        $kategori->save();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Kategori $kategori)
    {
        $adminPassword = $request->input('admin_password');

        if ($adminPassword && !Hash::check($adminPassword, auth()->user()->password)) {
            return back()->withErrors(['admin_password' => 'Password admin tidak valid.'])
            ->with(['id_kategori' => $kategori->id_kategori, 'nama_kategori' => $kategori->nama_kategori]);
        }

        if ($kategori->menu()->exists()) {
            \Log::info($kategori->menu()->toSql());
            $kategori->status = 'inactive';
            $kategori->save();
            return redirect()->route('kategori.index')->with('success', 'Kategori ditandai inactive.');
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
