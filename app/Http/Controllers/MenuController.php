<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve session values or set default values
        $search = session('menu_search', '');
        $entries = session('menu_entries', 5);

        // Update session values if new values are provided
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['menu_search' => $search]);
        }
        if ($request->has('entries')) {
            \Log::info('Entries value: ' . $request->input('entries')); // Log the entries value
            $entries = $request->input('entries');
            session(['menu_entries' => $entries]);
        } else {
            \Log::info('No entries value in the request.');
        }

        $query = Menu::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', '%' . $search . '%');
            });
        }

        $menu = $query->paginate($entries);

        \Log::info('Pagination per page: ' . $menu->perPage());
        \Log::info('Request object:', ['request' => $request->all()]);
        \Log::info('Entries parameter in request: ', ['entries' => $request->input('entries')]);

        return view('pages.menu.index', compact('menu', 'search', 'entries'));
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
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        //
    }
}
