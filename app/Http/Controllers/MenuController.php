<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Stok;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = session('menu_search', '');
        $entries = session('menu_entries', 5);
        $status = session('menu_status', 'active');

        if ($request->has('search')) {
            $search = $request->input('search');
            session(['menu_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['menu_entries' => $entries]);
        }
        if ($request->has('status')) {
            $status = $request->input('status');
            session(['menu_status' => $status]);
        }

        $query = Menu::with('kategori')->whereNotIn('id_menu', [97, 98, 99]);

        if ($status) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'LIKE', '%' . $search . '%')
                ->orWhereHas('kategori', function ($query) use($search) {
                    $query->where('nama_kategori', 'LIKE', '%'.$search.'%');
                });
            });
        }

        $menu = $query->paginate($entries);

        return view('pages.menu.index', compact('menu', 'search', 'entries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::all()->where('id_kategori', '!=', 99);
        $stok = Stok::all(); 
        return view('pages.menu.create', compact('kategori', 'stok'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string',
            'harga_menu' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'stok' => 'required|array',
            'stok.*' => 'exists:stok,id_barang',
            'jumlah' => 'required|array',
            'jumlah.*' => 'numeric|min:1',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $newName = now()->timestamp . '.' . $extension;
            $request->file('image')->move(public_path('/image/menu/'), $newName);
            $imagePath = 'image/menu/' . $newName; 
        }

        $menu = Menu::create([
            'id_kategori' => $request->input('id_kategori'),
            'nama_menu' => $request->input('nama_menu'),
            'harga_menu' => $request->input('harga_menu'),
            'image' => $imagePath,
            'status' => 'active',
        ]);

        $stokData = [];

        foreach ($request->input('stok') as $index => $barangId) {
            if (isset($request->input('jumlah')[$index])) {
                $stokData[$barangId] = ['jumlah' => $request->input('jumlah')[$index]];
            }
        }

        $menu->stok()->sync($stokData);

        // dd(session()->all());


        return redirect()->route('menu.index')
                    ->with('success', 'Menu has been added successfully!')
                    ->withInput();
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
        $kategori = Kategori::all()->where('id_kategori', '!=', 99);
        $stok = Stok::all();
        return view('pages.menu.edit', compact('menu', 'kategori', 'stok'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'nama_menu' => 'required|string|max:255',
            'harga_menu' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'stok' => 'required|array',
            'stok.*' => 'exists:stok,id_barang',
            'jumlah' => 'required|array',
            'jumlah.*' => 'numeric|min:1',
        ]);

        $imagePath = $menu->image; 
        if ($request->has('remove_existing_image') && $request->remove_existing_image == '1') {
            if ($menu->image && file_exists(public_path($menu->image))) {
                // Delete the existing image file
                unlink(public_path($menu->image));
            }
            $imagePath = null; // Clear the image reference
        }
        if ($request->hasFile('image')) {
            if ($menu->image && file_exists(public_path($menu->image))) {
                unlink(public_path($menu->image));
            }
            $extension = $request->file('image')->getClientOriginalExtension();
            $newName = now()->timestamp . '.' . $extension;
            $request->file('image')->move(public_path('image/menu/'), $newName);
            $imagePath = 'image/menu/' . $newName; 
        }

        $menu->update([
            'id_kategori' => $request->input('id_kategori'),
            'nama_menu' => $request->input('nama_menu'),
            'harga_menu' => $request->input('harga_menu'),
            'image' => $imagePath,
        ]);
        $menu->status = $request->status;
        $menu->save();

        $stokData = [];
        foreach ($request->input('stok') as $index => $stokId) {
            if (isset($request->input('jumlah')[$index])) {
                $stokData[$stokId] = ['jumlah' => $request->input('jumlah')[$index]];
            }
        }

        $menu->stok()->sync($stokData);

        return redirect()->route('menu.index')->with('success', 'Menu has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Menu $menu)
    {
        $adminPassword = $request->input('admin_password');

        if ($adminPassword && !Hash::check($adminPassword, auth()->user()->password)) {
            return back()->withErrors(['admin_password' => 'Password admin tidak valid.'])
            ->with(['id_menu' => $menu->id_menu, 'nama_menu' => $menu->nama_menu]);
        }
        
        if ($menu->detailTransaksi()->exists()) {
            $menu->status = 'inactive';
            $menu->save();
            return redirect()->route('menu.index')->with('success', 'Menu ditandai inactive.');
        }

        $menu->delete();

        return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus');
    }
}
