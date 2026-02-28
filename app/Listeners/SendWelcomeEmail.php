<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{

    use InteractsWithQueue;

    public function __construct()
    {
        //        
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        Mail::raw("Hello {$user->name},\n\nWelcome to our application!", function ($message) use ($user) {
            $message->to($user->email)->subject('Welcome!');
        });
    }
}
