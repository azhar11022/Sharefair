<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class delUserExpired implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        
    }

    // The job's logic
    public function handle()
    {
        $expiredUsers = User::where('otp_expires_at', '<', Carbon::now())
        ->where('user_status','pending')
        ->get();

        foreach ($expiredUsers as $user) {
            $user->delete();
        }
    }
}
