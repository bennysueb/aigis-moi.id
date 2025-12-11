<?php

namespace App\Livewire\Admin\Analytics;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InterestReport extends Component
{
    public $mostVisitedExhibitors;
    public $mostFavoritedExhibitors;
    public $averageVisitsPerAttendee;
    public $topRatedExhibitors;
    public $mostLovedExhibitors;

    public function mount()
    {
        // 1. Exhibitor mana yang paling banyak dikunjungi (di-scan)?
        $this->mostVisitedExhibitors = DB::table('exhibitor_attendee')
            ->join('users', 'exhibitor_attendee.exhibitor_id', '=', 'users.id')
            ->select('users.nama_instansi', DB::raw('count(exhibitor_attendee.attendee_id) as total'))
            ->groupBy('users.nama_instansi')
            ->orderBy('total', 'desc')
            ->take(10) // Ambil 10 teratas
            ->get();

        // 2. Berapa banyak exhibitor yang dikunjungi oleh rata-rata peserta?
        $totalVisits = DB::table('exhibitor_attendee')->count();
        $totalAttendeesWithScans = DB::table('exhibitor_attendee')->distinct('attendee_id')->count('attendee_id');
        $this->averageVisitsPerAttendee = ($totalAttendeesWithScans > 0) ? round($totalVisits / $totalAttendeesWithScans, 2) : 0;

        // 3. Exhibitor dengan Rata-rata Rating Tertinggi
        $this->topRatedExhibitors = DB::table('favorite_exhibitors')
            ->join('users', 'favorite_exhibitors.exhibitor_id', '=', 'users.id')
            ->select(
                'users.nama_instansi',
                DB::raw('AVG(rating) as average_rating'),
                DB::raw('COUNT(rating) as total_ratings') // <-- PERBAIKAN DI SINI
            )
            ->where('rating', '>', 0) // Hanya hitung yang sudah diberi rating
            ->groupBy('users.nama_instansi')
            ->orderBy('average_rating', 'desc')
            ->take(10)
            ->get();

        // 4. Exhibitor Paling Banyak di-"Love"
        $this->mostLovedExhibitors = DB::table('favorite_exhibitors')
            ->join('users', 'favorite_exhibitors.exhibitor_id', '=', 'users.id')
            ->select('users.nama_instansi', DB::raw('SUM(is_loved) as love_count'))
            ->where('is_loved', true)
            ->groupBy('users.nama_instansi')
            ->orderBy('love_count', 'desc')
            ->take(10)
            ->get();


        $this->mostFavoritedExhibitors = DB::table('favorite_exhibitors')
            ->join('users', 'favorite_exhibitors.exhibitor_id', '=', 'users.id')
            ->select('users.nama_instansi', DB::raw('count(favorite_exhibitors.user_id) as total'))
            ->groupBy('users.nama_instansi')
            ->orderBy('total', 'desc')
            ->take(10) // Ambil 10 teratas
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.analytics.interest-report')
            ->layout('layouts.app');
    }
}
