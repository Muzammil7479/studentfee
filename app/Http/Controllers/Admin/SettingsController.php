<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the settings page. Admin only.
     */
    public function index(): View
    {
        return view('admin.settings');
    }
}
