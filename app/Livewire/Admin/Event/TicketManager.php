<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use App\Models\TicketTier;
use Livewire\Component;

class TicketManager extends Component
{
    public Event $event;
    public $tiers;

    // Form properties
    public $tier_id;
    public $name;
    public $description;
    public $price;
    public $quota;
    public $max_per_user = 5; // Default 5
    public $is_active = true;

    public $isEditing = false;

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->loadTiers();
    }

    public function loadTiers()
    {
        $this->tiers = $this->event->ticketTiers()->get();
    }

    public function render()
    {
        return view('livewire.admin.event.ticket-manager');
    }

    public function resetInput()
    {
        $this->name = '';
        $this->description = '';
        $this->price = 0;
        $this->quota = 0;
        $this->max_per_user = 5;
        $this->is_active = true;
        $this->isEditing = false;
        $this->tier_id = null;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:0',
            'max_per_user' => 'required|integer|min:1',
        ]);

        if ($this->isEditing) {
            $tier = TicketTier::find($this->tier_id);
            $tier->update([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'quota' => $this->quota,
                'max_per_user' => $this->max_per_user,
                'is_active' => $this->is_active
            ]);
        } else {
            TicketTier::create([
                'event_id' => $this->event->id,
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'quota' => $this->quota,
                'max_per_user' => $this->max_per_user,
                'is_active' => $this->is_active,
                // Default tanggal penjualan bisa diset null (selalu buka) atau diatur nanti
            ]);
        }

        $this->resetInput();
        $this->loadTiers();
        session()->flash('message', 'Ticket Tier saved successfully.');
    }

    public function edit($id)
    {
        $tier = TicketTier::find($id);
        $this->tier_id = $tier->id;
        $this->name = $tier->name;
        $this->description = $tier->description;
        $this->price = $tier->price;
        $this->quota = $tier->quota;
        $this->max_per_user = $tier->max_per_user;
        $this->is_active = (bool) $tier->is_active;

        $this->isEditing = true;
    }

    public function delete($id)
    {
        TicketTier::find($id)->delete();
        $this->loadTiers();
    }
}
