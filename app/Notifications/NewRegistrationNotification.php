<?php

namespace App\Notifications;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRegistrationNotification extends Notification
{
    use Queueable;

    // Kita simpan data pendaftaran untuk digunakan di dalam notifikasi
    public Registration $registration;

    /**
     * Create a new notification instance.
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Tentukan channel pengiriman notifikasi (misal: mail, database, dll)
     */
    public function via(object $notifiable): array
    {
        // Kirim notifikasi via email DAN simpan ke database
        return ['mail', 'database'];
    }

    /**
     * Bangun representasi email dari notifikasi.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $eventName = $this->registration->event->name;
        // Ambil nama pendaftar dari kolom data JSON
        $registrantName = $this->registration->data['full_name'] ?? 'A Participant';
        $url = route('admin.events.registrants', $this->registration->event);

        return (new MailMessage)
            ->subject("New Registration for: {$eventName}")
            ->greeting("Hello, Admin!")
            ->line("A new participant has registered for the event: **{$eventName}**.")
            ->line("Participant Name: **{$registrantName}**")
            ->action('View All Registrants', $url)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New registration: ' . $this->registration->name . ' for event ' . $this->registration->event->getTranslation('name', 'en'),
            'url' => route('admin.events.registrants', $this->registration->event),
            'registrant_name' => $this->registration->name,
        ];
    }
}
