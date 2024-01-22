<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\HomeController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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
])->group(function () {
    Route::name('tenant.')->group(function () {
        require __DIR__.'/tenant-auth.php';
        Route::middleware('user.auth:user')->group(function () {
            Route::get('/home', [HomeController::class , 'index'])->name('home');
        });
    });
});
