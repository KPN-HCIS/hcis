<?php

namespace App\Mail;

use App\Models\ca_approval;
use App\Models\ca_sett_approval;
use App\Models\ca_extend;
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
    public $textNotification;
    public $declaration;
    public $linkApprove;
    public $linkReject;
    protected $base64Image;
    
    public function __construct($nextApproval = null, $caTransaction = null, $textNotification, $declaration = null, $linkApprove = null, $linkReject = null)
    {
        $path = public_path('images/kop.jpg');  
        $type = pathinfo($path, PATHINFO_EXTENSION);  
        $data = file_get_contents($path);  
        $this->base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);  

        // dd($model->no_ca);
        if ($nextApproval instanceof ca_approval) {  
            $this->nextApproval = $nextApproval;  
        } elseif ($nextApproval instanceof ca_sett_approval) {  
            $this->nextApproval = $nextApproval;
        } elseif ($nextApproval instanceof ca_extend) {  
            $this->nextApproval = $nextApproval;
        } 

        if ($caTransaction instanceof CATransaction) {
            $this->caTransaction = $caTransaction;
        }

        $this->textNotification = $textNotification;

        if ($declaration != null) {
            $this->declaration = $declaration;
        }

        if ($linkApprove != null) {
            $this->linkApprove = $linkApprove;
        }

        if ($linkReject != null) {
            $this->linkReject = $linkReject;
        }
    }

    public function build()
    {
        return $this->subject('New Cash Advanced Trip Request')
            ->view('hcis.reimbursements.approval.email.caNotification-bak')
            ->with([  
                'base64Image' => $this->base64Image,  
            ]);
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
