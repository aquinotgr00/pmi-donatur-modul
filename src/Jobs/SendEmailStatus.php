<?php

namespace BajakLautMalaka\PmiDonatur\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use BajakLautMalaka\PmiDonatur\Mail\DonationEmailStatus;
use BajakLautMalaka\PmiDonatur\Donation;
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
     * @var mixed donation
     */
    protected $donation;

    /**
     * Create a new job instance.
     * @param array $donation
     *
     * @return void
     */
    public function __construct(string $email,Donation $donation)
    {
        $this->email = $email;
        $this->donation = $donation;
    }

    /**
     * Execute the job to send email.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(
                    new DonationEmailStatus($this->donation)
                );
    }
}
