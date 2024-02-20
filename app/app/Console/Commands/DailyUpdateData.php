<?php

namespace App\Console\Commands;

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
use App\Mail\TestMail;
use App\Models\Tenant;
use App\Services\UserFieldsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DailyUpdateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-update-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $start_datetime = date('Y-m-d H:i:s');
        Log::info("[$start_datetime]: Update Data for all tenants has finished.");

        Tenant::all()->runForEach(function () {
            $id = tenant('id');
            $userFieldsService = new UserFieldsService();
            $userfields = config('tenantconfigfields.userfields');

            Log::info("Update process started for tenant {$id}");

            UpdateLearnerJob::withChain([
                new UpdateCallJob($id),
                new UpdateTicketJob($id),
                new UpdateMoocJob($id),
                new UpdateLpJob($id),
                new UpdateModuleJob($id),
                new UpdateEnrollementMoocJob($id),
                new UpdateEnrollementLangueJob($id),
                new UpdateEnrollementModuleJob($id),
                new UpdateEnrollementsLpsJob($id),
            ])->dispatch($userFieldsService, $id, $userfields);

        });

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("[$end_datetime]: Update Data for all tenants has finished.");
    }
}
