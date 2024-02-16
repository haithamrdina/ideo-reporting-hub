<?php

namespace App\Jobs;

use App\Http\Integrations\Zendesk\Requests\ZendeskOrganizationsTickets;
use App\Http\Integrations\Zendesk\ZendeskConnector;
use App\Models\Tenant;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateTicketJob implements ShouldQueue
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

        $start_datetime = date('Y-m-d H:i:s');
        Log::info("[$start_datetime]: UpdateTicketJob for tenant {$this->tenantId} has started.");

        tenancy()->initialize($tenant);
            $zendeskConnector = new ZendeskConnector();
            $request = new ZendeskOrganizationsTickets($tenant->zendesk_org_id);
            $orgResponse = $zendeskConnector->paginate($request);
            foreach($orgResponse as $md){
                $data  = array_filter($md->dto());

                DB::transaction(function () use ($data) {
                    Ticket::upsert(
                        $data,
                        [
                            'learner_docebo_id',
                            'subject',
                            'ticket_created_at'
                        ],
                        [
                            'status',
                            'ticket_updated_at',
                            'group_id',
                            'project_id',
                        ]
                    );
                });
            }
        tenancy()->end();

        $end_datetime = date('Y-m-d H:i:s');
        Log::info("[$end_datetime]: UpdateTicketJob for tenant {$this->tenantId} has finished.");
    }
}
