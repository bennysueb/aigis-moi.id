<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\User;
use Livewire\Component;

class ReturnByQr extends Component
{
    // Kita tidak butuh $event, $rfidTag
    public ?User $selectedUser = null;
    public array $lastScanned = [];
    public string $manualUuid = '';

    // Tidak perlu mount event

    public function findUserManually()
    {
        // Panggil method utama dengan nilai dari input
        $this->findUserByUuid(basename($this->manualUuid));
        // Kosongkan kembali input field setelah submit
        $this->reset('manualUuid');
    }

    // Method ini dipanggil oleh scanner QR
    public function findUserByUuid($uuid)
    {
        if (empty($uuid)) {
            return;
        }

        $user = null;

        $user = User::where('uuid', $uuid)->first();

        if (!$user) {
            $registrationFromTicket = \App\Models\Registration::where('uuid', $uuid)->first();
            if ($registrationFromTicket) {
                // Ambil data user dari relasi registrasi
                $user = $registrationFromTicket->user;
            }
        }

        if (!$user) {
            $this->lastScanned = ['status' => 'error', 'message' => 'PENGGUNA TIDAK DITEMUKAN.'];
            $this->dispatch('reset-scanner-view'); // Reset scanner
            return;
        }

        // =======================================================
        // LOGIKA DIBALIK: Cek jika pengguna TIDAK punya RFID
        // =======================================================
        if (!$user->rfid_tag) {
            $this->lastScanned = ['status' => 'warning', 'message' => $user->name . ' TIDAK MEMILIKI KARTU RFID UNTUK DIKEMBALIKAN.'];
            $this->dispatch('reset-scanner-view'); // Reset scanner
            return;
        }
        // =======================================================

        // Jika lolos, berarti pengguna DITEMUKAN dan PUNYA RFID
        $this->selectedUser = $user;
        $this->lastScanned = ['status' => 'info', 'message' => 'Pengguna ditemukan: ' . $user->name . '. Kartu: ' . $user->rfid_tag];

        // Ganti nama event modal agar tidak bentrok
        $this->dispatch('open-return-modal');
    }

    // =======================================================
    // FUNGSI BARU: Untuk konfirmasi pengembalian
    // =======================================================
    public function confirmReturn()
    {
        if (!$this->selectedUser) {
            $this->lastScanned = ['status' => 'error', 'message' => 'Tidak ada pengguna yang dipilih.'];
            return;
        }

        try {
            // 1. Ini adalah aksi utamanya: Hapus RFID Tag dari profil pengguna
            $this->selectedUser->update([
                'rfid_tag' => null,
            ]);

            // (Opsional: Anda bisa catat waktu pengembalian di tabel Registrations jika ada kolomnya)
            // $registration = \App\Models\Registration::where('user_id', $this->selectedUser->id)
            //     ->where('event_id', $this->event->id) // Anda perlu pass $event jika ingin ini
            //     ->first();
            // if($registration) {
            //     $registration->update(['rfid_returned_at' => now()]);
            // }

            $this->lastScanned = ['status' => 'success', 'message' => 'BERHASIL! RFID untuk ' . $this->selectedUser->name . ' telah dikembalikan.'];

            $this->reset('selectedUser');
            $this->dispatch('close-return-modal');
            $this->dispatch('reset-scanner-view'); // Mulai ulang scanner

        } catch (\Exception $e) {
            $this->lastScanned = ['status' => 'error', 'message' => 'GAGAL! Terjadi kesalahan saat mengembalikan RFID.'];
            $this->dispatch('close-return-modal');
            $this->dispatch('reset-scanner-view'); // Mulai ulang scanner
        }
    }

    public function render()
    {
        // Arahkan ke file view baru
        return view('livewire.admin.checkin.return-by-qr')->layout('layouts.app');
    }
}