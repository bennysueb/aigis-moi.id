<?php
// File: app/Exports/RegistrantsExport.php

namespace App\Exports;

use App\Models\Event;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegistrantsExport implements FromCollection, WithHeadings, WithMapping
{
    protected Event $event;
    protected array $columns;

    /**
     * Constructor sekarang menerima objek Event dan array kolom yang dipilih.
     */
    public function __construct(Event $event, array $columns)
    {
        $this->event = $event;
        $this->columns = $columns;
    }

    /**
     * Mengambil koleksi data pendaftar.
     */
    public function collection()
    {
        return $this->event->registrations()->with('user', 'checkinLogs')->get();
    }

    /**
     * Membuat header kolom secara dinamis berdasarkan pilihan user.
     */
    public function headings(): array
    {
        return collect($this->columns)->map(function ($columnKey) {
            // Logika ini mirip dengan yang ada di komponen Livewire
            // untuk mengubah 'phone_number' menjadi 'Phone Number', dst.
            return Str::title(str_replace('_', ' ', $columnKey));
        })->toArray();
    }

    /**
     * Memetakan data setiap baris sesuai dengan kolom yang dipilih.
     */
    public function map($registration): array
    {
        $row = [];

        // Siapkan data check-in untuk efisiensi
        $checkinDates = $registration->checkinLogs
            ->pluck('checkin_time')
            ->map(fn($time) => \Carbon\Carbon::parse($time)->format('Y-md'))
            ->unique()
            ->sort();

        // Loop melalui setiap kolom yang dipilih oleh user
        foreach ($this->columns as $column) {
            $value = ''; // Nilai default
            switch ($column) {
                case 'name':
                    $value = $registration->name;
                    break;
                case 'email':
                    $value = $registration->email;
                    break;
                case 'phone_number':
                    $value = $registration->phone_number;
                    break;
                case 'checked_in_at':
                    $value = $registration->checked_in_at;
                    break;
                case 'registered_at':
                    $value = $registration->created_at->format('d M Y, H:i');
                    break;

                case 'status':
                    // Cek di dalam JSON data, apakah source-nya dari Invitation System
                    $source = $registration->data['source'] ?? null;
                    $value = ($source === 'Invitation System') ? 'Invited / VIP' : 'Regular';
                    break;

                case 'rfid_registered_at':
                    // Cek jika datanya ada (tidak null)
                    $row[] = $registration->rfid_registered_at
                        ? \Carbon\Carbon::parse($registration->rfid_registered_at)->format('d M Y H:i')
                        : '-'; // Tampilkan strip '-' jika datanya kosong/null
                    break;
                // Anda bisa menambahkan kolom khusus lain di sini jika ada
                // Contoh dari file lama Anda:
                case 'total_days_attended':
                    $value = $checkinDates->count();
                    break;
                case 'checkin_dates':
                    $value = $checkinDates->implode(', ');
                    break;
                default:
                    // Mencari nilai di data formulir, lalu di data profil user
                    $value = data_get($registration, 'data.' . $column, data_get($registration, 'user.profile_data.' . $column, ''));
                    break;
            }
            $row[] = is_array($value) ? implode(', ', $value) : $value;
        }
        return $row;
    }
}
