<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\Event;
use App\Models\Registration;
use Livewire\Component;

class CameraScanner extends Component
{
    public Event $event;
    public $lastScanned = [];
    public $manualUuid = ''; // Untuk input handheld scanner

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    // Method untuk input manual dari handheld scanner
    public function manualCheckIn()
    {
        $this->checkIn($this->manualUuid);
        $this->manualUuid = ''; // Kosongkan input setelah submit
    }

    public function checkIn($inputValue)
    {
        if (empty($inputValue)) {
            return;
        }

        $uuid = basename($inputValue);
        $user = null;

        $user = \App\Models\User::where('uuid', $uuid)->first();

        if (!$user) {
            $registrationFromTicket = \App\Models\Registration::where('uuid', $uuid)->first();
            if ($registrationFromTicket) {
                $user = $registrationFromTicket->user;
            }
        }

        if (!$user) {
            $this->lastScanned = ['status' => 'error', 'message' => 'PENGGUNA / TIKET TIDAK DITEMUKAN.'];
            $this->dispatch('scan-failed');
            return;
        }

        $registration = \App\Models\Registration::where('user_id', $user->id)
            ->where('event_id', $this->event->id)
            ->first();

        if (!$registration) {
            $this->lastScanned = ['status' => 'error', 'message' => $user->name . ' TIDAK TERDAFTAR di event ini.'];
            $this->dispatch('scan-failed');
            return;
        }

        // LOGIKA BARU: Cek apakah sudah ada log check-in HARI INI
        $hasCheckedInToday = $registration->checkinLogs()->whereDate('checkin_time', today())->exists();

        if ($hasCheckedInToday) {
            $lastCheckinTime = $registration->checkinLogs()->whereDate('checkin_time', today())->latest('checkin_time')->first()->checkin_time;
            $this->lastScanned = ['status' => 'warning', 'message' => $registration->name . ' SUDAH CHECK-IN HARI INI pada ' . \Carbon\Carbon::parse($lastCheckinTime)->format('H:i:s')];
            $this->dispatch('scan-failed');
            $this->dispatch('scan-finished');
            return;
        }

        // LOGIKA BARU: Buat entri baru di checkin_logs
        $registration->checkinLogs()->create(['checkin_time' => now()]);
        $this->lastScanned = ['status' => 'success', 'message' => 'BERHASIL! Selamat datang, ' . $registration->name];

        $this->dispatch('scan-successful');
        $this->dispatch('scan-finished');
    }

    public function render()
    {
        return view('livewire.admin.checkin.camera-scanner')->layout('layouts.app');
    }
}
