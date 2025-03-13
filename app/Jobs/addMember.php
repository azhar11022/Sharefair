<?php

namespace App\Jobs;

use App\Mail\joinRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class addMember implements ShouldQueue
{
    use Queueable;
    protected $newName;
    protected $newEmail;
    protected $senderName;
    protected $group_name;
    /**
     * Create a new job instance.
     */
    public function __construct($name,$email,$sName,$gName)
    {
        $this->newName = $name;
        $this->newEmail = $email;
        $this->senderName = $sName;
        $this->group_name = $gName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->newEmail)->send(new joinRequestMail($this->senderName,$this->group_name,$this->newName,$this->newEmail));
    }
}
