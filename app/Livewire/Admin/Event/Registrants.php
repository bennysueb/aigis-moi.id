<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;
use App\Mail\EventBroadcastMail;
use App\Models\BroadcastHistory;
use Illuminate\Support\Facades\Mail;
use App\Models\Registration;
use App\Jobs\SendEventBroadcast;
use App\Exports\RegistrantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\FeedbackInvitationMail;
use App\Models\BroadcastTemplate;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Exception;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use App\Exports\CheckinHistoryExport;


#[Layout('layouts.app')]
class Registrants extends Component
{
    use WithPagination;

    public $search = '';
    public Event $event;
    public $broadcastSubject = '';
    public $broadcastContent = '';

    public $selectedRegistrants = [];
    public $selectAll = false;

    public $showDetailModal = false;
    public $selectedRegistrantForDetail;

    public $showExportModal = false;
    public $availableColumns = [];
    public $selectedColumns = [];

    public $templates;
    public $selectedDate;

    public $filterType = 'all';

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->templates = $this->event->broadcastTemplates;

        // --- LOGIKA BARU UNTUK MENGATUR TANGGAL DEFAULT ---
        $eventStartDate = Carbon::parse($this->event->start_date);
        $eventEndDate = Carbon::parse($this->event->end_date);

        // Jika event sedang berlangsung, set tanggal ke hari ini
        if (today()->between($eventStartDate, $eventEndDate)) {
            $this->selectedDate = today()->toDateString();
        } else {
            // Jika event sudah lewat atau belum mulai, set ke hari pertama event
            $this->selectedDate = $eventStartDate->toDateString();
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function showDetails($registrationId)
    {
        // Ambil data pendaftar lengkap dengan relasi checkinLogs
        $this->selectedRegistrantForDetail = Registration::with('checkinLogs')->findOrFail($registrationId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedRegistrantForDetail = null; // Reset data
    }

    #[On('delete-registration')]
    public function destroyRegistration($registrationId) // <-- Langsung terima registrationId
    {
        // Tidak perlu lagi mengambil dari array, karena Livewire sudah otomatis memasukkannya
        if ($registration = Registration::find($registrationId)) {
            $registration->delete();
            $this->dispatch('registration-deleted', message: 'Registrant has been successfully deleted.');
        } else {
            $this->dispatch('delete-failed', message: 'Error: Registrant not found.');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Jika dicentang, ambil semua ID pendaftar di halaman saat ini
            $this->selectedRegistrants = $this->event->registrations()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->pluck('id')->map(fn($id) => (string)$id);
        } else {
            // Jika tidak dicentang, kosongkan pilihan
            $this->selectedRegistrants = [];
        }
    }

    public function render()
    {
        // 1. Base Query & Eager Loading
        // Kita muat 'user' dan 'checkinLogs' (hanya untuk tanggal yang dipilih)
        $query = $this->event->registrations()
            ->with([
                'user',
                'checkinLogs' => function ($q) {
                    $q->whereDate('checkin_time', $this->selectedDate);
                }
            ]);

        // 2. Logika Pencarian (Search)
        // Mencakup Nama, Email, HP, Data JSON, dan Nama User terkait
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                ->orWhere('data', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%');
                });
        });

        // 3. Logika Filter (Invited vs Regular)
        // Menggunakan sintaks JSON Laravel yang lebih stabil
        if ($this->filterType === 'invited') {
            $query->where('data->source', 'Invitation System');
        } elseif ($this->filterType === 'regular') {
            $query->where(function ($q) {
                $q->whereNull('data->source')
                    ->orWhere('data->source', '!=', 'Invitation System');
            });
        }

        // 4. Eksekusi Query & Pagination
        $registrants = $query->latest()->paginate(10);

        return view('livewire.admin.event.registrants', [
            'registrants' => $registrants,
        ]);
    }

    public function sendBroadcast()
    {
        $this->validate([
            'broadcastSubject' => 'required|string',
            'broadcastContent' => 'required|string',
            'selectedRegistrants' => 'required|array|min:1'
        ], [
            'selectedRegistrants.required' => 'Please select at least one registrant.'
        ]);

        // Menggunakan with('user') untuk performa lebih baik (Eager Loading)
        $recipients = Registration::with('user')->whereIn('id', $this->selectedRegistrants)->get();
        $successfulSends = 0; // Penghitung email yang berhasil terkirim

        foreach ($recipients as $recipient) {
            try {
                // --- MULAI BLOK PERUBAHAN ---

                // 1. Siapkan data asli untuk mengganti placeholder
                $placeholders = [
                    '[nama_peserta]'      => $recipient->user->name ?? $recipient->name, // Mengambil nama dari relasi user dulu
                    '[nama_event]'        => $this->event->name,
                    '[link_event_detail]' => route('events.show', $this->event),
                    '[nama_instansi]'     => $recipient->user->nama_instansi ?? $recipient->data['nama_instansi'] ?? 'N/A',
                    '[jabatan]'           => $recipient->user->jabatan ?? $recipient->data['jabatan'] ?? 'N/A',
                    '[link_sertifikat]'   => route('admin.certificate.download', $recipient),
                    '[link_e_tiket]'      => route('tickets.qrcode', $recipient->uuid),
                    '[link_check_in]'     => route('checkin.scan', $recipient->uuid),
                    '[link_feedback]'     => $this->event->is_feedback_active ? route('feedback.show', ['event' => $this->event, 'registration' => $recipient->uuid]) : '#',
                ];

                // 2. Ganti placeholder di subject dan content
                $finalSubject = str_replace(array_keys($placeholders), array_values($placeholders), $this->broadcastSubject);
                $finalContent = str_replace(array_keys($placeholders), array_values($placeholders), $this->broadcastContent);

                // 3. Logika untuk tombol aksi dinamis
                $isOnlineAttendance = ($this->event->type === 'online') ||
                    ($this->event->type === 'hybrid' && $recipient->attendance_type === 'online');

                if ($isOnlineAttendance) {
                    $actionUrl = $this->event->meeting_link;
                    $actionText = 'Gabung Event Online';
                } else {
                    $actionUrl = route('tickets.qrcode', $recipient->uuid);
                    $actionText = 'Lihat E-Tiket Anda';
                }

                // Buat HTML untuk tombol dan ganti placeholder [tombol_aksi]
                $actionButtonHtml = '<a href="' . $actionUrl . '" style="background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">' . $actionText . '</a>';
                $finalContent = str_replace('[tombol_aksi]', $actionButtonHtml, $finalContent);

                // 4. Kirim email dengan konten yang sudah diubah
                Mail::to($recipient->user->email ?? $recipient->email)->send(new EventBroadcastMail($finalSubject, $finalContent));

                // 5. JIKA EMAIL BERHASIL, CATAT KE RIWAYAT
                BroadcastHistory::create([
                    'registration_id' => $recipient->id,
                    'event_id'        => $this->event->id,
                    'subject'         => $finalSubject,
                    'content'         => $finalContent,
                ]);

                $successfulSends++; // Tambah hitungan jika berhasil

                // --- SELESAI BLOK PERUBAHAN ---

            } catch (Exception $e) {
                // Jika terjadi error, catat di log (opsional) dan lanjut ke penerima berikutnya
                // Log::error('Failed to send broadcast to ' . $recipient->id . ': ' . $e->getMessage());
                continue;
            }
        }

        // Reset form
        $this->reset(['broadcastSubject', 'broadcastContent', 'selectedRegistrants', 'selectAll']);

        // Tampilkan pesan sukses dengan jumlah yang akurat
        session()->flash('message', 'Email broadcast has been sent to ' . $successfulSends . ' of ' . $recipients->count() . ' selected registrants.');
    }

    public function toggleCheckIn($registrationId)
    {
        $registration = Registration::findOrFail($registrationId);

        // MODIFIKASI: Ganti 'today()' dengan '$this->selectedDate'
        $logForSelectedDate = $registration->checkinLogs()
            ->whereDate('checkin_time', $this->selectedDate)
            ->first();

        if ($logForSelectedDate) {
            // Jika sudah ada, hapus log untuk tanggal yang dipilih (Undo Check-in)
            $logForSelectedDate->delete();
            session()->flash('message', "Check-in for " . $registration->name . " on " . $this->selectedDate . " has been undone.");
        } else {
            // Jika belum ada, buat log baru (Check-in)

            // MODIFIKASI: Kita tidak bisa pakai now() lagi.
            // Kita buat timestamp untuk jam 9:00 pagi PADA TANGGAL YANG DIPILIH.
            $checkinTimestamp = Carbon::parse($this->selectedDate)->startOfDay()->addHours(9);

            $registration->checkinLogs()->create(['checkin_time' => $checkinTimestamp]);
            session()->flash('message', $registration->name . ' has been checked in for ' . $this->selectedDate . '.');
        }
    }

    public function sendFeedbackLink($registrationId)
    {
        $registration = Registration::findOrFail($registrationId);

        // Keamanan: Pastikan hanya yang sudah check-in dan belum pernah dikirimi email
        if (!$registration->checked_in_at) {
            session()->flash('message', 'Error: Participant has not checked in yet.');
            return;
        }

        if ($registration->feedback_email_sent_at) {
            session()->flash('message', 'Info: Feedback link has already been sent to this participant.');
            return;
        }

        // Kirim email menggunakan Mailable yang baru kita buat
        Mail::to($registration->email)->send(new FeedbackInvitationMail($this->event, $registration));

        // Tandai bahwa email sudah terkirim dengan mengisi timestamp
        $registration->update(['feedback_email_sent_at' => now()]);

        session()->flash('message', 'Feedback invitation has been sent to ' . $registration->name);
    }

    public function saveTemplate()
    {
        $this->validate([
            'broadcastSubject' => 'required|string|max:255',
            'broadcastContent' => 'required|string',
        ]);

        // Logika "Update or Create" yang kita diskusikan
        $this->event->broadcastTemplates()->updateOrCreate(
            ['subject' => $this->broadcastSubject], // Kondisi untuk mencari
            ['content' => $this->broadcastContent]  // Data untuk diupdate atau dibuat
        );

        $this->templates = $this->event->broadcastTemplates()->get(); // Refresh daftar template
        session()->flash('message', 'Template "' . $this->broadcastSubject . '" has been saved.');
    }

    public function loadTemplate($templateId)
    {
        $template = BroadcastTemplate::findOrFail($templateId);
        $this->broadcastSubject = $template->subject;
        $this->broadcastContent = $template->content;

        // Kirim event ke browser untuk update CKEditor
        $this->dispatch('template-loaded', content: $this->broadcastContent);
        session()->flash('message', 'Template "' . $template->subject . '" has been loaded.');
    }

    public function deleteTemplate($templateId)
    {
        // Pastikan template yang dihapus milik event ini demi keamanan
        BroadcastTemplate::where('id', $templateId)->where('event_id', $this->event->id)->delete();
        $this->templates = $this->event->broadcastTemplates()->get(); // Refresh daftar template
        session()->flash('message', 'Template has been deleted.');
    }

    // --- METHOD BARU UNTUK EXPORT ---
    public function export()
    {
        $fileName = 'registrants-' . $this->event->slug . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new RegistrantsExport($this->event), $fileName);
    }

    public function openExportModal()
    {
        // 1. Definisikan kolom statis/standar
        $staticColumns = [
            'name' => 'Nama',
            'email' => 'Email',
            'phone_number' => 'No. Telepon',
            'registered_at' => 'Tanggal Daftar',
            'rfid_registered_at' => 'Tanggal Registrasi RFID', // <-- TAMBAHAN BARU
            'status' => 'Status / Tipe',
        ];

        // 2. Ambil semua key unik dari data dinamis (form & profile)
        $registrants = $this->event->registrations()->with('user')->get();
        $dynamicKeys = [];

        foreach ($registrants as $registrant) {
            if (is_array($registrant->data)) {
                $dynamicKeys = array_merge($dynamicKeys, array_keys($registrant->data));
            }
            if ($registrant->user && is_array($registrant->user->profile_data)) {
                $dynamicKeys = array_merge($dynamicKeys, array_keys($registrant->user->profile_data));
            }
        }

        $formattedDynamicColumns = [];
        foreach (array_unique($dynamicKeys) as $key) {
            $formattedDynamicColumns[$key] = Str::title(str_replace('_', ' ', $key));
        }

        // 3. Gabungkan semua kolom yang tersedia
        $this->availableColumns = array_merge($staticColumns, $formattedDynamicColumns);

        // 4. Pilih beberapa kolom umum sebagai default
        // --- DIUBAH untuk menyertakan kolom baru ---
        $this->selectedColumns = ['name', 'email', 'phone_number', 'registered_at', 'rfid_registered_at', 'status'];

        $this->showExportModal = true;
    }

    // FUNGSI BARU UNTUK MENUTUP MODAL EXPORT
    public function closeExportModal()
    {
        $this->showExportModal = false;
    }

    // GANTI NAMA FUNGSI 'export()' MENJADI 'exportSelected()'
    public function exportSelected()
    {
        // Validasi: pastikan setidaknya satu kolom dipilih
        if (empty($this->selectedColumns)) {
            // Anda bisa menambahkan pesan error di sini jika perlu
            return;
        }

        $fileName = 'registrants-' . $this->event->slug . '-' . now()->format('Y-m-d') . '.xlsx';
        // $this->closeExportModal();
        // $this->dispatch('export-success');

        // Kirim event ID dan kolom yang dipilih ke class Export
        // Mengirim ID lebih aman untuk proses antrian (queue) daripada seluruh objek
        return Excel::download(new RegistrantsExport($this->event, $this->selectedColumns), $fileName);
    }

    public function exportCheckinHistory()
    {
        // 1. Tentukan nama file
        $fileName = 'checkin-history-' . $this->event->slug . '-' . now()->format('Y-m-d') . '.xlsx';

        // 2. Panggil dan download Class Export BARU kita.
        // Kita meneruskan $this->event agar class-nya tahu event mana yang harus diekspor.
        return Excel::download(new CheckinHistoryExport($this->event), $fileName);
    }
}
