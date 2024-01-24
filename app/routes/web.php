<?php

use App\Enums\GroupStatusEnum;
use App\Http\Controllers\Central\GroupController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\ProjectController;
use App\Http\Controllers\Central\TenantController;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboCourseList;
use App\Http\Integrations\Docebo\Requests\DoceboCourseLosList;
use App\Http\Integrations\Docebo\Requests\DoceboCoursesEnrollements;
use App\Http\Integrations\Docebo\Requests\DoceboGroupeList;
use App\Http\Integrations\Docebo\Requests\DoceboGroupeUsersList;
use App\Http\Integrations\Docebo\Requests\DoceboLpList;
use App\Http\Integrations\Docebo\Requests\DoceboLpsEnrollements;
use App\Http\Integrations\Speex\Requests\SpeexUserId;
use App\Http\Integrations\Speex\SpeexConnector;
use App\Models\Group;
use App\Models\Project;
use App\Models\Tenant;
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
    $tenant = Tenant::find('a69773f7-43b4-46ab-8081-618b072a50d3');
    tenancy()->initialize($tenant);
        $doceboConnector = new DoceboConnector();
        $group = Group::find(31);
        $modulesDoceboIds = $group->modules()->where('category', 'CEGOS')->pluck('docebo_id')->toArray();
        $request = new DoceboCoursesEnrollements($modulesDoceboIds);

        $mdenrollsResponses = $doceboConnector->paginate($request);

        $resultMds = [];
        foreach($mdenrollsResponses as $md){
            $data = $md->dto();
            $resultMds = array_merge($resultMds, $data);
        }
        dd($resultMds);
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
        Route::get('tenants/{tenant}/moocs',[ TenantController::class, 'majMoocs'])->name('tenants.moocs');
    });
});

