<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PublicMediaController extends Controller
{
    public function stream(Request $request)
    {
        $path = $request->query('path');
        
        // Validasi input
        if (!$path) {
            abort(404);
        }

        // --- FITUR OPTIMASI (CACHE) ---
        // Kita gunakan Cache Server sebagai "Gudang Sementara"
        // Kunci cache dibuat unik berdasarkan path file
        $cacheKey = 'public_image_' . md5($path);

        // Coba ambil dari Cache dulu. 
        // Jika tidak ada, baru download dari Google Drive (berlaku 24 jam / 86400 detik)
        $fileData = Cache::remember($cacheKey, 86400, function () use ($path) {
            $disk = 'google';
            
            try {
                if (!Storage::disk($disk)->exists($path)) {
                    return null; // File tidak ditemukan di Drive
                }

                return [
                    'content' => Storage::disk($disk)->get($path),
                    'mime'    => Storage::disk($disk)->mimeType($path),
                ];
            } catch (\Exception $e) {
                Log::error("Gagal stream gambar publik: " . $e->getMessage());
                return null;
            }
        });

        // Jika file tidak ditemukan (di cache maupun drive)
        if (!$fileData) {
            abort(404);
        }

        // --- RESPON GAMBAR CEPAT ---
        return response($fileData['content'], 200)
            ->header('Content-Type', $fileData['mime'])
            // Header ini menyuruh Browser user: "Simpan gambar ini di HP kamu selama 1 tahun"
            // Jadi user tidak perlu minta ke server lagi saat refresh halaman.
            ->header('Cache-Control', 'public, max-age=31536000, immutable')
            ->header('Pragma', 'public');
    }
}