<?php

namespace App\Http\Controllers\Tenant\Project;

use App\Enums\CourseStatusEnum;
use App\Exports\Project\ActiveLearnerExport;
use App\Exports\Project\CallExport;
use App\Exports\Project\CegosExport;
use App\Exports\Project\EniExport;
use App\Exports\Project\InactiveLearnerExport;
use App\Exports\Project\LearnerExport;
use App\Exports\Project\LpExport;
use App\Exports\Project\LscExport;
use App\Exports\Project\ModuleExport;
use App\Exports\Project\MoocExport;
use App\Exports\Project\SmExport;
use App\Exports\Project\SpeexExport;
use App\Exports\Project\TicketExport;
use App\Http\Controllers\Controller;
use App\Jobs\ExportActiveJob;
use App\Jobs\ExportCallJob;
use App\Jobs\ExportCegosJob;
use App\Jobs\ExportEniJob;
use App\Jobs\ExportInactiveJob;
use App\Jobs\ExportMoocJob;
use App\Jobs\ExportSmJob;
use App\Jobs\ExportSpeexJob;
use App\Jobs\ExportTicketsJob;
use App\Jobs\ExportTransverseJob;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Models\Call;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Lpenroll;
use App\Models\Project;
use App\Models\Ticket;
use App\Services\ProjectReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Show the User dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $projectId = Auth::guard('user')->user()->project_id;
        $project = Project::find($projectId);
        return view('tenant.project.home', compact('project'));
    }

    public function getData($projectId)
    {
        $project = Project::find($projectId);
        $projectReportService = new ProjectReportService();
        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $learnersInscriptionsPerStatDate = $projectReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf, $project);
        $timingDetailsPerStatDate = $projectReportService->getTimingDetailsPerStatDate($contract_start_date_conf, $enrollfields, $project);

        $learnersInscriptions = $projectReportService->getLearnersInscriptions($project);
        $timingDetails = $projectReportService->getTimingDetails($enrollfields, $project);
        $learnersCharts = $projectReportService->getLearnersCharts($categorie, $project);

        $softStats = $projectReportService->getStatSoftskills($enrollfields, $project);
        $digitalStats = $projectReportService->getStatDigital($enrollfields, $project);
        $smStats = $projectReportService->getStatSM($enrollfields, $project);
        $speexStats = $projectReportService->getStatSpeex($enrollfields, $project);
        $moocStats = $projectReportService->getStatMooc($enrollfields, $project);
        $timingChart = $projectReportService->getTimingStats($enrollfields, $project);
        $timingCalculatedChart = $projectReportService->getCalculatedTimingStats($enrollfields, $project);
        $lpStats = $projectReportService->getLpStats($enrollfields, $project);
        $lscStats = $projectReportService->getLscStats($project);

        return response()->json([
            'learnersInscriptionsPerStatDate' => $learnersInscriptionsPerStatDate,
            'timingDetailsPerStatDate' => $timingDetailsPerStatDate,
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
            'softStats' => $softStats,
            'digitalStats' => $digitalStats,
            'speexStats' => $speexStats,
            'moocStats' => $moocStats,
            'timingChart' => $timingChart,
            'timingCalculatedChart' => $timingCalculatedChart,
            'lpStats' => $lpStats,
            'lscStats' => $lscStats,
            'smStats' => $smStats
        ]);
    }

    public function getLanguageData($projectId, $selectedLanguage)
    {
        $projectReportService = new ProjectReportService();
        $speexChart = $projectReportService->getStatSpeexChart($projectId, $selectedLanguage);
        return response()->json($speexChart);
    }

    public function getDigitalData($projectId, $selectedDigital)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $projectReportService = new ProjectReportService();

        if ($selectedDigital != "null") {
            $digitalStats = $projectReportService->getStatDigitalPerModule($enrollfields, $selectedDigital, $projectId);
        } else {
            $project = Project::find($projectId);
            $digitalStats = $projectReportService->getStatDigital($enrollfields, $project);
        }
        return response()->json($digitalStats);
    }

    public function getSMData($projectId, $selectedSM)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $projectReportService = new ProjectReportService();
        if ($selectedSM != "null") {
            $smStats = $projectReportService->getStatSMPerModule($enrollfields, $selectedSM, $projectId);
        } else {
            $project = Project::find($projectId);
            $smStats = $projectReportService->getStatSM($enrollfields, $project);
        }
        return response()->json($smStats);
    }

    public function getLpData($projectId, $selectedLp)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $projectReportService = new ProjectReportService();

        if ($selectedLp != "null") {
            $digitalStats = $projectReportService->geStatsPerLp($enrollfields, $selectedLp, $projectId);
        } else {
            $project = Project::find($projectId);
            $digitalStats = $projectReportService->getLpStats($enrollfields, $project);
        }
        return response()->json($digitalStats);
    }


    public function getInscritsPerDate(Request $request, $projectId)
    {

        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $projectReportService = new ProjectReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $learnersInscriptions = $projectReportService->getLearnersInscriptionsPerDate($dateStart, $dateEnd, $projectId);
            $timingDetails = $projectReportService->getTimingDetailsPerDate($enrollfields, $dateStart, $dateEnd, $projectId);
            $learnersCharts = $projectReportService->getLearnersChartsPerDate($categorie, $dateStart, $dateEnd, $projectId);
        } else {
            $project = Project::find($projectId);
            $learnersInscriptions = $projectReportService->getLearnersInscriptions($project);
            $timingDetails = $projectReportService->getTimingDetails($enrollfields, $project);
            $learnersCharts = $projectReportService->getLearnersCharts($categorie, $project);
        }
        return response()->json([
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
        ]);
    }

    public function getLscPerDate(Request $request, $projectId)
    {
        $projectReportService = new ProjectReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $lscStats = $projectReportService->getLscStatsPerDate($dateStart, $dateEnd, $projectId);
        } else {
            $project = Project::find($projectId);
            $lscStats = $projectReportService->getLscStats($project);
        }

        return response()->json($lscStats);
    }

    public function exportInscrits($projectId)
    {
        return Excel::download(new LearnerExport($projectId), 'rapport_des_inscrits.xlsx');
    }

    public function exportModules($projectId)
    {
        return Excel::download(new ModuleExport($projectId), 'rapport_des_modules.xlsx');
    }

    public function exportLps($projectId)
    {
        return Excel::download(new LpExport($projectId), 'rapport_de_formation_transverse.xlsx');
    }

    public function exportLsc($projectId)
    {
        return Excel::download(new LscExport($projectId), 'rapport_learner_success_center.xlsx');
    }

    public function export(Request $request)
    {
        $rapport = $request->input('rapport');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        $projectId = $request->input('project_id');
        if ($dateDebut != null && $dateFin != null) {
            if ($rapport == 'active') {
                return Excel::download(new ActiveLearnerExport($projectId, $dateDebut, $dateFin), 'rapport_des_inscrits_actifs.xlsx');
            } elseif ($rapport == 'inactive') {
                return Excel::download(new InactiveLearnerExport($projectId, $dateDebut, $dateFin), 'rapport_des_inscrits_inactifs.xlsx');
            } elseif ($rapport == 'transverse') {
                return Excel::download(new LpExport($projectId, $dateDebut, $dateFin), 'rapport_formation_transverse.xlsx');
            } elseif ($rapport == 'cegos') {
                return Excel::download(new CegosExport($projectId, $dateDebut, $dateFin), 'rapport_formation_softskills.xlsx');
            } elseif ($rapport == 'eni') {
                return Excel::download(new EniExport($projectId, $dateDebut, $dateFin), 'rapport_formation_digitals.xlsx');
            } elseif ($rapport == 'speex') {
                return Excel::download(new SpeexExport($projectId, $dateDebut, $dateFin), 'rapport_formation_langue.xlsx');
            } elseif ($rapport == 'sm') {
                return Excel::download(new SmExport($projectId, $dateDebut, $dateFin), 'rapport_formation_surmesure.xlsx');
            } elseif ($rapport == 'mooc') {
                return Excel::download(new MoocExport($projectId, $dateDebut, $dateFin), 'rapport_formation_moocs.xlsx');
            } elseif ($rapport == 'tickets') {
                return Excel::download(new TicketExport($projectId, $dateDebut, $dateFin), 'rapport_lsc_tickets.xlsx');
            } elseif ($rapport == 'calls') {
                return Excel::download(new CallExport($projectId, $dateDebut, $dateFin), 'rapport_lsc_calls.xlsx');
            }
        } else {
            if ($rapport == 'active') {
                return Excel::download(new ActiveLearnerExport($projectId), 'rapport_des_inscrits_actifs.xlsx');
            } elseif ($rapport == 'inactive') {
                return Excel::download(new InactiveLearnerExport($projectId), 'rapport_des_inscrits_inactifs.xlsx');
            } elseif ($rapport == 'transverse') {
                return Excel::download(new LpExport($projectId), 'rapport_formation_transverse.xlsx');
            } elseif ($rapport == 'cegos') {
                return Excel::download(new CegosExport($projectId), 'rapport_formation_softskills.xlsx');
            } elseif ($rapport == 'eni') {
                return Excel::download(new EniExport($projectId), 'rapport_formation_digitals.xlsx');
            } elseif ($rapport == 'speex') {
                return Excel::download(new SpeexExport($projectId), 'rapport_formation_langue.xlsx');
            } elseif ($rapport == 'sm') {
                return Excel::download(new SmExport($projectId), 'rapport_formation_surmesure.xlsx');
            } elseif ($rapport == 'mooc') {
                return Excel::download(new MoocExport($projectId), 'rapport_formation_moocs.xlsx');
            } elseif ($rapport == 'tickets') {
                return Excel::download(new TicketExport($projectId), 'rapport_lsc_tickets.xlsx');
            } elseif ($rapport == 'calls') {
                return Excel::download(new CallExport($projectId), 'rapport_lsc_calls.xlsx');
            }
        }
    }

    public function export2(Request $request)
    {
        $rapport = $request->input('rapport');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        $projectId = $request->input('project_id');

        $userfields = config('tenantconfigfields.userfields');
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $archive = config('tenantconfigfields.archive');
        $project = Project::find($projectId);

        if ($rapport == 'cegos') {
            $filename = 'rapport_formation_softskills.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['module_docebo_id'] = 'Module';
            $fields['learner_docebo_id'] = 'Username';
            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $fields['matricule'] = 'Matricule';
            }
            $fields['enrollment_created_at'] = 'Date d\'inscription';
            $fields['status'] = 'Statut';
            $fields['enrollment_updated_at'] = 'Date du dernière modification';
            $fields['enrollment_completed_at'] = 'Date d\'achèvement';
            $fields['session_time'] = 'Temps de session';
            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $fields['cmi_time'] = 'Temps d\'engagement';
            }
            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $fields['calculated_time'] = 'Temps calculé';
            }
            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $fields['recommended_time'] = 'Temps pédagogique recommandé';
            }

            $softModules = $project->modules->filter(function ($module) {
                return $module->category === 'CEGOS' && $module->status === CourseStatusEnum::ACTIVE;
            })->pluck('docebo_id')->toArray();

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $dateDebut, $dateFin, $project) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $dateDebut, $dateFin, $project) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                            ;
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $project) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $project) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->where('project_id', $project->id)->get();
                }
            }

            ExportCegosJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations softskills",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($softEnrolls, $fields, $filename);
        } elseif ($rapport == 'eni') {
            $filename = 'rapport_formation_digitals.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['module_docebo_id'] = 'Module';
            $fields['learner_docebo_id'] = 'Username';
            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $fields['matricule'] = 'Matricule';
            }
            $fields['enrollment_created_at'] = 'Date d\'inscription';
            $fields['status'] = 'Statut';
            $fields['enrollment_updated_at'] = 'Date du dernière modification';
            $fields['enrollment_completed_at'] = 'Date d\'achèvement';
            $fields['session_time'] = 'Temps de session';
            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $fields['cmi_time'] = 'Temps d\'engagement';
            }
            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $fields['calculated_time'] = 'Temps calculé';
            }
            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $fields['recommended_time'] = 'Temps pédagogique recommandé';
            }

            $softModules = $project->modules->filter(function ($module) {
                return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
            })->pluck('docebo_id')->toArray();

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $dateDebut, $dateFin, $project) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $dateDebut, $dateFin, $project) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                            ;
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $project) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $project) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->where('project_id', $project->id)->get();
                }
            }

            ExportEniJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations digitals",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($softEnrolls, $fields, $filename);
        } elseif ($rapport == 'sm') {
            $filename = 'rapport_formation_surmesure.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['module_docebo_id'] = 'Module';
            $fields['learner_docebo_id'] = 'Username';
            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $fields['matricule'] = 'Matricule';
            }
            $fields['enrollment_created_at'] = 'Date d\'inscription';
            $fields['status'] = 'Statut';
            $fields['enrollment_updated_at'] = 'Date du dernière modification';
            $fields['enrollment_completed_at'] = 'Date d\'achèvement';
            $fields['session_time'] = 'Temps de session';
            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $fields['cmi_time'] = 'Temps d\'engagement';
            }
            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $fields['calculated_time'] = 'Temps calculé';
            }
            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $fields['recommended_time'] = 'Temps pédagogique recommandé';
            }

            $softModules = $project->modules->filter(function ($module) {
                return $module->category === 'SM' && $module->status === CourseStatusEnum::ACTIVE;
            })->pluck('docebo_id')->toArray();

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $dateDebut, $dateFin, $project) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $dateDebut, $dateFin, $project) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                            ;
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $project) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $project) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->where('project_id', $project->id)->get();
                }
            }

            ExportSmJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations sur mesure",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($softEnrolls, $fields, $filename);
        } elseif ($rapport == 'mooc') {
            $filename = 'rapport_formation_mooc.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['mooc_docebo_id'] = 'Mooc';
            $fields['learner_docebo_id'] = 'Username';
            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $fields['matricule'] = 'Matricule';
            }
            $fields['enrollment_created_at'] = 'Date d\'inscription';
            $fields['status'] = 'Statut';
            $fields['enrollment_updated_at'] = 'Date du dernière modification';
            $fields['enrollment_completed_at'] = 'Date d\'achèvement';
            $fields['session_time'] = 'Temps de session';
            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $fields['cmi_time'] = 'Temps d\'engagement';
            }
            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $fields['calculated_time'] = 'Temps calculé';
            }
            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $fields['recommended_time'] = 'Temps pédagogique recommandé';
            }

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $moocEnrolls = Enrollmooc::where(function ($query) use ($dateDebut, $dateFin, $project) {
                        $query->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                        ;
                    })
                        ->orWhere(function ($query) use ($dateDebut, $dateFin, $project) {
                            $query->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                            ;
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $moocEnrolls = Enrollmooc::where(function ($query) use ($learnersIds, $dateDebut, $dateFin, $project) {
                        $query->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                        ;
                    })
                        ->orWhere(function ($query) use ($learnersIds, $dateDebut, $dateFin, $project) {
                            $query->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                            ;
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $moocEnrolls = Enrollmooc::where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $moocEnrolls = Enrollmooc::whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
                }
            }
            ExportMoocJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations mooc",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($moocEnrolls, $fields, $filename);
        } elseif ($rapport == 'transverse') {
            $filename = 'rapport_formation_transverse.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['lp_docebo_id'] = 'Plan de formation';
            $fields['learner_docebo_id'] = 'Username';
            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $fields['matricule'] = 'Matricule';
            }
            $fields['enrollment_created_at'] = 'Date d\'inscription';
            $fields['status'] = 'Statut';
            $fields['enrollment_completion_percentage'] = 'Avancement';
            $fields['enrollment_updated_at'] = 'Date du dernière modification';
            $fields['enrollment_completed_at'] = 'Date d\'achèvement';
            $fields['session_time'] = 'Temps de session';
            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $fields['cmi_time'] = 'Temps d\'engagement';
            }
            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $fields['calculated_time'] = 'Temps calculé';
            }
            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $fields['recommended_time'] = 'Temps pédagogique recommandé';
            }

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $lpEnrolls = Lpenroll::where(function ($query) use ($dateDebut, $dateFin, $project) {
                        $query->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($dateDebut, $dateFin, $project) {
                            $query->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $lpEnrolls = Lpenroll::where(function ($query) use ($learnersIds, $dateDebut, $dateFin, $project) {
                        $query->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($learnersIds, $dateDebut, $dateFin, $project) {
                            $query->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $lpEnrolls = Lpenroll::where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $lpEnrolls = Lpenroll::whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
                }
            }
            ExportTransverseJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations transverses",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($lpEnrolls, $fields, $filename);
        } elseif ($rapport == 'active') {

            $filename = 'rapport_inscrits_actifs.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['username'] = 'Username';
            $fields['lastname'] = 'Nom';
            $fields['firstname'] = 'Prénom';
            $fields['creation_date'] = 'Date de création';
            $fields['last_access_date'] = 'Date du dernier accès';
            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $fields['matricule'] = 'Matricule';
            }

            if (isset($userfields['fonction']) && $userfields['fonction'] === true) {
                $fields['fonction'] = 'Fonction';
            }

            if (isset($userfields['direction']) && $userfields['direction'] === true) {
                $fields['direction'] = 'Direction';
            }

            if (isset($userfields['categorie']) && $userfields['categorie'] === true) {
                $fields['categorie'] = 'Categorie';
            }

            if (isset($userfields['sexe']) && $userfields['sexe'] === true) {
                $fields['sexe'] = 'Sexe';
            }
            $fields['session_time'] = 'Heures sessions';
            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $fields['cmi_time'] = 'Heures d\'engagement';
            }
            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $fields['calculated_time'] = 'Heures calculé';
            }
            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $fields['recommended_time'] = 'Heures pédagogique recommandé';
            }
            $fields['count_ticket'] = 'Total des tickets';
            $fields['count_call'] = 'Total des appels';


            if ($dateDebut != null && $dateFin != null) {
                $learners = Learner::where('statut', 'active')->where('project_id', $project->id)->whereBetween('last_access_date', [$dateDebut, $dateFin])->get();
            } else {
                $learners = Learner::where('statut', 'active')->where('project_id', $project->id)->get();
            }

            ExportActiveJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des inscrits actifs",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($learners, $fields, $filename);

        } elseif ($rapport == 'inactive') {
            $filename = 'rapport_inscrits_inactifs.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['username'] = 'Username';
            $fields['lastname'] = 'Nom';
            $fields['firstname'] = 'Prénom';
            $fields['creation_date'] = 'Date de création';
            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $fields['matricule'] = 'Matricule';
            }

            if (isset($userfields['fonction']) && $userfields['fonction'] === true) {
                $fields['fonction'] = 'Fonction';
            }

            if (isset($userfields['direction']) && $userfields['direction'] === true) {
                $fields['direction'] = 'Direction';
            }

            if (isset($userfields['categorie']) && $userfields['categorie'] === true) {
                $fields['categorie'] = 'Categorie';
            }

            if (isset($userfields['sexe']) && $userfields['sexe'] === true) {
                $fields['sexe'] = 'Sexe';
            }
            if ($dateDebut != null && $dateFin != null) {
                $learners = Learner::where('statut', 'inactive')->where('project_id', $project->id)->whereBetween('creation_date', [$dateDebut, $dateFin])->get();
            } else {
                $learners = Learner::where('statut', 'inactive')->where('project_id', $project->id)->get();
            }
            ExportInactiveJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des inscrits inactifs",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($learners, $fields, $filename);
        } elseif ($rapport == 'tickets') {
            $filename = 'rapport_tickets.csv';
            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['learner_docebo_id'] = 'Username';
            $fields['status'] = 'Statut';
            $fields['subject'] = 'Sujet';
            $fields['ticket_created_at'] = 'Date de création';
            $fields['ticket_udpated_at'] = 'Date du dernière modification';

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $tickets = Ticket::whereBetween('ticket_created_at', [$dateDebut, $dateFin])->where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $tickets = Ticket::whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->whereBetween('ticket_created_at', [$dateDebut, $dateFin])->get();
                }
            } else {
                if ($archive == true) {
                    $tickets = Ticket::where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $tickets = Ticket::whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
                }
            }
            ExportTicketsJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des tickets",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($tickets, $fields, $filename);
        } elseif ($rapport == 'calls') {
            $filename = 'rapport_appels_telephoniques.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['learner_docebo_id'] = 'Username';
            $fields['status'] = 'Statut';
            $fields['date_call'] = 'Date d\'appel';

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $calls = Call::whereBetween('date_call', [$dateDebut, $dateFin])->where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $calls = Call::whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->whereBetween('date_call', [$dateDebut, $dateFin])->get();
                }
            } else {
                if ($archive == true) {
                    $calls = Call::where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $calls = Call::whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
                }
            }
            ExportCallJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des appels téléphoniques",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($calls, $fields, $filename);
        } elseif ($rapport == 'speex') {
            $filename = 'rapport_formation_langues.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['module_docebo_id'] = 'Module';
            $fields['learner_docebo_id'] = 'Username';
            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $fields['matricule'] = 'Matricule';
            }
            $fields['enrollment_created_at'] = 'Date d\'inscription';
            $fields['status'] = 'Statut';
            $fields['niveau'] = 'Niveau';
            $fields['language'] = 'Langue';
            $fields['enrollment_updated_at'] = 'Date du dernière modification';
            $fields['enrollment_completed_at'] = 'Date d\'achèvement';
            $fields['session_time'] = 'Temps de session';
            if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                $fields['cmi_time'] = 'Temps d\'engagement';
            }
            if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                $fields['calculated_time'] = 'Temps calculé';
            }
            if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                $fields['recommended_time'] = 'Temps pédagogique recommandé';
            }
            $speexModules = $project->modules->filter(function ($module) {
                return $module->category === 'SPEEX' && $module->status === CourseStatusEnum::ACTIVE;
            })->pluck('docebo_id')->toArray();

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $speexEnrolls = Enrollmodule::where(function ($query) use ($speexModules, $dateDebut, $dateFin, $project) {
                        $query->whereIn('module_docebo_id', $speexModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($speexModules, $dateDebut, $dateFin, $project) {
                            $query->whereIn('module_docebo_id', $speexModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $speexEnrolls = Langenroll::where(function ($query) use ($speexModules, $learnersIds, $dateDebut, $dateFin, $project) {
                        $query->whereIn('module_docebo_id', $speexModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('project_id', $project->id);
                    })
                        ->orWhere(function ($query) use ($speexModules, $learnersIds, $dateDebut, $dateFin, $project) {
                            $query->whereIn('module_docebo_id', $speexModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('project_id', $project->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $speexEnrolls = Langenroll::whereIn('module_docebo_id', $speexModules)->where('project_id', $project->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $speexEnrolls = Langenroll::whereIn('module_docebo_id', $speexModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->where('project_id', $project->id)->get();
                }
            }
            ExportSpeexJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations langues",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($speexEnrolls, $fields, $filename);
        }
        return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::guard('user')->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
}