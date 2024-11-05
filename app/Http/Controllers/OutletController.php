<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Outlets;
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

        // Update session values if new values are provided
        if ($request->has('search')) {
            $search = $request->input('search');
            session(['outlet_search' => $search]);
        }
        if ($request->has('entries')) {
            $entries = $request->input('entries');
            session(['outlet_entries' => $entries]);
        } 

        $query = Outlets::with('user');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama_user', 'like', '%' . $search . '%');
            });
        }
    
        $outlets = $query->paginate($entries);

        return view('pages.outlet.index', compact('outlets', 'search', 'entries'));
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
            'id_role' => 2, // Assuming '2' is the role ID for Outlet user
        ]);
    
        // Create the outlet associated with the user
        Outlets::create([
            'id_user' => $user->id_user,
            'alamat_outlet' => $request->alamat_outlet,
        ]);
    
        return redirect()->route('outlet.index')->with('success', 'Outlet created successfully.');
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
        ]);
    
        return redirect()->route('outlet.index')->with('success', 'Outlet updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
        public function destroy(Outlets $outlets)
        {
            // Verify the admin password before deleting
        if (!Hash::check(request()->admin_password, auth()->user()->password)) {
            return back()->withErrors(['admin_password' => 'Password admin tidak valid.']);
        }

        // Delete the outlet
        $outlets->delete();
        
        // Also delete the associated user
        $outlets->user()->delete();

        return redirect()->route('outlet.index')->with('success', 'Outlet deleted successfully.');
    }
}
