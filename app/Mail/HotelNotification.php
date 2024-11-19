<?php

namespace App\Mail;

use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HotelNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $hotelData;

    public function __construct(array $hotelData)
    {
        $this->hotelData = $hotelData;
    }

    public function build()
    {
        return $this->subject('New Business Trip Request')
            ->view('hcis.reimbursements.approval.email.htlNotification');
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hotel Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'hcis.reimbursements.approval.email.htlNotification',
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
