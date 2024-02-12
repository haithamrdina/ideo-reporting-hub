<?php

namespace App\Http\Controllers\Central;

use App\Enums\CourseStatusEnum;
use App\Enums\GroupStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Integrations\IdeoDash\IdeoDashConnector;
use App\Http\Integrations\IdeoDash\Requests\IdeoDashClientList;
use App\Http\Integrations\Zendesk\Requests\ZendeskOrganizations;
use App\Http\Integrations\Zendesk\ZendeskConnector;
use App\Http\Requests\Central\Tenant\StoreRequest;
use App\Http\Requests\Central\Tenant\UpdateRequest;
use App\Jobs\UpdateCallJob;
use App\Jobs\UpdateEnrollementLangueJob;
use App\Jobs\UpdateEnrollementModuleJob;
use App\Jobs\UpdateEnrollementMoocJob;
use App\Jobs\UpdateEnrollementsLpsJob;
use App\Jobs\UpdateGroupJob;
use App\Jobs\UpdateLearnerJob;
use App\Jobs\UpdateLpJob;
use App\Jobs\UpdateModuleJob;
use App\Jobs\UpdateMoocJob;
use App\Jobs\UpdateTicketJob;
use App\Models\Call;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Group;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Lpenroll;
use App\Models\Module;
use App\Models\Mooc;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Services\UserFieldsService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class TenantController extends Controller
{

    protected $userFieldsService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserFieldsService $userFieldsService)
    {
        $this->middleware('admin.auth:admin');
        $this->userFieldsService = $userFieldsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::all();
        return view('central.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ideoDashConnector = new IdeoDashConnector();
        $clientResponse = $ideoDashConnector->send(new IdeoDashClientList());
        $clients = $clientResponse->dto();
        $zendeskConnector = new ZendeskConnector;
        $zendeskResponse = $zendeskConnector->send(new ZendeskOrganizations());
        $organizations = $zendeskResponse->dto();
        return view('central.tenants.create' , compact('clients' , 'organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

        $contract_start_date = $request->has('contract_start_date') && $request->input('contract_start_date') !== null ? $request->input('contract_start_date') : null;

        $matricule = $request->has('matricule') ? true : false;
        $fonction = $request->has('fonction') ? true : false;
        $direction = $request->has('direction') ? true : false;
        $categorie = $request->has('categorie') ? true : false;
        $sexe = $request->has('sexe') ? true : false;
        $cin = $request->has('cin') ? true : false;
        $archive = $request->has('archive') ? true : false;
        $sur_mesure = $request->has('sur_mesure') ? true : false;

        $cmi_time = $request->has('cmi_time') ? true : false;
        $recommended_time = $request->has('recommended_time') ? true : false;

        if($request->has('calculated_time')){
            $cmi_time = true;
            $calculated_time = true;
            $recommended_time = true;
        }else{
            $calculated_time = false;
        }

        $validated = $request->validated();
        $tenant = Tenant::create([
            'company_code' => $validated['company_code'],
            'company_name' => $validated['company_name'],
            'docebo_org_id' => $validated['docebo_org_id'],
            'zendesk_org_id' => $validated['zendesk_org_id'],

            'contract_start_date'=> $contract_start_date,

            'archive'=> $archive,
            'sur_mesure'=> $sur_mesure,

            'matricule' => $matricule,
            'fonction'=> $fonction ,
            'direction'=> $direction,
            'categorie' => $categorie,
            'sexe'=> $sexe ,
            'cin'=> $cin,

            'cmi_time' => $cmi_time,
            'calculated_time'=> $calculated_time ,
            'recommended_time'=> $recommended_time,
        ]);

        $tenant->domains()->create(['domain' => $validated['subdomain']]);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant ajouté avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $stats = (Object) [
                'groups' => Group::where('status', GroupStatusEnum::ACTIVE)->count(),
                'learners' => Learner::count(),
                'tickets' => Ticket::count(),
                'calls' => Call::count(),
                'lps' => Lp::count(),
                'sm' => Module::where(['category' => 'SM' , 'status' => CourseStatusEnum::ACTIVE])->count() ,
                'cegos' =>Module::where(['category' => 'CEGOS' , 'status' => CourseStatusEnum::ACTIVE])->count() ,
                'eni' =>Module::where(['category' => 'ENI' , 'status' => CourseStatusEnum::ACTIVE])->count() ,
                'speex' => Module::where(['category' => 'SPEEX' , 'status' => CourseStatusEnum::ACTIVE])->count(),
                'moocs' => Mooc::count(),
            ];
        tenancy()->end();
        return view('central.tenants.show', compact('tenant' , 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $ideoDashConnector = new IdeoDashConnector();
        $clientResponse = $ideoDashConnector->send(new IdeoDashClientList());
        $clients = $clientResponse->dto();
        $zendeskConnector = new ZendeskConnector;
        $zendeskResponse = $zendeskConnector->send(new ZendeskOrganizations());
        $organizations = $zendeskResponse->dto();
        return view('central.tenants.edit', compact('tenant','clients' , 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Tenant::find($id)->delete();
        return redirect()->route('admin.tenants.index')->with('success', 'Tenant deleted successfully');
    }

    /**
     * Update Groups for the specified resource.
     */
    public function getProjects(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $projects = Project::with('groups')->get();
        tenancy()->end();
        return view('central.projects.index', compact('tenant', 'projects'));
    }

    /**
     * Update Groups for the specified resource.
     */
    public function majGroups(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateGroupJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

    /**
     * GET Groups for the specified resource.
     */
    public function getGroups(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $groupes = Group::all();
        tenancy()->end();
        return view('central.groups.index', compact('tenant', 'groupes'));
    }

    /**
     * GET learners for the specified resource.
     */
    public function getLearners(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $learners = Learner::with('project', 'group')->get();
        tenancy()->end();
        return view('central.learners.index', compact('tenant', 'learners'));
    }

    /**
     * GET Lps for the specified resource.
     */
    public function getLps(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $lps = Lp::all();
        tenancy()->end();
        return view('central.lps.index', compact('tenant', 'lps'));
    }

    /**
     * GET Modules for the specified resource.
     */
    public function getModules(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $modules = Module::where('status', CourseStatusEnum::ACTIVE)->get();
        tenancy()->end();
        return view('central.modules.index', compact('tenant', 'modules'));
    }

    /**
     * GET Moocs for the specified resource.
     */
    public function getMoocs(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $moocs = Mooc::all();
        tenancy()->end();
        return view('central.moocs.index', compact('tenant', 'moocs'));
    }

    /**
     * GET Tickets for the specified resource.
     */
    public function getTickets(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $tickets = Ticket::with('project', 'group', 'learner')->get();
        tenancy()->end();
        return view('central.tickets.index', compact('tenant', 'tickets'));
    }

    /**
     * GET Calls for the specified resource.
     */
    public function getCalls(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $calls = Call::with('project', 'group', 'learner')->get();
        tenancy()->end();
        return view('central.calls.index', compact('tenant', 'calls'));
    }

    /**
     * Update Learners for the specified resource.
     */
    public function majLearners(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $userfields = config('tenantconfigfields.userfields');
        tenancy()->end();
        UpdateLearnerJob::dispatch($this->userFieldsService, $id,$userfields);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

    /**
     * Update Learning plan for the specified resource.
     */
    public function majLps(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateLpJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

    /**
     * Update Courses for the specified resource.
     */
    public function majModules(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateModuleJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

    /**
     * Update Moocs for the specified resource.
     */
    public function majMoocs(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateMoocJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

    /**
     * Update Moocs for the specified resource.
     */
    public function majTickets(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateTicketJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

     /**
     * Update Moocs for the specified resource.
     */
    public function majCalls(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateCallJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }


    /**
     * Update  Enrollements courses for the specified resource.
     */
    public function majEnrollsModules(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateEnrollementModuleJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

    /**
     * Update  Enrollements langues for the specified resource.
     */
    public function majEnrollsLangues(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateEnrollementLangueJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }


    /**
     * Update  Enrollements langues for the specified resource.
     */
    public function majEnrollsMoocs(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateEnrollementMoocJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

      /**
     * Update  Enrollements langues for the specified resource.
     */
    public function majEnrollsLps(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        UpdateEnrollementsLpsJob::dispatch($id);
        return redirect()->route('admin.tenants.show' , ['tenant' => $tenant]);
    }

    /**
     * get   Enrollements Moocs for the specified resource.
     */
    public function getEnrollsMoocs(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $enrollMoocs = Enrollmooc::with('project', 'group', 'learner', 'mooc')->get();
        tenancy()->end();
        return view('central.moocs.enrollment', compact('tenant','enrollMoocs'));
    }

    /**
     * get   Enrollements Langues for the specified resource.
     */
    public function getEnrollsLangues(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $enrollLangues = Langenroll::with('project', 'group', 'learner', 'module')
            ->whereHas('module', function ($query) {
                $query->where(['category' => 'SPEEX' , 'status' => 1 ]);
            })
            ->get();
        tenancy()->end();
        return view('central.modules.langue', compact('tenant','enrollLangues'));
    }

    /**
     * get Enrollements Softskills for the specified resource.
     */
    public function getEnrollsSoftskills(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $enrollSoftskills = Enrollmodule::with('project', 'group', 'learner', 'module')
            ->whereHas('module', function ($query) {
                $query->where(['category' => 'CEGOS' , 'status' => 1 ]);
            })
            ->get();
        tenancy()->end();
        return view('central.modules.softskills', compact('tenant','enrollSoftskills'));
    }


    /**
     * get Enrollements Digitals for the specified resource.
     */
    public function getEnrollsDigitals(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $enrollDigitals = Enrollmodule::with('project', 'group', 'learner', 'module')
            ->whereHas('module', function ($query) {
                $query->where(['category' => 'ENI' , 'status' => 1 ]);
            })
            ->get();
        tenancy()->end();
        return view('central.modules.digital', compact('tenant','enrollDigitals'));
    }

     /**
     * get   Enrollements Moocs for the specified resource.
     */
    public function getEnrollsLps(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        tenancy()->initialize($tenant);
            $enrollLps = Lpenroll::with('project', 'group', 'learner', 'lp')->get();
        tenancy()->end();
        return view('central.lps.enrollment', compact('tenant','enrollLps'));
    }
}
