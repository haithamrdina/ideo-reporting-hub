<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Tenant\StoreRequest;
use App\Http\Requests\Central\Tenant\UpdateRequest;
use App\Jobs\UpdateGroupJob;
use App\Jobs\UpdateLearnerJob;
use App\Models\Group;
use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class TenantController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin.auth:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::all();
        return view('central.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('central.tenants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $tenant = Tenant::create([
            'company_code' => $validated['company_code'],
            'company_name' => $validated['company_name'],
            'docebo_org_id' => $validated['docebo_org_id'],
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $tenant->domains()->create(['domain' => $validated['subdomain']]);
        return redirect()->route('admin.tenants.index')->with('success', 'Tenant ajouté avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('central.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('central.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Tenant::find($id)->delete();
        return redirect()->route('admin.tenants.index')->with('success', 'Tenant deleted successfully');
    }

    /**
     * Update Groups for the specified resource.
     */
    public function getProjects(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $projects = Project::with('groups')->get();
        tenancy()->end();
        return view('central.projects.index', compact('tenant', 'projects'));
    }

    /**
     * Update Groups for the specified resource.
     */
    public function majGroups(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateGroupJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

     /**
     * Update Groups for the specified resource.
     */
    public function getGroups(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $groupes = Group::all();
        tenancy()->end();
        return view('central.groups.index', compact('tenant', 'groupes'));
    }

    /**
     * Update Learners for the specified resource.
     */
    public function majLearners(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateLearnerJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

    /**
     * Update Learning plan for the specified resource.
     */
    public function majLps(string $id)
    {

    }

    /**
     * Update Courses for the specified resource.
     */
    public function majModules(string $id)
    {

    }

    /**
     * Update Moocs for the specified resource.
     */
    public function majMoocs(string $id)
    {

    }
}
