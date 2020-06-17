<?php

namespace BajakLautMalaka\PmiDonatur\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonatorEmailResetPassword extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * Create a new parameter.
     *
     * @var mixed details
     */
    protected $content;
    /**
     * Create a new message instance.
     *
     * @param array $content
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Build the message to send token.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('donator::mail-forgot-password')
                    ->with([
                        'access_token' => $this->content
                    ]);
    }
}
