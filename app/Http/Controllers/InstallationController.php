<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
    public function index()
    {
    	return Inertia::render('Installation');
    }
}
