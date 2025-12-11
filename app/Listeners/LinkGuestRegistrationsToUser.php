<?php

namespace App\Listeners;

use App\Models\Registration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LinkGuestRegistrationsToUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        // Ambil data pengguna yang baru saja mendaftar
        $user = $event->user;

        // Cari semua pendaftaran di database yang memiliki email yang sama
        // DAN user_id nya masih kosong (NULL)
        // Kemudian, update user_id nya dengan ID pengguna yang baru
        Registration::where('email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);
    }
}
