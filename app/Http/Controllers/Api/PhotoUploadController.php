<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoUploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        // 1. Validasi input
        $validated = $request->validate([
            'id'   => ['required', 'string'],           // UUID dari client
            'type' => ['nullable', 'in:variant,component'],
            'file' => ['required', 'image', 'max:5120'], // 5 MB
        ]);

        $photoId = $validated['id'];
        $type    = $validated['type'] ?? 'generic';
        $file    = $validated['file'];

        // 2. Tentukan folder berdasarkan type
        $folder = match ($type) {
            'variant'   => 'variant_photos',
            'component' => 'component_photos',
            default     => 'photos',
        };

        // 3. Bangun nama file yang stabil dari id
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $fileName  = $photoId . '.' . $extension;

        // 4. Simpan ke disk "public" (pastikan sudah di-config & php artisan storage:link)
        $path = $file->storeAs($folder, $fileName, 'public');

        // 5. Buat URL publik (kalau pakai storage public)
        $disk = Storage::disk('public');
        $url  = $disk->url($path);

        return response()->json([
            'id'        => $photoId,
            'type'      => $type,
            'file_path' => $path,  // ini yang dikirim balik ke mobile
            'url'       => $url,   // optional, kalau mau langsung dipakai di web
        ]);
    }
}
