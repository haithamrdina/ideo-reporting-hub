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
use Illuminate\Support\Facades\Log;

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
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start_datetime = date('Y-m-d H:i:s');
        Log::info("['start'][$start_datetime]: UpdateEnrollementsLpsJob for tenant {$this->tenantId} has started.");

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
            foreach($lpsDoceboIds as $lpDoceboId){
                $request = new DoceboLpsEnrollements($lpDoceboId);
                $lpenrollsResponses = $doceboConnector->paginate($request);
                $results = [];
                foreach($lpenrollsResponses as $lp){
                    $results = array_merge($results, $lp->dto());
                }
                if(!empty($results)){
                    $LpEnrollmentsService->batchInsert(array_filter($results), $enrollFields);
                }
            }
        tenancy()->end();

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: UpdateEnrollementsLpsJob for tenant {$this->tenantId} has finished.");
    }
}
