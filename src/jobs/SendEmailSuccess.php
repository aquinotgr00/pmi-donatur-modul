<?php

namespace BajakLautMalaka\PmiDonatur\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use BajakLautMalaka\PmiDonatur\Mail\DonatorEmailSuccess;
use Mail;

class SendEmailSuccess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job to send email.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->details['email'])
                ->send(
                    new DonatorEmailSuccess($this->details['content'])
                );
    }
}
