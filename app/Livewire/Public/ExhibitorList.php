<?php

namespace App\Livewire\Public;

use App\Models\User;
use Illuminate\Support\Facades\Auth; // 1. Tambahkan ini
use Livewire\Component;
use Livewire\WithPagination;

class ExhibitorList extends Component
{
    use WithPagination;

    public $favoritedExhibitorIds = [];
    public $favoritesData = [];

    /**
     * Fungsi ini berjalan sekali saat komponen pertama kali dimuat
     */
    public function mount()
    {
        // Langsung muat status favorit pengguna saat halaman dibuka
        $this->loadFavoritedIds();
        $this->loadFavoritesData();
    }

    public function setRating($exhibitorId, $rating)
    {
        if (!Auth::check()) return $this->redirect(route('login'), navigate: true);

        // Gunakan syncWithoutDetaching untuk membuat/memperbarui pivot data
        Auth::user()->favoritedExhibitors()->syncWithoutDetaching([
            $exhibitorId => ['rating' => $rating]
        ]);
        $this->loadFavoritesData();
    }

    public function toggleLove($exhibitorId)
    {
        if (!Auth::check()) return $this->redirect(route('login'), navigate: true);

        $favorite = Auth::user()->favoritedExhibitors()->where('exhibitor_id', $exhibitorId)->first();
        if ($favorite) {
            $isCurrentlyLoved = $favorite->pivot->is_loved;
            Auth::user()->favoritedExhibitors()->updateExistingPivot($exhibitorId, ['is_loved' => !$isCurrentlyLoved]);
            $this->loadFavoritesData();
        }
    }

    /**
     * Fungsi ini akan dipanggil oleh tombol Bintang (⭐)
     */
    public function toggleFavorite($exhibitorId)
    {
        // Pastikan pengguna sudah login sebelum bisa mem-favoritkan
        if (!Auth::check()) {
            return $this->redirect(route('login'), navigate: true);
        }

        // Toggle (tambah/hapus) data di tabel pivot favorite_exhibitors
        Auth::user()->favoritedExhibitors()->toggle($exhibitorId);

        // Muat ulang daftar favorit agar tampilan tombolnya ter-update
        $this->loadFavoritedIds();
    }

    private function loadFavoritesData()
    {
        if (Auth::check()) {
            // Ambil semua data favorit dan ubah menjadi array yang mudah diakses
            $this->favoritesData = Auth::user()->favoritedExhibitors()
                ->get()
                ->keyBy('id') // Jadikan ID exhibitor sebagai key
                ->map(fn($ex) => [
                    'rating' => $ex->pivot->rating,
                    'is_loved' => $ex->pivot->is_loved,
                ])->toArray();
        }
    }

    /**
     * Fungsi bantuan untuk memuat ID favorit dari database
     */
    private function loadFavoritedIds()
    {
        if (Auth::check()) {
            $this->favoritedExhibitorIds = Auth::user()->favoritedExhibitors()->pluck('users.id')->all();
        }
    }

    public function render()
    {
        $this->loadFavoritedIds();

        $exhibitors = User::role('Exhibitor')->paginate(12);

        $scannedExhibitorIds = [];
        if (Auth::check()) {
            $scannedExhibitorIds = Auth::user()
                ->visitedExhibitors()
                ->pluck('users.id') // <-- PERBAIKAN DI SINI
                ->all();
        }

        return view('livewire.public.exhibitor-list', [
            'exhibitors' => $exhibitors,
            'scannedExhibitorIds' => $scannedExhibitorIds,
        ])->layout('layouts.app');
    }
}
