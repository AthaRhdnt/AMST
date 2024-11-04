<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     // Retrieve session values or set default values
    //     $search = session('search', '');
    //     $entries = session('entries', 5);

    //     // Update session values if new values are provided
    //     if ($request->has('search')) {
    //         $search = $request->input('search');
    //         session(['search' => $search]);
    //     }
    //     if ($request->has('')) {
    //         $entries = $request->input('entries');
    //         session(['entries' => $entries]);
    //     }
        
    //     $query = Kategori::query();

    //     if ($search) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('nama_kategori', 'like', '%'.$search.'%');
    //         });
    //     }

    //     $kategori = $query->paginate($entries);

    //     return view('pages.kategori.index', compact('kategori', 'search', 'entries'));
    // }
    public function index(Request $request)
    {
        // Retrieve session values or set default values
        $search = session('kategori_search', '');
        $entries = session('kategori_entries', 5);

        // Update session values if new values are provided
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['kategori_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['kategori_entries' => $entries]);
        } 

        // Query logic remains the same
        $query = Kategori::query();

        if ($search) {
            $query->where('nama_kategori', 'like', '%'.$search.'%');
        }

        $kategori = $query->paginate($entries);

        return view('pages.kategori.index', compact('kategori', 'search', 'entries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Outlets $outlets)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Outlets $outlets)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Outlets $outlets)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Outlets $outlets)
    {
        //
    }
}
