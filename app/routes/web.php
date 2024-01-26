<?php

use App\Enums\CourseStatusEnum;
use App\Http\Controllers\Central\GroupController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\ProjectController;
use App\Http\Controllers\Central\TenantController;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrolls;
use App\Http\Integrations\Docebo\Requests\DoceboGetLoCmiData;
use App\Http\Integrations\Docebo\Requests\DoceboLpsEnrollements;
use App\Http\Integrations\Speex\Requests\SpeexUserArticleResult;
use App\Http\Integrations\Speex\SpeexConnector;
use App\Http\Integrations\Zendesk\Requests\ZendeskOrganizations;
use App\Http\Integrations\Zendesk\Requests\ZendeskOrganizationsTickets;
use App\Http\Integrations\Zendesk\Requests\ZendeskRequesterUsername;
use App\Http\Integrations\Zendesk\ZendeskConnector;
use App\Models\Enrollmodule;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Lpenroll;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/test', function () {
    $tenant = Tenant::find('a69773f7-43b4-46ab-8081-618b072a50d3');
    tenancy()->initialize($tenant);
    $doceboConnector = new DoceboConnector();
    $lpsDoceboIds = Lp::pluck('docebo_id')->toArray();
    $request = new DoceboLpsEnrollements($lpsDoceboIds);
    $lpenrollsResponses = $doceboConnector->paginate($request);
    $resultMds = [];
    foreach($lpenrollsResponses as $md){
        $data = $md->dto();
        $resultMds = array_merge($resultMds, $data);
    }
    if(!empty($resultMds)){
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
        }, $resultMds);
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
    return view('welcome');
});
Route::get('/', function () {
    $tenant = Tenant::find('a69773f7-43b4-46ab-8081-618b072a50d3');
    tenancy()->initialize($tenant);
        $doceboConnector = new DoceboConnector();

        $lpsDoceboIds = Lp::pluck('docebo_id')->toArray();
        $learners = Learner::all();
        foreach( $learners as $learner){
            $request = new DoceboLpsEnrollements($lpsDoceboIds, $learner->docebo_id);
            $lpenrollsResponses = $doceboConnector->paginate($request);
            $resultMds = [];
            foreach($lpenrollsResponses as $md){
                $data = $md->dto();
                $resultMds = array_merge($resultMds, $data);
            }
            if(!empty($resultMds)){
                $result = array_map(function ($item) use($learner){
                    if($item['status'] != 'not_started')
                    {
                        $modulesIds = Lp::where('docebo_id' , $item['lp_docebo_id'])->first()->courses;

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

                }, $resultMds);
                DB::transaction(function () use ($result) {
                    Lpenroll::upsert(
                        $result,
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
            }
        }
    tenancy()->end();
    return view('welcome');
});
Route::get('/langue', function () {
    $tenant = Tenant::find('a69773f7-43b4-46ab-8081-618b072a50d3');
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
    return view('welcome');
});
Route::get('/zendesk', function () {
    $tenant = Tenant::find('a69773f7-43b4-46ab-8081-618b072a50d3');
    tenancy()->initialize($tenant);
    $zendeskConnector = new ZendeskConnector();
    $request = new ZendeskOrganizationsTickets('5744460951186');
    $orgResponse = $zendeskConnector->paginate($request);

    $result = [];
    foreach($orgResponse as $md){
        $data  = $md->dto();
        $result = array_merge($result, $data);
    }

    $result = array_map(function ($item) use($zendeskConnector){
        $requesterResponse = $zendeskConnector->send(new ZendeskRequesterUsername($item['requester_id']));
        $learner = Learner::where('username', $requesterResponse->dto())->first();
        if($learner){
            $item['group_id'] = $learner->group->id;
            $item['project_id'] = $learner->project->id;
            $item['learner_docebo_id'] = $learner->docebo_id;
            unset($item['requester_id']);
        }
        return $item;
    }, $result);

    $result = array_chunk(array_filter($result), 500);
    $upsertFunction = function ($chunk) {
        DB::transaction(function () use ($chunk) {
            Ticket::upsert(
                $chunk,
                [
                    'learner_docebo_id',
                    'subject',
                    'ticket_created_at'
                ],
                [
                    'status',
                    'ticket_updated_at',
                    'group_id',
                    'project_id',
                ]
            );
        });
    };

    // Use array_map to apply the upsert function to each chunk
    array_map($upsertFunction, $result);
    tenancy()->end();
    return view('welcome');
});


Route::name('admin.')->group(function () {
    require __DIR__.'/central-auth.php';

    Route::middleware('admin.auth:admin')->group(function () {
        // Routes for authenticated admins
        // Dashboard
        Route::get('/home', [HomeController::class , 'index'])->name('home');
        // Tenants
        Route::resource('tenants', TenantController::class);

        Route::get('tenants/{tenant}/projects',[ TenantController::class, 'getProjects'])->name('tenants.projects');
        Route::get('tenants/{tenant}/projects/create', [ProjectController::class, 'create'])->name('tenants.projects.create');
        Route::post('tenants/{tenant}/projects/create', [ProjectController::class, 'store'])->name('tenants.projects.store');
        Route::get('tenants/{tenant}/projects/{project}/edit', [ProjectController::class, 'edit'])->name('tenants.projects.edit');
        Route::put('tenants/{tenant}/projects/{project}/edit', [ProjectController::class, 'update'])->name('tenants.projects.update');
        Route::delete('tenants/{tenant}/projects/{project}/delete', [ProjectController::class, 'destroy'])->name('tenants.projects.destroy');

        Route::get('tenants/{tenant}/groups',[ TenantController::class, 'getGroups'])->name('tenants.groups');
        Route::get('tenants/{tenant}/maj/groups',[ TenantController::class, 'majGroups'])->name('tenants.groups.maj');
        Route::get('tenants/{tenant}/groups/{group}',[ GroupController::class, 'edit'])->name('tenants.groups.edit');

        Route::get('tenants/{tenant}/learners',[ TenantController::class, 'majLearners'])->name('tenants.learners');
        Route::get('tenants/{tenant}/maj/learners',[ TenantController::class, 'majLearners'])->name('tenants.learners.maj');
        Route::get('tenants/{tenant}/maj/lps',[ TenantController::class, 'majLps'])->name('tenants.lps.maj');
        Route::get('tenants/{tenant}/maj/modules',[ TenantController::class, 'majModules'])->name('tenants.modules.maj');
        Route::get('tenants/{tenant}/maj/moocs',[ TenantController::class, 'majMoocs'])->name('tenants.moocs.maj');

        Route::get('tenants/{tenant}/enrollements/modules/maj',[ TenantController::class, 'majEnrollsModules'])->name('tenants.modules.enroll.maj');
        Route::get('tenants/{tenant}/enrollements/langue/maj',[ TenantController::class, 'majEnrollsLangues'])->name('tenants.langues.enroll.maj');
        Route::get('tenants/{tenant}/enrollements/moocs/maj',[ TenantController::class, 'majEnrollsMoocs'])->name('tenants.moocs.enroll.maj');
        Route::get('tenants/{tenant}/enrollements/lps/maj',[ TenantController::class, 'majEnrollsLps'])->name('tenants.lps.enroll.maj');

    });
});

function getCmiTime($responseBody){
    $totalTime = 0;
    $totalTimeRegex = '/<td>cmi\.core\.total_time<\/td><td>(.*?)<\/td>/';
    $sessionTimeRegex = '/<td>cmi\.core\.session_time<\/td><td>(.*?)<\/td>/';
    if (preg_match($totalTimeRegex, $responseBody, $totalTimeMatches)) {
        $totalTimeValue = $totalTimeMatches[1];
        if($totalTimeValue ==  '0000:00:00.00'){
            if(preg_match($sessionTimeRegex, $responseBody, $sessionTimeMatches)) {
                $sessionTimeValue = $sessionTimeMatches[1];
                $totalTime += convertTimeToSeconds($sessionTimeValue);
            }
        }else{
            $totalTime += convertTimeToSeconds($totalTimeValue);
        }
    }
    return $totalTime;
}

function convertTimeToSeconds($time)
{
    $timeArray = explode(':', $time);
    if (count($timeArray) !== 3) {
        return 0; // Invalid time format, return 0 seconds
    }

    $hours = intval($timeArray[0]);
    $minutes = intval($timeArray[1]);
    $seconds = intval($timeArray[2]);

    return $hours * 3600 + $minutes * 60 + $seconds;
}






