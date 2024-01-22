<?php

namespace App\Jobs;

use App\Enums\UserRoleEnum;
use App\Models\Tenant;
use App\Models\User;
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
            User::create([
                'first_name' => $this->tenant->first_name,
                'last_name' => $this->tenant->last_name,
                'username' => $this->tenant->username,
                'email' => $this->tenant->email,
                'password' => $this->tenant->password,
                'role' => UserRoleEnum::PLATEFORME,
            ]);
        });
    }
}
