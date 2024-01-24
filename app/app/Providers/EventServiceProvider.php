<?php

namespace App\Providers;

use App\Jobs\UpdateGroupJob;
use App\Jobs\UpdateLearnerJob;
use App\Jobs\UpdateLpJob;
use App\Jobs\UpdateModuleJob;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        $this->app->bind(
            UpdateGroupJob::class .'@handle',
            fn($job) => $job->handle()
        );

        $this->app->bind(
            UpdateLearnerJob::class .'@handle',
            fn($job) => $job->handle()
        );

        $this->app->bind(
            UpdateLpJob::class .'@handle',
            fn($job) => $job->handle()
        );

        $this->app->bind(
            UpdateModuleJob::class .'@handle',
            fn($job) => $job->handle()
        );


    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
