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
use App\Http\Integrations\Docebo\Requests\DoceboMoocsEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboMoocsList;
use App\Http\Integrations\IdeoDash\IdeoDashConnector;
use App\Http\Integrations\IdeoDash\Requests\IdeoDashCallsList;
use App\Http\Integrations\IdeoDash\Requests\IdeoDashClientList;
use App\Http\Integrations\Speex\Requests\SpeexUserArticleResult;
use App\Http\Integrations\Speex\SpeexConnector;
use App\Http\Integrations\Zendesk\Requests\ZendeskOrganizations;
use App\Http\Integrations\Zendesk\Requests\ZendeskOrganizationsTickets;
use App\Http\Integrations\Zendesk\Requests\ZendeskRequesterUsername;
use App\Http\Integrations\Zendesk\ZendeskConnector;
use App\Models\Call;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Lpenroll;
use App\Models\Module;
use App\Models\Mooc;
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

Route::get('/', function () {
    $tenant = Tenant::find('e13bef2d-5e06-4f25-acdc-7d1f3f67b90d');
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

        Route::get('tenants/{tenant}/learners',[ TenantController::class, 'getLearners'])->name('tenants.learners');
        Route::get('tenants/{tenant}/maj/learners',[ TenantController::class, 'majLearners'])->name('tenants.learners.maj');

        Route::get('tenants/{tenant}/lps',[ TenantController::class, 'getLps'])->name('tenants.lps');
        Route::get('tenants/{tenant}/maj/lps',[ TenantController::class, 'majLps'])->name('tenants.lps.maj');

        Route::get('tenants/{tenant}/modules',[ TenantController::class, 'getModules'])->name('tenants.modules');
        Route::get('tenants/{tenant}/maj/modules',[ TenantController::class, 'majModules'])->name('tenants.modules.maj');

        Route::get('tenants/{tenant}/moocs',[ TenantController::class, 'getMoocs'])->name('tenants.moocs');
        Route::get('tenants/{tenant}/maj/moocs',[ TenantController::class, 'majMoocs'])->name('tenants.moocs.maj');

        Route::get('tenants/{tenant}/tickets',[ TenantController::class, 'getTickets'])->name('tenants.tickets');
        Route::get('tenants/{tenant}/maj/tickets',[ TenantController::class, 'majTickets'])->name('tenants.tickets.maj');

        Route::get('tenants/{tenant}/calls',[ TenantController::class, 'getCalls'])->name('tenants.calls');
        Route::get('tenants/{tenant}/maj/calls',[ TenantController::class, 'majCalls'])->name('tenants.calls.maj');

        Route::get('tenants/{tenant}/enrollements/modules/maj',[ TenantController::class, 'majEnrollsModules'])->name('tenants.modules.enroll.maj');
        Route::get('tenants/{tenant}/enrollements/softskills',[ TenantController::class, 'getEnrollsSoftskills'])->name('tenants.softskills.enroll');
        Route::get('tenants/{tenant}/enrollements/digitals',[ TenantController::class, 'getEnrollsDigitals'])->name('tenants.digitals.enroll');


        Route::get('tenants/{tenant}/enrollements/langues/maj',[ TenantController::class, 'majEnrollsLangues'])->name('tenants.langues.enroll.maj');
        Route::get('tenants/{tenant}/enrollements/langues',[ TenantController::class, 'getEnrollsLangues'])->name('tenants.langues.enroll');

        Route::get('tenants/{tenant}/enrollements/moocs',[ TenantController::class, 'getEnrollsMoocs'])->name('tenants.moocs.enroll');
        Route::get('tenants/{tenant}/enrollements/moocs/maj',[ TenantController::class, 'majEnrollsMoocs'])->name('tenants.moocs.enroll.maj');

        Route::get('tenants/{tenant}/enrollements/lps/maj',[ TenantController::class, 'majEnrollsLps'])->name('tenants.lps.enroll.maj');
        Route::get('tenants/{tenant}/enrollements/lps',[ TenantController::class, 'getEnrollsLps'])->name('tenants.lps.enroll');

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






