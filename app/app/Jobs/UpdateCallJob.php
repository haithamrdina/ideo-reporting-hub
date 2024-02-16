<?php

namespace App\Jobs;

use App\Http\Integrations\IdeoDash\IdeoDashConnector;
use App\Http\Integrations\IdeoDash\Requests\IdeoDashCallsList;
use App\Models\Call;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateCallJob implements ShouldQueue
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
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start_datetime = date('Y-m-d H:i:s');
        Log::info("[$start_datetime]: UpdateCallJob for tenant {$this->tenantId} has started.");

        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
        $ideoDashConnector = new IdeoDashConnector();
        $clientResponse = $ideoDashConnector->send(new IdeoDashCallsList($tenant->docebo_org_id));
        $result = $clientResponse->dto();
        $result = array_chunk(array_filter($result), 1000);
        $upsertFunction = function ($chunk) {
            DB::transaction(function () use ($chunk) {
                Call::upsert(
                    $chunk,
                    [
                        'learner_docebo_id',
                        'date_call',
                    ],
                    [
                        'type',
                        'status',
                        'subject',
                        'group_id',
                        'project_id',
                    ]
                );
            });
        };
        // Use array_map to apply the upsert function to each chunk
        array_map($upsertFunction, $result);
        tenancy()->end();

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("[$end_datetime]: UpdateCallJob for tenant {$this->tenantId} has finished.");
    }
}
