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
        $view ='donator::mail-status';
        if ($this->donation->fundraising) {
            switch ($this->donation->status) {
                case '1':
                    $view = 'donator::mail-donasi-dana-pending';
                    break;
                case '3':
                    $view = 'donator::mail-donasi-dana-complete';
                    break;
            }
        }else{
            switch ($this->donation->status) {
                case '1':
                    $view = 'donator::mail-donasi-barang-pending';
                    break;
                case '3':
                    $view = 'donator::mail-donasi-barang-complete';
                    break;
            }
        }
        if ($this->donation->status === '4') {
            $view = 'donator::mail-donasi-rejected';
        }
        return $this->view($view)->with([
            'donation' => $this->donation
        ]);
    }
}
