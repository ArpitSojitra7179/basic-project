<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use App\Models\User;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

   
    public function middleware() {
        return [
            new WithoutOverlapping($this->user->id),

            (new RateLimited('send-welcome'))->dontRelease(),

            (new ThrottlesExceptions(5, 1))->backoff(30),
        ];
    }

    public function handle(): void
    {
        $email = $this->user->email;
        $name = $this->user->name;

        Mail::raw("Hello $name,\n\nWelcome to our application!", function ($message) use ($email) {

            $message->to($email)->subject('Welcome to our app');
        });
    }
}
