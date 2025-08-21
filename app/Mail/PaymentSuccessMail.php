<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PaymentInvoice;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(PaymentInvoice $invoice)
    {
        $this->invoice = $invoice;
        $this->user = $invoice->user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Thanh toán thành công - Bee Food',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
            with: [
                'invoice' => $this->invoice,
                'user' => $this->user,
                'invoiceData' => $this->invoice->getInvoiceData()
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
