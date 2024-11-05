<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use Illuminate\Http\Request;

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stok $stok)
    {
        //
    }
}
