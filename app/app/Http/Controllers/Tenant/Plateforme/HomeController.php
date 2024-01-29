<?php

namespace App\Http\Controllers\Tenant\Plateforme;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the User dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $config = [
            'cmi' => config('features.cmi_time'),
            'calculated' =>  config('features.calculated_time'),
            'recommended' => config('features.recommended_time'),
            'start_date' => config('features.inscription_stats_between_date.status.start_date'),
        ];
        dd($config);
        return view('tenant.plateforme.home');
    }
}
