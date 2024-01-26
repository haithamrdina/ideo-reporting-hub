<?php

namespace App\Jobs;

use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboLpsEnrollements;
use App\Models\Enrollmodule;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Lpenroll;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
            $doceboConnector = new DoceboConnector();
            $lpsDoceboIds = Lp::pluck('docebo_id')->toArray();
            $request = new DoceboLpsEnrollements($lpsDoceboIds);
            $lpenrollsResponses = $doceboConnector->paginate($request);
            $resultLps = [];
            foreach($lpenrollsResponses as $md){
                $data = $md->dto();
                $resultLps = array_merge($resultLps, $data);
            }
            if(!empty($resultLps)){
                $result = array_map(function ($item){
                    $modulesIds = Lp::where('docebo_id' , $item['lp_docebo_id'])->first()->courses;
                    $learner = Learner::where('docebo_id',$item['learner_docebo_id'])->first();
                    if($learner){
                        if($item['status'] != 'not_started')
                        {
                            $sumData = Enrollmodule::where('learner_docebo_id', $learner->docebo_id)
                                    ->whereIn('module_docebo_id', $modulesIds)
                                    ->selectRaw('SUM(session_time) as total_session_time')
                                    ->selectRaw('SUM(cmi_time) as total_cmi_time')
                                    ->selectRaw('SUM(calculated_time) as total_calculated_time')
                                    ->selectRaw('SUM(recommended_time) as total_recommended_time')
                                    ->first();

                            $item['session_time'] = intval($sumData->total_session_time);
                            $item['cmi_time'] = intval($sumData->total_cmi_time);
                            $item['calculated_time'] = intval($sumData->total_calculated_time);
                            $item['recommended_time'] = intval($sumData->total_recommended_time);

                        }else{
                            $item['session_time'] = 0;
                            $item['cmi_time'] = 0;
                            $item['calculated_time'] = 0;
                            $item['recommended_time'] = 0;
                        }

                        $item['group_id'] = $learner->group->id;
                        $item['project_id'] = $learner->project->id;
                        return $item;
                    }
                }, $resultLps);
                // Remove null values
                $result = array_chunk(array_filter($result), 1000);
                $upsertFunction = function ($chunk) {
                    DB::transaction(function () use ($chunk) {
                        Lpenroll::upsert(
                            $chunk,
                            [
                                'learner_docebo_id',
                                'lp_docebo_id',
                            ],
                            [
                                'status',
                                'enrollment_completion_percentage',
                                'enrollment_created_at',
                                'enrollment_updated_at',
                                'enrollment_completed_at',
                                'session_time',
                                'cmi_time',
                                'calculated_time',
                                'recommended_time',
                                'group_id',
                                'project_id',
                            ]
                        );
                    });
                };
                // Use array_map to apply the upsert function to each chunk
                array_map($upsertFunction, $result);
            }
        tenancy()->end();
    }
}
