<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\EventAgenda as AgendaModel;
use Carbon\Carbon;

class EventAgenda extends Component
{
    public function render()
    {
        // Mengambil agenda yang akan datang, diurutkan berdasarkan waktu
        // Dikelompokkan berdasarkan Tanggal agar tampilan rapi
        $agendas = AgendaModel::with('events')
            ->where('start_time', '>=', Carbon::today()) // Hanya tampilkan hari ini ke depan
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->start_time)->format('Y-m-d'); // Group by Date
            });

        return view('livewire.public.event-agenda', [
            'groupedAgendas' => $agendas
        ])->layout('layouts.app'); // Pastikan layout sesuai dengan tema publik
    }
}