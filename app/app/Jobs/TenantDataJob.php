<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Services\InitTenantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TenantDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $tenant;
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->tenant->run(function(){
            $initTenantService = new InitTenantService();
            $initTenantService->createUsers($this->tenant);

            $archive = $this->tenant->archive;
            if($archive == true){
                $initTenantService->initialiseArchives($this->tenant);
            }
        });
    }
}
