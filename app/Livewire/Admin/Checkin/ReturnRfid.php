<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')] // Menggunakan layout admin yang sudah ada
class ReturnRfid extends Component
{
    public $rfidInput = '';
    public $statusMessage = 'Please Tap In the RFID card to return...';
    public $statusType = 'neutral'; // 'neutral', 'success', 'error'
    public $lastReturnedName = '';

    /**
     * Metode ini akan dipanggil saat scanner (yang bertindak sebagai keyboard)
     * menekan 'Enter' setelah memindai tag.
     */
    public function processReturn()
    {
        if (empty($this->rfidInput)) {
            return;
        }

        // 1. Cari pengguna berdasarkan rfid_tag
        $user = User::where('rfid_tag', $this->rfidInput)->first();

        if ($user) {
            // 2. Jika ditemukan, proses pengembalian
            $this->lastReturnedName = $user->name;
            $user->rfid_tag = null; // Ini adalah aksi utamanya
            $user->save();

            // 3. Atur pesan sukses
            $this->statusMessage = 'SUCCESSFULLY RETURNED';
            $this->statusType = 'success';
            $this->dispatch('play-sound', 'success'); // Memainkan suara sukses

        } else {
            // 4. Jika tidak ditemukan, atur pesan error
            $this->lastReturnedName = '';
            $this->statusMessage = 'TAG NOT FOUND';
            $this->statusType = 'error';
            $this->dispatch('play-sound', 'error'); // Memainkan suara error
        }

        // 5. Reset input untuk pemindaian berikutnya
        $this->reset('rfidInput');

        // Fokus kembali ke input field setelah diproses
        $this->dispatch('scan-processed'); 
        $this->dispatch('refocus-input');
        
    }
    
    public function resetStatus()
    {
        $this->statusMessage = 'Please Tap In the RFID card to return...';
        $this->statusType = 'neutral';
        $this->lastReturnedName = '';
    }
    
    public function resetAllTags()
    {
        try {
            // 1. Jalankan update query untuk semua user yang rfid_tag-nya TIDAK NULL
            $updatedCount = User::whereNotNull('rfid_tag')->update([
                'rfid_tag' => null
            ]);

            // 2. Atur pesan sukses
            $this->lastReturnedName = "Aksi Massal";
            $this->statusMessage = "BERHASIL: $updatedCount RFID tag telah direset.";
            $this->statusType = 'success';
            $this->dispatch('play-sound', 'success');

        } catch (\Exception $e) {
            // 3. Jika terjadi error database
            $this->lastReturnedName = "Error Sistem";
            $this->statusMessage = 'GAGAL MELAKUKAN RESET MASSAL.';
            $this->statusType = 'error';
            $this->dispatch('play-sound', 'error');
            
            // Catat error ke log untuk debugging
            Log::error('Gagal reset all RFID tags: ' . $e->getMessage());
        }

        // 4. Gunakan event yang sama untuk memicu timer reset status di Alpine.js
        $this->dispatch('scan-processed'); 
        $this->dispatch('refocus-input');
    }

    public function render()
    {
        return view('livewire.admin.checkin.return-rfid')
            ->title('Pengembalian Kartu RFID');
    }
}