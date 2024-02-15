<?php

namespace App\Jobs;

use App\Enums\CourseStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboSpeexEnrollements;
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

            $modulesDoceboIds = Module::where(['category'=> 'SPEEX', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $learners = Learner::whereNotNull('speex_id')->get();

            foreach( $learners as $learner){
                // GET LEARNER Enrollements
                $request = new DoceboSpeexEnrollements($modulesDoceboIds, $learner->docebo_id);
                $mdenrollsResponses = $doceboConnector->paginate($request);
                $results = [];
                foreach($mdenrollsResponses as $md){
                    $data = $md->dto();
                    $results = array_merge($results, $data);
                }
                // BATCH INSERT LEARNER DATA
                if(!empty($results)){
                    $speexEnrollmentsService->batchInsert($results, $enrollFields);
                }
            }
        tenancy()->end();
    }
}
