<?php

use App\Enums\CourseStatusEnum;
use App\Http\Controllers\Central\GroupController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\ProjectController;
use App\Http\Controllers\Central\TenantController;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCourseLosList;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboGroupeList;
use App\Http\Integrations\Docebo\Requests\DoceboLpsEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboMoocsEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboSpeexEnrollements;
use App\Models\Group;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Module;
use App\Models\Mooc;
use App\Models\Tenant;
use App\Services\InitTenantService;
use App\Services\LpEnrollmentsService;
use App\Services\ModuleEnrollmentsService;
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
Route::get('/test', function(){
   $tenant = Tenant::find('523badfa-3d62-475c-bbe8-4fdf2bcaed1b');

    tenancy()->initialize($tenant);
    $doceboConnector = new DoceboConnector();
    $moduleEnrollmentsService = new ModuleEnrollmentsService();

    //Define Enrollments Fields
    $fields = config('tenantconfigfields.enrollmentfields');
    $enrollFields = $moduleEnrollmentsService->getEnrollmentsFields($fields);

    $modulesDoceboIds = Module::whereIn('category', ['CEGOS','ENI', 'SM'])->pluck('docebo_id')->toArray();
    $learners = Learner::all();
    $i=0;
    foreach( $learners as $learner){
        $i++;
        // GET LEARNER Enrollements
        $request = new DoceboCoursesEnrollements($modulesDoceboIds, $learner->docebo_id);
        $mdenrollsResponses = $doceboConnector->paginate($request);
        $results = [];
        foreach($mdenrollsResponses as $md){
            $data = $md->dto();
            $results = array_merge($results, $data);
        }
        if($i==3){
            dd($results);
        }
        // BATCH INSERT LEARNER DATA
       /* if(!empty($results)){
            if(count($results) > 1000)
            {
                $batchData = array_chunk(array_filter($results), 1000);
                foreach($batchData as $data){
                    $moduleEnrollmentsService->batchInsert($data, $enrollFields);
                }
            }else{
                $moduleEnrollmentsService->batchInsert($results, $enrollFields);
            }
        }*/
    }
    tenancy()->end();
});

Route::get('/speex', function(){
    $tenant = Tenant::find('7473019a-0a48-4db7-ac7d-7e84a9aef424');
    tenancy()->initialize($tenant);

    $doceboConnector = new DoceboConnector();
    $speexEnrollmentsService = new SpeexEnrollmentsService();

    //Define Enrollments Fields
    $fields = config('tenantconfigfields.enrollmentfields');
    $enrollFields = $speexEnrollmentsService->getEnrollmentsFields($fields);

    $modulesDoceboIds = Module::where(['category'=> 'SPEEX', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
    $learners = Learner::whereNotNull('speex_id')->get();

    foreach( $learners as $learner){
        // GET LEARNER Enrollements
        $request = new DoceboSpeexEnrollements($modulesDoceboIds, $learner->docebo_id);
        $mdenrollsResponses = $doceboConnector->paginate($request);
        $results = [];
        foreach($mdenrollsResponses as $md){
            $data = $md->dto();
            $results = array_merge($results, $data);
        }
        // BATCH INSERT LEARNER DATA
        if(!empty($results)){
            $speexEnrollmentsService->batchInsert($results, $enrollFields);
        }
    }
    tenancy()->end();
});

Route::get('/mooc', function(){

    $tenant = Tenant::find('85caeca1-a182-424b-a776-7cf5c1e2a5af');
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
    foreach($moocsDoceboIds as $moocsDoceboId){
        $request = new DoceboMoocsEnrollements($moocsDoceboId);
        $mdenrollsResponses = $doceboConnector->paginate($request);
        $results = [];
        foreach($mdenrollsResponses as $md){
            $data = $md->dto();
            $results = array_merge($results, $data);
        }
        if(!empty($results)){
            if(count($results) > 1000)
            {
                $batchData = array_chunk(array_filter($results), 1000);
                foreach($batchData as $data){
                    $moocEnrollmentsService->batchInsert($data, $enrollFields);
                }
            }else{
                $moocEnrollmentsService->batchInsert($results, $enrollFields);
            }
        }
    }
    tenancy()->end();


});

Route::get('/lp', function(){
    $tenant = Tenant::find('85caeca1-a182-424b-a776-7cf5c1e2a5af');
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
});

Route::get('groups', function(){
    $tenant = Tenant::find('fbca1f6b-83cd-44cd-92ec-1c3528f7b928');
    tenancy()->initialize($tenant);
        $archive = $tenant->archive;
        if($archive == true){
            $initTenantService = new InitTenantService();
            $initTenantService->syncArchives($tenant);
        }

        $doceboConnector = new DoceboConnector;
        $paginator = $doceboConnector->paginate(new DoceboGroupeList($tenant->docebo_org_id));
        $result = [];
        foreach($paginator as $pg){
            $data = $pg->dto();
            $result = array_merge($result, $data);
        }
        DB::transaction(function () use ($result) {
            Group::upsert(
                $result,
                ['docebo_id'],
                [
                    'code',
                    'name'
                ]
            );
        });
        tenancy()->end();
});

Route::name('admin.')->group(function () {
    require __DIR__.'/central-auth.php';

    Route::middleware('admin.auth:admin')->group(function () {
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

