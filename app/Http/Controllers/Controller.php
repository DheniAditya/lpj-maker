<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /** Bentuk respons JSON yang seragam untuk semua aksi AJAX. */
    protected function success(string $message, array $data = []): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message] + $data);
    }
}
