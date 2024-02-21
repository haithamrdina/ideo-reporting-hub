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
use Illuminate\Support\Facades\Log;

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
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start_datetime = date('Y-m-d H:i:s');
        Log::info("['start'][$start_datetime]: UpdateEnrollementLangueJob for tenant {$this->tenantId} has started.");

        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
            $doceboConnector = new DoceboConnector();
            $speexEnrollmentsService = new SpeexEnrollmentsService();

            //Define Enrollments Fields
            $fields = config('tenantconfigfields.enrollmentfields');
            $enrollFields = $speexEnrollmentsService->getEnrollmentsFields($fields);
            $learners = Learner::whereNotNull('speex_id')->get();
            foreach( $learners as $learner){
                // GET LEARNER Enrollements
                $request = new DoceboSpeexEnrollements($learner->docebo_id);
                $mdenrollsResponses = $doceboConnector->paginate($request);
                $results = [];
                foreach($mdenrollsResponses as $md){
                    $results = array_merge($results, $md->dto());
                }
                // BATCH INSERT LEARNER DATA
                if(!empty($results)){
                    $speexEnrollmentsService->batchInsert(array_filter($results), $enrollFields);
                }
            }
        tenancy()->end();

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: UpdateEnrollementLangueJob for tenant {$this->tenantId} has finished.");
    }
}
