<?php
namespace App\Mail;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        return $this->subject('Приглашение в проект')
            ->view('emails.invitation')
            ->with(['url' => route('specialist.invitation.view', $this->invitation->token)]);
    }
}
