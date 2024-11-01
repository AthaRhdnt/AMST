<?php

namespace App\Http\Controllers;

use App\Models\Outlets;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = 10;
        $query = Outlets::with('user');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama_user', 'like', '%' . $search . '%');
            });
        }
    
        $outlets = $query->paginate($perPage);

        return view('pages.outlet.index', compact('outlets'));
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
