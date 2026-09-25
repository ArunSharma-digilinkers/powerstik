<?php

namespace App\Mail;

use App\Models\Lead;
use App\Support\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The receipt the enquirer gets, with their reference number. */
class LeadAcknowledged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        $reply = Site::setting('email');

        return new Envelope(
            subject: 'We have your '.strtolower(Lead::TYPES[$this->lead->type] ?? 'enquiry').' — Powerstik #'.$this->lead->id,
            replyTo: $reply ? [new Address($reply, 'Powerstik')] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.lead-acknowledged');
    }
}
