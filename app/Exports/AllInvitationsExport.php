<?php

namespace App\Exports;

use App\Models\Invitation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllInvitationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function collection()
    {
        // Ambil semua data undangan event ini
        return Invitation::where('event_id', $this->eventId)->get();
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
            'Status Kehadiran',
            'Nama Perwakilan',
            'Link Konfirmasi',
        ];
    }

    public function map($invitation): array
    {
        // 1. Label Status
        $statusLabel = match ($invitation->status) {
            'confirmed' => 'Hadir',
            'represented' => 'Diwakilkan',
            'declined' => 'Menolak',
            default => 'Belum Respon',
        };

        // 2. Ambil Nama Wakil (Jika ada)
        $repName = '-';
        if ($invitation->status === 'represented' && !empty($invitation->representative_data)) {
            $repName = $invitation->representative_data['name'] ?? '-';
        }

        return [
            $invitation->name,
            $invitation->email,
            $invitation->phone_number,
            $invitation->company,
            $invitation->category,
            $invitation->is_sent_email ? 'Sudah' : 'Belum',
            $invitation->is_sent_whatsapp ? 'Sudah' : 'Belum',
            $statusLabel,
            $repName, // <--- DATA KOLOM BARU
            route('invitation.confirm', $invitation->uuid),
        ];
    }
}
