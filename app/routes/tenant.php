<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\Project\HomeController as ProjectHomeController;
use App\Http\Controllers\Tenant\Group\HomeController as GroupHomeController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\Plateforme\HomeController as PlateformeHomeController;
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
    Route::name('tenant.')->group(function () {
        require __DIR__.'/tenant-auth.php';
       /* Route::middleware('user.auth:user')->group(function () {
            Route::get('/home', [HomeController::class , 'index'])->name('home');
        });*/

        Route::middleware(['user.auth:user', 'plateforme'])->prefix('plateforme')->name('plateforme.')->group(function () {
            Route::get('/home', [PlateformeHomeController::class , 'index'])->name('home');
        });

        Route::middleware(['user.auth:user', 'project'])->prefix('project')->name('project.')->group(function () {
            Route::get('/home', [ProjectHomeController::class , 'index'])->name('home');
        });

        Route::middleware(['user.auth:user', 'group'])->prefix('group')->name('group.')->group(function () {
            Route::get('/home', [GroupHomeController::class , 'index'])->name('home');
        });
    });
});
