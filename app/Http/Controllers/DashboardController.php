<?php

namespace App\Http\Controllers;

use App\Models\Outlets;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $outlet = Outlets::with('user')->where('id_user', auth()->user()->id_user)->first();

        return view('pages.dashboard.dashboard', compact('outlet'));
    }
}
