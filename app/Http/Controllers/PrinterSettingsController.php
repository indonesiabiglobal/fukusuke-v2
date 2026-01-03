<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrinterSettingsController extends Controller
{
    /**
     * Display printer settings page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('printer-settings');
    }
}
