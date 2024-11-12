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
        // Retrieve session values or set default values
        $search = session('menu_search', '');
        $entries = session('menu_entries', 5);

        // Update session values if new values are provided
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['menu_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['menu_entries' => $entries]);
        }

        $query = Menu::with('kategori');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', '%' . $search . '%')
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
        $kategori = Kategori::all();
        $stok = Stok::all(); 
        return view('pages.menu.create', compact('kategori', 'stok'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'nama_menu' => 'required|string',
            'harga_menu' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            $imagePath = 'image/menu/' . $newName; // Set the file path to save in the database
        }
        // Debugging: Check the image path
        \Log::info('Image Path: ' . $imagePath);
        // Check if image path is not null before proceeding
        if (!$imagePath) {
            return redirect()->back()->withErrors(['image' => 'Image upload failed.']);
        }

        // Create the new menu
        $menu = Menu::create([
            'id_kategori' => $request->input('id_kategori'),
            'nama_menu' => $request->input('nama_menu'),
            'harga_menu' => $request->input('harga_menu'),
            'image' => $imagePath,
        ]);

        // Prepare stok data for syncing with quantities
        $stokData = [];
        foreach ($request->input('stok') as $index => $barangId) {
            if (isset($request->input('jumlah')[$index])) {
                $stokData[$barangId] = ['jumlah' => $request->input('jumlah')[$index]];
            }
        }

        // Sync the stok with the menu and attach the 'jumlah' value to the pivot table
        $menu->stok()->sync($stokData);

        return redirect()->route('menu.index')->with('success', 'Menu has been added successfully!');
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
        $kategori = Kategori::all();
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
            'stok' => 'required|array',
            'stok.*' => 'exists:stok,id_barang',
            'jumlah' => 'required|array',
            'jumlah.*' => 'numeric|min:1',
        ]);

        $imagePath = $menu->image; // Keep the current image path by default
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($menu->image && file_exists(public_path($menu->image))) {
                unlink(public_path($menu->image));
            }

            // Generate a new image name and move it to the desired location
            $extension = $request->file('image')->getClientOriginalExtension();
            $newName = now()->timestamp . '.' . $extension;
            $request->file('image')->move(public_path('image/menu/'), $newName);
            $imagePath = 'image/menu/' . $newName; // Set the new image path
        }

        $menu->update([
            'id_kategori' => $request->input('id_kategori'),
            'nama_menu' => $request->input('nama_menu'),
            'harga_menu' => $request->input('harga_menu'),
            'image' => $imagePath,
        ]);

        // Update stok data
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
        // Check if the admin password is provided
        $adminPassword = $request->input('admin_password');
        
        if ($adminPassword && Hash::check($adminPassword, auth()->user()->password)) {
            // Delete the category
            $menu->delete();
            return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
        }

        return back()->withErrors(['admin_password' => 'Password tidak valid.']);
    }
}
