<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ScannerPage extends Component
{
    public function processScan($scannedUrl)
    {
        $scanner = Auth::user();
        $attendee = null;

        // KASUS 1: Jika yang di-scan adalah QR Code Profil Pengguna (`/connect/{uuid}`)
        if (Str::contains($scannedUrl, '/connect/')) {
            $uuid = last(explode('/', $scannedUrl));
            $scannedUser = User::where('uuid', $uuid)->first();

            if (!$scannedUser) {
                $this->dispatch('scan-failed', ['error' => 'User QR Code not valid.']);
                return;
            }
            $attendee = $scannedUser;
        }
        // KASUS 2: Jika yang di-scan adalah QR Code Tiket (kita asumsikan URL-nya mengandung '/check-in/')
        elseif (Str::contains($scannedUrl, '/check-in/')) {
            $uuid = last(explode('/', $scannedUrl));
            $registration = Registration::where('uuid', $uuid)->with('user')->first(); // Eager load relasi user

            if (!$registration || !$registration->user) {
                $this->dispatch('scan-failed', ['error' => 'Ticket QR Code not valid or not linked to a user.']);
                return;
            }
            $attendee = $registration->user; // Ambil data pengguna dari pendaftaran
        }
        // KASUS LAIN: Format QR Code tidak dikenali
        else {
            $this->dispatch('scan-failed', ['error' => 'Unknown QR Code format.']);
            return;
        }

        // --- VALIDASI & PROSES KONEKSI ---

        // 1. Pengguna tidak bisa men-scan diri sendiri
        if ($scanner->id === $attendee->id) {
            $this->dispatch('scan-failed', ['error' => 'You cannot connect with yourself.']);
            return;
        }

        $exhibitor = null;

        // 2. Logika untuk menentukan siapa Exhibitor dan siapa Peserta
        if ($scanner->hasRole('Exhibitor') && !$attendee->hasRole('Exhibitor')) {
            $exhibitor = $scanner;
            // Attendee sudah ditentukan dari logika di atas
        } elseif (!$scanner->hasRole('Exhibitor') && $attendee->hasRole('Exhibitor')) {
            // Skenario ini terjadi jika seorang pengunjung memindai QR code Exhibitor
            $exhibitor = $attendee;
            $attendee = $scanner; // Tukar posisi
        } else {
            // Koneksi tidak valid (misal: Exhibitor scan Exhibitor lain)
            $this->dispatch('scan-failed', ['error' => 'This QR code is not valid for connection.']);
            return;
        }

        // 3. Simpan hubungan ke database
        $exhibitor->attendees()->syncWithoutDetaching($attendee->id);

        // 4. Kirim sinyal sukses kembali ke browser
        $this->dispatch('scan-successful', ['exhibitorName' => $exhibitor->nama_instansi]);
    }

    public function render()
    {
        return view('livewire.scanner-page')->layout('layouts.app');
    }
}
