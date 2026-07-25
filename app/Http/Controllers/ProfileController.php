<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile.
     */
    public function show(): View
    {
        return view('profile', ['user' => Auth::user()]);
    }
}
