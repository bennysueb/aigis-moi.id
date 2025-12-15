<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(Event $event, Registration $registration)
    {
        $this->event = $event;
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Feedback for Event: ' . $this->event->name, // Judul Email
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.feedback-invitation', // Menunjuk ke file template
        );
    }
}
