<?php

namespace App\Jobs;

use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboMoocsEnrollements;
use App\Models\Enrollmooc;
use App\Models\Learner;
use App\Models\Mooc;
use App\Models\Tenant;
use App\Services\MoocEnrollmentsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateEnrollementMoocJob implements ShouldQueue
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
            $moocEnrollmentsService = new MoocEnrollmentsService();

            //Define Enrollments Fields
            $fields = config('tenantconfigfields.enrollmentfields');
            $enrollFields = $moocEnrollmentsService->getEnrollmentsFields($fields);

            // GET Enrollements List DATA
            $moocsDoceboIds = Mooc::pluck('docebo_id')->toArray();
            $moocsDoceboIds = array_chunk($moocsDoceboIds , 100);
            $results = [];
            foreach($moocsDoceboIds as $moocsDoceboId){
                $request = new DoceboMoocsEnrollements($moocsDoceboId);
                $mdenrollsResponses = $doceboConnector->paginate($request);
                foreach($mdenrollsResponses as $md){
                    $data = $md->dto();
                    $results = array_merge($results, $data);
                }
            }

            // BATCH insert DATA
            if(!empty($results)){
                $result = $moocEnrollmentsService->getEnrollmentsList($results, $fields);
                if(count($result) > 1000)
                {
                    $batchData = array_chunk(array_filter($result), 1000);
                    foreach($batchData as $data){
                        $moocEnrollmentsService->batchInsert($data, $enrollFields);
                    }
                }else{
                    $moocEnrollmentsService->batchInsert($result, $enrollFields);
                }
            }

        tenancy()->end();
    }
}
