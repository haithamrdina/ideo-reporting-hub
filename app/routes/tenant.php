<?php

declare(strict_types=1);
use App\Http\Controllers\Tenant\Plateforme\HomeController as PlateformeHomeController;
use App\Http\Controllers\Tenant\Plateforme\GroupController as PlateformeGroupController;
use App\Http\Controllers\Tenant\Plateforme\ProjectController as PlateformeProjectController;

use App\Http\Controllers\Tenant\Project\HomeController as ProjectHomeController;
use App\Http\Controllers\Tenant\Project\GroupController as ProjectGroupController;

use App\Http\Controllers\Tenant\Group\HomeController as GroupHomeController;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\getLeaderboardsData;
use App\Models\Tenant;
use Illuminate\Support\Facades\Route;
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

    Route::get('gamif' , function(){
        $doceboConnector = new DoceboConnector();
        $leaderbordDataResponse = $doceboConnector->send(new getLeaderboardsData(tenant('leaderboard_id')));
        dd($leaderbordDataResponse->dto());
    });
    Route::name('tenant.')->group(function () {
        require __DIR__.'/tenant-auth.php';

        Route::middleware(['user.auth:user', 'plateforme'])->prefix('plateforme')->name('plateforme.')->group(function () {
            Route::get('/home', [PlateformeHomeController::class , 'index'])->name('home');
            Route::get('/getdata',[PlateformeHomeController::class , 'getData']);
            Route::get('/getlanguagedata/{selectedLanguage}',[PlateformeHomeController::class , 'getLanguageData']);
            Route::get('/getdigitaldata/{selectedDigital}',[PlateformeHomeController::class , 'getDigitalData']);
            Route::get('/getsmdata/{selectedSM}',[PlateformeHomeController::class , 'getSMData']);
            Route::get('/getlpdata/{selectedLp}',[PlateformeHomeController::class , 'getLpData']);
            Route::get('/getinscritsdata/filter',[PlateformeHomeController::class , 'getInscritsPerDate']);
            Route::get('/getlscdata/filter',[PlateformeHomeController::class , 'getLscPerDate']);
            Route::get('/inscrits/export',[PlateformeHomeController::class , 'exportInscrits'])->name('inscrits.export');
            Route::get('/modules/export',[PlateformeHomeController::class , 'exportModules'])->name('modules.export');
            Route::get('/lps/export',[PlateformeHomeController::class , 'exportLps'])->name('lps.export');
            Route::get('/lsc/export',[PlateformeHomeController::class , 'exportLsc'])->name('lsc.export');
            Route::get('/gamification/export',[PlateformeHomeController::class , 'exportGamification'])->name('gamification.export');


            Route::get('/projects', [PlateformeProjectController::class , 'index'])->name('projects');
            Route::get('/projects/{projectId}/getdata',[PlateformeProjectController::class , 'getData']);
            Route::get('/projects/{projectId}/getlanguagedata/{selectedLanguage}',[PlateformeProjectController::class , 'getLanguageData']);
            Route::get('/projects/{projectId}/getdigitaldata/{selectedDigital}',[PlateformeProjectController::class , 'getDigitalData']);
            Route::get('/projects/{projectId}/getsmdata/{selectedSM}',[PlateformeProjectController::class , 'getSMData']);
            Route::get('/projects/{projectId}/getlpdata/{selectedLp}',[PlateformeProjectController::class , 'getLpData']);
            Route::get('/projects/{projectId}/getinscritsdata/filter',[PlateformeProjectController::class , 'getInscritsPerDate']);
            Route::get('/projects/{projectId}/getlscdata/filter',[PlateformeProjectController::class , 'getLscPerDate']);


            Route::get('/groups', [PlateformeGroupController::class , 'index'])->name('groups');
            Route::get('/groups/{groupId}/getdata',[PlateformeGroupController::class , 'getData']);
            Route::get('/groups/{groupId}/getlanguagedata/{selectedLanguage}',[PlateformeGroupController::class , 'getLanguageData']);
            Route::get('/groups/{groupId}/getdigitaldata/{selectedDigital}',[PlateformeGroupController::class , 'getDigitalData']);
            Route::get('/groups/{groupId}/getsmdata/{selectedSM}',[PlateformeGroupController::class , 'getSMData']);
            Route::get('/groups/{groupId}/getlpdata/{selectedLp}',[PlateformeGroupController::class , 'getLpData']);
            Route::get('/groups/{groupId}/getinscritsdata/filter',[PlateformeGroupController::class , 'getInscritsPerDate']);
            Route::get('/groups/{groupId}/getlscdata/filter',[PlateformeGroupController::class , 'getLscPerDate']);
        });

        Route::middleware(['user.auth:user', 'project'])->prefix('project')->name('project.')->group(function () {
            Route::get('/home', [ProjectHomeController::class , 'index'])->name('home');
            Route::get('/{projectId}/getdata',[ProjectHomeController::class , 'getData']);
            Route::get('/{projectId}/getlanguagedata/{selectedLanguage}',[ProjectHomeController::class , 'getLanguageData']);
            Route::get('/{projectId}/getdigitaldata/{selectedDigital}',[ProjectHomeController::class , 'getDigitalData']);
            Route::get('/{projectId}/getsmdata/{selectedSM}',[ProjectHomeController::class , 'getSMData']);
            Route::get('/{projectId}/getlpdata/{selectedLp}',[ProjectHomeController::class , 'getLpData']);
            Route::get('/{projectId}/getinscritsdata/filter',[ProjectHomeController::class , 'getInscritsPerDate']);
            Route::get('/{projectId}/getlscdata/filter',[ProjectHomeController::class , 'getLscPerDate']);
            Route::get('/{projectId}/inscrits/export',[ProjectHomeController::class , 'exportInscrits'])->name('inscrits.export');
            Route::get('/{projectId}/modules/export',[ProjectHomeController::class , 'exportModules'])->name('modules.export');
            Route::get('/{projectId}/lps/export',[ProjectHomeController::class , 'exportLps'])->name('lps.export');
            Route::get('/{projectId}/lsc/export',[ProjectHomeController::class , 'exportLsc'])->name('lsc.export');

            Route::get('/groups', [ProjectGroupController::class , 'index'])->name('groups');
            Route::get('/groups/{groupId}/getdata',[ProjectGroupController::class , 'getData']);
            Route::get('/groups/{groupId}/getlanguagedata/{selectedLanguage}',[ProjectGroupController::class , 'getLanguageData']);
            Route::get('/groups/{groupId}/getdigitaldata/{selectedDigital}',[ProjectGroupController::class , 'getDigitalData']);
            Route::get('/groups/{groupId}/getsmdata/{selectedSM}',[ProjectGroupController::class , 'getSMData']);
            Route::get('/groups/{groupId}/getlpdata/{selectedLp}',[ProjectGroupController::class , 'getLpData']);
            Route::get('/groups/{groupId}/getinscritsdata/filter',[ProjectGroupController::class , 'getInscritsPerDate']);
            Route::get('/groups/{groupId}/getlscdata/filter',[ProjectGroupController::class , 'getLscPerDate']);
        });

        Route::middleware(['user.auth:user', 'group'])->prefix('group')->name('group.')->group(function () {
            Route::get('/home', [GroupHomeController::class , 'index'])->name('home');
            Route::get('/{groupId}/getdata',[GroupHomeController::class , 'getData']);
            Route::get('/{groupId}/getlanguagedata/{selectedLanguage}',[GroupHomeController::class , 'getLanguageData']);
            Route::get('/{groupId}/getdigitaldata/{selectedDigital}',[GroupHomeController::class , 'getDigitalData']);
            Route::get('/{groupId}/getsmdata/{selectedSM}',[GroupHomeController::class , 'getSMData']);
            Route::get('/{groupId}/getlpdata/{selectedLp}',[GroupHomeController::class , 'getLpData']);
            Route::get('/{groupId}/getinscritsdata/filter',[GroupHomeController::class , 'getInscritsPerDate']);
            Route::get('/{groupId}/getlscdata/filter',[GroupHomeController::class , 'getLscPerDate']);
            Route::get('/{groupId}/inscrits/export',[GroupHomeController::class , 'exportInscrits'])->name('inscrits.export');
            Route::get('/{groupId}/modules/export',[GroupHomeController::class , 'exportModules'])->name('modules.export');
            Route::get('/{groupId}/lps/export',[GroupHomeController::class , 'exportLps'])->name('lps.export');
            Route::get('/{groupId}/lsc/export',[GroupHomeController::class , 'exportLsc'])->name('lsc.export');
        });
    });
});

