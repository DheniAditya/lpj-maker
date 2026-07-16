<?php

namespace App\Http\Controllers;

use App\Models\ExpenseReport;
use App\Models\ExpenseEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LpjController extends Controller
{
    public function create()
    {
        return view('lpj.create');
    }

    public function storeReport(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'creator_name' => 'required|string|max:255',
        ]);

        $userId = Auth::id(); 
        
        $title       = $request->title;
        $creatorName = $request->creator_name;

        $report = ExpenseReport::create([
            'title' => $title,        
            'creator_name' => $creatorName,  
            'slug' => Str::slug($title) . '-' . uniqid(),
            'user_id' => $userId,
            'share_token' => Str::random(32), // Generate token default saat pembuatan
        ]);

        if (is_null($userId)) {
            session()->push('guest_lpj_slugs', $report->slug);
        }

        return redirect()->route('lpj.show', $report->slug);
    }

    public function show(ExpenseReport $report)
    {
        // Akses minimal: viewer
        $this->checkAccess($report, 'viewer');
        
        $report->load('entries');
        
        return view('lpj.workspace', compact('report'));
    }

    public function storeEntry(Request $request, ExpenseReport $report)
    {
        // Akses minimal: editor
        $this->checkAccess($report, 'editor');

        $validated = $request->validate([
            'type' => 'required|in:debit,credit',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:5120' 
        ]);

        $entry = $report->entries()->create([
            'type' => $validated['type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('receipts', 'public');
                $entry->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Transaksi berhasil disimpan'
        ]);
    }

    public function downloadPdf(ExpenseReport $report)
    {
        // Akses minimal: viewer
        $this->checkAccess($report, 'viewer');
        
        $report->load(['entries.images']); 

        $pdf = Pdf::loadView('lpj.pdf_template', compact('report'));
        
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'chroot' => public_path(),
        ]);

        return $pdf->download("LPJ - {$report->title}.pdf");
    }

    public function updateEntry(Request $request, ExpenseEntry $entry)
    {
        // Akses minimal: editor
        $this->checkAccess($entry->expenseReport, 'editor');

        $validated = $request->validate([
            'type' => 'required|in:debit,credit',
            'created_at' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:5120',
            'deleted_images' => 'nullable|array'
        ]);

        $entry->update([
            'type' => $validated['type'],
            'created_at' => $validated['created_at'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
        ]);

        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $imageId) {
                $image = $entry->images()->find($imageId);
                if ($image) {
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('receipts', 'public');
                $entry->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Entri berhasil diperbarui!'
        ]);
    }

    public function destroyEntry(ExpenseEntry $entry)
    {
        // Akses minimal: editor
        $this->checkAccess($entry->expenseReport, 'editor');

        foreach ($entry->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $entry->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Entri dan semua nota berhasil dihapus.'
        ]);
    }

    public function updateTitle(Request $request, ExpenseReport $report)
    {
        // Akses minimal: editor
        $this->checkAccess($report, 'editor');

        $request->validate(['title' => 'required|string|max:255']);
        $title=$request->title;
        $report->update(['title' => $title]);

        return response()->json([
            'success' => true,
            'message' => 'Judul LPJ berhasil diperbarui!',
            'new_title' => $title
        ]);
    }

    public function updateCreator(Request $request, ExpenseReport $report)
    {
        // Akses minimal: editor
        $this->checkAccess($report, 'editor');

        $request->validate(['creator_name' => 'required|string|max:255']);
        $creator_name=$request->creator_name;
        $report->update(['creator_name' => $creator_name]);

        return response()->json([
            'success' => true,
            'message' => 'Nama diperbarui!',
            'new_name' => $creator_name 
        ]);
    }

    /**
     * LOGIKA SHARING VIA LINK
     */
    public function handleShareLink($token)
{
    $report = ExpenseReport::where('share_token', $token)
                            ->where('is_shared', true)
                            ->firstOrFail(); // Jika token salah/is_shared false, lari ke 404

    // Simpan ke session agar checkAccess() mengizinkan
    $tokens = session()->get('accessed_share_tokens', []);
    if (!in_array($token, $tokens)) {
        session()->push('accessed_share_tokens', $token);
    }

    return redirect()->route('lpj.show', $report->slug);
}

    public function toggleSharing(Request $request, ExpenseReport $report)
{
    if ($report->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Jika belum ada token, kita buatkan dulu sebelum update
    if (!$report->share_token) {
        $report->share_token = Str::random(32);
    }

    $report->update([
        'is_shared' => $request->is_shared === 'true' || $request->is_shared === true,
        'share_role' => $request->share_role,
        'share_token' => $report->share_token
    ]);

    return response()->json([
        'success' => true,
        'share_token' => $report->share_token // Kirim balik tokennya ke JS
    ]);
}

    /**
     * SECURITY & ACCESS CONTROL
     */
    private function checkAccess(ExpenseReport $report, $requiredRole = 'viewer')
    {
        $userId = Auth::id();

        // 1. Cek jika Owner (User yang login)
        if ($userId && $report->user_id === $userId) {
            return true;
        }

        // 2. Cek jika Guest (Sesi pembuat di browser yang sama)
        $guestSlugs = session()->get('guest_lpj_slugs', []);
        if (in_array($report->slug, $guestSlugs)) {
            return true;
        }

        // 3. Cek Fitur Sharing via Link
        $accessedTokens = session()->get('accessed_share_tokens', []);
        if ($report->is_shared && in_array($report->share_token, $accessedTokens)) {
            
            // Jika butuh akses 'editor', tapi link hanya diset 'viewer', tolak.
            if ($requiredRole === 'editor' && $report->share_role !== 'editor') {
                abort(403, 'Anda hanya memiliki izin pelihat (viewer).');
            }
            return true;
        }

        abort(403, 'Anda tidak memiliki izin untuk mengakses LPJ ini.');
    }

    public function destroy($id)
    {
        $report = ExpenseReport::with('entries.images')->find($id); 

        if (!$report) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        // PROTEKSI KETAT: Hanya pemilik asli yang bisa menghapus total laporan
        // Guest atau link editor tidak boleh menghapus file orang lain
        if ($report->user_id !== Auth::id() && !in_array($report->slug, session()->get('guest_lpj_slugs', []))) {
            abort(403, 'Hanya pemilik laporan yang diizinkan untuk menghapus laporan ini.');
        }

        foreach ($report->entries as $entry) {
            foreach ($entry->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }
        }

        $report->delete();

        return response()->json([
            'status' => 'success', 
            'message' => 'Laporan dan semua lampiran berhasil dihapus.'
        ]);
    }
}