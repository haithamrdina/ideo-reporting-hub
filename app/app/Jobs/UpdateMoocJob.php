<?php

namespace App\Jobs;

use App\Enums\GroupStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboMoocsList;
use App\Models\Group;
use App\Models\Mooc;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateMoocJob implements ShouldQueue
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
        Log::info("['start'][$start_datetime]: UpdateMoocJob for tenant {$this->tenantId} has started.");

        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
            // Get COURSES from DOCEBO API
            $doceboConnector = new DoceboConnector();
            $doceboConnector = new DoceboConnector();
            $searchList = [$tenant->company_code.'-MOOC' , 'MOOC-'.$tenant->company_code, 'PUBLIC-CT-MOOC'];
            $result = [];
            foreach($searchList as $search){
                $moocResponse = $doceboConnector->paginate(new DoceboMoocsList($search));
                foreach($moocResponse as $mc){
                    $data = $mc->dto();
                    $result = array_merge($result, $data);
                }
            }

            // BATCH INSERT AND UPDATE DATA INTO DATABASE
            DB::transaction(function () use ($result) {
                Mooc::upsert(
                    $result,
                    ['docebo_id'],
                    [
                        'code',
                        'name',
                        'recommended_time'
                    ]
                );
            });

            // RETRIEVE ACTIVE COURSES ID FROM  DATABASE
            $moocsIds = Mooc::pluck('id')->toArray();

            // RETRIEVE ACTIVE GROUPS  FROM  DATABASE
            $groups = Group::where('status' , GroupStatusEnum::ACTIVE)->get();
            foreach($groups as $group){
                // ASSIGN COURSES TO GROUP AND PROJECT
                $group->moocs()->sync($moocsIds);
                $group->projects()->first()->moocs()->sync($moocsIds);
            }
        tenancy()->end();

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: UpdateMoocJob for tenant {$this->tenantId} has finished.");

    }
}
