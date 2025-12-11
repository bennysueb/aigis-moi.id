<?php

namespace App\Livewire\Admin\Agenda;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EventAgenda;
use App\Models\Event;

class Index extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $agendaId;
    
    public $event_ids = []; 
    
    public $title;
    public $description;
    public $start_time;
    public $end_time;
    public $location;
    public $speaker;
    public $link_url;

    protected $rules = [
        'event_ids' => 'nullable|array', // Boleh kosong (optional), harus array
        'event_ids.*' => 'exists:events,id', // Pastikan ID event valid
        'title' => 'required|string|max:255',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after_or_equal:start_time',
        'link_url' => 'nullable|url',
    ];

    public function render()
    {
        // Eager load 'events' (plural)
        return view('livewire.admin.agenda.index', [
            'agendas' => EventAgenda::with('events')->latest('start_time')->paginate(10),
            'events' => Event::select('id', 'name')->get()
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->event_ids = []; // Reset jadi array kosong
        $this->title = '';
        $this->description = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->location = '';
        $this->speaker = '';
        $this->link_url = '';
        $this->agendaId = null;
    }

    public function store()
    {
        $this->validate();

        // 1. Buat atau Update Data Agenda (Tanpa event_id)
        $agenda = EventAgenda::updateOrCreate(['id' => $this->agendaId], [
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location,
            'speaker' => $this->speaker,
            'link_url' => $this->link_url,
        ]);

        // 2. Sinkronisasi Relasi Many-to-Many
        $agenda->events()->sync($this->event_ids);

        session()->flash('message', $this->agendaId ? 'Agenda Updated Successfully.' : 'Agenda Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $agenda = EventAgenda::with('events')->findOrFail($id);
        $this->agendaId = $id;
        
        // Ambil ID events yang terhubung dan jadikan array
        $this->event_ids = $agenda->events->pluck('id')->toArray();
        
        $this->title = $agenda->title;
        $this->description = $agenda->description;
        $this->start_time = $agenda->start_time->format('Y-m-d\TH:i');
        $this->end_time = $agenda->end_time->format('Y-m-d\TH:i');
        $this->location = $agenda->location;
        $this->speaker = $agenda->speaker;
        $this->link_url = $agenda->link_url;

        $this->openModal();
    }

    public function delete($id)
    {
        EventAgenda::find($id)->delete();
        session()->flash('message', 'Agenda Deleted Successfully.');
    }
}