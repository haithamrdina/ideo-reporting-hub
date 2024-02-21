<?php

namespace App\Jobs;

use App\Enums\GroupStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboLpList;
use App\Models\Group;
use App\Models\Lp;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateLpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        Log::info("['start'][$start_datetime]: UpdateLpJob for tenant {$this->tenantId} has started.");

        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
            // Get LPS from DOCEBO API
            $doceboConnector = new DoceboConnector();
            $paginator = $doceboConnector->paginate(new DoceboLpList($tenant->company_code));
            $result = [];

            foreach($paginator as $pg){
                $data = $pg->dto();
                $result = array_merge($result, $data);
            }

            // BATCH INSERT AND UPDATE DATA INTO DATABASE
            DB::transaction(function () use ($result) {
                Lp::upsert(
                    $result,
                    ['docebo_id'],
                    [
                        'code' ,
                        'name' ,
                        'courses',
                    ]
                );
            });

            // RETRIEVE LPS ID FROM  DATABASE
            $lpsIds = Lp::pluck('id')->toArray();

            // RETRIEVE ACTIVE GROUPS  FROM  DATABASE
            $groups = Group::where('status' , GroupStatusEnum::ACTIVE)->get();
            foreach($groups as $group){
                // ASSIGN LPS TO GROUP AND PROJECT
                $group->lps()->sync($lpsIds);
                $group->projects()->first()->lps()->sync($lpsIds);
            }
        tenancy()->end();

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: UpdateLpJob for tenant {$this->tenantId} has finished.");
    }
}


