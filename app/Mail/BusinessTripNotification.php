<?php

namespace App\Mail;

use App\Models\BusinessTrip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessTripNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $businessTrip;

    public function __construct(BusinessTrip $businessTrip)
    {
        $this->businessTrip = $businessTrip;
    }

    public function build()
    {
        return $this->subject('New Business Trip Request')
                    ->view('hcis.reimbursements.businessTrip.email.btNotification');
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Business Trip Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'hcis.reimbursements.businessTrip.email.btNotification',
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
