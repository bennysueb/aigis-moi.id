<?php

namespace App\Livewire\Admin\Broadcast;

use App\Models\EventEmailTemplate;
use App\Models\Registration;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;
use App\Mail\GlobalBroadcastMail;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendGlobalBroadcastJob;
use Illuminate\Support\Facades\Queue;
use App\Models\PendingBroadcast;


class GlobalManager extends Component
{
    use WithFileUploads, WithPagination;

    // Properti untuk form modal editor
    public $showModal = false;
    public $editingTemplateId;
    public $subject;
    public $content;
    public $banner;
    public $existingBannerPath;

    // Properti untuk modal pengiriman tes
    public $showTestSendModal = false;
    public $templateForTestId;
    public $testEmail;
    public $totalRecipients = 0;
    public $showSendModal = false;
    public $templateForBroadcastId;

    protected $rules = [
        'subject' => 'required|string|max:255',
        'content' => 'required|string',
        'banner' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->testEmail = auth()->user()->email;
        $this->totalRecipients = Registration::distinct()->count('email');
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

        $this->showModal = true;
        $this->dispatch('set-content', content: $this->content);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'event_id' => null, // Selalu NULL karena ini template global
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

        session()->flash('message', 'Template global berhasil disimpan.');
        $this->closeModal();
    }

    public function delete($templateId)
    {
        $template = EventEmailTemplate::findOrFail($templateId);
        if ($template->banner_path) {
            Storage::disk('public')->delete($template->banner_path);
        }
        $template->delete();
        session()->flash('message', 'Template global berhasil dihapus.');
    }

    public function openTestSendModal($templateId)
    {
        $this->templateForTestId = $templateId;
        $this->showTestSendModal = true;
    }

    public function openSendModal($templateId)
    {
        $this->templateForBroadcastId = $templateId;
        $this->showSendModal = true;
    }

    public function sendTestEmail()
    {
        $this->validate(['testEmail' => 'required|email']);

        $template = EventEmailTemplate::find($this->templateForTestId);
        if (!$template) {
            session()->flash('error', 'Template tidak ditemukan.');
            return;
        }

        $testRecipient = (object)[
            'name' => auth()->user()->name,
            'email' => $this->testEmail,
        ];
        Mail::to($this->testEmail)->queue(new GlobalBroadcastMail($template, $testRecipient));

        $this->showTestSendModal = false;
        session()->flash('message', 'Email tes berhasil dikirim ke ' . $this->testEmail);
    }

    public function confirmAndSendBroadcast()
    {
        // Cukup buat catatan di database. Selesai!
        PendingBroadcast::create([
            'template_id' => $this->templateForBroadcastId,
            'status' => 'pending',
        ]);

        session()->flash('message', 'Permintaan broadcast telah dicatat dan akan diproses dalam satu menit.');
        
        $this->showSendModal = false;
        $this->templateForBroadcastId = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingTemplateId = null;
        $this->subject = '';
        $this->content = '';
        $this->banner = null;
        $this->existingBannerPath = null;
    }

    public function render()
    {
        $templates = EventEmailTemplate::whereNull('event_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        // -- TAMBAHKAN KODE DI BAWAH INI --
        $broadcastHistory = PendingBroadcast::with('template')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'broadcastPage');
    
        // Ambil total penerima untuk kalkulasi progress bar
        $totalRecipients = Registration::distinct()->count('email');
        // -- AKHIR DARI KODE TAMBAHAN --
    
        return view('livewire.admin.broadcast.global-manager', [
            'templates' => $templates,
            'broadcastHistory' => $broadcastHistory, // <-- Kirim data riwayat ke view
            'totalRecipients' => $totalRecipients > 0 ? $totalRecipients : 1, // <-- Kirim total penerima
        ])->layout('layouts.app');
    }
}
