<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExhibitorExport extends Component
{
    // Kolom standar yang selalu tersedia
    public $standardColumns = [
        'name' => 'Nama Lengkap',
        'email' => 'Alamat Email',
        'phone_number' => 'Nomor Telepon',
        'institution' => 'Institusi/Perusahaan',
    ];

    public $profileDataColumns = [
        'date_of_birth' => 'Tanggal Lahir',
        'gender' => 'Jenis Kelamin',
        'city' => 'Kota',
        'last_education' => 'Pendidikan Terakhir',
        'institution_name' => 'Nama Institusi Pendidikan',
        'major' => 'Jurusan',
        'graduation_year' => 'Tahun Lulus',
        'study_plan' => 'Rencana Studi',
        'study_destination' => 'Negara Tujuan Studi',
        'info_source' => 'Sumber Informasi',
    ];

    // Properti untuk menyimpan kolom yang dipilih
    public array $selectedColumns = [];

    // Properti untuk dropdown event
    public $events;
    public $selectedEventId;

    // Properti untuk menampung kolom dinamis dari event terpilih
    public array $dynamicColumns = [];

    public function mount()
    {
        // Ambil semua event untuk ditampilkan di dropdown
        $this->events = Event::orderBy('name')->get();

        // Muat pengaturan yang sudah tersimpan
        $settings = json_decode(Setting::where('key', 'exhibitor_export_columns')->value('value'), true) ?? [];

        // Pisahkan antara kolom standar dan dinamis saat memuat
        $this->selectedColumns = $settings['columns'] ?? [];
        $this->selectedEventId = $settings['event_id'] ?? null;

        // Jika ada event yang sudah tersimpan, muat juga kolom dinamisnya
        if ($this->selectedEventId) {
            $this->findDynamicColumns();
        }
    }

    // Method ini akan berjalan secara otomatis setiap kali admin memilih event
    public function updatedSelectedEventId()
    {
        $this->findDynamicColumns();
    }

    // Fungsi untuk mencari key unik dari kolom 'data'
    private function findDynamicColumns()
    {
        if (!$this->selectedEventId) {
            $this->dynamicColumns = [];
            return;
        }

        // Ambil semua data JSON dari registrasi untuk event yang dipilih
        $jsonData = Registration::where('event_id', $this->selectedEventId)
            ->whereNotNull('data')
            ->pluck('data');

        // Kumpulkan semua keys unik dari data JSON
        $keys = $jsonData->reduce(function (Collection $carry, $item) {
            // Karena 'data' sudah di-cast sebagai array, kita bisa langsung ambil keys-nya
            $itemKeys = array_keys($item);
            return $carry->merge($itemKeys);
        }, new Collection());

        $this->dynamicColumns = $keys->unique()->sort()->values()->toArray();
    }

    public function save()
    {
        // Gabungkan pengaturan menjadi satu array untuk disimpan
        $settingsToSave = [
            'event_id' => $this->selectedEventId,
            'columns' => $this->selectedColumns,
        ];

        Setting::updateOrCreate(
            ['key' => 'exhibitor_export_columns'],
            ['value' => json_encode($settingsToSave)]
        );

        $this->dispatch('alert', type: 'success', message: 'Pengaturan berhasil disimpan!');
    }


    public function render()
    {
        return view('livewire.admin.settings.exhibitor-export')
            ->layout('layouts.app');
    }
}
