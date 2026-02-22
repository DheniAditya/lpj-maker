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

    return response()->json(['status' => 'success', 'message' => 'Berhasil!']);
}

public function downloadPdf(ExpenseReport $report)
{
    $this->checkAccess($report);
    ini_set('memory_limit', '512M');

    try {
        
        $report->load('entries.images');

        $receiptImages = [];
        foreach ($report->entries as $entry) {
            
            foreach ($entry->images as $image) {
                $realPath = storage_path('app/public/' . $image->image_path);

                if (file_exists($realPath)) {
                    
                    $base64 = $this->compressImageToDataUrl($realPath);

                    if ($base64) {
                        $receiptImages[] = [
                            'image_src' => $base64,
                            'description' => $entry->description,
                            'amount' => $entry->amount,
                            'type' => $entry->type,
                        ];
                    }
                }
            }
        }

        $groupedReceipts = collect($receiptImages)->chunk(4);
        $pdf = Pdf::loadView('lpj.pdf_template', compact('report', 'groupedReceipts'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("LPJ - {$report->title}.pdf");

    } catch (\Throwable $e) {
        return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
    }
}


private function compressImageToDataUrl($sourcePath)
{
    try {
        
        list($width, $height, $type) = getimagesize($sourcePath);
        
        
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($sourcePath);
                
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

        
        $maxWidth = 800;
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = ($height / $width) * $newWidth;
            $imageResized = imagescale($image, $newWidth, $newHeight);
            imagedestroy($image); 
            $image = $imageResized;
        }

        
        ob_start();
        imagejpeg($image, null, 60); 
        $data = ob_get_clean();
        imagedestroy($image); 

        
        return 'data:image/jpeg;base64,' . base64_encode($data);

    } catch (\Exception $e) {
        Log::error("Gagal kompres gambar: " . $e->getMessage());
        return null; 
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

        $imagePath = $entry->receipt_image_path; 

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
        
        
        $report = ExpenseReport::find($id); 

        
        if (!$report) {
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Data laporan tidak ditemukan'
            ], 404);
        }

        
        if (\Illuminate\Support\Facades\Auth::id() !== $report->user_id) {
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Anda tidak berhak menghapus laporan ini.'
            ], 403);
        }

        
        $report->delete();

        
        
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

    
$formattedTitle = Str::title($request->title); 
    $report->update([
        'title' => $formattedTitle,
    ]);

    
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

    
    
    $formattedName = Str::title($request->title); 

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

