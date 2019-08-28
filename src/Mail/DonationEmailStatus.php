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
        
        switch ($this->donation->status) {
            case '1':
            $subject    = '[Pending] Donasi '.$this->donation->campaign->title;
            $view       =  ($this->donation->fundraising)? 'donator::mail-donasi-dana-pending' : 'donator::mail-donasi-barang-pending';
            break;
            case '2':
            $subject    = '[Menunggu] Donasi '.$this->donation->campaign->title;
            break;
            case '3':
            $subject    = '[Berhasil] Donasi '.$this->donation->campaign->title;
            $view       = ($this->donation->fundraising)? 'donator::mail-donasi-dana-complete' : 'donator::mail-donasi-barang-complete';
            break;
            case '4':
            $view       = 'donator::mail-donasi-rejected';
            break;
            default :
            $subject    = 'Donasi '.$this->donation->campaign->title;
            break;
        }
        
        return $this->subject($subject)
            ->view($view)
            ->with([
                'donation' => $this->donation
            ]);
    }
}
