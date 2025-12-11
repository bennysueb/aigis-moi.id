<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\EventProgramme as ProgrammeModel;
use Carbon\Carbon;

class EventProgramme extends Component
{
    public function render()
    {
        $programmes = ProgrammeModel::with('events') // <-- PENTING: Pakai 'events'
            ->where('start_time', '>=', Carbon::today())
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->start_time)->format('Y-m-d');
            });

        return view('livewire.public.event-programme', [
            'groupedProgrammes' => $programmes
        ])->layout('layouts.app');
    }
}
