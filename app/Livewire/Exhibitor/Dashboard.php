<?php

namespace App\Livewire\Exhibitor;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Exports\ExhibitorAttendeesExport;
use Maatwebsite\Excel\Facades\Excel;

class Dashboard extends Component
{
    use WithPagination;

    public $user;
    // public $qrCode; // <-- HAPUS ATAU BERI KOMENTAR PADA PROPERTI INI

    /**
     * Method ini berjalan saat komponen dimuat.
     */
    public function mount()
    {
        // Cukup ambil data user yang sedang login
        $this->user = Auth::user();

        // JANGAN buat QR Code di sini
    }

    public function export()
    {
        // Cek apakah ada attendee untuk diekspor
        if (auth()->user()->attendees()->count() == 0) {
            // Anda bisa tambahkan notifikasi error di sini jika mau
            return;
        }

        // Nama file yang akan di-download
        $fileName = 'attendees-export-' . now()->format('Y-m-d') . '.xlsx';

        // Panggil class export dan mulai download
        return Excel::download(new ExhibitorAttendeesExport(auth()->user()), $fileName);
    }

    /**
     * Render tampilan.
     */
    public function render()
    {
        // Ambil data peserta yang terhubung dengan exhibitor ini
        $attendees = $this->user->attendees()->paginate(10);

        // ======================================================
        // BARU: Buat QR Code di sini, langsung di dalam render()
        // ======================================================
        $url = route('scan.connect', ['uuid' => $this->user->uuid]);
        $qrCode = QrCode::size(300)->generate($url);


        return view('livewire.exhibitor.dashboard', [
            'attendees' => $attendees,
            'qrCode' => $qrCode, // Kirim QR code sebagai variabel biasa ke view
        ])
            ->layout('layouts.app');
    }
}
