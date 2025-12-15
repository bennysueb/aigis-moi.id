<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventController extends Controller
{
    // Method untuk menampilkan daftar semua event aktif
    public function index(Request $request)
    {
        // 1. Setup Banner & Iklan (Tetap sama)
        $bannerEvents = Event::where('is_active', true)
            ->where('end_date', '>=', now())
            ->where('visibility', 'public')
            ->has('media')
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        $leftAd = Banner::where('position', 'event_ad_left')->where('is_active', true)->latest()->first();
        $rightAd = Banner::where('position', 'event_ad_right')->where('is_active', true)->latest()->first();
        $bottomAd = Banner::where('position', 'event_ad_bottom')->where('is_active', true)->latest()->first();

        // 2. Setup Filter & View Logic
        $month = $request->input('month');
        $year = $request->input('year');
        $viewType = $request->input('view', 'list');

        if ($viewType === 'calendar' && !$month && !$year) {
            $month = now()->month;
            $year = now()->year;
        }

        // Inisialisasi variabel agar tidak error di mode List
        $upcomingEvents = collect();
        $pastEvents = collect();
        $calendarEvents = collect();
        $calendarDate = null;
        $prevDate = null; // <--- PENTING: Inisialisasi dengan null
        $nextDate = null; // <--- PENTING: Inisialisasi dengan null

        if ($viewType === 'calendar') {
            // === LOGIC TAMPILAN KALENDER ===
            $calendarDate = Carbon::createFromDate($year ?? now()->year, $month ?? now()->month, 1);

            // Tombol navigasi (Prev/Next)
            $prevDate = $calendarDate->copy()->subMonth();
            $nextDate = $calendarDate->copy()->addMonth();

            $calendarEvents = Event::where('is_active', true)
                ->where('visibility', 'public')
                ->where(function ($q) use ($calendarDate) {
                    $startOfMonth = $calendarDate->copy()->startOfMonth();
                    $endOfMonth = $calendarDate->copy()->endOfMonth();

                    $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                        ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                        ->orWhere(function ($sub) use ($startOfMonth, $endOfMonth) {
                            $sub->where('start_date', '<', $startOfMonth)
                                ->where('end_date', '>', $endOfMonth);
                        });
                })
                ->orderBy('start_date', 'asc')
                ->get();
        } else {
            // === LOGIC TAMPILAN LIST (DEFAULT) ===
            $upcomingQuery = Event::where('is_active', true)
                ->where('end_date', '>=', now())
                ->where('visibility', 'public');

            $pastQuery = Event::where('is_active', true)
                ->where('end_date', '<', now())
                ->where('visibility', 'public');

            if ($month) {
                $upcomingQuery->whereMonth('start_date', $month);
                $pastQuery->whereMonth('start_date', $month);
            }
            if ($year) {
                $upcomingQuery->whereYear('start_date', $year);
                $pastQuery->whereYear('start_date', $year);
            }

            $upcomingEvents = $upcomingQuery->orderBy('start_date', 'asc')->get();
            $pastEvents = $pastQuery->orderBy('start_date', 'desc')->get();
        }

        return view('event.index', [
            'bannerEvents' => $bannerEvents,
            'leftAd' => $leftAd,
            'rightAd' => $rightAd,
            'bottomAd' => $bottomAd,
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
            'viewType' => $viewType,
            'calendarEvents' => $calendarEvents,
            'calendarDate' => $calendarDate,
            'selectedMonth' => $month,
            'selectedYear' => $year,
            'prevDate' => $prevDate, // Sekarang aman karena sudah didefinisikan null di atas
            'nextDate' => $nextDate, // Sekarang aman
        ]);
    }

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
