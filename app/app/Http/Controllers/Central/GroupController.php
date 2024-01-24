<?php

namespace App\Http\Controllers\Central;

use App\Enums\GroupStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Tenant;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id, string $idg)
    {
        $tenant = Tenant::find($id);
        tenancy()->initialize($tenant);
            $groupe = Group::find($idg);
            $status = $groupe->status == GroupStatusEnum::ACTIVE ?   GroupStatusEnum::INACTIVE : GroupStatusEnum::ACTIVE;
            $message =   $groupe->status == GroupStatusEnum::ACTIVE ?   'Ce groupe a été désactiver avec succès' : 'Ce groupe a été activer avec succès';
            $groupe->update([
                'status' => $status
            ]);
        tenancy()->end();
        return redirect()->route('admin.tenants.groups', ['tenant' => $tenant->id])->with('success', $message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
