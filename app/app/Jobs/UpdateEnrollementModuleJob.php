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
        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);

            // Initialize all neccessary Service
            $doceboConnector = new DoceboConnector();
            $moduleEnrollmentsService = new ModuleEnrollmentsService();

            //Define Enrollments Fields
            $fields = config('tenantconfigfields.enrollmentfields');
            $enrollFields = $moduleEnrollmentsService->getEnrollmentsFields($fields);

            $modulesDoceboIds = Module::whereIn('category', ['CEGOS','ENI', 'SM'])->pluck('docebo_id')->toArray();
            $learners = Learner::all();
            foreach( $learners as $learner){

                // GET LEARNER Enrollements
                $request = new DoceboCoursesEnrollements($modulesDoceboIds, $learner->docebo_id);
                $mdenrollsResponses = $doceboConnector->paginate($request);
                $results = [];
                foreach($mdenrollsResponses as $md){
                    $data = $md->dto();
                    $results = array_merge($results, $data);
                }

                // BATCH INSERT LEARNER DATA
                if(!empty($results)){
                    $result = $moduleEnrollmentsService->getEnrollmentsList($results, $fields);
                    if(count($result) > 1000)
                    {
                        $batchData = array_chunk(array_filter($result), 1000);
                        foreach($batchData as $data){
                            $moduleEnrollmentsService->batchInsert($data, $enrollFields);
                        }
                    }else{
                        $moduleEnrollmentsService->batchInsert($result, $enrollFields);
                    }
                }
            }
        tenancy()->end();
    }
}
