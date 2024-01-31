<?php

use App\Enums\CourseStatusEnum;
use App\Enums\GroupStatusEnum;
use App\Http\Controllers\Central\GroupController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\ProjectController;
use App\Http\Controllers\Central\TenantController;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboLpsEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboMoocsEnrollements;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Lpenroll;
use App\Models\Module;
use App\Models\Mooc;
use App\Models\Tenant;
use App\Services\LpEnrollmentsService;
use App\Services\ModuleEnrollmentsService;
use App\Services\ModuleTimingFieldsService;
use App\Services\MoocEnrollmentsService;
use App\Services\SpeexEnrollmentsService;
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




Route::get('/lp', function () {
    $tenant = Tenant::find('523badfa-3d62-475c-bbe8-4fdf2bcaed1b');
    tenancy()->initialize($tenant);
        $doceboConnector = new DoceboConnector();
        $LpEnrollmentsService = new LpEnrollmentsService();

        $fields = config('tenantconfigfields.enrollmentfields');
        $enrollFields = $LpEnrollmentsService->getEnrollmentsFields($fields);

        $lpsDoceboIds = Lp::pluck('docebo_id')->toArray();

        $request = new DoceboLpsEnrollements($lpsDoceboIds);
        $lpenrollsResponses = $doceboConnector->paginate($request);
        $results = [];
        foreach($lpenrollsResponses as $md){
            $data = $md->dto();
            $results = array_merge($results, $data);
        }
        if(!empty($results)){
            $result = $LpEnrollmentsService->getEnrollmentsList($results, $fields);
            dd($result);
            /*if(count($result) > 1000)
            {
                $batchData = array_chunk(array_filter($result), 1000);
                foreach($batchData as $data){
                    $LpEnrollmentsService->batchInsert($data, $enrollFields);
                }
            }else{
                $LpEnrollmentsService->batchInsert($result, $enrollFields);
            }*/
        }
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






