<?php


namespace App\Services;

use App\Enums\ProjectStatusEnum;
use App\Enums\UserRoleEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboArchiveGroupList;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InitTenantService{

    public function createUsers($tenant)
    {
        User::create([
            'firstname' => $tenant->company_code,
            'lastname' => 'Plateforme',
            'email' => 'rplateforme@ideo-reporting.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::PLATEFORME,
        ]);

        User::create([
            'firstname' => $tenant->company_code,
            'lastname' => 'Project',
            'email' => 'rproject@ideo-reporting.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::PROJECT,
        ]);


        User::create([
            'firstname' => $tenant->company_code,
            'lastname' => 'Group',
            'email' => 'rgroup@ideo-reporting.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::GROUP,
        ]);
    }


    public function initialiseArchives($tenant){
        $project = Project::create([
            'name' => 'Archives',
            'status' => ProjectStatusEnum::INACTIVE
        ]);

        $doceboConnector = new DoceboConnector();
        $paginator = $doceboConnector->paginate(new DoceboArchiveGroupList($tenant->company_code));
        $result = [];
        foreach($paginator as $pg){
            $data = $pg->dto();
            $result = array_merge($result, $data);
        }
        DB::transaction(function () use ($result) {
            Group::upsert(
                $result,
                ['docebo_id'],
                [
                    'code',
                    'name',
                    'status'
                ]
            );
        });

        $groupsIDs = Group::pluck('id')->toArray();
        $project->groups()->sync($groupsIDs);
    }
}
