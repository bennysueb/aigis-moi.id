<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\Event;
use App\Models\Registration;
use Livewire\Component;

class HandheldScanner extends Component
{
    public Event $event;
    public $manualUuid = '';

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function checkIn()
    {
        $uuid = basename($this->manualUuid);
        $user = null;

        $user = \App\Models\User::where('uuid', $uuid)->first();

        if (!$user) {
            $registrationFromTicket = \App\Models\Registration::where('uuid', $uuid)->first();
            if ($registrationFromTicket) {
                $user = $registrationFromTicket->user;
            }
        }

        if (!$user) {
            session()->flash('error', 'PENGGUNA / TIKET TIDAK DITEMUKAN.');
            $this->reset('manualUuid');
            $this->dispatch('refocus-scanner-input');
            return;
        }

        $registration = \App\Models\Registration::where('user_id', $user->id)
            ->where('event_id', $this->event->id)
            ->first();

        if (!$registration) {
            session()->flash('error', $user->name . ' TIDAK TERDAFTAR di event ini.');
            $this->reset('manualUuid');
            $this->dispatch('refocus-scanner-input');
            return;
        }

        // LOGIKA BARU: Cek apakah sudah ada log check-in HARI INI
        $hasCheckedInToday = $registration->checkinLogs()->whereDate('checkin_time', today())->exists();

        if ($hasCheckedInToday) {
            session()->flash('warning', $registration->name . ' sudah check-in hari ini.');
            $this->reset('manualUuid');
            $this->dispatch('refocus-scanner-input');
            return;
        }

        // LOGIKA BARU: Buat entri baru di checkin_logs
        $registration->checkinLogs()->create(['checkin_time' => now()]);
        session()->flash('success', 'Berhasil! Selamat datang, ' . $registration->name);
        $this->reset('manualUuid');
        $this->dispatch('refocus-scanner-input');
    }

    public function render()
    {
        return view('livewire.admin.checkin.handheld-scanner')->layout('layouts.app');
    }
}
