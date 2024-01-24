<?php

namespace App\Jobs;

use App\Enums\CourseStatusEnum;
use App\Enums\GroupStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCourseList;
use App\Http\Integrations\Docebo\Requests\DoceboCourseLosList;
use App\Models\Group;
use App\Models\Module;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateModuleJob implements ShouldQueue
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
        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
            // Get COURSES from DOCEBO API
            $doceboConnector = new DoceboConnector();
            $paginator = $doceboConnector->paginate(new DoceboCourseList($tenant->company_code));
            $result = [];

            foreach($paginator as $pg){
                $data = $pg->dto();
                $result = array_merge($result, $data);
            }

            // MAP COURSES to set los details
            $result = array_map(function ($item) use($doceboConnector){
                if($item['category'] == "CEGOS" || $item['category'] == "ENI"){
                    $losResponse = $doceboConnector->send(new DoceboCourseLosList($item['docebo_id']));
                    $item['los'] = json_encode($losResponse->dto());
                }else{
                    $item['los'] = null;
                }
                return  $item;
            }, $result);

            // BATCH INSERT AND UPDATE DATA INTO DATABASE
            DB::transaction(function () use ($result) {
                Module::upsert(
                    $result,
                    ['docebo_id'],
                    [
                        'code',
                        'name',
                        'language',
                        'recommended_time',
                        'category',
                        'niveau',
                        'article_id',
                        'status',
                        'los',
                    ]
                );
            });

            // RETRIEVE ACTIVE COURSES ID FROM  DATABASE
            $modulesIds = Module::where('status', CourseStatusEnum::ACTIVE)->pluck('id')->toArray();

            // RETRIEVE ACTIVE GROUPS  FROM  DATABASE
            $groups = Group::where('status' , GroupStatusEnum::ACTIVE)->get();
            foreach($groups as $group){
                // ASSIGN COURSES TO GROUP AND PROJECT
                $group->modules()->sync($modulesIds);
                $group->projects()->first()->modules()->sync($modulesIds);
            }
        tenancy()->end();
    }
}



