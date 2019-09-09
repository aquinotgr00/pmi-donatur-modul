<?php

namespace BajakLautMalaka\PmiDonatur\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonationRejectedMail extends Mailable
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
        return $this->subject('[BATAL] DONASI '.$this->donation->invoice_id)
            ->view('donator::mail-donasi-rejected')
            ->with([
                'donation' => $this->donation
            ]);
    }
}
