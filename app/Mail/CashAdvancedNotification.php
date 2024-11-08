<?php

namespace App\Mail;

use App\Models\ca_approval;
use App\Models\ca_transaction;
use App\Models\CATransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CashAdvancedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $nextApproval;
    public $caTransaction;
    public $model;
    
    public function __construct($nextApproval = null, $caTransaction = null, $model = null)
    {
        // dd($model->no_ca);
        if ($nextApproval instanceof ca_approval) {
            $this->nextApproval = $nextApproval;
        }

        if ($caTransaction instanceof CATransaction) {
            $this->caTransaction = $caTransaction;
        }

        if ($model instanceof CATransaction) {
            $this->model = $model;
        }
    }

    public function build()
    {
        return $this->subject('New Business Trip Request')
            ->view('hcis.reimbursements.approval.email.caNotification-bak');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cash Advanced Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'hcis.reimbursements.approval.email.caNotification-bak',
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
