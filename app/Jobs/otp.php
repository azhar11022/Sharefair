<?php

namespace App\Jobs;

use App\Mail\loginMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class otp implements ShouldQueue
{
    use Queueable;
    public $name ;
    public $email;
    public $otp;
    /**
     * Create a new job instance.
     */
    public function __construct($na, $em, $ot)
    {
        $this->name = $na;
        $this->email = $em;
        $this->otp = $ot;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new loginMail($this->email, $this->name, $this->otp));
    }
}
