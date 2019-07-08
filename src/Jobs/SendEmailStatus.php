<?php

namespace BajakLautMalaka\PmiDonatur\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use BajakLautMalaka\PmiDonatur\Mail\DonationEmailStatus;
use Mail;

class SendEmailStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Undocumented variable
     *
     * @var mixed email
     */
    protected $email;

    /**
     * Create a new parameter.
     *
     * @var mixed details
     */
    protected $details;

    /**
     * Create a new job instance.
     * @param array $details
     *
     * @return void
     */
    public function __construct($email, $details)
    {
        $this->email = $email;
        $this->details = $details;
    }

    /**
     * Execute the job to send email.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)
                ->send(
                    new DonationEmailStatus($this->details)
                );
    }
}
