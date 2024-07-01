<?php

namespace App\Http\Controllers\Tenant\Group;

use App\Enums\CourseStatusEnum;
use App\Exports\Group\ActiveLearnerExport;
use App\Exports\Group\CallExport;
use App\Exports\Group\CegosExport;
use App\Exports\Group\EniExport;
use App\Exports\Group\InactiveLearnerExport;
use App\Exports\Group\LearnerExport;
use App\Exports\Group\LpExport;
use App\Exports\Group\LscExport;
use App\Exports\Group\ModuleExport;
use App\Exports\Group\MoocExport;
use App\Exports\Group\SmExport;
use App\Exports\Group\SpeexExport;
use App\Exports\Group\TicketExport;
use App\Http\Controllers\Controller;
use App\Jobs\ExportActiveJob;
use App\Jobs\ExportCallJob;
use App\Jobs\ExportCegosJob;
use App\Jobs\ExportConnexionJob;
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
use App\Models\Group;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Lpenroll;
use App\Models\Module;
use App\Models\Ticket;
use App\Services\GroupeReportService;
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
        $groupId = Auth::guard('user')->user()->group_id;
        $group = Group::find($groupId);
        return view('tenant.group.home', compact('group'));
    }

    public function getData($groupId)
    {
        $group = Group::find($groupId);
        $groupReportService = new GroupeReportService();
        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $learnersInscriptionsPerStatDate = $groupReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf, $group);
        $timingDetailsPerStatDate = $groupReportService->getTimingDetailsPerStatDate($contract_start_date_conf, $enrollfields, $group);

        $learnersInscriptions = $groupReportService->getLearnersInscriptions($group);
        $timingDetails = $groupReportService->getTimingDetails($enrollfields, $group);
        $learnersCharts = $groupReportService->getLearnersCharts($categorie, $group);

        $softStats = $groupReportService->getStatSoftskills($enrollfields, $group);
        $digitalStats = $groupReportService->getStatDigital($enrollfields, $group);
        $smStats = $groupReportService->getStatSM($enrollfields, $group);
        $speexStats = $groupReportService->getStatSpeex($enrollfields, $group);
        $moocStats = $groupReportService->getStatMooc($enrollfields, $group);
        $timingChart = $groupReportService->getTimingStats($enrollfields, $group);
        $timingCalculatedChart = $groupReportService->getCalculatedTimingStats($enrollfields, $group);
        $lpStats = $groupReportService->getLpStats($enrollfields, $group);
        $lscStats = $groupReportService->getLscStats($group);

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

    public function getLanguageData($groupId, $selectedLanguage)
    {
        $groupReportService = new GroupeReportService();
        $speexChart = $groupReportService->getStatSpeexChart($groupId, $selectedLanguage);
        return response()->json($speexChart);
    }

    public function getDigitalData($groupId, $selectedDigital)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $groupReportService = new GroupeReportService();

        if ($selectedDigital != "null") {
            $digitalStats = $groupReportService->getStatDigitalPerModule($enrollfields, $selectedDigital, $groupId);
        } else {
            $group = Group::find($groupId);
            $digitalStats = $groupReportService->getStatDigital($enrollfields, $group);
        }
        return response()->json($digitalStats);
    }

    public function getSMData($groupId, $selectedSM)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $groupReportService = new GroupeReportService();
        if ($selectedSM != "null") {
            $smStats = $groupReportService->getStatSMPerModule($enrollfields, $selectedSM, $groupId);
        } else {
            $group = Group::find($groupId);
            $smStats = $groupReportService->getStatSM($enrollfields, $group);
        }
        return response()->json($smStats);
    }

    public function getLpData($groupId, $selectedLp)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $groupReportService = new GroupeReportService();

        if ($selectedLp != "null") {
            $digitalStats = $groupReportService->geStatsPerLp($enrollfields, $selectedLp, $groupId);
        } else {
            $group = Group::find($groupId);
            $digitalStats = $groupReportService->getLpStats($enrollfields, $group);
        }
        return response()->json($digitalStats);
    }


    public function getInscritsPerDate(Request $request, $groupId)
    {

        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $groupReportService = new GroupeReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $learnersInscriptions = $groupReportService->getLearnersInscriptionsPerDate($dateStart, $dateEnd, $groupId);
            $timingDetails = $groupReportService->getTimingDetailsPerDate($enrollfields, $dateStart, $dateEnd, $groupId);
            $learnersCharts = $groupReportService->getLearnersChartsPerDate($categorie, $dateStart, $dateEnd, $groupId);
        } else {
            $group = Group::find($groupId);
            $learnersInscriptions = $groupReportService->getLearnersInscriptions($group);
            $timingDetails = $groupReportService->getTimingDetails($enrollfields, $group);
            $learnersCharts = $groupReportService->getLearnersCharts($categorie, $group);
        }
        return response()->json([
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
        ]);
    }

    public function getLscPerDate(Request $request, $groupId)
    {
        $groupReportService = new GroupeReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $lscStats = $groupReportService->getLscStatsPerDate($dateStart, $dateEnd, $groupId);
        } else {
            $group = Group::find($groupId);
            $lscStats = $groupReportService->getLscStats($group);
        }

        return response()->json($lscStats);
    }

    public function exportInscrits($groupId)
    {
        return Excel::download(new LearnerExport($groupId), 'rapport_des_inscrits.xlsx');
    }

    public function exportModules($groupId)
    {
        return Excel::download(new ModuleExport($groupId), 'rapport_des_modules.xlsx');
    }

    public function exportLps($groupId)
    {
        return Excel::download(new LpExport($groupId), 'rapport_de_formation_transverse.xlsx');
    }

    public function exportLsc($groupId)
    {
        return Excel::download(new LscExport($groupId), 'rapport_learner_success_center.xlsx');
    }

    public function export(Request $request)
    {
        $rapport = $request->input('rapport');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        $groupId = $request->input('group_id');
        if ($dateDebut != null && $dateFin != null) {
            if ($rapport == 'active') {
                return Excel::download(new ActiveLearnerExport($groupId, $dateDebut, $dateFin), 'rapport_des_inscrits_actifs.xlsx');
            } elseif ($rapport == 'inactive') {
                return Excel::download(new InactiveLearnerExport($groupId, $dateDebut, $dateFin), 'rapport_des_inscrits_inactifs.xlsx');
            } elseif ($rapport == 'transverse') {
                return Excel::download(new LpExport($groupId, $dateDebut, $dateFin), 'rapport_formation_transverse.xlsx');
            } elseif ($rapport == 'cegos') {
                return Excel::download(new CegosExport($groupId, $dateDebut, $dateFin), 'rapport_formation_softskills.xlsx');
            } elseif ($rapport == 'eni') {
                return Excel::download(new EniExport($groupId, $dateDebut, $dateFin), 'rapport_formation_digitals.xlsx');
            } elseif ($rapport == 'speex') {
                return Excel::download(new SpeexExport($groupId, $dateDebut, $dateFin), 'rapport_formation_langue.xlsx');
            } elseif ($rapport == 'sm') {
                return Excel::download(new SmExport($groupId, $dateDebut, $dateFin), 'rapport_formation_surmesure.xlsx');
            } elseif ($rapport == 'mooc') {
                return Excel::download(new MoocExport($groupId, $dateDebut, $dateFin), 'rapport_formation_moocs.xlsx');
            } elseif ($rapport == 'tickets') {
                return Excel::download(new TicketExport($groupId, $dateDebut, $dateFin), 'rapport_lsc_tickets.xlsx');
            } elseif ($rapport == 'calls') {
                return Excel::download(new CallExport($groupId, $dateDebut, $dateFin), 'rapport_lsc_calls.xlsx');
            }
        } else {
            if ($rapport == 'active') {
                return Excel::download(new ActiveLearnerExport($groupId), 'rapport_des_inscrits_actifs.xlsx');
            } elseif ($rapport == 'inactive') {
                return Excel::download(new InactiveLearnerExport($groupId), 'rapport_des_inscrits_inactifs.xlsx');
            } elseif ($rapport == 'transverse') {
                return Excel::download(new LpExport($groupId), 'rapport_formation_transverse.xlsx');
            } elseif ($rapport == 'cegos') {
                return Excel::download(new CegosExport($groupId), 'rapport_formation_softskills.xlsx');
            } elseif ($rapport == 'eni') {
                return Excel::download(new EniExport($groupId), 'rapport_formation_digitals.xlsx');
            } elseif ($rapport == 'speex') {
                return Excel::download(new SpeexExport($groupId), 'rapport_formation_langue.xlsx');
            } elseif ($rapport == 'sm') {
                return Excel::download(new SmExport($groupId), 'rapport_formation_surmesure.xlsx');
            } elseif ($rapport == 'mooc') {
                return Excel::download(new MoocExport($groupId), 'rapport_formation_moocs.xlsx');
            } elseif ($rapport == 'tickets') {
                return Excel::download(new TicketExport($groupId), 'rapport_lsc_tickets.xlsx');
            } elseif ($rapport == 'calls') {
                return Excel::download(new CallExport($groupId), 'rapport_lsc_calls.xlsx');
            }
        }
    }

    public function export2(Request $request)
    {
        $rapport = $request->input('rapport');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        $groupId = $request->input('group_id');

        $userfields = config('tenantconfigfields.userfields');
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $archive = config('tenantconfigfields.archive');
        $group = Group::find($groupId);

        if ($rapport == 'cegos') {
            $date = date('Ymd_His');
            $filename = 'rapport_formation_softskills' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['module_docebo_id'] = 'Module';
            $fields['learner_docebo_id'] = 'Username';
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

            $softModules = $group->modules->filter(function ($module) {
                return $module->category === 'CEGOS' && $module->status === CourseStatusEnum::ACTIVE;
            })->pluck('docebo_id')->toArray();

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $dateDebut, $dateFin, $group) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $dateDebut, $dateFin, $group) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                            ;
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $group) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $group) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->where('group_id', $group->id)->get();
                }
            }

            ExportCegosJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations softskills",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($softEnrolls, $fields, $filename);
        } elseif ($rapport == 'eni') {
            $date = date('Ymd_His');
            $filename = 'rapport_formation_digitals_' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['module_docebo_id'] = 'Module';
            $fields['learner_docebo_id'] = 'Username';
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

            $softModules = $group->modules->filter(function ($module) {
                return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
            })->pluck('docebo_id')->toArray();

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $dateDebut, $dateFin, $group) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $dateDebut, $dateFin, $group) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                            ;
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $group) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $group) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->where('group_id', $group->id)->get();
                }
            }

            ExportEniJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations digitals",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($softEnrolls, $fields, $filename);
        } elseif ($rapport == 'sm') {
            $date = date('Ymd_His');
            $filename = 'rapport_formation_surmesure' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['module_docebo_id'] = 'Module';
            $fields['learner_docebo_id'] = 'Username';
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

            $softModules = $group->modules->filter(function ($module) {
                return $module->category === 'SM' && $module->status === CourseStatusEnum::ACTIVE;
            })->pluck('docebo_id')->toArray();

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $dateDebut, $dateFin, $group) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $dateDebut, $dateFin, $group) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                            ;
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $group) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin, $group) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->where('group_id', $group->id)->get();
                }
            }

            ExportSmJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations sur mesure",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($softEnrolls, $fields, $filename);
        } elseif ($rapport == 'mooc') {
            $date = date('Ymd_His');
            $filename = 'rapport_formation_mooc' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['mooc_docebo_id'] = 'Mooc';
            $fields['learner_docebo_id'] = 'Username';
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
                    $moocEnrolls = Enrollmooc::where(function ($query) use ($dateDebut, $dateFin, $group) {
                        $query->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                        ;
                    })
                        ->orWhere(function ($query) use ($dateDebut, $dateFin, $group) {
                            $query->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                            ;
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $moocEnrolls = Enrollmooc::where(function ($query) use ($learnersIds, $dateDebut, $dateFin, $group) {
                        $query->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                        ;
                    })
                        ->orWhere(function ($query) use ($learnersIds, $dateDebut, $dateFin, $group) {
                            $query->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                            ;
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $moocEnrolls = Enrollmooc::where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $moocEnrolls = Enrollmooc::whereIn('learner_docebo_id', $learnersIds)->where('group_id', $group->id)->get();
                }
            }
            ExportMoocJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations mooc",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($moocEnrolls, $fields, $filename);
        } elseif ($rapport == 'transverse') {
            $date = date('Ymd_His');
            $filename = 'rapport_formation_transverse' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['lp_docebo_id'] = 'Plan de formation';
            $fields['learner_docebo_id'] = 'Username';
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
                    $lpEnrolls = Lpenroll::where(function ($query) use ($dateDebut, $dateFin, $group) {
                        $query->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($dateDebut, $dateFin, $group) {
                            $query->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $lpEnrolls = Lpenroll::where(function ($query) use ($learnersIds, $dateDebut, $dateFin, $group) {
                        $query->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($learnersIds, $dateDebut, $dateFin, $group) {
                            $query->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $lpEnrolls = Lpenroll::where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $lpEnrolls = Lpenroll::whereIn('learner_docebo_id', $learnersIds)->where('group_id', $group->id)->get();
                }
            }
            ExportTransverseJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations transverses",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($lpEnrolls, $fields, $filename);
        } elseif ($rapport == 'active') {
            $date = date('Ymd_His');
            $filename = 'rapport_inscrits_actifs' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['username'] = 'Username';
            $fields['lastname'] = 'Nom';
            $fields['firstname'] = 'Prénom';
            $fields['creation_date'] = 'Date de création';
            $fields['last_access_date'] = 'Date du dernier accès';

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
                $learners = Learner::where('statut', 'active')->where('group_id', $group->id)->whereBetween('last_access_date', [$dateDebut, $dateFin])->get();
            } else {
                $learners = Learner::where('statut', 'active')->where('group_id', $group->id)->get();
            }

            ExportActiveJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des inscrits actifs",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($learners, $fields, $filename);

        } elseif ($rapport == 'inactive') {
            $date = date('Ymd_His');
            $filename = 'rapport_inscrits_inactifs' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['username'] = 'Username';
            $fields['lastname'] = 'Nom';
            $fields['firstname'] = 'Prénom';
            $fields['creation_date'] = 'Date de création';

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
                $learners = Learner::where('statut', 'inactive')->where('group_id', $group->id)->whereBetween('creation_date', [$dateDebut, $dateFin])->get();
            } else {
                $learners = Learner::where('statut', 'inactive')->where('group_id', $group->id)->get();
            }
            ExportInactiveJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des inscrits inactifs",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($learners, $fields, $filename);
        } elseif ($rapport == 'tickets') {
            $date = date('Ymd_His');
            $filename = 'rapport_tickets' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['learner_docebo_id'] = 'Username';
            $fields['status'] = 'Statut';
            $fields['subject'] = 'Sujet';
            $fields['ticket_created_at'] = 'Date de création';
            $fields['ticket_udpated_at'] = 'Date du dernière modification';

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $tickets = Ticket::whereBetween('ticket_created_at', [$dateDebut, $dateFin])->where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $tickets = Ticket::whereIn('learner_docebo_id', $learnersIds)->where('group_id', $group->id)->whereBetween('ticket_created_at', [$dateDebut, $dateFin])->get();
                }
            } else {
                if ($archive == true) {
                    $tickets = Ticket::where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $tickets = Ticket::whereIn('learner_docebo_id', $learnersIds)->where('group_id', $group->id)->get();
                }
            }
            ExportTicketsJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des tickets",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($tickets, $fields, $filename);
        } elseif ($rapport == 'calls') {
            $date = date('Ymd_His');
            $filename = 'rapport_appels_telephoniques' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['learner_docebo_id'] = 'Username';
            $fields['status'] = 'Statut';
            $fields['date_call'] = 'Date d\'appel';

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $calls = Call::whereBetween('date_call', [$dateDebut, $dateFin])->where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $calls = Call::whereIn('learner_docebo_id', $learnersIds)->where('group_id', $group->id)->whereBetween('date_call', [$dateDebut, $dateFin])->get();
                }
            } else {
                if ($archive == true) {
                    $calls = Call::where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $calls = Call::whereIn('learner_docebo_id', $learnersIds)->where('group_id', $group->id)->get();
                }
            }
            ExportCallJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des appels téléphoniques",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($calls, $fields, $filename);
        } elseif ($rapport == 'speex') {
            $date = date('Ymd_His');
            $filename = 'rapport_formation_langues' . $date . '.csv';

            $fields['project_id'] = 'Branche';
            $fields['group_id'] = 'Filiale';
            $fields['module_docebo_id'] = 'Module';
            $fields['learner_docebo_id'] = 'Username';
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
            $speexModules = $group->modules->filter(function ($module) {
                return $module->category === 'SPEEX' && $module->status === CourseStatusEnum::ACTIVE;
            })->pluck('docebo_id')->toArray();

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $speexEnrolls = Enrollmodule::where(function ($query) use ($speexModules, $dateDebut, $dateFin, $group) {
                        $query->whereIn('module_docebo_id', $speexModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($speexModules, $dateDebut, $dateFin, $group) {
                            $query->whereIn('module_docebo_id', $speexModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $speexEnrolls = Langenroll::where(function ($query) use ($speexModules, $learnersIds, $dateDebut, $dateFin, $group) {
                        $query->whereIn('module_docebo_id', $speexModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin])
                            ->where('group_id', $group->id);
                    })
                        ->orWhere(function ($query) use ($speexModules, $learnersIds, $dateDebut, $dateFin, $group) {
                            $query->whereIn('module_docebo_id', $speexModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin])
                                ->where('group_id', $group->id);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $speexEnrolls = Langenroll::whereIn('module_docebo_id', $speexModules)->where('group_id', $group->id)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $speexEnrolls = Langenroll::whereIn('module_docebo_id', $speexModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->where('group_id', $group->id)->get();
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