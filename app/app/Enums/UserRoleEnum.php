<?php

namespace App\Enums;

enum UserRoleEnum:string{
    case PLATEFORME = 'responsable_plateforme';
    case PROJECT =  'reponsable_branche';
    case GROUP =  'responsable_filiale';


    public function description(): string
    {
        return match($this)
        {
            self::PLATEFORME => 'Responsable plateforme',
            self::PROJECT => 'Responsable branche',
            self::GROUP => 'Responsable filiale',
        };
    }

    public function logo(): string
    {
        return match($this)
        {
            self::PLATEFORME => 'tenant.plateforme.home',
            self::PROJECT => 'tenant.project.home',
            self::GROUP => 'tenant.group.home',
        };
    }

    public function menu(): Array
    {
        return match($this)
        {
            self::PLATEFORME => [
                [
                    'name' => 'Tableau de bord',
                    'route' => 'tenant.plateforme.home',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z" /><path d="M4 16h6v4h-6z" /><path d="M14 12h6v8h-6z" /><path d="M14 4h6v4h-6z" /></svg>'
                ],
                [
                    'name' => 'Branches',
                    'route' => 'tenant.plateforme.projects',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-binary-tree" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M16 4a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M16 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M11 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M21 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M5.058 18.306l2.88 -4.606" /><path d="M10.061 10.303l2.877 -4.604" /><path d="M10.065 13.705l2.876 4.6" /><path d="M15.063 5.7l2.881 4.61" /></svg>'
                ],
                [
                    'name' => 'Filiales',
                    'route' => 'tenant.plateforme.groups',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-affiliate" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5.931 6.936l1.275 4.249m5.607 5.609l4.251 1.275" /><path d="M11.683 12.317l5.759 -5.759" /><path d="M5.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M18.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M18.5 18.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M8.5 15.5m-4.5 0a4.5 4.5 0 1 0 9 0a4.5 4.5 0 1 0 -9 0" /></svg>'
                ],
            ],
            self::PROJECT => [
                [
                    'name' => 'Tableau de bord',
                    'route' => 'tenant.project.home',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z" /><path d="M4 16h6v4h-6z" /><path d="M14 12h6v8h-6z" /><path d="M14 4h6v4h-6z" /></svg>'
                ],
                [
                    'name' => 'Filiales',
                    'route' => 'tenant.project.groups',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-affiliate" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5.931 6.936l1.275 4.249m5.607 5.609l4.251 1.275" /><path d="M11.683 12.317l5.759 -5.759" /><path d="M5.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M18.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M18.5 18.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M8.5 15.5m-4.5 0a4.5 4.5 0 1 0 9 0a4.5 4.5 0 1 0 -9 0" /></svg>'
                ]
            ],
            self::GROUP => [],
        };
    }

    public function livewiremenu(): Array
    {
        return match($this)
        {
            self::PLATEFORME => [
                [
                    'name' => 'Tableau de bord',
                    'route' => 'tenant.plateforme.livewire.home',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z" /><path d="M4 16h6v4h-6z" /><path d="M14 12h6v8h-6z" /><path d="M14 4h6v4h-6z" /></svg>'
                ],
                [
                    'name' => 'Branches',
                    'route' => 'tenant.plateforme.livewire.projects',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-binary-tree" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M16 4a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M16 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M11 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M21 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M5.058 18.306l2.88 -4.606" /><path d="M10.061 10.303l2.877 -4.604" /><path d="M10.065 13.705l2.876 4.6" /><path d="M15.063 5.7l2.881 4.61" /></svg>'
                ],
                [
                    'name' => 'Filiales',
                    'route' => 'tenant.plateforme.livewire.groups',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-affiliate" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5.931 6.936l1.275 4.249m5.607 5.609l4.251 1.275" /><path d="M11.683 12.317l5.759 -5.759" /><path d="M5.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M18.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M18.5 18.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M8.5 15.5m-4.5 0a4.5 4.5 0 1 0 9 0a4.5 4.5 0 1 0 -9 0" /></svg>'
                ],
            ],
            self::PROJECT => [
                [
                    'name' => 'Tableau de bord',
                    'route' => 'tenant.project.livewire.home',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z" /><path d="M4 16h6v4h-6z" /><path d="M14 12h6v8h-6z" /><path d="M14 4h6v4h-6z" /></svg>'
                ],
                [
                    'name' => 'Filiales',
                    'route' => 'tenant.project.livewire.groups',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-affiliate" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"  stroke="#C2181A"  fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5.931 6.936l1.275 4.249m5.607 5.609l4.251 1.275" /><path d="M11.683 12.317l5.759 -5.759" /><path d="M5.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M18.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M18.5 18.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0" /><path d="M8.5 15.5m-4.5 0a4.5 4.5 0 1 0 9 0a4.5 4.5 0 1 0 -9 0" /></svg>'
                ]
            ],
            self::GROUP => [],
        };
    }
}
