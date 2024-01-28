<?php

namespace App\Jobs;

use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboMoocsEnrollements;
use App\Models\Enrollmooc;
use App\Models\Learner;
use App\Models\Mooc;
use App\Models\Tenant;
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
            $doceboConnector = new DoceboConnector();
            $moocsDoceboIds = Mooc::pluck('docebo_id')->toArray();
            $moocsDoceboIds = array_chunk($moocsDoceboIds , 100);
            $result = [];
            foreach($moocsDoceboIds as $moocsDoceboId){
                $request = new DoceboMoocsEnrollements($moocsDoceboId);
                $mdenrollsResponses = $doceboConnector->paginate($request);
                foreach($mdenrollsResponses as $md){
                    $data = $md->dto();
                    $result = array_merge($result, $data);
                }

            }
            $result = array_map(function ($item){
                $learner = Learner::where('docebo_id' , $item['learner_docebo_id'])->first();
                $mooc = Mooc::where('docebo_id' , $item['mooc_docebo_id'])->first();
                if($learner){
                    if($item['status'] != 'enrolled' || $item['status'] != 'waiting' )
                    {
                        if($item['status'] == 'completed'){
                            $calculated_time = $mooc->recommended_time;
                        }elseif($item['status'] == 'in_progress' && $item['session_time'] > $mooc->recommended_time){
                            $calculated_time = $mooc->recommended_time;
                        }else{
                            $calculated_time = $item['session_time'];
                        }
                        $item['calculated_time'] = $calculated_time;
                        $item['recommended_time'] = $mooc->recommended_time;

                    }else{
                        $item['calculated_time'] = 0;
                        $item['recommended_time'] = 0;
                    }

                    $item['group_id'] = $learner->group->id;
                    $item['project_id'] = $learner->project->id;
                    return $item;
                }
            }, $result);

            $result = array_chunk(array_filter($result), 1000);
            $upsertFunction = function ($chunk) {
                DB::transaction(function () use ($chunk) {
                    Enrollmooc::upsert(
                        $chunk,
                        [
                            'learner_docebo_id',
                            'mooc_docebo_id',
                        ],
                        [
                            'status',
                            'enrollment_created_at',
                            'enrollment_updated_at',
                            'enrollment_completed_at',
                            'session_time',
                            'calculated_time',
                            'recommended_time',
                            'group_id',
                            'project_id',
                        ]
                    );
                });
            };
            array_map($upsertFunction, $result);
        tenancy()->end();
    }
}
