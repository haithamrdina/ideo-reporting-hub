<?php

declare(strict_types=1);

use App\Enums\CourseStatusEnum;
use App\Http\Controllers\Tenant\Plateforme\HomeController as PlateformeHomeController;
use App\Http\Controllers\Tenant\Plateforme\GroupController as PlateformeGroupController;
use App\Http\Controllers\Tenant\Plateforme\ProjectController as PlateformeProjectController;

use App\Http\Controllers\Tenant\Project\HomeController as ProjectHomeController;
use App\Http\Controllers\Tenant\Project\GroupController as ProjectGroupController;

use App\Http\Controllers\Tenant\Group\HomeController as GroupHomeController;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\getLeaderboardsData;
use App\Models\Enrollmodule;
use App\Models\Group;
use App\Models\Learner;
use App\Models\Module;
use App\Models\Project;
use App\Models\Tenant;
use App\Services\TimeConversionService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use League\Csv\CharsetConverter;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomainOrSubdomain::class,
    PreventAccessFromCentralDomains::class,
    ScopeSessions::class
])->group(function () {

    Route::get('gamif', function () {
        $doceboConnector = new DoceboConnector();
        $leaderbordDataResponse = $doceboConnector->send(new getLeaderboardsData(tenant('leaderboard_id')));
        dd($leaderbordDataResponse->dto());
    });
    Route::name('tenant.')->group(function () {
        require __DIR__ . '/tenant-auth.php';
        Route::get('test-export', function () {


            $userfields = config('tenantconfigfields.userfields');
            $enrollfields = config('tenantconfigfields.enrollmentfields');

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['module_docebo_id'] = 'Module';
            $fields['learner_docebo_id'] = 'Username';

            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $fields['matricule'] = 'Matricule';
            }

            $fields['enrollment_created_at'] = 'Date d\'inscription';
            $fields['status'] = 'Statut';
            $fields['enrollment_updated_at'] = 'Date du dernière modification';
            $fields['enrollment_completed_at'] = 'Date d\'achèvement';
            $fields['session_time'] = 'Temps de session';

            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $fields['cmi_time'] = 'Temps d\'engagement';
            }

            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $fields['calculated_time'] = 'Temps calculé';
            }

            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $fields['recommended_time'] = 'Temps pédagogique recommandé';
            }
            //dd($fields);
            $softModules = Module::where(['category' => 'CEGOS', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->get();
            $csvExporter = new \Laracsv\Export();

            $csvExporter->beforeEach(function ($enroll) use ($userfields, $enrollfields) {
                $timeConversionService = new TimeConversionService();
                $enroll->project_id = Project::find($enroll->project_id)->name;
                $enroll->group_id = Group::find($enroll->group_id)->name;
                $enroll->module_docebo_id = Module::where('docebo_id', $enroll->module_docebo_id)->first()->name;
                $enroll->learner_docebo_id = Learner::where('docebo_id', $enroll->learner_docebo_id)->first()->username;
                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $enroll->matricule = Learner::where('docebo_id', $enroll->learner_docebo_id)->first()->matricule;
                }

                $enroll->enrollment_created_at = $enroll->enrollment_created_at != null ? $enroll->enrollment_created_at : '******';

                if ($enroll->status == 'waiting') {
                    $enroll->status = "En attente";
                } elseif ($enroll->status == 'enrolled') {
                    $enroll->status = "Inscrit";
                } elseif ($enroll->status == 'in_progress') {
                    $enroll->status = "En cours";
                } elseif ($enroll->status == 'completed') {
                    $enroll->status = "Terminé";
                }


                $enroll->enrollment_updated_at = $enroll->enrollment_updated_at != null ? $enroll->enrollment_updated_at : '******';
                $enroll->enrollment_completed_at = $enroll->enrollment_completed_at != null ? $enroll->enrollment_completed_at : '******';
                $enroll->session_time = $timeConversionService->convertSecondsToTime($enroll->session_time);

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $enroll->cmi_time = $timeConversionService->convertSecondsToTime($enroll->cmi_time);
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $enroll->calculated_time = $timeConversionService->convertSecondsToTime($enroll->calculated_time);
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $enroll->recommended_time = $timeConversionService->convertSecondsToTime($enroll->recommended_time);
                }
            });
            $writer = $csvExporter->build($softEnrolls, $fields)->getWriter();
            Storage::put('enrollements.csv', "\xEF\xBB\xBF" . $writer->getContent());

        });
        Route::middleware(['user.auth:user', 'plateforme'])->prefix('plateforme')->name('plateforme.')->group(function () {
            Route::get('/home', [PlateformeHomeController::class, 'index'])->name('home');
            Route::post('/home', [PlateformeHomeController::class, 'export2'])->name('export');
            Route::get('/getdata', [PlateformeHomeController::class, 'getData']);
            Route::get('/notifications/{notification}/mark-as-read', [PlateformeHomeController::class, 'markAsRead'])->name('notifications.markAsRead');
            Route::get('/getlanguagedata/{selectedLanguage}', [PlateformeHomeController::class, 'getLanguageData']);
            Route::get('/getdigitaldata/{selectedDigital}', [PlateformeHomeController::class, 'getDigitalData']);
            Route::get('/getsmdata/{selectedSM}', [PlateformeHomeController::class, 'getSMData']);
            Route::get('/getlpdata/{selectedLp}', [PlateformeHomeController::class, 'getLpData']);
            Route::get('/getinscritsdata/filter', [PlateformeHomeController::class, 'getInscritsPerDate']);
            Route::get('/getlscdata/filter', [PlateformeHomeController::class, 'getLscPerDate']);
            Route::get('/inscrits/export', [PlateformeHomeController::class, 'exportInscrits'])->name('inscrits.export');
            Route::get('/modules/export', [PlateformeHomeController::class, 'exportModules'])->name('modules.export');
            Route::get('/lps/export', [PlateformeHomeController::class, 'exportLps'])->name('lps.export');
            Route::get('/lsc/export', [PlateformeHomeController::class, 'exportLsc'])->name('lsc.export');
            Route::get('/gamification/export', [PlateformeHomeController::class, 'exportGamification'])->name('gamification.export');




            Route::get('/projects', [PlateformeProjectController::class, 'index'])->name('projects');
            Route::get('/projects/{projectId}/getdata', [PlateformeProjectController::class, 'getData']);
            Route::get('/projects/{projectId}/getlanguagedata/{selectedLanguage}', [PlateformeProjectController::class, 'getLanguageData']);
            Route::get('/projects/{projectId}/getdigitaldata/{selectedDigital}', [PlateformeProjectController::class, 'getDigitalData']);
            Route::get('/projects/{projectId}/getsmdata/{selectedSM}', [PlateformeProjectController::class, 'getSMData']);
            Route::get('/projects/{projectId}/getlpdata/{selectedLp}', [PlateformeProjectController::class, 'getLpData']);
            Route::get('/projects/{projectId}/getinscritsdata/filter', [PlateformeProjectController::class, 'getInscritsPerDate']);
            Route::get('/projects/{projectId}/getlscdata/filter', [PlateformeProjectController::class, 'getLscPerDate']);


            Route::get('/groups', [PlateformeGroupController::class, 'index'])->name('groups');
            Route::get('/groups/{groupId}/getdata', [PlateformeGroupController::class, 'getData']);
            Route::get('/groups/{groupId}/getlanguagedata/{selectedLanguage}', [PlateformeGroupController::class, 'getLanguageData']);
            Route::get('/groups/{groupId}/getdigitaldata/{selectedDigital}', [PlateformeGroupController::class, 'getDigitalData']);
            Route::get('/groups/{groupId}/getsmdata/{selectedSM}', [PlateformeGroupController::class, 'getSMData']);
            Route::get('/groups/{groupId}/getlpdata/{selectedLp}', [PlateformeGroupController::class, 'getLpData']);
            Route::get('/groups/{groupId}/getinscritsdata/filter', [PlateformeGroupController::class, 'getInscritsPerDate']);
            Route::get('/groups/{groupId}/getlscdata/filter', [PlateformeGroupController::class, 'getLscPerDate']);
        });

        Route::middleware(['user.auth:user', 'project'])->prefix('project')->name('project.')->group(function () {
            Route::get('/home', [ProjectHomeController::class, 'index'])->name('home');
            Route::post('/home', [ProjectHomeController::class, 'export2'])->name('export');
            Route::get('/{projectId}/getdata', [ProjectHomeController::class, 'getData']);
            Route::get('/notifications/{notification}/mark-as-read', [ProjectHomeController::class, 'markAsRead'])->name('notifications.markAsRead');
            Route::get('/{projectId}/getlanguagedata/{selectedLanguage}', [ProjectHomeController::class, 'getLanguageData']);
            Route::get('/{projectId}/getdigitaldata/{selectedDigital}', [ProjectHomeController::class, 'getDigitalData']);
            Route::get('/{projectId}/getsmdata/{selectedSM}', [ProjectHomeController::class, 'getSMData']);
            Route::get('/{projectId}/getlpdata/{selectedLp}', [ProjectHomeController::class, 'getLpData']);
            Route::get('/{projectId}/getinscritsdata/filter', [ProjectHomeController::class, 'getInscritsPerDate']);
            Route::get('/{projectId}/getlscdata/filter', [ProjectHomeController::class, 'getLscPerDate']);
            Route::get('/{projectId}/inscrits/export', [ProjectHomeController::class, 'exportInscrits'])->name('inscrits.export');
            Route::get('/{projectId}/modules/export', [ProjectHomeController::class, 'exportModules'])->name('modules.export');
            Route::get('/{projectId}/lps/export', [ProjectHomeController::class, 'exportLps'])->name('lps.export');
            Route::get('/{projectId}/lsc/export', [ProjectHomeController::class, 'exportLsc'])->name('lsc.export');

            Route::get('/groups', [ProjectGroupController::class, 'index'])->name('groups');
            Route::get('/groups/{groupId}/getdata', [ProjectGroupController::class, 'getData']);
            Route::get('/groups/{groupId}/getlanguagedata/{selectedLanguage}', [ProjectGroupController::class, 'getLanguageData']);
            Route::get('/groups/{groupId}/getdigitaldata/{selectedDigital}', [ProjectGroupController::class, 'getDigitalData']);
            Route::get('/groups/{groupId}/getsmdata/{selectedSM}', [ProjectGroupController::class, 'getSMData']);
            Route::get('/groups/{groupId}/getlpdata/{selectedLp}', [ProjectGroupController::class, 'getLpData']);
            Route::get('/groups/{groupId}/getinscritsdata/filter', [ProjectGroupController::class, 'getInscritsPerDate']);
            Route::get('/groups/{groupId}/getlscdata/filter', [ProjectGroupController::class, 'getLscPerDate']);
        });

        Route::middleware(['user.auth:user', 'group'])->prefix('group')->name('group.')->group(function () {
            Route::get('/home', [GroupHomeController::class, 'index'])->name('home');
            Route::post('/home', [GroupHomeController::class, 'export2'])->name('export');
            Route::get('/{groupId}/getdata', [GroupHomeController::class, 'getData']);
            Route::get('/notifications/{notification}/mark-as-read', [GroupHomeController::class, 'markAsRead'])->name('notifications.markAsRead');
            Route::get('/{groupId}/getlanguagedata/{selectedLanguage}', [GroupHomeController::class, 'getLanguageData']);
            Route::get('/{groupId}/getdigitaldata/{selectedDigital}', [GroupHomeController::class, 'getDigitalData']);
            Route::get('/{groupId}/getsmdata/{selectedSM}', [GroupHomeController::class, 'getSMData']);
            Route::get('/{groupId}/getlpdata/{selectedLp}', [GroupHomeController::class, 'getLpData']);
            Route::get('/{groupId}/getinscritsdata/filter', [GroupHomeController::class, 'getInscritsPerDate']);
            Route::get('/{groupId}/getlscdata/filter', [GroupHomeController::class, 'getLscPerDate']);
            Route::get('/{groupId}/inscrits/export', [GroupHomeController::class, 'exportInscrits'])->name('inscrits.export');
            Route::get('/{groupId}/modules/export', [GroupHomeController::class, 'exportModules'])->name('modules.export');
            Route::get('/{groupId}/lps/export', [GroupHomeController::class, 'exportLps'])->name('lps.export');
            Route::get('/{groupId}/lsc/export', [GroupHomeController::class, 'exportLsc'])->name('lsc.export');
        });
    });
});