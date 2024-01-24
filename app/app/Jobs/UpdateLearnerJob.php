<?php

namespace App\Jobs;

use App\Enums\GroupStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboGroupeUsersList;
use App\Http\Integrations\Speex\Requests\SpeexUserId;
use App\Http\Integrations\Speex\SpeexConnector;
use App\Models\Group;
use App\Models\Learner;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateLearnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    /**
     * Create a new job instance.
     */
    protected $tenantId;
    public function __construct(string $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
            $groups = Group::where('status' , GroupStatusEnum::ACTIVE)->get();

            $doceboConnector = new DoceboConnector();
            $speexConnector = new SpeexConnector();
            foreach($groups as $group){
                $paginator = $doceboConnector->paginate(new DoceboGroupeUsersList($group->docebo_id));
                $result = [];
                foreach($paginator as $pg){
                    $data = $pg->dto();
                    $result = array_merge($result, $data);
                }

                $filteredItems = array_map(function ($item) use($speexConnector, $group){
                    $speexResponse = $speexConnector->send(new SpeexUserId($item['username']));
                    $item['speex_id'] = $speexResponse->dto();
                    $item['group_id'] = $group->id;
                    $item['project_id'] = $group->projects()->first()->id;
                    return  $item;
                }, $result);

                DB::transaction(function () use ($filteredItems) {
                    Learner::upsert(
                        $filteredItems,
                        ['docebo_id'],
                        [
                            'firstname',
                            'lastname',
                            'email',
                            'username',
                            'creation_date',
                            'last_access_date',
                            'statut',
                            'categorie',
                            'speex_id',
                            'group_id',
                            'project_id',
                        ]
                    );
                });
            }
        tenancy()->end();
    }
}




