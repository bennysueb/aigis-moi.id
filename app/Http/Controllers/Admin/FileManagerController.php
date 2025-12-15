<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileManagerController extends Controller
{
    public function stream(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->query('path');
        $disk = 'google';

        try {
            // 1. Cek apakah file ada
            if (!Storage::disk($disk)->exists($path)) {
                abort(404);
            }

            // 2. Ambil konten file
            $fileContent = Storage::disk($disk)->get($path);
            
            // 3. Ambil tipe mime awal dari driver
            $mimeType = Storage::disk($disk)->mimeType($path);
            
            // 4. Logika Perbaikan MIME Type (Fallback)
            // Ambil ekstensi dari path file
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            
            // Daftar MIME yang sering salah dideteksi Google Drive
            $ambiguousMimes = ['application/octet-stream', 'unknown', 'application/x-troff-man'];
        
            // Jika MIME tidak jelas ATAU ekstensinya adalah gambar/pdf (kita prioritaskan ekstensi)
            if (empty($mimeType) || in_array($mimeType, $ambiguousMimes) || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'pdf'])) {
                
                $mimeType = match($extension) {
                    'jpg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'pdf' => 'application/pdf',
                    'svg' => 'image/svg+xml',
                    default => $mimeType ?: 'application/octet-stream' // Kembalikan ke asal atau default
                };
            }

            // 5. Kembalikan Response Gambar/File
            return response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"');

        } catch (\Exception $e) {
            // Log error untuk debugging admin
            Log::error('Stream Error: ' . $e->getMessage());
            abort(500);
        }
    }
}