<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{

    /**
     * Menangani koneksi antara pengguna setelah scan QR Code.
     */
    public function connect($uuid)
    {
        // 1. Siapa yang melakukan scan? (Pengguna yang sedang login)
        $scanner = Auth::user();

        // 2. Siapa/apa yang di-scan? (User pemilik QR Code)
        $scannedUser = User::where('uuid', $uuid)->firstOrFail();

        // 3. Pengguna tidak bisa men-scan diri sendiri
        if ($scanner->id === $scannedUser->id) {
            // Kita bisa arahkan kembali dengan pesan error
            return redirect()->route('dashboard')->with('error', 'You cannot connect with yourself.');
        }

        $exhibitor = null;
        $attendee = null;

        // 4. Logika untuk menentukan siapa Exhibitor dan siapa Peserta
        if ($scanner->hasRole('Exhibitor') && !$scannedUser->hasRole('Exhibitor')) {
            $exhibitor = $scanner;
            $attendee = $scannedUser;
        } elseif (!$scanner->hasRole('Exhibitor') && $scannedUser->hasRole('Exhibitor')) {
            $exhibitor = $scannedUser;
            $attendee = $scanner;
        } else {
            return redirect()->route('dashboard')->with('error', 'This QR code is not valid for connection.');
        }

        // 5. Simpan hubungan ke database
        $exhibitor->attendees()->syncWithoutDetaching($attendee->id);

        // 6. Arahkan kembali ke halaman profil exhibitor dengan pesan sukses
        return redirect()->route('exhibitors.show', $exhibitor->uuid)
            ->with('message', 'Successfully connected with ' . $exhibitor->nama_instansi . '!');
    }


    /**
     * Menghubungkan seorang Exhibitor dan seorang Peserta untuk mensimulasikan scan.
     */
    public function linkExhibitorAndAttendee($exhibitor_uuid, $attendee_uuid)
    {
        // Cari user exhibitor berdasarkan UUID
        $exhibitor = User::where('uuid', $exhibitor_uuid)->firstOrFail();

        // Cari user peserta berdasarkan UUID
        $attendee = User::where('uuid', $attendee_uuid)->firstOrFail();

        // Gunakan relasi yang sudah kita buat untuk mencatat hubungan
        // syncWithoutDetaching akan menambahkan hubungan jika belum ada,
        // dan tidak akan melakukan apa-apa jika sudah ada (mencegah duplikat).
        $exhibitor->attendees()->syncWithoutDetaching($attendee->id);

        return "BERHASIL: Peserta '{$attendee->name}' sekarang terhubung dengan Exhibitor '{$exhibitor->name}'. Silakan cek Dashboard Exhibitor.";
    }
}
