<?php

namespace BajakLautMalaka\PmiDonatur\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonationEmailStatus extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * Create a new parameter.
     *
     * @var mixed details
     */
    protected $detail;

    /**
     * Create a new message instance.
     *
     * @param array $detail
     *
     * @return void
     */
    public function __construct($detail)
    {
        $this->detail = $detail;
    }

    /**
     * Build the message to send token.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('donator::mail-status')
                    ->with([
                        'detail' => $this->detail
                    ]);
    }
}
