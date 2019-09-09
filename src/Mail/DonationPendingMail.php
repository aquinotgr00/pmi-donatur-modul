<?php

namespace BajakLautMalaka\PmiDonatur\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonationPendingMail extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * Create a new parameter.
     *
     * @var mixed donation
     */
     protected $donation;

    /**
     * Create a new message instance.
     *
     * @param array $donation
     *
     * @return void
     */
    public function __construct($donation)
    {
        $this->donation = $donation;
    }

    /**
     * Build the message to send token.
     *
     * @return $this
     */
    public function build()
    {
        $view = ($this->donation->campaign->fundraising)? 'donator::mail-donasi-dana-pending' : 'donator::mail-donasi-barang-pending';
        return $this->subject('[PENDING] DONASI '.$this->donation->invoice_id)
            ->view($view)
            ->with([
                'donation' => $this->donation
            ]);
    }
}
