<?php

namespace App\Http\Controllers\Tenant\Plateforme;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Show the User dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        return view('tenant.plateforme.project');
    }
}
