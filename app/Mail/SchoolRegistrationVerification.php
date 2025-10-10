<?php

namespace App\Mail;

use App\Models\SchoolRegistrationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolRegistrationVerification extends Mailable
{
    use Queueable, SerializesModels;

    public SchoolRegistrationRequest $registrationRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(SchoolRegistrationRequest $registrationRequest)
    {
        $this->registrationRequest = $registrationRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Okul Kayıt Talebinizi Doğrulayın - Ders Programı Sistemi',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.school-registration-verification',
            with: [
                'registrationRequest' => $this->registrationRequest,
                'verificationUrl' => url('/verify-email.html?token=' . $this->registrationRequest->email_verification_token)
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
