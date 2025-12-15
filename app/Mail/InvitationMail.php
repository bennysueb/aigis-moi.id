<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Invitation;

class InvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invitation;
    public $event;
    public $confirmationLink;
    public $customSubject;
    public $customContent;

    public function __construct(Invitation $invitation, $content = null, $subject = null)
    {
        $this->invitation = $invitation;
        $this->event = $invitation->event;
        // Kita asumsikan nama route konfirmasi adalah 'invitation.confirm' (dibuat di langkah nanti)
        $this->confirmationLink = route('invitation.confirm', $invitation->uuid);
        $this->customContent = $content;
        $this->customSubject = $subject ?? 'Undangan Resmi: ' . $this->event->name;
    }

    public function build()
    {
        return $this->subject($this->customSubject)
            ->view('emails.invitation-custom');
    }
}
