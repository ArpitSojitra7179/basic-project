<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Ticket;
use App\policies\EventPolicy;
use App\policies\TicketPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
        Ticket::class => TicketPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        Gate::define('view-events', function ($user) {
            return auth()->check();
        });

        Gate::define('create-event', function ($user) {
            return auth()->check();
        });

        Gate::define('view-tickets', function ($user) {
            return auth()->check();
        });

        Gate::define('book-ticket', function ($user) {
            return auth()->check();
        });

        Gate::before(function ($user) {
            echo 'You are authorized user';
        });
    }
}
