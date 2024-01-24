<?php

namespace App\Http\Controllers\Central;

use App\Enums\GroupStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        $tenant = Tenant::find($id);
        tenancy()->initialize($tenant);
            $groups = Group::where('status', GroupStatusEnum::ACTIVE)->get();
        tenancy()->end();
        return view('central.projects.create', compact('groups', 'tenant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request , string $id)
    {
        $tenant = Tenant::find($id);
        tenancy()->initialize($tenant);
            $validated = $request->validate([
                'name' => 'required|unique:projects|max:255',
                'groups' => 'required|array',
                'groups.*' => 'exists:groups,id',
            ]);
            $project = Project::create([
                'name' => $validated['name']
            ]);
            $project->groups()->attach($validated['groups']);
        tenancy()->end();

        return redirect()->route('admin.tenants.projects', ['tenant' => $tenant->id])->with('success','Projet ajouté avec succès');

    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, string $idp)
    {
        $tenant = Tenant::find($id);
        tenancy()->initialize($tenant);
            $project = Project::find($idp);
            $projectGroups = $project->groups()->pluck('group_id')->toArray();
            $groups = Group::where('status', GroupStatusEnum::ACTIVE)->get();
        tenancy()->end();
        return view('central.projects.edit', compact('groups', 'tenant' , 'project' , 'projectGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, string $idp)
    {
        $tenant = Tenant::find($id);
        tenancy()->initialize($tenant);
            $validated = $request->validate([
                'name' => 'required|unique:projects,name,' . $idp . '|max:255',
                'groups' => 'required|array',
                'groups.*' => 'exists:groups,id',
            ]);
            $project = Project::find($idp);
            $project->update([
                'name' => $validated['name']
            ]);
            $project->groups()->sync($validated['groups']);
        tenancy()->end();
        return redirect()->route('admin.tenants.projects', ['tenant' => $tenant->id])->with('success','Projet modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, string $idp)
    {
        $tenant = Tenant::find($id);
        tenancy()->initialize($tenant);
            $project = Project::find($idp);
            $project->delete();
        tenancy()->end();

        return redirect()->route('admin.tenants.projects', ['tenant' => $tenant->id])->with('success','Projet supprimé avec succès');
    }
}
