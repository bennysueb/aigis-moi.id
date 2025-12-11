<?php

namespace App\Livewire\Admin\Event;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Event;
use App\Models\Invitation;
use App\Imports\InvitationsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;
use App\Exports\InvitationTemplateExport;
use App\Models\Registration;


class InvitationManager extends Component
{
    use WithFileUploads, WithPagination;

    public Event $event;
    public $file;

    // Template Pesan
    public $waTemplate;
    public $emailSubject;
    public $emailBody;


    // Filter & Search
    public $filterStatus = 'all';
    public $search = '';

    public $isEditing = false;
    public $editingInvitationId;
    public $editForm = [
        'name' => '',
        'email' => '',
        'phone_number' => '',
        'company' => '',
        'category' => '',
    ];

    public $letterBody = '';
    public $newLetterHeader; // Upload sementara
    public $existingLetterHeader; // Path dari DB
    public $newAttachments = []; // Upload sementara (multiple)
    public $existingAttachments = []; // Array Path dari DB

    public $confirmingHeaderDeletion = false;
    public $confirmingInvitationDeletion = false;
    public $invitationIdToDelete;

    public $registrationToDelete = null;

    public function mount(Event $event)
    {
        $this->event = $event;

        // Default Template WA
        $this->waTemplate = $this->event->invitation_wa_template
            ?? "Halo {name},\n\nKami mengundang Anda ke acara *{event_name}*.\n\nLihat Surat Undangan:\n{link_surat}\n\nKonfirmasi Kehadiran:\n{link_konfirmasi}\n\nTerima kasih.";

        // Default Template Email
        $this->emailSubject = $this->event->invitation_email_subject
            ?? "Undangan: " . $event->name;

        $this->emailBody = $this->event->invitation_email_body
            ?? "Kami mengundang Anda untuk hadir di acara kami. Silakan klik tombol di bawah ini untuk konfirmasi kehadiran Anda.";

        $this->letterBody = $this->event->invitation_letter_body ?? '';
        $this->existingLetterHeader = $this->event->invitation_letter_header;
        $this->existingAttachments = $this->event->invitation_files ?? [];
    }

    // Method untuk membuka modal konfirmasi
    public function confirmDeleteInvitation($id)
    {
        $this->invitationIdToDelete = $id;
        $invitation = Invitation::find($id);

        // Cek apakah email undangan ini sudah terdaftar di registrasi event ini
        if ($invitation && $invitation->email) {
            $this->registrationToDelete = Registration::where('event_id', $this->event->id)
                ->where('email', $invitation->email)
                ->first();
        } else {
            $this->registrationToDelete = null;
        }

        $this->confirmingInvitationDeletion = true;
    }
    // Method eksekusi hapus
    public function deleteInvitation($withRegistration = false)
    {
        $invitation = Invitation::find($this->invitationIdToDelete);

        if ($invitation) {
            // Logika Hapus Registrasi (Opsional)
            if ($withRegistration && $this->registrationToDelete) {
                Registration::where('id', $this->registrationToDelete->id)->delete();
                $msg = 'Data undangan dan data peserta (registrant) berhasil dihapus.';
            } else {
                $msg = 'Data undangan berhasil dihapus.';
            }

            $invitation->delete();
            session()->flash('message', $msg);
        } else {
            session()->flash('error', 'Gagal menghapus data.');
        }

        // Reset state
        $this->confirmingInvitationDeletion = false;
        $this->invitationIdToDelete = null;
        $this->registrationToDelete = null;
    }

    // Method batal hapus
    public function cancelDeleteInvitation()
    {
        $this->confirmingInvitationDeletion = false;
        $this->invitationIdToDelete = null;
        $this->registrationToDelete = null; // Reset juga yang ini
    }

    // ▼▼▼ METHOD UNTUK SIMPAN PENGATURAN SURAT ▼▼▼
    public function saveLetterSettings()
    {
        $this->validate([
            'letterBody' => 'nullable|string',
            'newLetterHeader' => 'nullable|image|max:2048', // Max 2MB
            'newAttachments.*' => 'nullable|file|max:5120', // Max 5MB per file
        ]);

        $dataToUpdate = [
            'invitation_letter_body' => $this->letterBody,
        ];

        // 1. Handle Upload Kop Surat (Replace)
        if ($this->newLetterHeader) {
            $path = $this->newLetterHeader->store('event-assets/' . $this->event->id, 'public');
            $dataToUpdate['invitation_letter_header'] = $path;
            $this->existingLetterHeader = $path; // Update view
        }

        // 2. Handle Upload Lampiran (Append)
        if (!empty($this->newAttachments)) {
            $currentFiles = $this->existingAttachments;
            foreach ($this->newAttachments as $file) {
                $currentFiles[] = $file->store('event-docs/' . $this->event->id, 'public');
            }
            $dataToUpdate['invitation_files'] = $currentFiles;
            $this->existingAttachments = $currentFiles; // Update view
        }

        // Simpan ke DB
        $this->event->update($dataToUpdate);

        // Reset input upload
        $this->reset(['newLetterHeader', 'newAttachments']);

        session()->flash('message', 'Pengaturan Surat & Lampiran berhasil disimpan.');
    }

    public function saveMessageSettings()
    {
        $this->validate([
            'waTemplate' => 'required|string',
            'emailSubject' => 'required|string|max:255',
            'emailBody' => 'required|string',
        ]);

        $this->event->update([
            'invitation_wa_template' => $this->waTemplate,
            'invitation_email_subject' => $this->emailSubject,
            'invitation_email_body' => $this->emailBody,
        ]);

        session()->flash('message', 'Template Pesan (WA & Email) berhasil disimpan.');
    }

    public function confirmDeleteHeader()
    {
        $this->confirmingHeaderDeletion = true; // Buka modal
    }

    public function cancelDeleteHeader()
    {
        $this->confirmingHeaderDeletion = false; // Tutup modal
    }

    public function deleteLetterHeader()
    {
        // Jika ada file di database, hapus fisiknya
        if ($this->event->invitation_letter_header) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->event->invitation_letter_header);
        }

        // Update database menjadi null
        $this->event->update(['invitation_letter_header' => null]);

        // Reset properti component
        $this->existingLetterHeader = null;
        $this->newLetterHeader = null;

        // Tutup modal
        $this->confirmingHeaderDeletion = false;

        session()->flash('message', 'Kop surat berhasil dihapus.');
    }


    // Method Hapus Lampiran Tertentu
    public function removeAttachment($index)
    {
        $files = $this->existingAttachments;
        if (isset($files[$index])) {
            // Hapus file fisik (Opsional, agar hemat storage)
            // \Storage::disk('public')->delete($files[$index]); 

            unset($files[$index]);
            $this->existingAttachments = array_values($files); // Re-index array

            $this->event->update(['invitation_files' => $this->existingAttachments]);
            session()->flash('message', 'Lampiran dihapus.');
        }
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            Excel::import(new InvitationsImport($this->event->id), $this->file);
            $this->file = null;
            session()->flash('message', 'Data undangan berhasil diimport!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function sendEmail($invitationId)
    {
        $invitation = Invitation::find($invitationId);
        if (!$invitation || !$invitation->email) return;

        // Generate Link
        $linkSurat = route('invitation.letter', $invitation->uuid);
        $linkKonfirmasi = route('invitation.confirm', $invitation->uuid);

        // Replace Variabel di Body Email
        $processedBody = str_replace(
            ['{name}', '{company}', '{event_name}', '{link_surat}', '{link_konfirmasi}'],
            [
                $invitation->name,
                $invitation->company ?? '-',
                $this->event->name,
                $linkSurat,
                $linkKonfirmasi
            ],
            $this->emailBody
        );

        // Kirim Email
        Mail::to($invitation->email)->send(new InvitationMail(
            $invitation,
            $processedBody,
            $this->emailSubject
        ));

        // Update Status
        $invitation->update([
            'is_sent_email' => true,
            'email_sent_at' => now(),
        ]);

        $this->dispatch('notify', message: 'Email terkirim ke ' . $invitation->email);
    }

    public function markWaSent($invitationId)
    {
        $invitation = Invitation::find($invitationId);
        if ($invitation) {
            $invitation->update([
                'is_sent_whatsapp' => true,
                'whatsapp_sent_at' => now(),
            ]);
            $this->dispatch('notify', message: 'Status WhatsApp diperbarui untuk ' . $invitation->name);
        }
    }

    public function delete($id)
    {
        Invitation::destroy($id);
    }

    public function downloadTemplate()
    {
        return Excel::download(new InvitationTemplateExport, 'template_undangan.xlsx');
    }

    public function edit($id)
    {
        $invitation = Invitation::findOrFail($id);
        $this->editingInvitationId = $id;

        // Isi form dengan data yang ada
        $this->editForm = [
            'name' => $invitation->name,
            'email' => $invitation->email,
            'phone_number' => $invitation->phone_number,
            'company' => $invitation->company,
            'category' => $invitation->category,
        ];

        $this->isEditing = true;
    }

    public function updateInvitation()
    {
        $this->validate([
            'editForm.name' => 'required|string|max:255',
            'editForm.email' => 'nullable|email|max:255',
            'editForm.phone_number' => 'nullable|string|max:20',
            'editForm.company' => 'nullable|string|max:255',
            'editForm.category' => 'nullable|string|max:255',
        ]);

        if ($this->editingInvitationId) {
            $invitation = Invitation::find($this->editingInvitationId);
            if ($invitation) {
                $invitation->update($this->editForm);
                session()->flash('message', 'Data undangan berhasil diperbarui.');
            }
        }

        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->editingInvitationId = null;
        $this->reset('editForm');
    }

    public function render()
    {
        $invitations = Invitation::where('event_id', $this->event->id)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('company', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus !== 'all', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.event.invitation-manager', [
            'invitations' => $invitations
        ])->layout('layouts.app');
    }
}
