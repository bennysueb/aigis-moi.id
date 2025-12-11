<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // Method untuk menampilkan daftar semua event aktif
    public function index()
    {
        $bannerEvents = Event::where('is_active', true)
            ->where('end_date', '>=', now())
            ->where('visibility', 'public')
            ->has('media') // <-- Hanya ambil event yang punya media
            ->orderBy('start_date', 'asc')
            ->take(5) // <-- Ambil 5 saja untuk banner
            ->get();

        $leftAd = Banner::where('position', 'event_ad_left')
            ->where('is_active', true)
            ->latest() // Ambil yang paling baru dibuat
            ->first(); // Ambil satu saja

        $rightAd = Banner::where('position', 'event_ad_right')
            ->where('is_active', true)
            ->latest() // Ambil yang paling baru dibuat
            ->first(); // Ambil satu saja

        $bottomAd = Banner::where('position', 'event_ad_bottom')
            ->where('is_active', true)
            ->latest()
            ->first();



        // 1. Ambil event yang akan datang (Upcoming)
        // Diurutkan dari yang paling dekat tanggalnya
        $upcomingEvents = Event::where('is_active', true)
            ->where('end_date', '>=', now())
            ->where('visibility', 'public')
            ->orderBy('start_date', 'asc')
            ->get();

        // 2. Ambil event yang sudah lampau (Past)
        // Diurutkan dari yang paling baru selesai
        $pastEvents = Event::where('is_active', true)
            ->where('end_date', '<', now())
            ->where('visibility', 'public')
            ->orderBy('start_date', 'desc')
            ->get(); // Anda bisa menambahkan ->take(6) jika hanya ingin menampilkan beberapa event terakhir

        // 3. Kirim kedua variabel ke view
        return view('event.index', [
            'bannerEvents' => $bannerEvents,
            'leftAd' => $leftAd,           // <-- Variabel baru
            'rightAd' => $rightAd,
            'bottomAd' => $bottomAd,
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
        ]);
    }
    // Method untuk menampilkan satu event berdasarkan slug
    // app/Http/Controllers/EventController.php

    public function show(Event $event)
    {
        // Pastikan hanya event aktif yang bisa diakses
        if (!$event->is_active) {
            abort(404);
        }

        $event->load(['ticketTiers' => function ($q) {
            $q->where('is_active', true);
        }]);

        $event->loadCount('registrations');

        // --- BUAT PETA PERSONNEL UNTUK AKSES MUDAH DI VIEW ---
        $personnelMap = [];
        if (!empty($event->personnel)) {
            // Gabungkan speakers dan moderators ke dalam satu peta
            $allPersonnel = array_merge($event->personnel['speakers'] ?? [], $event->personnel['moderators'] ?? []);
            foreach ($allPersonnel as $person) {
                // Gunakan ID unik sebagai key
                $personnelMap[$person['id']] = $person;
            }
        }

        // --- Cek Registrasi Pengguna ---
        $userRegistration = null;
        if (Auth::check()) {
            $userRegistration = Registration::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('event.show', [
            'event' => $event,
            'personnelMap' => $personnelMap,
            'userRegistration' => $userRegistration
        ]);
    }
}
