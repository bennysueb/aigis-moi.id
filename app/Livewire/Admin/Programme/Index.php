<?php

namespace App\Livewire\Admin\Programme;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EventProgramme;
use App\Models\Event;

class Index extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $programmeId;

    // UBAH: Dari single $event_id ke array $event_ids
    public $event_ids = [];

    public $title;
    public $description;
    public $start_time;
    public $end_time;
    public $location;
    public $speaker;
    public $link_url;

    protected $rules = [
        'event_ids' => 'nullable|array',
        'event_ids.*' => 'exists:events,id',
        'title' => 'required|string|max:255',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after_or_equal:start_time',
        'link_url' => 'nullable|url',
    ];

    public function render()
    {
        // Load 'events' (plural)
        return view('livewire.admin.programme.index', [
            'programmes' => EventProgramme::with('events')->latest('start_time')->paginate(10),
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
        $this->event_ids = []; // Reset array
        $this->title = '';
        $this->description = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->location = '';
        $this->speaker = '';
        $this->link_url = '';
        $this->programmeId = null;
    }

    public function store()
    {
        $this->validate();

        // 1. Simpan Data Program (Tanpa event_id)
        $programme = EventProgramme::updateOrCreate(['id' => $this->programmeId], [
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location,
            'speaker' => $this->speaker,
            'link_url' => $this->link_url,
        ]);

        // 2. Sinkronisasi Relasi Many-to-Many
        $programme->events()->sync($this->event_ids);

        session()->flash('message', $this->programmeId ? 'Program Updated Successfully.' : 'Program Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $programme = EventProgramme::with('events')->findOrFail($id);
        $this->programmeId = $id;

        // Ambil ID events yang terhubung
        $this->event_ids = $programme->events->pluck('id')->toArray();

        $this->title = $programme->title;
        $this->description = $programme->description;
        $this->start_time = $programme->start_time->format('Y-m-d\TH:i');
        $this->end_time = $programme->end_time->format('Y-m-d\TH:i');
        $this->location = $programme->location;
        $this->speaker = $programme->speaker;
        $this->link_url = $programme->link_url;

        $this->openModal();
    }

    public function delete($id)
    {
        EventProgramme::find($id)->delete();
        session()->flash('message', 'Program Deleted Successfully.');
    }
}
