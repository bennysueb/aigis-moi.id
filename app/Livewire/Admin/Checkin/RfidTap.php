<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Livewire\Component;

class RfidTap extends Component
{
    public Event $event;
    public string $rfidTag = '';
    public array $lastStatus = [];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    // Method utama yang dipanggil saat RFID di-tap
    public function checkInByRfid()
    {
        $this->validate(['rfidTag' => 'required']);

        $user = \App\Models\User::where('rfid_tag', $this->rfidTag)->first();

        if (!$user) {
            $this->lastStatus = ['status' => 'error', 'message' => 'KARTU RFID TIDAK TERDAFTAR.'];
            $this->dispatch('scan-processed'); // (BARU) Kirim event untuk auto-reset
            $this->reset('rfidTag');
            $this->dispatch('refocus-rfid-input');
            return;
        }

        $registration = \App\Models\Registration::where('user_id', $user->id)
            ->where('event_id', $this->event->id)
            ->first();

        if (!$registration) {
            // (BARU) Tampilkan data pengguna meski tidak terdaftar
            $this->lastStatus = [
                'status' => 'error',
                'message' => $user->name . ' TIDAK TERDAFTAR di event ini.',
                'data' => [
                    'Nama Tag' => $user->name,
                    'Email Tag' => $user->email,
                ]
            ];
            $this->dispatch('scan-processed'); // (BARU) Kirim event untuk auto-reset
            $this->reset('rfidTag');
            $this->dispatch('refocus-rfid-input');
            return;
        }

        // LOGIKA BARU: Cek apakah sudah ada log check-in HARI INI
        $hasCheckedInToday = $registration->checkinLogs()->whereDate('checkin_time', today())->exists();
        
        $participantData = [
            'Nama' => $registration->name,
            'Email' => $user->email,
            'Telepon' => $user->phone_number ?? '-',
            'Tipe Kehadiran' => $registration->attendance_type ? ucwords($registration->attendance_type) : 'N/A',
        ];

        if ($hasCheckedInToday) {
            // (DIUBAH) Tambahkan 'data' ke status warning
            $this->lastStatus = [
                'status' => 'warning',
                'message' => $registration->name . ' SUDAH CHECK-IN HARI INI.', //
                'data' => $participantData
            ];
            $this->dispatch('scan-processed'); // (BARU) Kirim event untuk auto-reset
            $this->reset('rfidTag');
            $this->dispatch('refocus-rfid-input');
            return;
        }

        // LOGIKA BARU: Buat entri baru di checkin_logs
        $registration->checkinLogs()->create(['checkin_time' => now()]);
        $this->lastStatus = [
            'status' => 'success',
            'message' => 'BERHASIL! Selamat datang, ' . $registration->name, //
            'data' => $participantData
        ];
        $this->dispatch('scan-processed');
        $this->reset('rfidTag');
        $this->dispatch('refocus-rfid-input');
    }
    
    public function resetStatus()
    {
        $this->lastStatus = [];
    }

    public function render()
    {
        return view('livewire.admin.checkin.rfid-tap')
            ->layout('layouts.app');
    }
}
