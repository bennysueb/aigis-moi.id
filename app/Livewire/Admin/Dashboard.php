<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Properti untuk Stat Cards
    public $activeEventsCount;
    public $totalRegistrantsCount;
    public $totalUsersCount;

    // Properti untuk Grafik & Tabel
    public $registrationChartData;
    public $popularEventsData;
    public $recentRegistrations;
    public $eventPerformanceData;

    public function mount()
    {
        // --- Query untuk Stat Cards ---
        $this->activeEventsCount = Event::where('start_date', '>=', now())->count();
        $this->totalRegistrantsCount = Registration::whereHas('event', fn($q) => $q->where('start_date', '>=', now()))->count();
        $this->totalUsersCount = User::count();

        // --- Query untuk Grafik Pendaftaran (30 Hari Terakhir) ---
        $registrationsByDate = Registration::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(29)) // Ambil data 30 hari termasuk hari ini
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date');

        // Memastikan semua 30 hari ada di dalam data, meskipun jumlahnya 0
        $labels = [];
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = date('d M', strtotime($date));
            $data[] = $registrationsByDate->get($date, 0);
        }

        $this->registrationChartData = [
            'labels' => $labels,
            'data' => $data,
        ];

        // --- Query untuk Event Terpopuler ---
        $popularEvents = Event::withCount('registrations')
            ->orderBy('registrations_count', 'desc')
            ->take(5)
            ->get();

        // Ambil nama event secara eksplisit menggunakan getTranslation
        $eventLabels = $popularEvents->map(function ($event) {
            return $event->getTranslation('name', config('app.locale')); // Ambil terjemahan sesuai bahasa aplikasi
        });

        $this->popularEventsData = [
            'labels' => $eventLabels,
            'data' => $popularEvents->pluck('registrations_count'),
        ];

        // --- Query untuk Tabel Performa Event ---
        $this->eventPerformanceData = Event::withCount('registrations')
            ->where('end_date', '>=', now()) // Ambil event yg belum selesai
            ->orderBy('start_date', 'asc') // Urutkan berdasarkan yg paling dekat
            ->get();

        // --- Query untuk Aktivitas Terbaru ---
        $this->recentRegistrations = Registration::with(['user', 'event'])->latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout('layouts.app');
    }
}
