<?php

namespace App\Http\Controllers;

use App\Models\ExpenseReport;
use App\Support\SignatureImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/** Tanda tangan penanggung jawab LPJ: upload gambar atau gambar langsung di canvas. */
class LpjSignatureController extends Controller
{
    public function store(Request $request, ExpenseReport $report): JsonResponse
    {
        Gate::authorize('update', $report);

        $request->validate([
            'signature' => ['required_without:signature_data', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'signature_data' => ['required_without:signature', 'nullable', 'string', 'max:3000000'],
        ], [
            'signature.required_without' => 'Pilih gambar tanda tangan atau gambar di kolom tanda tangan.',
            'signature_data.required_without' => 'Pilih gambar tanda tangan atau gambar di kolom tanda tangan.',
        ]);

        try {
            $png = $request->hasFile('signature')
                ? SignatureImage::fromUpload($request->file('signature'))
                : SignatureImage::fromDataUrl($request->input('signature_data'));
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages(['signature' => $e->getMessage()]);
        }

        $report->storeSignature($png);

        return $this->success('Tanda tangan disimpan.', ['signature_url' => $report->signatureUrl()]);
    }

    public function destroy(ExpenseReport $report): JsonResponse
    {
        Gate::authorize('update', $report);

        $report->removeSignature();

        return $this->success('Tanda tangan dihapus.');
    }
}
