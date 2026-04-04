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
use App\Models\Report;

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
        
        $formattedTitle = Str::title($request->title);
        $formattedName  = Str::title($request->creator_name);

        $report = ExpenseReport::create([
            'title' => $formattedTitle,        
            'creator_name' => $formattedName,  
            'slug' => Str::slug($formattedTitle) . '-' . uniqid(),
            'user_id' => $userId
        ]);

        if (is_null($userId)) {
            session()->push('guest_lpj_slugs', $report->slug);
        }

        return redirect()->route('lpj.show', $report->slug);
    }

    public function show(ExpenseReport $report)
    {
        $this->checkAccess($report);
        $report->load('entries');
        
        return view('lpj.workspace', compact('report'));
    }

   public function storeEntry(Request $request, ExpenseReport $report)
{
    // dd($request->all(), $request->file('images'));
    $this->checkAccess($report);

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
    $this->checkAccess($report);
    
    // PASTIKAN line ini ada untuk memuat data gambar dari tabel relasi
    $report->load(['entries.images']); 

    // Render PDF
    $pdf = Pdf::loadView('lpj.pdf_template', compact('report'));
    
    // Tambahkan opsi ini agar DomPDF diizinkan membaca file lokal
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'chroot' => public_path(), // Memberi izin akses ke folder public
    ]);

    return $pdf->download("LPJ - {$report->title}.pdf");
}

    public function updateEntry(Request $request, ExpenseEntry $entry)
        {
            $this->checkAccess($entry->expenseReport);

            $validated = $request->validate([
                'type' => 'required|in:debit,credit',
                'created_at' => 'required|date',
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:5120',
                'deleted_images' => 'nullable|array' // ID gambar yang diklik silang merah
            ]);

            // 1. Update data dasar (Teks, Angka, Tanggal)
            $entry->update([
                'type' => $validated['type'],
                'created_at' => $validated['created_at'],
                'description' => $validated['description'],
                'amount' => $validated['amount'],
            ]);

            // 2. HANYA HAPUS gambar jika ada di list 'deleted_images'
            // Jangan hapus semua gambar lama secara otomatis!
            if ($request->has('deleted_images')) {
                foreach ($request->deleted_images as $imageId) {
                    $image = $entry->images()->find($imageId);
                    if ($image) {
                        // Hapus file fisik dari storage
                        if (Storage::disk('public')->exists($image->image_path)) {
                            Storage::disk('public')->delete($image->image_path);
                        }
                        // Hapus record dari database
                        $image->delete();
                    }
                }
            }

            // 3. TAMBAHKAN gambar baru (Upload/Kamera) tanpa mengganggu yang lama
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
    $this->checkAccess($entry->expenseReport);

    
    foreach ($entry->images as $image) {
        Storage::disk('public')->delete($image->image_path);
    }

    
    
    $entry->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Entri dan semua nota berhasil dihapus.'
    ]);
}

    private function checkAccess(ExpenseReport $report)
    {
        if ($report->user_id) {
            if (Auth::guest() || $report->user_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki izin untuk mengakses LPJ ini.');
            }
        }
        else {
            $guestSlugs = session()->get('guest_lpj_slugs', []);
            
            if (!in_array($report->slug, $guestSlugs)) {
                abort(403, 'LPJ ini tidak ditemukan di sesi Anda, atau sesi Anda telah berakhir.');
            }
        }
    }

    
    public function destroy($id)
{
    $report = ExpenseReport::with('entries.images')->find($id); 

    if (!$report) {
        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
    }

    // Gunakan checkAccess yang sudah kamu buat agar konsisten
    $this->checkAccess($report);

    // HAPUS FILE FISIK SEBELUM DELETE DATA
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


    public function updateTitle(Request $request, ExpenseReport $report)
{
    $this->checkAccess($report); // TAMBAHKAN INI agar aman
    $request->validate(['title' => 'required|string|max:255']);

    $formattedTitle = Str::title($request->title); 
    $report->update(['title' => $formattedTitle]);

    return response()->json([
        'success' => true,
        'message' => 'Judul LPJ berhasil diperbarui!',
        'new_title' => $formattedTitle
    ]);
}

public function updateCreator(Request $request, ExpenseReport $report)
{
    $this->checkAccess($report); // TAMBAHKAN INI
    $request->validate(['creator_name' => 'required|string|max:255']);

    $formattedName = Str::title($request->creator_name); 
    $report->update(['creator_name' => $formattedName]);

    return response()->json([
        'success' => true,
        'message' => 'Nama diperbarui!',
        'new_name' => $formattedName 
    ]);
}

}

