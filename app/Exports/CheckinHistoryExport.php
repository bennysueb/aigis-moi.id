<?php

namespace App\Exports;

use App\Models\Event;
use App\Models\CheckinLog;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CheckinHistoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $event;

    /**
     * Terima objek Event saat class ini dibuat
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Menentukan judul (header) untuk kolom-kolom di Excel
     * --- DIUBAH ---
     */
    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'No. Telepon',
            'Tanggal Registrasi RFID', // <-- Kolom baru
            'Tanggal Check-in',      // <-- Disederhanakan
        ];
    }

    /**
     * Kueri database untuk mengambil data yang akan diekspor
     * (Tidak perlu diubah, kueri ini sudah benar)
     */
    public function query()
    {
        return CheckinLog::query()
            // Ambil hanya log yang relasi 'registration'-nya milik event ini
            ->whereHas('registration', function ($query) {
                $query->where('event_id', $this->event->id);
            })
            // Eager load relasi untuk performa cepat di method map()
            ->with('registration.user')
            // Urutkan dari yang terbaru
            ->orderBy('checkin_time', 'desc');
    }

    /**
     * Memetakan/memformat setiap baris data dari hasil query()
     * --- DIUBAH ---
     *
     * @param CheckinLog $log Objek CheckinLog dari kueri
     */
    public function map($log): array
    {
        // Ambil objek Carbon untuk timestamp
        $checkinTime = Carbon::parse($log->checkin_time);

        // Ambil data registrasi rfid (mungkin null, jadi kita cek)
        $rfidDate = $log->registration->rfid_registered_at
            ? Carbon::parse($log->registration->rfid_registered_at)->format('d M Y')
            : '-'; // Tampilkan strip '-' jika tidak ada

        return [
            // Kolom 1: Nama
            $log->registration->user->name ?? $log->registration->name,
            // Kolom 2: Email
            $log->registration->user->email ?? $log->registration->email,
            // Kolom 3: No. Telepon
            $log->registration->user->phone_number ?? $log->registration->phone_number,

            // Kolom 4: Tanggal Registrasi RFID (Baru)
            $rfidDate,

            // Kolom 5: Tanggal Check-in Saja
            $checkinTime->format('d M Y'),
        ];
    }
}
