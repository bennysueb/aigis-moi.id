<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use App\Models\EventEmailTemplate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicBroadcastMail;
use App\Models\PendingEventBroadcast;

class EmailTemplateManager extends Component
{
    use WithFileUploads;

    public Event $event;
    public $templates;

    // Properti untuk form modal
    public $showModal = false;
    public $editingTemplateId;
    public $subject;
    public $content;
    public $banner;
    public $existingBannerPath;

    // Properti untuk modal pengiriman
    public $showSendModal = false;
    public $templateToSendId;
    public $sendTarget = 'test'; // 'test' atau 'all'
    public $testEmail;
    public $is_global = false;

    protected $rules = [
        'subject' => 'required|string|max:255',
        'content' => 'required|string',
        'banner' => 'nullable|image|max:2048',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->testEmail = auth()->user()->email; // Default email test adalah email admin
        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        $this->templates = EventEmailTemplate::where('event_id', $this->event->id)
            ->orWhereNull('event_id')
            ->orderBy('event_id', 'desc') // Tampilkan template spesifik dulu
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('init-create-modal');
    }

    public function edit($templateId)
    {
        $template = EventEmailTemplate::findOrFail($templateId);
        $this->editingTemplateId = $template->id;
        $this->subject = $template->subject;
        $this->content = $template->content;
        $this->existingBannerPath = $template->banner_path;
        $this->is_global = is_null($template->event_id);

        $this->showModal = true;
        $this->dispatch('set-content', content: $this->content);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'event_id' => $this->is_global ? null : $this->event->id,
            'subject' => $this->subject,
            'content' => $this->content,
        ];

        if ($this->banner) {
            if ($this->editingTemplateId && $this->existingBannerPath) {
                Storage::disk('public')->delete($this->existingBannerPath);
            }
            $data['banner_path'] = $this->banner->store('email-banners', 'public');
        }

        EventEmailTemplate::updateOrCreate(['id' => $this->editingTemplateId], $data);

        session()->flash('message', 'Template email berhasil disimpan.');
        $this->closeModal();
    }

    public function delete($templateId)
    {
        $template = EventEmailTemplate::findOrFail($templateId);
        if ($template->banner_path) {
            Storage::disk('public')->delete($template->banner_path);
        }
        $template->delete();

        session()->flash('message', 'Template email berhasil dihapus.');
        $this->loadTemplates();
    }

    public function openSendModal($templateId)
    {
        $this->templateToSendId = $templateId;
        $this->showSendModal = true;
    }

    public function sendEmail()
    {
        $this->validate([
            'testEmail' => 'required_if:sendTarget,test|email',
        ]);

        $template = EventEmailTemplate::find($this->templateToSendId);
        if (!$template) {
            session()->flash('error', 'Template not found.');
            return;
        }

        if ($this->sendTarget === 'test') {
            // Kirim ke email test
            $firstRegistration = $this->event->registrations()->first();
            if (!$firstRegistration) {
                session()->flash('error', 'Tidak ada data pendaftar untuk dijadikan sampel. Tidak bisa mengirim test.');
                $this->showSendModal = false;
                return;
            }
            Mail::to($this->testEmail)->queue(new DynamicBroadcastMail($template, $firstRegistration));
            session()->flash('message', 'Email tes berhasil dikirim ke ' . $this->testEmail);
        
            
        } elseif ($this->sendTarget === 'all') {
            
            $totalRecipients = $this->event->registrations()->count();

            if ($totalRecipients === 0) {
                session()->flash('error', 'Tidak ada pendaftar pada acara ini untuk dikirimi email.');
                $this->showSendModal = false;
                return;
            }

            PendingEventBroadcast::create([
                'event_id' => $this->event->id,
                'template_id' => $this->templateToSendId,
                'status' => 'pending',
                'total_recipients' => $totalRecipients,
            ]);

            session()->flash('message', 'Permintaan broadcast untuk ' . $totalRecipients . ' penerima telah dicatat dan akan segera diproses.');
        }

        $this->showSendModal = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->loadTemplates();
        $this->dispatch('close-modal');
    }

    private function resetForm()
    {
        $this->editingTemplateId = null;
        $this->subject = '';
        $this->content = '';
        $this->banner = null;
        $this->existingBannerPath = null;
        $this->is_global = false;
    }

    public function render()
    {
        $broadcastHistory = PendingEventBroadcast::with('template')
            ->where('event_id', $this->event->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'broadcastPage');

        return view('livewire.admin.event.email-template-manager', [
            'broadcastHistory' => $broadcastHistory,
        ])->layout('layouts.app');
    }
}
