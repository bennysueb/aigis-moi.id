<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\QueryException;
use Livewire\Component;

class RegisterRfid extends Component
{
    public Event $event;
    public ?User $selectedUser = null;
    public string $rfidTag = '';
    public array $lastScanned = [];
    public string $manualUuid = '';

    public function mount(Event $event)
    {
        $this->event = $event;
    }

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
            return;
        }

        if ($user->rfid_tag) {
            $this->lastScanned = ['status' => 'warning', 'message' => $user->name . ' SUDAH MEMILIKI KARTU RFID: ' . $user->rfid_tag];

            // TAMBAHKAN BARIS INI untuk mengirim sinyal reset
            $this->dispatch('reset-scanner-view');

            return;
        }

        $this->selectedUser = $user;
        $this->lastScanned = ['status' => 'info', 'message' => 'Pengguna ditemukan: ' . $user->name . '. Silakan tempelkan kartu RFID.'];

        $this->dispatch('open-rfid-modal');
    }

    // Method ini dipanggil saat form di popup disubmit
    public function associateRfid()
    {
        $this->validate([
            'rfidTag' => 'required|string|min:4|max:12',
        ]);

        if (!$this->selectedUser) {
            $this->lastScanned = ['status' => 'error', 'message' => 'Tidak ada pengguna yang dipilih.'];
            return;
        }

        // =======================================================
        // 1. Cari data registrasi...
        // =======================================================
        $registration = \App\Models\Registration::where('user_id', $this->selectedUser->id)
            ->where('event_id', $this->event->id)
            ->first();
        // =======================================================


        // =======================================================
        // 2. JIKA TIDAK TERDAFTAR: Lakukan pengecekan kuota & daftarkan otomatis
        // =======================================================
        if (!$registration) {
            
            // AKTIF: Pengecekan Kuota Event
            // Kuota 0 berarti tidak terbatas
            if ($this->event->quota > 0) {
                $currentRegistrants = \App\Models\Registration::where('event_id', $this->event->id)->count();
                
                // Jika jumlah pendaftar saat ini SUDAH MENCAPAI atau MELEBIHI kuota
                if ($currentRegistrants >= $this->event->quota) {
                    $this->lastScanned = ['status' => 'error', 'message' => 'GAGAL: Kuota untuk event ini sudah penuh. ' . $this->selectedUser->name . ' tidak dapat didaftarkan.'];
                    $this->reset(['selectedUser', 'rfidTag']);
                    $this->dispatch('close-rfid-modal');
                    $this->dispatch('reset-scanner-view');
                    return; // Hentikan proses
                }
            }

            // Kuota aman, lanjutkan pendaftaran otomatis
            try {
                $registration = \App\Models\Registration::create([
                    'event_id' => $this->event->id,
                    'user_id' => $this->selectedUser->id,
                    'name' => $this->selectedUser->name,
                    'email' => $this->selectedUser->email,
                    'phone_number' => $this->selectedUser->phone_number,
                    'data' => [], // Set data kustom ke array kosong
                    'attendance_type' => 'offline', // Asumsi 'offline'
                ]);

                $this->lastScanned = ['status' => 'info', 'message' => $this->selectedUser->name . ' otomatis didaftarkan ke event ini. Lanjutkan asosiasi RFID...'];

            } catch (QueryException $e) {
                // Ini bisa terjadi jika ada race condition (sangat jarang) atau error database lain
                $this->lastScanned = ['status' => 'error', 'message' => 'Gagal mendaftarkan pengguna ke event. Error: ' . $e->getMessage()];
                $this->reset(['selectedUser', 'rfidTag']);
                $this->dispatch('close-rfid-modal');
                $this->dispatch('reset-scanner-view');
                return;
            }
        }
        // =======================================================
        // 3. (Registrasi sudah ada / baru dibuat) Lanjutkan asosiasi RFID
        // =======================================================

        try {
            // 1. Simpan RFID Tag ke profil pengguna
            $this->selectedUser->update([
                'rfid_tag' => $this->rfidTag,
            ]);

            // 2. Catat HANYA waktu registrasi RFID (sesuai permintaan)
            $updateData = [
                'rfid_registered_at' => now()
            ];

            // (Logika Auto Check-in DIHAPUS sesuai permintaan)

            $registration->update($updateData);

            $this->lastScanned = ['status' => 'success', 'message' => 'BERHASIL! ' . $this->selectedUser->name . ' terdaftar & RFID tercatat.'];

            $this->reset(['selectedUser', 'rfidTag']);
            $this->dispatch('close-rfid-modal');
            $this->dispatch('reset-scanner-view');

        } catch (QueryException $e) {
            // Ini menangani jika RFID tag sudah terdaftar untuk PENGGUNA LAIN
            $this->lastScanned = ['status' => 'error', 'message' => 'GAGAL! Nomor RFID ini sudah terdaftar.'];
            // Reset state
            $this->reset(['selectedUser', 'rfidTag']);
            // Tutup modal
            $this->dispatch('close-rfid-modal');
            // Restart scanner
            $this->dispatch('reset-scanner-view');
            return;
        }
    }

    public function render()
    {
        return view('livewire.admin.checkin.register-rfid')->layout('layouts.app');
    }
}
