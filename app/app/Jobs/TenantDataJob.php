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
use Illuminate\Support\Facades\Hash;

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
                'firstname' => $this->tenant->company_code,
                'lastname' => 'Plateforme',
                'email' => 'rplateforme@ideo-reporting.com',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::PLATEFORME,
            ]);

            User::create([
                'firstname' => $this->tenant->company_code,
                'lastname' => 'Branche',
                'email' => 'rproject@ideo-reporting.com',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::PROJECT,
            ]);


            User::create([
                'firstname' => $this->tenant->company_code,
                'lastname' => 'Plateforme',
                'email' => 'rgroup@ideo-reporting.com',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::GROUP,
            ]);
        });
    }
}
