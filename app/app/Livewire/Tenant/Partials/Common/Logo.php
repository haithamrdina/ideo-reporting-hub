<?php

namespace App\Livewire\Tenant\Partials\Common;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logo extends Component
{
    public function render()
    {
        $route = Auth::guard('user')->user()->role->logo();
        return view('livewire.tenant.partials.common.logo' ,[
            'route' => $route
        ]);
    }
}
