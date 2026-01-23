<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil history LPJ milik user tsb, urutkan dari yang terbaru
        $lpjHistory = $user->expenseReports()
                           ->orderBy('updated_at', 'desc')
                           ->get();

        // Kirim data history ke view
        return view('dashboard', compact('lpjHistory'));
    }
}