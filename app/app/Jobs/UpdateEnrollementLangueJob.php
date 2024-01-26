<?php

namespace App\Jobs;

use App\Enums\CourseStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrollements;
use App\Http\Integrations\Speex\Requests\SpeexUserArticleResult;
use App\Http\Integrations\Speex\SpeexConnector;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Module;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
            $doceboConnector = new DoceboConnector();
            $speexConnector = new SpeexConnector();

            $modulesDoceboIds = Module::where(['category'=> 'SPEEX', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();


            $learners = Learner::whereNotNull('speex_id')->get();
            foreach( $learners as $learner){
                $request = new DoceboCoursesEnrollements($modulesDoceboIds, $learner->docebo_id);

                $mdenrollsResponses = $doceboConnector->paginate($request);
                $resultMds = [];
                foreach($mdenrollsResponses as $md){
                    $data = $md->dto();
                    $resultMds = array_merge($resultMds, $data);
                }
                if(!empty($resultMds)){
                    $result = array_map(function ($item) use($speexConnector, $learner){
                        $module = Module::where('docebo_id', $item['module_docebo_id'])->first();
                        if($item['status'] != 'enrolled' || $item['status'] != 'waiting')
                        {
                            $articleId = $module->article_id;
                            $speexId = $learner->speex_id;

                            $speexResponse = $speexConnector->send(new SpeexUserArticleResult($speexId, $articleId));
                            $speexReponseData = $speexResponse->dto();
                            $item['cmi_time'] = $speexReponseData['time'];
                            $item['niveau'] = $speexReponseData['niveau'];
                        }else{
                            $item['cmi_time'] = 0;
                            $item['niveau'] = null;

                        }

                        $item['language'] = $module->language;
                        $item['group_id'] = $learner->group->id;
                        $item['project_id'] = $learner->project->id;
                        return $item;

                    }, $resultMds);
                    DB::transaction(function () use ($result) {
                        Langenroll::upsert(
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
                                'niveau',
                                'language',
                                'session_time',
                                'cmi_time',
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
