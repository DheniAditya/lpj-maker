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
            'title' => $formattedTitle,        // <-- Pakai variabel yang sudah diformat
            'creator_name' => $formattedName,  // <-- Pakai variabel yang sudah diformat
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
        $this->checkAccess($report);

        $validated = $request->validate([
            'type' => 'required|in:debit,credit',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'receipt_image' => 'nullable|image|mimes:jpg,png,jpeg|max:5120' // maks 5MB
        ]);
        
        $imagePath = null;
        if ($request->hasFile('receipt_image')) {
            $imagePath = $request->file('receipt_image')->store('receipts', 'public');
        }

        $report->entries()->create([
            'type' => $validated['type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'receipt_image_path' => $imagePath,
        ]);

        // return redirect()->back()->with('success', 'Entri berhasil disimpan!');
        return response()->json([
        'status' => 'success',
        'message' => 'Transaksi berhasil disimpan!',
    ]);
    }


public function downloadPdf(ExpenseReport $report)
{
    $this->checkAccess($report);

    // 1. Setting Wajib untuk PDF Gambar
    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 300);

    try {
        $report->load('entries');

        $receiptImages = [];
        
        foreach ($report->entries as $entry) {
            if ($entry->receipt_image_path) {
                
                // --- PERBAIKAN UTAMA: PATH GAMBAR ---
                // Jangan pakai public_path('storage/...') karena kadang symlink error.
                // Pakai storage_path('app/public/...') untuk tembak file aslinya langsung.
                
                $realPath = storage_path('app/public/' . $entry->receipt_image_path);

                // Jika di database path-nya sudah ada kata 'public/', sesuaikan:
                // $realPath = storage_path('app/' . $entry->receipt_image_path);

                if (file_exists($realPath)) {
                    
                    // Ambil ekstensi file (jpg/png)
                    $type = pathinfo($realPath, PATHINFO_EXTENSION);
                    
                    // Baca file mentah
                    $data = file_get_contents($realPath);
                    
                    if ($data !== false) {
                        // Encode ke Base64
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                        $receiptImages[] = [
                            'image_src' => $base64,
                            'description' => $entry->description,
                            'amount' => $entry->amount,
                            'type' => $entry->type,
                        ];
                    }
                } else {
                    // Debugging: Cek Log jika file tidak ketemu
                    // \Log::error("File tidak ditemukan di path: " . $realPath);
                }
            }
        }

        $groupedReceipts = collect($receiptImages)->chunk(4);

        $pdf = Pdf::loadView('lpj.pdf_template', compact('report', 'groupedReceipts'));
        $pdf->setPaper('a4', 'portrait');

        $filename = "LPJ - {$report->title}.pdf";

        // --- PERBAIKAN PRILAKU DOWNLOAD ---
        // Gunakan download(), bukan stream() atau view()
        return $pdf->download($filename);

    } catch (\Throwable $e) {
        return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
    }
}

// --- FUNGSI TAMBAHAN (Letakkan di bawah fungsi downloadPdf, di dalam Class yang sama) ---
private function compressImageToDataUrl($sourcePath)
{
    try {
        // 1. Ambil Info Gambar
        list($width, $height, $type) = getimagesize($sourcePath);
        
        // 2. Load Gambar ke Memory berdasarkan tipe
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($sourcePath);
                // Handle transparansi PNG (ubah jadi putih)
                $bg = imagecreatetruecolor($width, $height);
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagecopy($bg, $image, 0, 0, 0, 0, $width, $height);
                imagedestroy($image);
                $image = $bg;
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                return null;
        }

        // 3. Resize jika terlalu besar (Max lebar 800px sudah cukup untuk PDF A4)
        $maxWidth = 800;
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = ($height / $width) * $newWidth;
            $imageResized = imagescale($image, $newWidth, $newHeight);
            imagedestroy($image); // Hapus yang besar dari memory
            $image = $imageResized;
        }

        // 4. Output ke Buffer sebagai JPG Kualitas 60% (Cukup jelas tapi ringan)
        ob_start();
        imagejpeg($image, null, 60); 
        $data = ob_get_clean();
        imagedestroy($image); // Bersihkan memory

        // 5. Jadikan Base64
        return 'data:image/jpeg;base64,' . base64_encode($data);

    } catch (\Exception $e) {
        Log::error("Gagal kompres gambar: " . $e->getMessage());
        return null; // Jika gagal, gambar di-skip agar PDF tetap jalan
    }
}

    public function updateEntry(Request $request, ExpenseEntry $entry)
    {
        $this->checkAccess($entry->expenseReport);
        $validated = $request->validate([
            'type' => 'required|in:debit,credit',
            'created_at' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'receipt_image' => 'nullable|image|mimes:jpg,png,jpeg|max:5120'
        ]);

        $imagePath = $entry->receipt_image_path; // Default: path gambar lama

        if ($request->hasFile('receipt_image')) {
            if ($entry->receipt_image_path) {
                Storage::disk('public')->delete($entry->receipt_image_path);
            }
            $imagePath = $request->file('receipt_image')->store('receipts', 'public');
        }

        $entry->update([
            'type' => $validated['type'],
            'created_at' => $validated['created_at'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'receipt_image_path' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Entri berhasil diperbarui!');
    }

    public function destroyEntry(ExpenseEntry $entry)
{
    // 1. Cek Akses (Biarkan tetap ada)
    $this->checkAccess($entry->expenseReport);

    // 2. Hapus Gambar Fisik (Biarkan tetap ada)
    if ($entry->receipt_image_path) {
        Storage::disk('public')->delete($entry->receipt_image_path);
    }

    // 3. Hapus Data di Database (Biarkan tetap ada)
    $entry->delete();

    // 4. --- BAGIAN INI YANG DIUBAH ---
    // Jangan redirect()->back();
    // Ganti dengan respon JSON agar AJAX tahu sukses:
    
    return response()->json([
        'status' => 'success',
        'message' => 'Entri berhasil dihapus.'
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

    //delete history
    public function destroy($id)
    {
        // 1. Cari Data
        // Kita pakai ExpenseReport::find karena sudah di-use di atas
        $report = ExpenseReport::find($id); 

        // 2. Jika Data Tidak Ditemukan
        if (!$report) {
            // Ubah respon redirect menjadi JSON Error 404
            return response()->json([
                'status' => 'error', 
                'message' => 'Data laporan tidak ditemukan'
            ], 404);
        }

        // 3. Cek Keamanan (Otorisasi)
        if (auth()->id() !== $report->user_id) {
            // Ubah abort menjadi JSON Error 403
            return response()->json([
                'status' => 'error', 
                'message' => 'Anda tidak berhak menghapus laporan ini.'
            ], 403);
        }

        // 4. Hapus Data
        $report->delete();

        // 5. Berhasil
        // Ubah redirect menjadi JSON Success
        return response()->json([
            'status' => 'success', 
            'message' => 'Laporan berhasil dihapus.'
        ]);
    }


    public function updateTitle(Request $request, ExpenseReport $report)
{
    $request->validate([
        'title' => 'required|string|max:255',
    ]);

    $formattedTitle = \Str::title($request->title);

    $report->update([
        'title' => $formattedTitle,
    ]);

    // Kirim respon JSON untuk ditangkap jQuery
    return response()->json([
        'success' => true,
        'message' => 'Judul LPJ berhasil diperbarui!',
        'new_title' => $formattedTitle
    ]);
}

public function updateCreator(Request $request, ExpenseReport $report)
{
    $request->validate([
        'creator_name' => 'required|string|max:255',
    ]);

    // Format otomatis (Huruf Besar di Awal Kata)
    $formattedName = \Str::title($request->creator_name);

    $report->update([
        'creator_name' => $formattedName,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Nama diperbarui!',
        'new_name' => $formattedName
    ]);
}

}

