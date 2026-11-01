<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $lpjHistory = $request->user()
            ->expenseReports()
            ->withTotals()
            ->latest('updated_at')
            ->get();

        return view('dashboard', compact('lpjHistory'));
    }
}
