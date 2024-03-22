<?php

use App\Enums\CourseStatusEnum;
use App\Enums\UserRoleEnum;
use App\Http\Controllers\Central\GroupController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\ProjectController;
use App\Http\Controllers\Central\TenantController;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCourseLosList;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboGetLoCmiData;
use App\Http\Integrations\Docebo\Requests\DoceboGroupeList;
use App\Http\Integrations\Docebo\Requests\DoceboLpsEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboMoocsEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboSpeexEnrollements;
use App\Http\Integrations\Speex\Requests\SpeexUserArticleResult;
use App\Http\Integrations\Speex\SpeexConnector;
use App\Jobs\UpdateCallJob;
use App\Jobs\UpdateEnrollementLangueJob;
use App\Jobs\UpdateEnrollementModuleJob;
use App\Jobs\UpdateEnrollementMoocJob;
use App\Jobs\UpdateEnrollementsLpsJob;
use App\Jobs\UpdateLearnerJob;
use App\Jobs\UpdateLpJob;
use App\Jobs\UpdateModuleJob;
use App\Jobs\UpdateMoocJob;
use App\Jobs\UpdateTicketJob;
use App\Models\Group;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Module;
use App\Models\Mooc;
use App\Models\Tenant;
use App\Models\User;
use App\Services\InitTenantService;
use App\Services\LpEnrollmentsService;
use App\Services\ModuleEnrollmentsService;
use App\Services\MoocEnrollmentsService;
use App\Services\SpeexEnrollmentsService;
use App\Services\UserFieldsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Saloon\Exceptions\Request\Statuses\InternalServerErrorException;
use OpenAI\Laravel\Facades\OpenAI;
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


/**
 *  start TEST FUNCTION
 */

Route::get('cdg-account' , function(){
    $tenant = Tenant::find('7473019a-0a48-4db7-ac7d-7e84a9aef424');
    tenancy()->initialize($tenant);

    User::insert(
        [
            'firstname' => 'CDG Développement',
            'lastname' => 'BRANCHE',
            'email' => 'rbranche@cdg-developpment.com',
            'password' => Hash::make('CDGDev@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '9',
        ],
        [
            'firstname' => 'CDG Invest',
            'lastname' => 'BRANCHE',
            'email' => 'rbranche@cdg-invest.com',
            'password' => Hash::make('CDGInvest@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '10',
        ],
        [
            'firstname' => 'Prévoyance',
            'lastname' => 'BRANCHE',
            'email' => 'rbranche@cdg-prevoyance.com',
            'password' => Hash::make('CDGPREV@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '13',
        ],
        [
            'firstname' => 'CDG corporate',
            'lastname' => 'BRANCHE',
            'email' => 'rbranche@cdg-corporate.com',
            'password' => Hash::make('CDGCORPO@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '21',
        ],
        [
            'firstname' => 'Ajar Invest',
            'lastname' => 'Filiale',
            'email' => 'rbranche@cdg-ajar-invest.com',
            'password' => Hash::make('AJARINVEST@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '26',
        ],
        [
            'firstname' => 'Auda',
            'lastname' => 'Filiale',
            'email' => 'rbranche@cdg-auda.com',
            'password' => Hash::make('AUDA@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '27',
        ],
        [
            'firstname' => 'CDG Capital',
            'lastname' => 'Filiale',
            'email' => 'rbranche@cdg-capital.com',
            'password' => Hash::make('CapMaisonMere@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '28',
        ],
        [
            'firstname' => 'CDG Capital Gestion',
            'lastname' => 'Filiale',
            'email' => 'rbranche@cdg-capital-gestion.com',
            'password' => Hash::make('CapGestion@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '29',
        ],
        [
            'firstname' => 'CDG Invest Growth',
            'lastname' => 'Filiale',
            'email' => 'rbranche@cdg-invest-growth.com',
            'password' => Hash::make('CapInvGrowth@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '30',
        ],
        [
            'firstname' => 'CDG Invest Infrastructures',
            'lastname' => 'Filiale',
            'email' => 'rbranche@cdg-invest-infra.com',
            'password' => Hash::make('CapInvInfra@2024'),
            'role' => UserRoleEnum::GROUP,
            'group_id' => '31',
        ],
        [
            "firstname" => "Cellulose du Maroc Eucaforest",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-cellulose.com",
            "password" => Hash::make("CelluloseME@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '32',
          ],
          [
            "firstname" => "CGI",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-cgi.com",
            "password" => Hash::make("CGI@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '33',
          ],
          [
            "firstname" => "Dyar AL Madina",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-dyar-madina.com",
            "password" => Hash::make("DyarElMadina@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '34',
          ],
          [
            "firstname" => "Finea",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-finea.com",
            "password" => Hash::make("Finea@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '36',
          ],
          [
            "firstname" => "Fonciere Chellah",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-fonciere-chellah.com",
            "password" => Hash::make("FonciereChellah@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '37',
          ],
          [
            "firstname" => "Loterie Nationnale",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-loterie-nationale.com",
            "password" => Hash::make("LoterieNationnale@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '40',
          ],
          [
            "firstname" => "MedZ",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-medz.com",
            "password" => Hash::make("MedZ@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '41',
          ],
          [
            "firstname" => "Novec",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-novec.com",
            "password" => Hash::make("Novec@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '42',
          ],
          [
            "firstname" => "SAPST",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-sapst.com",
            "password" => Hash::make("SAPST@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '43',
          ],
          [
            "firstname" => "SAZ",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-saz.com",
            "password" => Hash::make("SAZ@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '44',
          ],
          [
            "firstname" => "SCR",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-scr.com",
            "password" => Hash::make("SCR@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '45',
          ],
          [
            "firstname" => "SDS",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-sds.com",
            "password" => Hash::make("SDS@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '46',
          ],
          [
            "firstname" => "SHRA",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-shra.com",
            "password" => Hash::make("SHRA@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '47',
          ],
          [
            "firstname" => "XPERIS",
            "lastname" => "Filiale",
            "email" => "rbranche@cdg-xperis.com",
            "password" => Hash::make("XPERIS@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '48',
          ],
          [
            "firstname" => "MADAEF",
            "lastname" => "CDG",
            "email" => "rbranche@cdg-madaef.com",
            "password" => Hash::make("MADAEF@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '49',
          ],
          [
            "firstname" => "Ewane",
            "lastname" => "CDG",
            "email" => "rbranche@cdg-ewane.com",
            "password" => Hash::make("EWANE@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '35',
          ],
          [
            "firstname" => "Jaida",
            "lastname" => "CDG",
            "email" => "rbranche@cdg-jaida.com",
            "password" => Hash::make("JAIDA@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '39',
          ],
          [
            "firstname" => "Madaef Golf",
            "lastname" => "CDG",
            "email" => "rbranche@cdg-madaef-golf.com",
            "password" => Hash::make("MADAEFGOLF@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '50',
          ],
          [
            "firstname" => "HRM",
            "lastname" => "CDG",
            "email" => "rbranche@cdg-hrm.com",
            "password" => Hash::make("HRM@2024"),
            "role" => UserRoleEnum::GROUP,
            "group_id" => '38',
          ]
          ,
          [
            "firstname" => "Plateforme",
            "lastname" => "CDG",
            "email" => "rplateforme@groupe-cdg.com",
            "password" => Hash::make("CDG@report2024"),
            "role" => UserRoleEnum::PLATEFORME,
          ]
    );
    tenancy()->end();

    return 'user created';
});
Route::get('/all-test' , function(){
    $start_datetime = date('Y-m-d H:i:s');
    Log::info("[$start_datetime]: Update Data for all tenants has finished.");

    Tenant::all()->runForEach(function () {
        $id = tenant('id');
        $userFieldsService = new UserFieldsService();
        $userfields = config('tenantconfigfields.userfields');

        Log::info("Update process started for tenant {$id}");

        UpdateLearnerJob::withChain([
            new UpdateCallJob($id),
            new UpdateTicketJob($id),
            new UpdateMoocJob($id),
            new UpdateLpJob($id),
            new UpdateModuleJob($id),
            new UpdateEnrollementMoocJob($id),
            new UpdateEnrollementLangueJob($id),
            new UpdateEnrollementModuleJob($id),
            new UpdateEnrollementsLpsJob($id),
        ])->dispatch($userFieldsService, $id, $userfields);

    });

    $end_datetime = date('Y-m-d H:i:s');
    Log::info("[$end_datetime]: Update Data for all tenants has finished.");
});
Route::get('test-speex-data' , function(){
    try {
        $speexConnector = new SpeexConnector();
        $speexResponse = $speexConnector->send(new SpeexUserArticleResult('3889260', '388661'));

        // Check if the request was successful before accessing response
        if ($speexResponse->status() == 200) {
            return $speexResponse->dto();
        } else {
            // Handle error response
            return [
                'time' => 0,
                'niveau' => NULL
            ];
        }
    } catch (\Exception $e) {
        return [
            'time' => 0,
            'niveau' => NULL
        ];
    }
});
Route::get('test-cmi', function(){
    $cmi_time = 0;
    try {
        $doceboConnector = new DoceboConnector();
        $cmiRequest = new DoceboGetLoCmiData('6656', '13067', '67951');
        $cmiResponse = $doceboConnector->send($cmiRequest);
        if($cmiResponse->status() === 200){
            $cmi_time += $cmiResponse->dto();
        }else{
            $cmi_time += 0;
        }
        // Process $cmiResponse
    } catch (InternalServerErrorException $e) {
        $cmi_time = 0;
    } catch (Exception $e) {
        $cmi_time = 0;
    }

    return $cmi_time;

});
Route::get('/test-module', function(){
    $tenant = Tenant::find('54923e49-d845-4a7a-a595-ff704b1f88e2');
    tenancy()->initialize($tenant);

    $doceboConnector = new DoceboConnector();
    $moduleEnrollmentsService = new ModuleEnrollmentsService();

    $fields = config('tenantconfigfields.enrollmentfields');
    $enrollFields = $moduleEnrollmentsService->getEnrollmentsFields($fields);
    $learners = Learner::all();
    foreach( $learners as $learner){
        $request = new DoceboCoursesEnrollements($learner->docebo_id);
        $mdenrollsResponses = $doceboConnector->send($request);
        $mdenrollsResponses = $doceboConnector->paginate($request);
        $results = [];
        foreach($mdenrollsResponses as $md){
            $data = $md->dto();
            $results = array_merge($results, $data);
        }
        if(!empty($results)){
            $moduleEnrollmentsService->batchInsert(array_filter($results), $enrollFields);
        }
    }
    tenancy()->end();
});
Route::get('/test-speex', function(){
    $tenant = Tenant::find('54923e49-d845-4a7a-a595-ff704b1f88e2');
    tenancy()->initialize($tenant);

    $doceboConnector = new DoceboConnector();
    $speexEnrollmentsService = new SpeexEnrollmentsService();

    //Define Enrollments Fields
    $fields = config('tenantconfigfields.enrollmentfields');
    $enrollFields = $speexEnrollmentsService->getEnrollmentsFields($fields);
    $learners = Learner::whereNotNull('speex_id')->get();
    foreach( $learners as $learner){
        // GET LEARNER Enrollements
        $request = new DoceboSpeexEnrollements($learner->docebo_id);
        $mdenrollsResponses = $doceboConnector->paginate($request);
        $results = [];
        foreach($mdenrollsResponses as $md){
            $data = $md->dto();
            $results = array_merge($results, $data);
        }
        // BATCH INSERT LEARNER DATA
        if(!empty($results)){
            $speexEnrollmentsService->batchInsert(array_filter($results), $enrollFields);
        }
    }
    die();
    tenancy()->end();
});
Route::get('/test-mooc', function(){

    $tenant = Tenant::find('54923e49-d845-4a7a-a595-ff704b1f88e2');
    tenancy()->initialize($tenant);
    // Initialize all neccessary Service
    $doceboConnector = new DoceboConnector();
    $moocEnrollmentsService = new MoocEnrollmentsService();

    //Define Enrollments Fields
    $fields = config('tenantconfigfields.enrollmentfields');
    $enrollFields = $moocEnrollmentsService->getEnrollmentsFields($fields);

    // GET Enrollements List DATA
    $moocsDoceboIds = Mooc::pluck('docebo_id')->toArray();
    foreach($moocsDoceboIds as $moocDoceboId){
        $request = new DoceboMoocsEnrollements($moocDoceboId);
        $mdenrollsResponses = $doceboConnector->paginate($request);
        $results = [];
        foreach($mdenrollsResponses as $md){
            $data = $md->dto();
            $results = array_merge($results, $data);
        }
        if(!empty($results)){
            $moocEnrollmentsService->batchInsert(array_filter($results), $enrollFields);
        }
    }
    tenancy()->end();


});
Route::get('/test-lp', function(){
    $tenant = Tenant::find('54923e49-d845-4a7a-a595-ff704b1f88e2');
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
            dd($results);
            /*if(!empty($results)){
                $LpEnrollmentsService->batchInsert(array_filter($results), $enrollFields);
            }*/
        }
    tenancy()->end();
});

/**
 *  END TEST FUNCTION
 */
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
