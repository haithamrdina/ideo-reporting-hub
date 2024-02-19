<?php

namespace App\Jobs;

use App\Enums\GroupStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboGroupeList;
use App\Http\Integrations\Docebo\Requests\DoceboGroupeUsersList;
use App\Models\Group;
use App\Models\Tenant;
use App\Services\InitTenantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        Log::info("['start'][$start_datetime]: UpdateGroupJob for tenant {$this->tenantId} has started.");

        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
            $archive = $tenant->archive;
            if($archive == true){
                $initTenantService = new InitTenantService();
                $initTenantService->syncArchives($tenant);
            }

            $doceboConnector = new DoceboConnector;
            $paginator = $doceboConnector->paginate(new DoceboGroupeList($tenant->docebo_org_id));
            $result = [];
            foreach($paginator as $pg){
                $data = $pg->dto();
                $result = array_merge($result, $data);
            }
            DB::transaction(function () use ($result) {
                Group::upsert(
                    $result,
                    ['docebo_id'],
                    [
                        'code',
                        'name'
                    ]
                );
            });
        tenancy()->end();

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: UpdateGroupJob for tenant {$this->tenantId} has finished.");
    }
}
