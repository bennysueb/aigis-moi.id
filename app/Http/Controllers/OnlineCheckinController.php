<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class OnlineCheckinController extends Controller
{
    // Method untuk menampilkan halaman form check-in
    public function show(Event $event)
    {
        // Pastikan hanya event online/hybrid yang bisa diakses
        if ($event->type === 'offline') {
            abort(404);
        }
        return view('online-checkin.show', ['event' => $event]);
    }

    // Method untuk memproses form check-in
    public function store(Request $request, Event $event)
    {
        $request->validate(['email' => 'required|email']);

        // 2. Cari pendaftaran berdasarkan event dan email
        $registration = Registration::where('event_id', $event->id)
            ->where('email', $request->email)
            ->first();

        if (!$registration) {
            return back()->with('error', 'This email is not registered for this event.');
        }

        // 3. Pastikan pendaftar ini adalah peserta online (jika eventnya hybrid)
        if ($event->type === 'hybrid' && $registration->attendance_type !== 'online') {
            return back()->with('error', 'This registration is for offline attendance.');
        }

        // 4. Cek apakah sudah pernah check-in
        if ($registration->checked_in_at) {
            return back()->with('error', 'This email has already been checked in at ' . $registration->checked_in_at->format('H:i T'));
        }

        // 5. Jika semua valid, lakukan check-in
        $timestamp = now();

        // A. Update status di tabel utama (registrations)
        $registration->update(['checked_in_at' => $timestamp]);

        // ▼▼▼ B. TAMBAHAN BARU: Simpan ke checkin_logs (Agar muncul di Report Harian) ▼▼▼
        $registration->checkinLogs()->create([
            'checkin_time' => $timestamp
        ]);
        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

        // 6. Arahkan ke halaman sukses
        return redirect()->route('online.checkin.success', $registration->uuid);
    }

    // Method untuk menampilkan halaman sukses
    public function success(Registration $registration)
    {
        return view('online-checkin.success', ['registration' => $registration]);
    }
}
