<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\InquiryForm;
use App\Models\InquiryFormSubmission;
use App\Models\InquiryFormField;
use Carbon\Carbon;
use App\Models\TicketTier;

class UserDashboard extends Component
{
    // Properti untuk menampung semua event yang akan ditampilkan
    public $allEvents;

    public $pastEvents;

    // Properti untuk menyimpan ID event yang sudah diikuti pengguna
    public $myRegistrations;

    // Properti untuk menangani modal form kustom
    public $showCustomFieldsModal = false;
    public ?Event $eventToRegister = null;
    public $customFormData = [];

    public $showAttendanceTypeModal = false;
    public ?Event $eventForAttendanceChoice = null;
    public $selectedAttendanceType = '';

    public $showTicketModal = false;
    public $ticketTiers = [];
    public $selectedTierId = null;


    /**
     * Method ini berjalan saat komponen pertama kali dimuat.
     * Kita gunakan untuk mengambil semua data awal.
     */
    public function mount()
    {
        $user = Auth::user();

        // Ambil semua event yang aktif dan akan datang
        $this->allEvents = Event::where('is_active', true)
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->get();

        $this->pastEvents = Event::where('start_date', '<', now())
            ->whereHas('registrations', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('start_date', 'desc') // Tampilkan yang paling baru dulu
            ->get();

        // Ambil ID event yang sudah diikuti oleh pengguna yang sedang login
        $this->myRegistrations = Auth::user()->registrations()->get()->keyBy('event_id');
    }

    private function refreshMyRegistrations()
    {
        // Fungsi ini akan dipanggil setelah registrasi berhasil
        $this->myRegistrations = Auth::user()->registrations()->get()->keyBy('event_id');
    }


    /**
     * Method ini dipanggil saat pengguna mengklik tombol "Ikuti Event".
     */
    public function joinEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        $user = Auth::user();

        if (!empty($event->external_registration_link)) {

            // A. Cek apakah user sudah tercatat sebelumnya?
            // Kita gunakan firstOrCreate agar tidak ada data ganda jika dia klik berkali-kali
            Registration::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ],
                [
                    // B. Isi data default untuk pendaftar eksternal
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'status' => 'confirmed', // PENTING: Langsung 'confirmed' agar bisa akses Recordings
                    'payment_status' => 'paid', // Anggap paid/free agar tidak ditagih sistem
                    'attendance_type' => 'offline', // Default, atau sesuaikan
                    'data' => ['registration_source' => 'external_link'], // Penanda data
                    'uuid' => Str::uuid(),
                ]
            );

            // C. Redirect ke Link Luar
            return redirect()->away($event->external_registration_link);
        }

        if ($event->is_paid_event) {
            $this->dispatch('swal:error', message: 'Event ini berbayar. Silakan klik tombol "Beli Tiket".');
            return;
        }

        // LANGKAH 1: Cek apakah pengguna sudah terdaftar
        $isAlreadyRegistered = Registration::where('event_id', $event->id)
            ->where('email', $user->email)
            ->exists();

        if ($isAlreadyRegistered) {
            session()->flash('error', 'You are already registered for this event.');
            return;
        }

        if ($event->is_paid_event) {
            $this->eventToRegister = $event;
            // Ambil tiket yang aktif dan kuota masih ada
            $this->ticketTiers = $event->ticketTiers()
                ->where('is_active', true)
                ->get();

            $this->selectedTierId = null; // Reset pilihan
            $this->showTicketModal = true; // Tampilkan Modal
            return; // Stop proses di sini, tunggu user pilih tiket di modal
        }

        // LANGKAH 2: Cek kuota
        if ($event->quota > 0 && $event->remaining_quota <= 0) {
            session()->flash('error', 'Sorry, the registration quota for this event is full.');
            return;
        }

        // LANGKAH 3: Cek tipe event untuk menentukan alur selanjutnya
        if ($event->type === 'hybrid') {
            // Jika hybrid, tampilkan modal untuk memilih tipe kehadiran
            $this->eventForAttendanceChoice = $event;
            $this->selectedAttendanceType = 'online'; // Pilihan default
            $this->showAttendanceTypeModal = true;
        } else {
            // Jika online atau offline, langsung proses pendaftaran
            $this->processRegistration($event, $event->type);
        }
    }

    public function proceedToPayment()
    {
        $this->validate([
            'selectedTierId' => 'required|exists:ticket_tiers,id'
        ], [
            'selectedTierId.required' => 'Silakan pilih jenis tiket terlebih dahulu.'
        ]);

        // Redirect ke halaman form pendaftaran (Fase 4) dengan membawa parameter tiket
        // Kita passing ID tiket via query string agar form sana otomatis memilihnya
        return redirect()->route('event.register', [
            'event' => $this->eventToRegister->slug,
            'tier' => $this->selectedTierId
        ]);
    }


    public function submitAttendanceType()
    {
        $this->validate(['selectedAttendanceType' => 'required|in:online,offline']);

        if ($this->eventForAttendanceChoice && $this->eventForAttendanceChoice->is_paid_event) {
            $this->dispatch('swal:error', message: 'Event berbayar tidak dapat diproses di sini.');
            return;
        }

        $this->processRegistration($this->eventForAttendanceChoice, $this->selectedAttendanceType);

        $this->showAttendanceTypeModal = false;
        $this->reset('eventForAttendanceChoice', 'selectedAttendanceType');
    }

    private function processRegistration(Event $event, $attendanceType)
    {
        if ($event->is_paid_event) {
            $this->dispatch('swal:error', message: 'Event berbayar memerlukan pembayaran.');
            return;
        }

        $user = Auth::user();

        // Cek apakah ada field kustom yang wajib diisi
        $requiredFields = [];
        if ($event->inquiryForm) {
            foreach ($event->inquiryForm->fields as $field) {
                if ($field['required']) {
                    $requiredFields[] = $field;
                }
            }
        }

        if (empty($requiredFields)) {
            // KASUS A: TIDAK ADA form kustom, langsung daftarkan pengguna
            $newRegistration = Registration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'data' => [],
                'attendance_type' => $attendanceType, // Simpan tipe kehadiran

                'status' => 'confirmed',
                'payment_status' => 'paid', // Dianggap lunas karena gratis
                'total_price' => 0
            ]);

            if ($event->confirmation_template_id) {
                $template = \App\Models\EventEmailTemplate::find($event->confirmation_template_id);
                if ($template) {
                    Mail::to($newRegistration->email)->queue(new \App\Mail\DynamicBroadcastMail($template, $newRegistration));
                }
            }

            $this->refreshMyRegistrations();
            session()->flash('message', 'You have successfully registered for ' . $event->name . '!');
            $this->dispatch('$refresh');
        } else {
            // KASUS B: ADA form kustom, tampilkan modalnya
            $this->eventToRegister = $event;
            $this->customFormData['attendance_type'] = $attendanceType; // Simpan tipe kehadiran untuk nanti
            foreach ($requiredFields as $field) {
                $this->customFormData[$field['name']] = '';
            }
            $this->showCustomFieldsModal = true;

            $this->dispatch('custom-form-modal-shown');
        }
    }


    /**
     * Method ini dipanggil saat form di dalam modal di-submit.
     */
    public function submitCustomFields()
    {
        $user = Auth::user();
        $event = $this->eventToRegister;

        if (!$event || $event->is_paid_event) {
            $this->dispatch('swal:error', message: 'Aksi tidak diizinkan untuk event berbayar.');
            return;
        }

        // 1. Bangun SEMUA aturan validasi terlebih dahulu
        $rules = [];

        // Tambahkan aturan validasi WAJIB untuk attendance_type
        $rules['customFormData.attendance_type'] = ['required'];

        // Tambahkan aturan validasi untuk field dinamis lainnya
        foreach ($event->inquiryForm->fields as $field) {
            if ($field['required']) {
                $rules['customFormData.' . $field['name']] = ['required'];
            }
        }

        // 2. Lakukan validasi SEKARANG. Proses akan berhenti di sini jika ada yang gagal.
        $this->validate($rules, [
            'customFormData.attendance_type.required' => 'Anda harus memilih tipe kehadiran.', // Pesan error kustom
        ]);

        // 3. Setelah validasi berhasil, baru akses dan proses datanya dengan aman
        $attendanceType = $this->customFormData['attendance_type'];

        // Buat salinan data untuk disimpan di kolom 'data' (JSON)
        $dataToSave = $this->customFormData;
        unset($dataToSave['attendance_type']); // Hapus attendance_type dari salinan

        // Buat pendaftaran baru dengan data yang sudah aman
        $newRegistration = \App\Models\Registration::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'data' => $dataToSave, // Simpan data yang sudah bersih
            'attendance_type' => $attendanceType,
            'status' => 'confirmed',
            'payment_status' => 'paid', // Dianggap lunas karena gratis
            'total_price' => 0
        ]);

        if ($event->confirmation_template_id) {
            $template = \App\Models\EventEmailTemplate::find($event->confirmation_template_id);
            if ($template) {
                \Illuminate\Support\Facades\Mail::to($newRegistration->email)->queue(new \App\Mail\DynamicBroadcastMail($template, $newRegistration));
            }
        }

        $this->updateUserProfileFromRegistration($newRegistration);

        // Tutup modal, reset properti, dan beri notifikasi
        $this->showCustomFieldsModal = false;
        $this->refreshMyRegistrations();
        $this->reset(['eventToRegister', 'customFormData']);
        session()->flash('message', 'You have successfully registered for ' . $event->name . '!');

        $this->dispatch('$refresh');
    }

    private function updateUserProfileFromRegistration(Registration $registration)
    {
        // Cek apakah pendaftaran ini terhubung ke seorang pengguna
        if ($user = $registration->user) {
            // Daftar field yang ingin kita sinkronkan
            $fieldsToSync = ['nama_instansi', 'tipe_instansi', 'jabatan', 'alamat', 'tanda_tangan', 'phone_number'];

            $profileNeedsUpdate = false;

            foreach ($fieldsToSync as $field) {
                // Cek jika field di profil pengguna masih kosong DAN ada data baru dari form
                if (empty($user->{$field}) && !empty($registration->{$field} ?? $registration->data[$field] ?? null)) {

                    // Ambil data baru
                    $newData = $registration->{$field} ?? $registration->data[$field];

                    // Update properti di model User
                    $user->{$field} = $newData;
                    $profileNeedsUpdate = true;
                }
            }

            // Simpan ke database hanya jika ada perubahan
            if ($profileNeedsUpdate) {
                $user->save();
            }
        }
    }


    /**
     * Render tampilan.
     */
    public function render()
    {
        return view('livewire.user-dashboard')->layout('layouts.app');
    }
}
