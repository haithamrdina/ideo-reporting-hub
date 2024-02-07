<?php

namespace App\Livewire\Tenant\Partials\Navbar;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Menu extends Component
{
    public function render()
    {
        $menus = Auth::guard('user')->user()->role->livewiremenu();
        $menus = array_map(function($item) {
            return (Object)[
                'name' => $item['name'],
                'route' => $item['route'],
                'icon' => $item['icon']
            ];
        }, $menus);
        return view('livewire.tenant.partials.navbar.menu' ,[
            'menus' => $menus
        ]);
    }
}
