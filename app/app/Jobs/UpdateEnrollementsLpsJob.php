<?php

namespace App\Jobs;

use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboLpsEnrollements;
use App\Models\Lp;
use App\Models\Tenant;
use App\Services\LpEnrollmentsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateEnrollementsLpsJob implements ShouldQueue
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
        $LpEnrollmentsService = new LpEnrollmentsService();

        //Define Enrollments Fields
        $fields = config('tenantconfigfields.enrollmentfields');
        $enrollFields = $LpEnrollmentsService->getEnrollmentsFields($fields);

        // GET Enrollements List DATA
        $lpsDoceboIds = Lp::pluck('docebo_id')->toArray();
        $request = new DoceboLpsEnrollements($lpsDoceboIds);
        $lpenrollsResponses = $doceboConnector->paginate($request);
        foreach($lpenrollsResponses as $md){
            $results = $md->dto();
            if(!empty($results)){
                $LpEnrollmentsService->batchInsert($results, $enrollFields);
            }
        }
        tenancy()->end();
    }
}
