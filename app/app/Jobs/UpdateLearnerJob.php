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
use App\Services\UserFieldsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateLearnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    /**
     * Create a new job instance.
     */
    protected $tenantId;
    protected $userfields;
    protected $userFieldsService;

    public function __construct(UserFieldsService $userFieldsService, string $tenantId, Array $userfields)
    {
        $this->tenantId = $tenantId;
        $this->userfields = $userfields;
        $this->userFieldsService = $userFieldsService;
        $this->onQueue('reporting');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start_datetime = date('Y-m-d H:i:s');
        Log::info("['start'][$start_datetime]: UpdateLearnerJob for tenant {$this->tenantId} has started.");

        $tenant = Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);
            $doceboConnector = new DoceboConnector();
            $speexConnector = new SpeexConnector();
            $userFieldsService = new UserFieldsService();
            $userfields = config('tenantconfigfields.userfields');
            $datafields = $userFieldsService->getTenantUserFields($userfields);
            $groups = Group::whereIn('status' , [GroupStatusEnum::ACTIVE, GroupStatusEnum::ARCHIVE])->get();
            foreach($groups as $group){
                $paginator = $doceboConnector->paginate(new DoceboGroupeUsersList($userFieldsService, $group->docebo_id, $userfields, $group->status));
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
                DB::transaction(function () use ($filteredItems,$datafields) {
                    Learner::upsert(
                        $filteredItems,
                        ['docebo_id'],
                        $datafields
                    );
                });
            }
        tenancy()->end();

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("['end'][$end_datetime]: UpdateLearnerJob for tenant {$this->tenantId} has finished.");
    }
}




