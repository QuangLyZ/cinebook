<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public object $ticket;

    public function __construct(object $ticket)
    {
        $this->ticket = $ticket;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vé xem phim của bạn tại CineBook',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_ticket',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
