<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasScopedValidationRules;
use Stancl\Tenancy\Database\Concerns\MaintenanceMode;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;
    use MaintenanceMode;
    use HasScopedValidationRules;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'company_code',
            'company_name',
            'docebo_org_id',
            'zendesk_org_id',
        ];
    }

    public function setPasswordAttribute($value){
        return $this->attributes['password'] = Hash::make($value);
    }
}
