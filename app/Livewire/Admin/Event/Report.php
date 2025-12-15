<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Invitation;
use App\Exports\PendingInvitationsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllInvitationsExport;

class Report extends Component
{
    public Event $event;

    // Properti untuk menyimpan data statistik
    public $totalRegistrations;
    public $uniqueAttendees;
    public $dailyBreakdown = [];
    public $chartSeries = [];
    public $chartCategories = [];

    public $invitationStats = [
        'total' => 0,
        'sent' => 0,
        'confirmed' => 0,
        'represented' => 0,
        'declined' => 0,
        'pending' => 0,
        'response_rate' => 0,
    ];

    /**
     * Method mount() dieksekusi saat komponen dimuat.
     * Ia menerima event dari URL dan memanggil kalkulasi statistik.
     */
    public function mount(Event $event)
    {
        // 1. Simpan event yang sedang dilihat
        $this->event = $event;

        // 2. Hitung semua data statistik
        $this->calculateStats();
    }

    /**
     * Menghitung semua statistik yang diperlukan untuk laporan.
     */
    public function calculateStats()
    {
        // 1. Total Pendaftar (Kode ini sudah ada)
        $this->totalRegistrations = $this->event->registrations()->count();

        // 2. Total Hadir (Unik) (Kode ini sudah ada)
        $this->uniqueAttendees = $this->event->checkinLogs()
            ->distinct()
            ->count('registration_id');

        // 3. Rincian Harian (Kode ini sudah ada)
        $this->dailyBreakdown = $this->event->checkinLogs()
            ->selectRaw('DATE(checkin_time) as checkin_date, COUNT(*) as count')
            ->groupByRaw('DATE(checkin_time), registrations.event_id')
            ->orderBy('checkin_date', 'asc')
            ->get();

        // Ambil semua tanggal dan format (cth: '10 Nov')
        $this->chartCategories = $this->dailyBreakdown->map(function ($item) {
            return \Carbon\Carbon::parse($item->checkin_date)->format('d M');
        })->all();

        // Ambil semua angka (jumlah)
        $this->chartSeries = $this->dailyBreakdown->map(function ($item) {
            return $item->count;
        })->all();

        // 4. Statistik Undangan
        $invitations = Invitation::where('event_id', $this->event->id)->get();

        $this->invitationStats['total']       = $invitations->count();
        // Menghitung yang sudah dikirim (Email ATAU WA)
        $this->invitationStats['sent']        = $invitations->where(fn($i) => $i->is_sent_email || $i->is_sent_whatsapp)->count();

        $this->invitationStats['confirmed']   = $invitations->where('status', 'confirmed')->count();
        $this->invitationStats['represented'] = $invitations->where('status', 'represented')->count();
        $this->invitationStats['declined']    = $invitations->where('status', 'declined')->count();
        $this->invitationStats['pending']     = $invitations->where('status', 'pending')->count();

        // Hitung Response Rate (Total Respon / Total Undangan) * 100
        $totalResponded = $this->invitationStats['confirmed'] + $this->invitationStats['represented'] + $this->invitationStats['declined'];
        $this->invitationStats['response_rate'] = $this->invitationStats['total'] > 0
            ? round(($totalResponded / $this->invitationStats['total']) * 100, 1)
            : 0;
    }


    /**
     * Menghapus semua log check-in untuk event ini pada tanggal tertentu.
     */
    public function deleteLogsForDate($dateString)
    {
        // 1. Validasi tanggal (untuk keamanan)
        try {
            $date = \Carbon\Carbon::parse($dateString)->toDateString();
        } catch (\Exception $e) {
            // Jika tanggal tidak valid, jangan lakukan apa-apa
            return;
        }

        // 2. Hapus log yang sesuai
        // Kita gunakan relasi `checkinLogs()` untuk memastikan
        // kita hanya menghapus log milik event ini.
        $this->event->checkinLogs()
            ->whereDate('checkin_time', $date)
            ->delete();

        // 3. Hitung ulang statistik
        // Ini adalah langkah PENTING. Setelah data dihapus,
        // kita panggil lagi calculateStats() agar semua angka
        // (Total Hadir, Rincian Harian) diperbarui.
        $this->calculateStats();

        // 4. [OPSIONAL] Kirim pesan sukses ke tampilan
        session()->flash('message', 'Data check-in untuk tanggal ' . \Carbon\Carbon::parse($date)->format('d M Y') . ' berhasil dihapus.');
    }

    public function exportPending()
    {
        $date = now()->format('Y-m-d');
        return Excel::download(
            new PendingInvitationsExport($this->event->id),
            "pending-invitations-{$this->event->slug}-{$date}.xlsx"
        );
    }

    public function exportAll()
    {
        $date = now()->format('Y-m-d');
        return \Maatwebsite\Excel\Facades\Excel::download(
            new AllInvitationsExport($this->event->id),
            "rekap-undangan-{$this->event->slug}-{$date}.xlsx"
        );
    }

    /**
     * Merender tampilan (view) untuk laporan.
     */

    public function render()
    {
        // Ini akan mengarahkan ke file view yang akan kita buat di langkah 4
        return view('livewire.admin.event.report')
            ->layout('layouts.app');
    }
}
