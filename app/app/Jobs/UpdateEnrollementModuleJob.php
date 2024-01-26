<?php

namespace App\Jobs;

use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboGetLoCmiData;
use App\Models\Enrollmodule;
use App\Models\Learner;
use App\Models\Module;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
            $doceboConnector = new DoceboConnector();

            $modulesDoceboIds = Module::whereIn('category', ['CEGOS','ENI', 'SM'])->pluck('docebo_id')->toArray();

            $learners = Learner::all();
            foreach( $learners as $learner){
                $request = new DoceboCoursesEnrollements($modulesDoceboIds, $learner->docebo_id);

                $mdenrollsResponses = $doceboConnector->paginate($request);
                $resultMds = [];
                foreach($mdenrollsResponses as $md){
                    $data = $md->dto();
                    $resultMds = array_merge($resultMds, $data);
                }
                if(!empty($resultMds)){
                    $result = array_map(function ($item) use($doceboConnector, $learner){
                        if($item['status'] != 'enrolled')
                        {
                            $module = Module::where('docebo_id' , $item['module_docebo_id'])->first();
                            $cmiTime = 0;
                            foreach($module->los as $lo){
                                $cmiRequest = new DoceboGetLoCmiData($lo,$item['module_docebo_id'],$item['learner_docebo_id']);
                                $cmiResponse = $doceboConnector->send($cmiRequest);
                                if($cmiResponse->status() === 200){
                                    $cmiTime += getCmiTime($cmiResponse->body());
                                }else{
                                    $cmiTime += 0;
                                }
                            }
                            $item['cmi_time'] = $cmiTime;
                            $item['calculated_time'] = $item['status'] == 'completed' ? $module->recommended_time : $cmiTime;
                            $item['recommended_time'] = $module->recommended_time;

                        }else{
                            $item['cmi_time'] = 0;
                            $item['calculated_time'] = 0;
                            $item['recommended_time'] = 0;
                        }

                        $item['group_id'] = $learner->group->id;
                        $item['project_id'] = $learner->project->id;
                        return $item;

                    }, $resultMds);
                    DB::transaction(function () use ($result) {
                        Enrollmodule::upsert(
                            $result,
                            [
                                'learner_docebo_id',
                                'module_docebo_id',
                            ],
                            [
                                'status',
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
                }
            }
        tenancy()->end();
    }
}
