<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobFailed;
use App\Observers\UserObserver;
use App\Models\User;
use App\Models\Car;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => 'App\Models\User',
            'Post' => 'App\Models\Post',
            'Video' => 'App\Models\Video',
        ]);
    
        User::resolveRelationUsing('highpriceCar', function (User $user) {
            return $this->hasOne(Car::class)->where('price', '>', 2000000);
        });

        Queue::before(function (JobProcessing $event) {
            \Log::info("Starting job: " . $event->job->resolveName());
        });

        Queue::after(function (JobProcessed $finished) {
            \Log::info("Finished job: " . $finished->job->resolveName());
        });

        Queue::failing(function (JobFailed $failed) {
            \Log::info("Job Failed: " . $failed->job->resolveName());
        });

        User::observe(UserObserver::class);
        
    }

}
