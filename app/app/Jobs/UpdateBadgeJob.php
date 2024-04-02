<?php

namespace App\Jobs;

use App\Enums\GroupStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\GetAllBadges;
use App\Models\Badge;
use App\Models\Group;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateBadgeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    /**
     * Create a new job instance.
     */
    protected $tenantId;
    public function __construct(string $tenantId)
    {
        $this->tenantId = $tenantId;
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start_datetime = date('Y-m-d H:i:s');
        Log::info("['start'][$start_datetime]: UpdateBadgeJob for tenant {$this->tenantId} has started.");
        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
            // Get COURSES from DOCEBO API
            $doceboConnector = new DoceboConnector();
            $badgeResponse = $doceboConnector->send(new GetAllBadges($tenant->company_code));
            $result = $badgeResponse->dto();

            // BATCH INSERT AND UPDATE DATA INTO DATABASE
            DB::transaction(function () use ($result) {
                Badge::upsert(
                    $result,
                    ['docebo_id'],
                    [
                        'code',
                        'name',
                        'points'
                    ]
                );
            });
        tenancy()->end();
        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: UpdateBadgeJob for tenant {$this->tenantId} has finished.");
    }
}
