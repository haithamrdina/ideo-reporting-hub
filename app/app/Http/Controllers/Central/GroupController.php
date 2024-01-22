<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateGroupJob;
use App\Models\Tenant;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function majGroups(string $id){
        UpdateGroupJob::dispatch($id);
        dd('dispateched');
        // $tenant = Tenant::find($id);
        // return view('central.tenants.show', compact('tenant'));
    }
}
