<?php

namespace App\Jobs;

use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrollements;
use App\Models\Learner;
use App\Models\Module;
use App\Models\Tenant;
use App\Services\ModuleEnrollmentsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateEnrollementModuleJob implements ShouldQueue
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
        Log::info("['start'][$start_datetime]: UpdateEnrollementModuleJob for tenant {$this->tenantId} has started.");

        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
             // Initialize all neccessary Service
            $doceboConnector = new DoceboConnector();
            $moduleEnrollmentsService = new ModuleEnrollmentsService();

            //Define Enrollments Fields
            $fields = config('tenantconfigfields.enrollmentfields');
            $enrollFields = $moduleEnrollmentsService->getEnrollmentsFields($fields);

            $modulesDoceboIds = Module::whereIn('category', ['CEGOS','ENI', 'SM'])->pluck('docebo_id')->toArray();
            $modulesDoceboIds = array_chunk($modulesDoceboIds , 100);
            $learners = Learner::all();
            foreach( $learners as $learner){

                // GET LEARNER Enrollements
                foreach($modulesDoceboIds as $moduleDoceboIds){
                    $request = new DoceboCoursesEnrollements($moduleDoceboIds, $learner->docebo_id);
                    $mdenrollsResponses = $doceboConnector->paginate($request);
                    $results = [];
                    foreach($mdenrollsResponses as $md){
                        $data = $md->dto();
                        $results = array_merge($results, $data);
                    }
                    // BATCH INSERT LEARNER DATA
                    if(!empty($results)){
                        if(count($results) > 1000)
                        {
                            $batchData = array_chunk(array_filter($results), 1000);
                            foreach($batchData as $data){
                                $moduleEnrollmentsService->batchInsert($data, $enrollFields);
                            }
                        }else{
                            $moduleEnrollmentsService->batchInsert($results, $enrollFields);
                        }
                    }
                }
            }
        tenancy()->end();

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: UpdateEnrollementMoocJob for tenant {$this->tenantId} has finished.");
    }
}
