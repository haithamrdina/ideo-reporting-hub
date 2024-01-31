<?php

namespace App\Jobs;

use App\Enums\CourseStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrollements;
use App\Models\Learner;
use App\Models\Module;
use App\Models\Tenant;
use App\Services\SpeexEnrollmentsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateEnrollementLangueJob implements ShouldQueue
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
            $speexEnrollmentsService = new SpeexEnrollmentsService();

            //Define Enrollments Fields
            $fields = config('tenantconfigfields.enrollmentfields');

            $enrollFields = $speexEnrollmentsService->getEnrollmentsFields($fields);

            // get Speex Learners and Speex Active Modules
            $modulesDoceboIds = Module::where(['category'=> 'SPEEX', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $learners = Learner::whereNotNull('speex_id')->get();

            // Get Learners Speex Enrollments
            $results = [];
            foreach( $learners as $learner){
                $request = new DoceboCoursesEnrollements($modulesDoceboIds, $learner->docebo_id);
                $mdenrollsResponses = $doceboConnector->paginate($request);
                $result = [];
                foreach($mdenrollsResponses as $md){
                    $data = $md->dto();
                    $result = array_merge($result, $data);
                }
                if(!empty($result)){
                    $data = $speexEnrollmentsService->getEnrollmentsList($result, $fields);
                    $results = array_merge($results, $data);
                }
            }

            // BATCH insert DATA
            if(count($results) > 1000)
            {
                $batchData = array_chunk(array_filter($results), 1000);
                foreach($batchData as $data){
                    $speexEnrollmentsService->batchInsert($data, $enrollFields);
                }
            }else{
                $speexEnrollmentsService->batchInsert($results, $enrollFields);
            }

        tenancy()->end();
    }
}
