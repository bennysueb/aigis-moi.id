<?php

namespace App\Exports;

use App\Models\Setting;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Str;

class ExhibitorAttendeesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $exhibitor;
    protected $settings;
    protected $allColumns;

    // Mendefinisikan sumber data untuk setiap kolom
    protected $registrationSpecificColumns = ['institution'];
    protected $userSpecificColumns = ['name', 'email', 'phone_number'];

    protected $profileSpecificColumns = [
        'date_of_birth',
        'gender',
        'city',
        'last_education',
        'institution_name',
        'major',
        'graduation_year',
        'study_plan',
        'study_destination',
        'info_source'
    ];


    public function __construct(User $exhibitor)
    {
        $this->exhibitor = $exhibitor;
        $this->settings = json_decode(Setting::where('key', 'exhibitor_export_columns')->value('value'), true) ?? ['columns' => []];
        $this->allColumns = $this->settings['columns'] ?? [];
    }

    public function collection()
    {
        return $this->exhibitor->attendees()->with('registrations')->get();
    }

    public function headings(): array
    {
        $headings = [];
        foreach ($this->allColumns as $columnKey) {
            $headings[] = Str::title(str_replace('_', ' ', $columnKey));
        }
        return $headings;
    }

    public function map($attendee): array
    {
        $row = [];

        $registration = null;
        if (isset($this->settings['event_id'])) {
            $registration = $attendee->registrations->firstWhere('event_id', $this->settings['event_id']);
        }

        foreach ($this->allColumns as $columnKey) {
            $value = ''; // Nilai default

            // LOGIKA FINAL YANG SUDAH DIPERBAIKI:
            // 1. Cek apakah kolom ada di data profil utama (users)
            if (in_array($columnKey, $this->userSpecificColumns)) {
                $value = $attendee->{$columnKey} ?? '';
            }
            // 2. Jika tidak, cek apakah ada di data pendaftaran (registrations)
            elseif (in_array($columnKey, $this->registrationSpecificColumns)) {
                $value = $registration->{$columnKey} ?? '';
            }

            // 3. (BARU) Jika tidak, cek apakah ada di JSON profile_data (users)
            elseif (in_array($columnKey, $this->profileSpecificColumns)) {
                // Ambil dari kolom JSON 'profile_data' di model User ($attendee)
                $value = $attendee->profile_data[$columnKey] ?? '';
            }
            // 4. Jika tidak, anggap sebagai kolom kustom dari 'data' JSON (registrations)
            else {
                // Pastikan $registration tidak null sebelum mengakses propertinya
                $value = $registration->data[$columnKey] ?? '';
            }

            $row[] = $value;
        }
        return $row;
    }
}
