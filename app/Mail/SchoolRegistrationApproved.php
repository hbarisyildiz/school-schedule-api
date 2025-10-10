<?php

namespace App\Mail;

use App\Models\School;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolRegistrationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public School $school;
    public User $adminUser;
    public string $temporaryPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(School $school, User $adminUser, string $temporaryPassword)
    {
        $this->school = $school;
        $this->adminUser = $adminUser;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Okul Kaydınız Onaylandı - Ders Programı Sistemi',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.school-registration-approved',
            with: [
                'school' => $this->school,
                'adminUser' => $this->adminUser,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => url('/admin-panel')
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
