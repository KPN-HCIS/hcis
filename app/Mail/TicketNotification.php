<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $noSppd;
    public $noTktList;
    public $namaPenumpang;
    public $dariTkt;
    public $keTkt;
    public $tglBrktTkt;
    public $jamBrktTkt;
    public $approvalStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->noSppd = $data['noSppd'];
        $this->noTktList = $data['noTkt'];
        $this->namaPenumpang = $data['namaPenumpang'];
        $this->dariTkt = $data['dariTkt'];
        $this->keTkt = $data['keTkt'];
        $this->tglBrktTkt = $data['tglBrktTkt'];
        $this->jamBrktTkt = $data['jamBrktTkt'];
        $this->approvalStatus = $data['approvalStatus'];
    }

    public function build()
    {
        return $this->subject('New Ticket Request')
            ->view('hcis.reimbursements.businessTrip.email.tktNotification');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket Request Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'hcis.reimbursements.businessTrip.email.tktNotification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
