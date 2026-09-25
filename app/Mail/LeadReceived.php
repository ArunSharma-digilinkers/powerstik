<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to the coordinators in Settings → Lead notification emails. */
class LeadReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        $who = trim($this->lead->name.($this->lead->company ? ' · '.$this->lead->company : ''));

        return new Envelope(
            subject: (Lead::TYPES[$this->lead->type] ?? 'Enquiry').' #'.$this->lead->id.' — '.$who,
            // Replying to the alert reaches the enquirer, not the sending mailbox
            replyTo: $this->lead->email ? [new Address($this->lead->email, $this->lead->name)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.lead-received');
    }
}
