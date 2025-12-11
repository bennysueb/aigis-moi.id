<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use App\Models\CheckinLog;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\InquiryForm;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\FeedbackForm;
use App\Models\EventEmailTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;


use function Livewire\Volt\rules;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;
    
    public $showFilePicker = false;
    public $activePickerTarget = null;

    public $theme_en, $theme_id;
    public array $personnel = [];
    public array $sponsors = [];

    // Properti untuk form
    public $banner;
    public $existingBannerUrl;
    public $drive_banner_path = null;
    public $search = '';
    public $event_id;
    public $name_en, $name_id, $slug;
    public $description_en, $description_id;
    public array $daily_schedules = [];
    public $venue_en, $venue_id;
    public $google_maps_iframe = '';
    public $quota = 0;
    public $is_active = '0';
    public $status = 'upcoming';
    public $requires_account = false;
    public array $youtube_recordings = [];

    // Properti untuk UI
    public $showModal = false;
    public $isEditMode = false;

    public $inquiry_form_id = null;
    public $allForms;
    public $fieldConfig = [];

    public $external_registration_link;
    public $use_external_link = false;

    // Properti untuk menampung template yang dipilih
    public $confirmation_template_id = null;
    // Properti untuk menampung daftar template yang tersedia untuk dropdown
    public $availableTemplates = [];

    // ===== BAGIAN BARU UNTUK EVENT ONLINE =====
    public $type = 'offline'; // 'offline' atau 'online'
    public $platform; // e.g., 'Zoom Meeting', 'Google Meet', 'Lainnya...'
    public $meeting_link;
    public array $meeting_info = []; // Untuk menyimpan data terstruktur

    // Properti untuk Feedback
    public $showFeedbackModal = false;
    public $selectedEventIdForFeedback = null;
    public $feedback_form_id_to_assign = null;
    public $allFeedbackForms;

    // Properti untuk tab
    public $activeTab = 'basic'; // 'basic', 'content', 'advanced', 'personnel'
    public $activePersonnelTab = 'ui'; // 'ui', 'json'
    public $activeSponsorTab = 'ui'; // 'ui', 'json'

    // Properti baru untuk Personnel & Sponsor
    public $personnel_json = '';
    public $sponsors_json = '';

    // Properti untuk form config
    public $field_config = [];
    public $newFieldName = '';
    public $newFieldType = 'text';

    public $visibility = 'public';

    public $is_paid_event = false; // Default Free
    public $showTicketModal = false; // Modal Manager Tiket
    public $selectedEventForTicket = null;
    
    public function openFilePicker($target)
    {
        $this->activePickerTarget = $target;
        $this->showFilePicker = true;
        
        // --- TAMBAHAN: Paksa buka modal via Event ---
        $this->dispatch('open-modal', 'file-manager-picker'); 
    }
    
    #[On('fileSelected')]
    public function handleFileSelected($data)
    {
        if ($this->activePickerTarget === 'banner') {
            // Update Preview
            $this->existingBannerUrl = $data['preview_url'];
            
            // Simpan Path Drive ke Properti
            $this->drive_banner_path = $data['path']; 
            
            // Reset upload manual
            $this->banner = null; 

        } elseif (str_starts_with($this->activePickerTarget, 'personnel.')) {
            $parts = explode('.', $this->activePickerTarget);
            
            if (count($parts) >= 3) {
                $type = $parts[1]; // 'speakers' atau 'moderators'
                $index = $parts[2];

                if (isset($this->personnel[$type][$index])) {
                    $this->personnel[$type][$index]['photo_url'] = $data['preview_url'];
                    
                    // Simpan Path Drive langsung ke array personnel
                    $this->personnel[$type][$index]['drive_photo_path'] = $data['path'];
                    
                    // Reset upload manual
                    unset($this->personnel[$type][$index]['photo']);
                }
            }
        }
        
        elseif (str_starts_with($this->activePickerTarget, 'sponsors.')) {
            // Format Target: sponsors.CAT_INDEX.items.ITEM_INDEX
            // Contoh: sponsors.0.items.2
            $parts = explode('.', $this->activePickerTarget);

            if (count($parts) == 4) {
                $catIdx = $parts[1];
                $itemIdx = $parts[3];

                // Pastikan index valid
                if (isset($this->sponsors[$catIdx]['items'][$itemIdx])) {
                    // 1. Set Preview URL (Pakai Streamer Admin agar tampil di form)
                    $this->sponsors[$catIdx]['items'][$itemIdx]['logo_url'] = $data['preview_url'];
                    
                    // 2. Simpan Path Drive Sementara
                    $this->sponsors[$catIdx]['items'][$itemIdx]['drive_logo_path'] = $data['path'];
                    
                    // 3. Hapus upload manual jika ada
                    unset($this->sponsors[$catIdx]['items'][$itemIdx]['logo']);
                }
            }
        }

        $this->closeFilePicker();
    }
    
    public function closeFilePicker()
    {
        $this->showFilePicker = false;
        $this->activePickerTarget = null;

        // --- TAMBAHAN: Paksa tutup modal via Event ---
        $this->dispatch('close-modal', 'file-manager-picker'); 
    }

    public function addYoutubeRecording()
    {
        $this->youtube_recordings[] = ['title' => '', 'link' => ''];
    }

    public function removeYoutubeRecording($index)
    {
        unset($this->youtube_recordings[$index]);
        $this->youtube_recordings = array_values($this->youtube_recordings);
    }


    // Fungsi untuk inisialisasi/reset konfigurasi field
    private function initializeFieldConfig()
    {
        $this->fieldConfig = [
            'nama_instansi' => ['active' => false, 'required' => false],
            'phone_number' => ['active' => false, 'required' => false],
            'tipe_instansi' => ['active' => false, 'required' => false, 'options' => ''],
            'jabatan'       => ['active' => false, 'required' => false],
            'alamat'        => ['active' => false, 'required' => false],
            'tanda_tangan'  => ['active' => false, 'required' => false],
        ];
    }

    public function mount()
    {
        // Muat semua form kustom yang tersedia
        $this->personnel = ['speakers' => [], 'moderators' => []];
        $this->sponsors = [];
        $this->allForms = InquiryForm::all();
        $this->initializeFieldConfig();
        $this->resetDynamicProperties();
        $this->allFeedbackForms = FeedbackForm::all();
    }


    private function resetDynamicProperties()
    {
        $this->personnel = ['speakers' => [], 'moderators' => []];
        $this->sponsors = [];
    }

    // ===== METHOD BARU: Lifecycle Hook untuk form interaktif =====
    public function updatedType($value)
    {
        // Jika user kembali memilih 'offline', kosongkan data online
        if ($value === 'offline') {
            $this->platform = null;
            $this->meeting_link = '';
            $this->meeting_info = [];
        }
    }

    public function updatedPlatform($value)
    {
        // Reset meeting_info setiap kali platform diganti
        $this->meeting_info = [];
        if ($value === 'Zoom Meeting') {
            $this->meeting_info = ['meeting_id' => '', 'passcode' => ''];
        } elseif ($value === 'Lainnya...') {
            $this->meeting_info = ['platform_name' => '', 'instructions' => ''];
        }
    }


    // Method untuk mengelola Personnel (Speakers/Moderators)
    public function addPersonnel($type)
    {
        $this->personnel[$type][] = ['id' => uniqid(), 'name' => '', 'organization' => '', 'photo_url' => '', 'social_links' => []];
        $this->syncPersonnelArrayToJson(); // Sinkronkan
    }

    public function removePersonnel($type, $index)
    {
        unset($this->personnel[$type][$index]);
        $this->personnel[$type] = array_values($this->personnel[$type]);
        $this->syncPersonnelArrayToJson(); // Sinkronkan
    }

    public function addSocialLink($type, $index)
    {
        // Periksa jika 'social_links' tidak ada atau bukan array (karena data lama/salah)
        if (!isset($this->personnel[$type][$index]['social_links']) || !is_array($this->personnel[$type][$index]['social_links'])) {
            // Inisialisasi ulang sebagai array kosong
            $this->personnel[$type][$index]['social_links'] = [];
        }

        // Tambahkan struktur data baru
        $this->personnel[$type][$index]['social_links'][] = ['url' => '', 'favicon' => null];

        $this->syncPersonnelArrayToJson(); // Panggil sinkronisasi
    }

    public function removeSocialLink($type, $personIndex, $linkIndex)
    {
        unset($this->personnel[$type][$personIndex]['social_links'][$linkIndex]);
        $this->personnel[$type][$personIndex]['social_links'] = array_values($this->personnel[$type][$personIndex]['social_links']);
        $this->syncPersonnelArrayToJson(); // Sinkronkan
    }

    public function addSchedule()
    {
        // Menambahkan satu hari baru yang kosong, lengkap dengan array agenda di dalamnya
        $this->daily_schedules[] = [
            'date' => '',
            'agenda' => []
        ];
    }

    public function removeSchedule($index)
    {
        unset($this->daily_schedules[$index]);
        $this->daily_schedules = array_values($this->daily_schedules);
    }

    public function addAgenda($dayIndex)
    {
        $this->daily_schedules[$dayIndex]['agenda'][] = [
            'id' => 'session_' . uniqid(),
            'start_time' => '',
            'end_time' => '',
            'title' => ['en' => '', 'id' => ''],
            'description' => ['en' => '', 'id' => ''],
            'speaker_ids' => [],
            'moderator_ids' => [],
            'materials_link' => '',
            'extra_info' => []
        ];
    }

    public function removeAgenda($dayIndex, $agendaIndex)
    {
        unset($this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]);
        $this->daily_schedules[$dayIndex]['agenda'] = array_values($this->daily_schedules[$dayIndex]['agenda']);
    }

    public function addExtraInfo($dayIndex, $agendaIndex)
    {
        // Menambahkan satu baris info tambahan kosong
        $this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]['extra_info'][] = [
            'key' => '',
            'value' => ''
        ];
    }

    public function removeExtraInfo($dayIndex, $agendaIndex, $infoIndex)
    {
        unset($this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]['extra_info'][$infoIndex]);
        $this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]['extra_info'] = array_values(
            $this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]['extra_info']
        );
    }

    private function syncPersonnelArrayToJson()
    {
        $this->personnel_json = json_encode($this->personnel, JSON_PRETTY_PRINT);
    }

    public function updatedPersonnelJson($value)
    {
        $this->personnel = json_decode($value, true) ?? ['speakers' => [], 'moderators' => []];
    }

    public function updated($name, $value)
    {
        // Regex untuk mendeteksi perubahan pada URL social link
        // Contoh: 'personnel.speakers.0.social_links.0.url'
        if (preg_match('/personnel\.(speakers|moderators)\.(\d+)\.social_links\.(\d+)\.url/', $name, $matches)) {

            $type = $matches[1];        // 'speakers' or 'moderators'
            $personIndex = (int)$matches[2]; // 0
            $linkIndex = (int)$matches[3];   // 0

            // Panggil fungsi baru kita untuk mengambil favicon
            $faviconUrl = $this->fetchFavicon($value);

            // Simpan URL favicon ke dalam array
            $this->personnel[$type][$personIndex]['social_links'][$linkIndex]['favicon'] = $faviconUrl;

            // Sinkronkan ke properti JSON
            $this->syncPersonnelArrayToJson();
        }
        // Fallback untuk perubahan lain di array $personnel (spt mengubah nama)
        elseif (str_starts_with($name, 'personnel.')) {
            // Cukup sinkronkan ke JSON
            $this->syncPersonnelArrayToJson();
        }
    }

    private function fetchFavicon($url)
    {
        if (empty($url)) {
            return null;
        }

        try {
            // Pastikan URL memiliki skema (http/https)
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "https://" . $url;
            }

            // Dapatkan 'host' dari URL (cth: "linkedin.com")
            $host = parse_url($url, PHP_URL_HOST);

            if (!$host) {
                return null;
            }

            // Kita akan gunakan layanan Google untuk mendapatkan favicon
            $faviconUrl = "https://www.google.com/s2/favicons?domain={$host}&sz=64";

            // Cek apakah gambar benar-benar ada
            $response = Http::timeout(3)->head($faviconUrl);

            if ($response->successful() && str_contains($response->header('Content-Type'), 'image')) {
                return $faviconUrl;
            }

            return null; // Gagal mendapatkan favicon

        } catch (\Exception $e) {
            // Catat error jika perlu, tapi jangan ganggu user
            Log::error("Failed to fetch favicon for {$url}: " . $e->getMessage());
            return null;
        }
    }
    
    public function addSponsorCategory()
    {
        // Menambah Kategori Baru
        $this->sponsors[] = [
            'category_name' => '', // Contoh: 'Main Sponsor', 'Media Partner'
            'items' => []
        ];
    }
    
    public function removeSponsorCategory($catIndex)
    {
        unset($this->sponsors[$catIndex]);
        $this->sponsors = array_values($this->sponsors);
    }
    
    public function addSponsorItem($catIndex)
    {
        // Menambah Item ke dalam Kategori tertentu
        $this->sponsors[$catIndex]['items'][] = [
            'name' => '',
            'logo' => '',
            'website' => ''
        ];
    }
    
    public function removeSponsorItem($catIndex, $itemIndex)
    {
        unset($this->sponsors[$catIndex]['items'][$itemIndex]);
        $this->sponsors[$catIndex]['items'] = array_values($this->sponsors[$catIndex]['items']);
    }


    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Mengubah status aktif/non-aktif fitur feedback untuk sebuah event.
     */
    public function toggleFeedbackStatus($eventId)
    {
        $event = Event::findOrFail($eventId);
        $event->is_feedback_active = !$event->is_feedback_active;
        $event->save();
        session()->flash('message', 'Feedback status for "' . $event->name . '" has been updated.');
    }

    /**
     * Membuka modal untuk memilih form feedback.
     */
    public function openFeedbackFormModal($eventId)
    {
        $this->selectedEventIdForFeedback = $eventId;
        $event = Event::findOrFail($eventId);
        // Isi dropdown dengan form yang sudah terpilih sebelumnya (jika ada)
        $this->feedback_form_id_to_assign = $event->feedback_form_id;
        $this->showFeedbackModal = true;
    }

    /**
     * Menutup modal pemilihan form.
     */
    public function closeFeedbackFormModal()
    {
        $this->showFeedbackModal = false;
        $this->reset(['selectedEventIdForFeedback', 'feedback_form_id_to_assign']);
    }

    /**
     * Menyimpan form feedback yang dipilih dari modal ke event.
     */
    public function assignFeedbackForm()
    {
        $this->validate(['feedback_form_id_to_assign' => 'required|exists:feedback_forms,id']);

        $event = Event::findOrFail($this->selectedEventIdForFeedback);
        $event->update([
            'feedback_form_id' => $this->feedback_form_id_to_assign
        ]);

        $this->closeFeedbackFormModal();
        session()->flash('message', 'Feedback form has been successfully assigned.');
    }

    public function render()
    {
        $events = Event::withCount([
            'registrations',
            'checkinLogs as today_checkins_count' => function ($query) {
                $query->whereDate('checkin_time', today());
            }
        ])
            ->with('feedbackForm')
            ->where(function ($query) {
                $searchTerm = '%' . strtolower($this->search) . '%';

                // Gunakan whereRaw untuk membandingkan dalam huruf kecil
                $query->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.id"))) LIKE ?', [$searchTerm]);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.event.index', ['events' => $events])
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->availableTemplates = EventEmailTemplate::whereNull('event_id')->get();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $this->event_id = $event->id;
        $this->name_en = $event->getTranslation('name', 'en');
        $this->name_id = $event->getTranslation('name', 'id');
        $this->slug = $event->slug;
        $this->description_en = $event->getTranslation('description', 'en');
        $this->description_id = $event->getTranslation('description', 'id');
        $this->daily_schedules = $event->daily_schedules ?? [];
        $this->quota = $event->quota;
        $this->is_active = $event->is_active ? '1' : '0';
        $this->requires_account = $event->requires_account;
        $this->status = $event->status;
        $this->visibility = $event->visibility;
        $this->youtube_recordings = $event->youtube_recordings ?? [];
        $this->inquiry_form_id = $event->inquiry_form_id;
        $this->existingBannerUrl = $event->getFirstMediaUrl('default', 'card-banner');
        $this->theme_en = $event->getTranslation('theme', 'en');
        $this->theme_id = $event->getTranslation('theme', 'id');
        $this->personnel = $event->personnel ?? ['speakers' => [], 'moderators' => []];
        $this->sponsors = $event->sponsors ?? [];
        $this->syncPersonnelArrayToJson();
        $this->sponsors_json = json_encode($this->sponsors, JSON_PRETTY_PRINT);
        $this->fieldConfig = array_merge($this->fieldConfig, $event->field_config ?? []);
        $this->availableTemplates = EventEmailTemplate::where('event_id', $event->id)
            ->orWhereNull('event_id')
            ->get();
        $this->confirmation_template_id = $event->confirmation_template_id;
        $this->is_paid_event = (bool) $event->is_paid_event;


        // DIUBAH: Mengisi data event offline/online
        $this->type = $event->type;
        // Selalu isi data offline jika eventnya offline atau hybrid
        if ($event->type === 'offline' || $event->type === 'hybrid') {
            $this->venue_en = $event->getTranslation('venue', 'en');
            $this->venue_id = $event->getTranslation('venue', 'id');
            $this->google_maps_iframe = $event->google_maps_iframe;
        }
        // Selalu isi data online jika eventnya online atau hybrid
        if ($event->type === 'online' || $event->type === 'hybrid') {
            $this->platform = $event->platform;
            $this->meeting_link = $event->meeting_link;
            $this->meeting_info = $event->meeting_info ?? [];
        }

        $this->external_registration_link = $event->external_registration_link;
        // Jika link ada isinya, maka Switch otomatis ON
        $this->use_external_link = !empty($event->external_registration_link);

        $this->isEditMode = true;
        $this->showModal = true;
        $this->dispatch('load-content-to-editors', description_en: $this->description_en, description_id: $this->description_id);
    }

    public function save()
    {
        $rules = [
            'name_en' => 'required|string|max:255',
            'name_id' => 'required|string|max:255',

            'slug' => 'required|string|max:255|unique:events,slug,' . $this->event_id,
            'quota' => 'required|integer|min:0',
            'is_paid_event' => 'boolean', // Validasi tipe event
            'external_registration_link' => $this->use_external_link ? 'required|url' : 'nullable',
        ];

        $this->validate([
            'name_en' => 'required|string|max:255',
            'name_id' => 'required|string|max:255',
            'banner' => 'nullable|image|max:2048',
            'type' => 'required|in:offline,online,hybrid',
            'google_maps_iframe' => 'nullable|string',
            'is_paid_event' => 'boolean',

            // --- VALIDASI UNTUK AGENDA ---
            'daily_schedules' => 'required|array|min:1',
            'daily_schedules.*.date' => 'required|date_format:Y-m-d',
            'daily_schedules.*.agenda' => 'present|array',
            'daily_schedules.*.agenda.*.start_time' => 'required|date_format:H:i',
            'daily_schedules.*.agenda.*.end_time' => 'required|date_format:H:i|after:daily_schedules.*.agenda.*.start_time',
            'daily_schedules.*.agenda.*.title.en' => 'required|string',
        ]);

        // Proses upload foto untuk personnel
        foreach ($this->personnel as $type => $people) {
            foreach ($people as $index => $person) {
                
                // KASUS A: Upload Manual (Kode Lama)
                if (!empty($person['photo']) && is_object($person['photo'])) {
                    $path = $person['photo']->store('photos', 'public');
                    $this->personnel[$type][$index]['photo_url'] = Storage::url($path);
                }
                
                // KASUS B: Ambil dari Google Drive (BARU)
                // Jika ada 'drive_photo_path', kita download dan simpan ke lokal
                elseif (!empty($person['drive_photo_path'])) {
                    try {
                        // Baca file dari Google Drive
                        $fileContent = Storage::disk('google')->get($person['drive_photo_path']);
                        
                        // Buat nama file unik
                        $fileName = 'photos/' . uniqid() . '_' . basename($person['drive_photo_path']);
                        
                        // Simpan ke Storage Public Lokal
                        Storage::disk('public')->put($fileName, $fileContent);
                        
                        // Update URL ke file lokal
                        $this->personnel[$type][$index]['photo_url'] = Storage::url($fileName);
                        
                        // Hapus path drive agar tidak tersimpan kotor di JSON
                        unset($this->personnel[$type][$index]['drive_photo_path']);
                        
                    } catch (\Exception $e) {
                        session()->flash('error', 'Gagal mengambil foto personil dari Drive: ' . $e->getMessage());
                    }
                }
                
                unset($this->personnel[$type][$index]['photo']);
            }
        }

        // Proses upload logo sponsor
        $cleanSponsors = [];
        foreach ($this->sponsors as $catIndex => $category) {
            $cleanItems = [];
            if (isset($category['items']) && is_array($category['items'])) {
                foreach ($category['items'] as $itemIndex => $item) {
                    
                    // KASUS A: Upload Manual (Kode Lama)
                    if (isset($item['logo']) && is_object($item['logo'])) {
                        $path = $item['logo']->store('sponsors', 'public');
                        $item['logo_url'] = Storage::url($path);
                    }
                    
                    // KASUS B: Ambil dari Google Drive (BARU)
                    elseif (!empty($item['drive_logo_path'])) {
                        try {
                            // 1. Download dari Drive
                            $content = Storage::disk('google')->get($item['drive_logo_path']);
                            
                            // 2. Buat nama file unik di lokal
                            $ext = pathinfo($item['drive_logo_path'], PATHINFO_EXTENSION) ?: 'jpg';
                            $localName = 'sponsors/' . uniqid() . '.' . $ext;
                            
                            // 3. Simpan ke Public Storage
                            Storage::disk('public')->put($localName, $content);
                            
                            // 4. Update URL ke file lokal (Fast Loading)
                            $item['logo_url'] = Storage::url($localName);
                            
                        } catch (\Exception $e) {
                            // Log error tapi jangan hentikan proses save
                            Log::error('Gagal download sponsor dari drive: ' . $e->getMessage());
                        }
                        
                        // Bersihkan data temp
                        unset($item['drive_logo_path']);
                    }

                    // Hapus objek livewire temporary file agar tidak error saat json_encode
                    unset($item['logo']);
                    $cleanItems[] = $item;
                }
            }
            $category['items'] = $cleanItems;
            $cleanSponsors[] = $category;
        }
        
        $this->sponsors = $cleanSponsors;

        // --- ROSES JADWAL UNTUK MENDAPATKAN TANGGAL KESELURUHAN ---
        $allDatetimes = [];
        foreach ($this->daily_schedules as $schedule) {
            if (empty($schedule['agenda'])) {
                // Jika satu hari tidak punya agenda, gunakan tanggalnya saja
                $allDatetimes[] = \Carbon\Carbon::parse($schedule['date']);
            } else {
                foreach ($schedule['agenda'] as $agendaItem) {
                    $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'] . ' ' . $agendaItem['start_time']);
                    $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'] . ' ' . $agendaItem['end_time']);
                }
            }
        }
        // Fallback jika tidak ada jadwal sama sekali, meskipun sudah divalidasi
        $overallStartDate = !empty($allDatetimes) ? min($allDatetimes) : now();
        $overallEndDate = !empty($allDatetimes) ? max($allDatetimes) : now();

        $inquiryFormId = $this->inquiry_form_id === '' ? null : $this->inquiry_form_id;

        // Menyiapkan data untuk disimpan
        $data = [
            'name' => ['en' => $this->name_en, 'id' => $this->name_id],
            'slug' => Str::slug($this->name_en),
            'description' => ['en' => $this->description_en, 'id' => $this->description_id],
            'start_date' => $overallStartDate, // Diisi otomatis
            'end_date' => $overallEndDate,     // Diisi otomatis
            'quota' => $this->quota,
            'google_maps_iframe' => $this->google_maps_iframe,
            'is_active' => (bool)$this->is_active,
            'requires_account' => (bool)$this->requires_account,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'youtube_recordings' => $this->youtube_recordings,
            'inquiry_form_id' => $inquiryFormId,
            'theme' => ['en' => $this->theme_en, 'id' => $this->theme_id],
            'personnel' => $this->personnel,
            'sponsors' => $this->sponsors,
            'field_config' => $this->fieldConfig,
            'type' => $this->type,
            'confirmation_template_id' => $this->confirmation_template_id,
            'daily_schedules' => $this->daily_schedules,
            'is_paid_event' => $this->is_paid_event,
            'external_registration_link' => $this->use_external_link ? $this->external_registration_link : null,
        ];

        // ===== Logika penyimpanan untuk HYBRID =====
        // Atur data offline
        if ($this->type === 'offline' || $this->type === 'hybrid') {
            $data['venue'] = ['en' => $this->venue_en, 'id' => $this->venue_id];
        } else {
            $data['venue'] = null;
        }

        // Atur data online
        if ($this->type === 'online' || $this->type === 'hybrid') {
            $data['platform'] = $this->platform;
            $data['meeting_link'] = $this->meeting_link;
            $data['meeting_info'] = $this->meeting_info;
        } else {
            $data['platform'] = null;
            $data['meeting_link'] = null;
            $data['meeting_info'] = null;
        }


        if ($this->isEditMode) {
            $event = Event::findOrFail($this->event_id);
            $event->update($data);
        } else {
            $event = Event::create($data);
        }

        // KASUS A: Jika ada input dari Google Drive
        if ($this->drive_banner_path) {
            try {
                $event->clearMediaCollection(); // Hapus banner lama
                
                // Spatie punya fitur ajaib 'addMediaFromDisk'
                // Dia otomatis copy dari disk 'google' ke disk lokal
                $event->addMediaFromDisk($this->drive_banner_path, 'google')
                      ->preservingOriginal()
                      ->toMediaCollection();
                      
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal memproses Banner dari Drive: ' . $e->getMessage());
            }
        }
        // KASUS B: Jika ada input Manual (Kode Lama)
        elseif ($this->banner) {
            $event->clearMediaCollection();
            $event->addMedia($this->banner->getRealPath())
                ->usingName($this->banner->getClientOriginalName())
                ->toMediaCollection();
        }

        $this->closeModal();
        session()->flash('message', 'Event successfully saved.');
    }

    public function manageTickets($eventId)
    {
        $this->selectedEventForTicket = Event::find($eventId);
        $this->showTicketModal = true;
    }

    public function closeTicketModal()
    {
        $this->showTicketModal = false;
        $this->selectedEventForTicket = null;
    }

    public function delete($id)
    {
        Event::findOrFail($id)->delete();
        session()->flash('message', 'Event successfully deleted.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->event_id = null;
        $this->name_en = '';
        $this->name_id = '';
        $this->slug = '';
        $this->description_en = '';
        $this->description_id = '';
        $this->daily_schedules = [];
        $this->venue_en = '';
        $this->venue_id = '';
        $this->google_maps_iframe = '';
        $this->quota = 0;
        $this->is_active = '0';
        $this->youtube_recordings = [];
        $this->requires_account = false;
        $this->banner = null;
        $this->existingBannerUrl = null;
        $this->theme_en = '';
        $this->theme_id = '';
        $this->personnel_json = '';
        $this->sponsors_json = '';
        $this->initializeFieldConfig();
        $this->status = 'upcoming';
        $this->visibility = 'public';

        $this->external_registration_link = null;
        $this->use_external_link = false;

        $this->confirmation_template_id = null;
        $this->availableTemplates = [];

        $this->type = 'offline';
        $this->platform = null;
        $this->meeting_link = '';
        $this->meeting_info = [];
        $this->is_paid_event = false;
    }
}
