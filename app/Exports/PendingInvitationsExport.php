<?php

namespace App\Exports;

use App\Models\Invitation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PendingInvitationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function collection()
    {
        // Ambil hanya yang statusnya 'pending'
        return Invitation::where('event_id', $this->eventId)
            ->where('status', 'pending')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'No. WhatsApp',
            'Instansi',
            'Kategori',
            'Status Kirim Email',
            'Status Kirim WA',
            'Link Konfirmasi Personal', // <-- Penting untuk follow up
        ];
    }

    public function map($invitation): array
    {
        return [
            $invitation->name,
            $invitation->email,
            $invitation->phone_number,
            $invitation->company,
            $invitation->category,
            $invitation->is_sent_email ? 'Sudah' : 'Belum',
            $invitation->is_sent_whatsapp ? 'Sudah' : 'Belum',
            route('invitation.confirm', $invitation->uuid),
        ];
    }
}
