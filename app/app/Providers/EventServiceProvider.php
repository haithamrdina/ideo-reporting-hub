<?php

namespace App\Providers;

use App\Jobs\ExportActiveJob;
use App\Jobs\ExportCallJob;
use App\Jobs\ExportCegosJob;
use App\Jobs\ExportConnexionJob;
use App\Jobs\ExportEniJob;
use App\Jobs\ExportInactiveJob;
use App\Jobs\ExportMoocJob;
use App\Jobs\ExportSmJob;
use App\Jobs\ExportSpeexJob;
use App\Jobs\ExportTicketsJob;
use App\Jobs\ExportTransverseJob;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\UpdateBadgeJob;
use App\Jobs\UpdateCallJob;
use App\Jobs\UpdateEnrollementLangueJob;
use App\Jobs\UpdateEnrollementModuleJob;
use App\Jobs\UpdateEnrollementMoocJob;
use App\Jobs\UpdateEnrollementsLpsJob;
use App\Jobs\UpdateGroupJob;
use App\Jobs\UpdateLearnerJob;
use App\Jobs\UpdateLpJob;
use App\Jobs\UpdateModuleJob;
use App\Jobs\UpdateMoocJob;
use App\Jobs\UpdateTicketJob;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        $this->bindJobs([
            UpdateGroupJob::class,
            UpdateLearnerJob::class,
            UpdateCallJob::class,
            UpdateTicketJob::class,
            UpdateLpJob::class,
            UpdateModuleJob::class,
            UpdateMoocJob::class,
            UpdateEnrollementMoocJob::class,
            UpdateEnrollementLangueJob::class,
            UpdateEnrollementModuleJob::class,
            UpdateEnrollementsLpsJob::class,
            UpdateBadgeJob::class,
            ExportConnexionJob::class,
            ExportActiveJob::class,
            ExportInactiveJob::class,
            ExportTransverseJob::class,
            ExportCegosJob::class,
            ExportEniJob::class,
            ExportSmJob::class,
            ExportMoocJob::class,
            ExportSpeexJob::class,
            ExportTicketsJob::class,
            ExportCallJob::class,
            NotifyUserOfCompletedExport::class
        ]);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }


    protected function bindJobs(array $jobs)
    {
        foreach ($jobs as $job) {
            $this->app->bind(
                $job . '@handle',
                fn($job) => $job->handle()
            );
        }
    }
}