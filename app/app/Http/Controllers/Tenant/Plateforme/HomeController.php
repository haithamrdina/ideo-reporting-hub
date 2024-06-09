<?php

namespace App\Http\Controllers\Tenant\Plateforme;

use App\Charts\InscritPerCategory;
use App\Charts\InscritPerCategoryAndStatus;
use App\Enums\CourseStatusEnum;
use App\Exports\ActiveLearnerExport;
use App\Exports\CallExport;
use App\Exports\CegosExport;
use App\Exports\ConnexionExport;
use App\Exports\EniExport;
use App\Exports\GamificationExport;
use App\Exports\InactiveLearnerExport;
use App\Exports\LearnerExport;
use App\Exports\LpExport;
use App\Exports\LscExport;
use App\Exports\ModuleExport;
use App\Exports\MoocExport;
use App\Exports\SmExport;
use App\Exports\SpeexExport;
use App\Exports\TicketExport;
use App\Http\Controllers\Controller;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\getBadgeData;
use App\Http\Integrations\Docebo\Requests\getLeaderboardsData;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Models\Badge;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Group;
use App\Models\Learner;
use App\Models\Lpenroll;
use App\Models\Module;
use App\Services\PlateformeReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Spatie\SimpleExcel\SimpleExcelWriter;

class HomeController extends Controller
{
    /**
     * Show the User dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (tenant('gamification') == true) {
            $doceboConnector = new DoceboConnector;
            $leaderbordDataResponse = $doceboConnector->send(new getLeaderboardsData(tenant('leaderboard_id')));
            $leaderboard = $leaderbordDataResponse->dto();
            $badges = Badge::all();

            $badgeData = [];
            foreach ($badges as $badge) {
                $badgeDataResponse = $doceboConnector->send(new getBadgeData($badge->docebo_id));

                $badgeData[] = [
                    'name' => $badge->name,
                    'code' => $badge->code,
                    'points' => $badge->points,
                    'total' => $badgeDataResponse->json('data.total_count')
                ];
            }
            return view('tenant.plateforme.home', compact('leaderboard', 'badgeData'));
        } else {
            return view('tenant.plateforme.home');
        }
    }

    public function getData()
    {
        $plateformeReportService = new PlateformeReportService();

        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $learnersInscriptionsPerStatDate = $plateformeReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf);
        $timingDetailsPerStatDate = $plateformeReportService->getTimingDetailsPerStatDate($contract_start_date_conf, $enrollfields);

        $learnersInscriptions = $plateformeReportService->getLearnersInscriptions();
        $timingDetails = $plateformeReportService->getTimingDetails($enrollfields);
        $learnersCharts = $plateformeReportService->getLearnersCharts($categorie);

        $softStats = $plateformeReportService->getStatSoftskills($enrollfields);
        $digitalStats = $plateformeReportService->getStatDigital($enrollfields);
        $smStats = $plateformeReportService->getStatSM($enrollfields);
        $speexStats = $plateformeReportService->getStatSpeex($enrollfields);
        $moocStats = $plateformeReportService->getStatMooc($enrollfields);
        $timingChart = $plateformeReportService->getTimingStats($enrollfields);
        $timingCalculatedChart = $plateformeReportService->getCalculatedTimingStats($enrollfields);
        $lpStats = $plateformeReportService->getLpStats($enrollfields);
        $lscStats = $plateformeReportService->getLscStats();

        return response()->json([
            'learnersInscriptionsPerStatDate' => $learnersInscriptionsPerStatDate,
            'timingDetailsPerStatDate' => $timingDetailsPerStatDate,
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
            'softStats' => $softStats,
            'digitalStats' => $digitalStats,
            'smStats' => $smStats,
            'speexStats' => $speexStats,
            'moocStats' => $moocStats,
            'timingCalculatedChart' => $timingCalculatedChart,
            'timingChart' => $timingChart,
            'lpStats' => $lpStats,
            'lscStats' => $lscStats,

        ]);
    }

    public function getLanguageData($selectedLanguage)
    {
        $plateformeReportService = new PlateformeReportService();
        $speexChart = $plateformeReportService->getStatSpeexChart($selectedLanguage);
        return response()->json($speexChart);
    }

    public function getDigitalData($selectedDigital)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $plateformeReportService = new PlateformeReportService();
        if ($selectedDigital != "null") {
            $digitalStats = $plateformeReportService->getStatDigitalPerModule($enrollfields, $selectedDigital);
        } else {
            $digitalStats = $plateformeReportService->getStatDigital($enrollfields);
        }
        return response()->json($digitalStats);
    }

    public function getSMData($selectedSM)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $plateformeReportService = new PlateformeReportService();
        if ($selectedSM != "null") {
            $smStats = $plateformeReportService->getStatSMPerModule($enrollfields, $selectedSM);
        } else {
            $smStats = $plateformeReportService->getStatSM($enrollfields);
        }
        return response()->json($smStats);
    }

    public function getLpData($selectedLp)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $plateformeReportService = new PlateformeReportService();
        if ($selectedLp != "null") {
            $digitalStats = $plateformeReportService->geStatsPerLp($enrollfields, $selectedLp);
        } else {
            $digitalStats = $plateformeReportService->getLpStats($enrollfields);
        }
        return response()->json($digitalStats);
    }

    public function getInscritsPerDate(Request $request)
    {

        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $plateformeReportService = new PlateformeReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $learnersInscriptions = $plateformeReportService->getLearnersInscriptionsPerDate($dateStart, $dateEnd);
            $timingDetails = $plateformeReportService->getTimingDetailsPerDate($enrollfields, $dateStart, $dateEnd);
            $learnersCharts = $plateformeReportService->getLearnersChartsPerDate($categorie, $dateStart, $dateEnd);
        } else {
            $learnersInscriptions = $plateformeReportService->getLearnersInscriptions();
            $timingDetails = $plateformeReportService->getTimingDetails($enrollfields);
            $learnersCharts = $plateformeReportService->getLearnersCharts($categorie);
        }
        return response()->json([
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
        ]);
    }

    public function getLscPerDate(Request $request)
    {
        $plateformeReportService = new PlateformeReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $lscStats = $plateformeReportService->getLscStatsPerDate($dateStart, $dateEnd);
        } else {
            $lscStats = $plateformeReportService->getLscStats();
        }

        return response()->json($lscStats);
    }

    public function exportInscrits()
    {
        return Excel::download(new LearnerExport, 'rapport_des_inscrits.xlsx');
        // (new LearnerExport())->queue('rapport_des_inscrits.xlsx')->chain([
        //     new NotifyUserOfCompletedExport(Auth::guard('user')->user(), ["name" => "des inscrits", "link" => tenant_asset('rapport_des_inscrits.xlsx')]),
        // ]);

        // return back();
    }

    public function exportModules()
    {
        return Excel::download(new ModuleExport, 'rapport_des_modules.xlsx');
    }

    public function exportLps()
    {
        // (new LpExport())->store('rapport_formation_transverse.xlsx');
        // return back();
        return Excel::download(new LpExport, 'rapport_de_formation_transverse.xlsx');
    }

    public function exportLsc()
    {
        return Excel::download(new LscExport, 'rapport_learner_success_center.xlsx');
    }

    public function exportGamification()
    {
        $badgesIDs = Badge::pluck('id')->toArray();
        return Excel::download(new GamificationExport($badgesIDs), 'rapport_gamification.xlsx');
    }

    public function export(Request $request)
    {
        $rapport = $request->input('rapport');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        if ($dateDebut != null && $dateFin != null) {
            if ($rapport == 'connexion') {
                return Excel::download(new ConnexionExport($dateDebut, $dateFin), 'rapport_des_connexions.xlsx');
            } elseif ($rapport == 'active') {
                return Excel::download(new ActiveLearnerExport($dateDebut, $dateFin), 'rapport_des_inscrits_actifs.xlsx');
            } elseif ($rapport == 'inactive') {
                return Excel::download(new InactiveLearnerExport($dateDebut, $dateFin), 'rapport_des_inscrits_inactifs.xlsx');
            } elseif ($rapport == 'transverse') {
                return Excel::download(new LpExport($dateDebut, $dateFin), 'rapport_formation_transverse.xlsx');
                // (new LpExport($dateDebut, $dateFin))->queue('rapport_formation_transverse.xlsx')->chain([
                //     new NotifyUserOfCompletedExport(Auth::guard('user')->user(), ["name" => "des formations transverses", "link" => tenant_asset('rapport_formation_transverse.xlsx')]),
                // ]);
                // return back();
            } elseif ($rapport == 'cegos') {
                return Excel::download(new CegosExport($dateDebut, $dateFin), 'rapport_formation_softskills.xlsx');
                // (new CegosExport($dateDebut, $dateFin))->queue('rapport_formation_softskills.xlsx')->chain([
                //     new NotifyUserOfCompletedExport(Auth::guard('user')->user(), ["name" => "des formations softskills", "link" => tenant_asset('rapport_formation_softskills.xlsx')]),
                // ]);

                // return back();
            } elseif ($rapport == 'eni') {
                return Excel::download(new EniExport($dateDebut, $dateFin), 'rapport_formation_digitals.xlsx');
            } elseif ($rapport == 'speex') {
                return Excel::download(new SpeexExport($dateDebut, $dateFin), 'rapport_formation_langue.xlsx');
            } elseif ($rapport == 'sm') {
                return Excel::download(new SmExport($dateDebut, $dateFin), 'rapport_formation_surmesure.xlsx');
            } elseif ($rapport == 'mooc') {
                return Excel::download(new MoocExport($dateDebut, $dateFin), 'rapport_formation_moocs.xlsx');
            } elseif ($rapport == 'tickets') {
                return Excel::download(new TicketExport($dateDebut, $dateFin), 'rapport_lsc_tickets.xlsx');
            } elseif ($rapport == 'calls') {
                return Excel::download(new CallExport($dateDebut, $dateFin), 'rapport_lsc_calls.xlsx');
            }
        } else {
            if ($rapport == 'connexion') {
                return Excel::download(new ConnexionExport, 'rapport_des_connexions.xlsx');
            } elseif ($rapport == 'active') {
                return Excel::download(new ActiveLearnerExport, 'rapport_des_inscrits_actifs.xlsx');
            } elseif ($rapport == 'inactive') {
                return Excel::download(new InactiveLearnerExport, 'rapport_des_inscrits_inactifs.xlsx');
            } elseif ($rapport == 'transverse') {
                return Excel::download(new LpExport, 'rapport_formation_transverse.xlsx');
                //(new LpExport())->store('rapport_formation_transverse.xlsx');
                // (new LpExport())->queue('rapport_formation_transverse.xlsx')->chain([
                //     new NotifyUserOfCompletedExport(Auth::guard('user')->user(), ["name" => "des formations transverses", "link" => tenant_asset('rapport_formation_transverse.xlsx')]),
                // ]);
                // return back();
            } elseif ($rapport == 'cegos') {
                return Excel::download(new CegosExport, 'rapport_formation_softskills.xlsx');
                // (new CegosExport())->queue('rapport_formation_softskills.xlsx')->chain([
                //     new NotifyUserOfCompletedExport(Auth::guard('user')->user(), ["name" => "des formations softskills", "link" => tenant_asset('rapport_formation_softskills.xlsx')]),
                // ]);
                // // (new CegosExport())->store('rapport_formation_softskills.xlsx');
                // return back();
            } elseif ($rapport == 'eni') {
                return Excel::download(new EniExport, 'rapport_formation_digitals.xlsx');
            } elseif ($rapport == 'speex') {
                return Excel::download(new SpeexExport, 'rapport_formation_langue.xlsx');
            } elseif ($rapport == 'sm') {
                return Excel::download(new SmExport, 'rapport_formation_surmesure.xlsx');
            } elseif ($rapport == 'mooc') {
                return Excel::download(new MoocExport, 'rapport_formation_moocs.xlsx');
            } elseif ($rapport == 'tickets') {
                return Excel::download(new TicketExport, 'rapport_lsc_tickets.xlsx');
            } elseif ($rapport == 'calls') {
                return Excel::download(new CallExport, 'rapport_lsc_calls.xlsx');
            }
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::guard('user')->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }


    public function export2(Request $request)
    {
        $rapport = $request->input('rapport');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        $archive = config('tenantconfigfields.archive');
        if ($rapport == "cegos") {
            $softModules = Module::where(['category' => 'CEGOS', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $softEnrollsQuery = Enrollmodule::whereIn('module_docebo_id', $softModules);

            if ($dateDebut != null && $dateFin != null) {
                $softEnrollsQuery->where(function ($query) use ($dateDebut, $dateFin, $archive) {
                    $query->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    if (!$archive) {
                        $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                        $query->whereIn('learner_docebo_id', $learnersIds);
                    }
                })
                    ->orWhere(function ($query) use ($dateDebut, $dateFin, $archive) {
                        $query->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        if (!$archive) {
                            $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                            $query->whereIn('learner_docebo_id', $learnersIds);
                        }
                    });
            } else {
                if (!$archive) {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrollsQuery->whereIn('learner_docebo_id', $learnersIds);
                }
            }

            $softEnrolls = $softEnrollsQuery->get();

            return (new FastExcel($softEnrolls))->download('rapport_formation_softskills.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Module' => $enroll->module->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        } elseif ($rapport == "transverse") {
            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $lpEnrolls = Lpenroll::where(function ($query) use ($dateDebut, $dateFin) {
                        $query->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($dateDebut, $dateFin) {
                            $query->whereNull('enrollment_updated_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $lpEnrolls = Lpenroll::where(function ($query) use ($learnersIds, $dateDebut, $dateFin) {
                        $query->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($learnersIds, $dateDebut, $dateFin) {
                            $query->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_updated_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $lpEnrolls = Lpenroll::get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $lpEnrolls = Lpenroll::whereIn('learner_docebo_id', $learnersIds)->get();
                }
            }

            return (new FastExcel($lpEnrolls))->download('rapport_formation_transverse.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Plan de formation' => $enroll->lp->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        } elseif ($rapport == "eni") {
            $softModules = Module::where(['category' => 'ENI', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $softEnrollsQuery = Enrollmodule::whereIn('module_docebo_id', $softModules);

            if ($dateDebut != null && $dateFin != null) {
                $softEnrollsQuery->where(function ($query) use ($dateDebut, $dateFin, $archive) {
                    $query->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    if (!$archive) {
                        $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                        $query->whereIn('learner_docebo_id', $learnersIds);
                    }
                })
                    ->orWhere(function ($query) use ($dateDebut, $dateFin, $archive) {
                        $query->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        if (!$archive) {
                            $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                            $query->whereIn('learner_docebo_id', $learnersIds);
                        }
                    });
            } else {
                if (!$archive) {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrollsQuery->whereIn('learner_docebo_id', $learnersIds);
                }
            }

            $softEnrolls = $softEnrollsQuery->get();

            return (new FastExcel($softEnrolls))->download('rapport_formation_digital.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Module' => $enroll->module->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        } elseif ($rapport == "speex") {
            $softModules = Module::where(['category' => 'SPEEX', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $softEnrollsQuery = Enrollmodule::whereIn('module_docebo_id', $softModules);

            if ($dateDebut != null && $dateFin != null) {
                $softEnrollsQuery->where(function ($query) use ($dateDebut, $dateFin, $archive) {
                    $query->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    if (!$archive) {
                        $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                        $query->whereIn('learner_docebo_id', $learnersIds);
                    }
                })
                    ->orWhere(function ($query) use ($dateDebut, $dateFin, $archive) {
                        $query->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        if (!$archive) {
                            $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                            $query->whereIn('learner_docebo_id', $learnersIds);
                        }
                    });
            } else {
                if (!$archive) {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrollsQuery->whereIn('learner_docebo_id', $learnersIds);
                }
            }

            $softEnrolls = $softEnrollsQuery->get();

            return (new FastExcel($softEnrolls))->download('rapport_formation_langues.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Module' => $enroll->module->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        } elseif ($rapport == "sm") {
            $softModules = Module::where(['category' => 'SM', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $softEnrollsQuery = Enrollmodule::whereIn('module_docebo_id', $softModules);

            if ($dateDebut != null && $dateFin != null) {
                $softEnrollsQuery->where(function ($query) use ($dateDebut, $dateFin, $archive) {
                    $query->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    if (!$archive) {
                        $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                        $query->whereIn('learner_docebo_id', $learnersIds);
                    }
                })
                    ->orWhere(function ($query) use ($dateDebut, $dateFin, $archive) {
                        $query->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        if (!$archive) {
                            $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                            $query->whereIn('learner_docebo_id', $learnersIds);
                        }
                    });
            } else {
                if (!$archive) {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrollsQuery->whereIn('learner_docebo_id', $learnersIds);
                }
            }

            $softEnrolls = $softEnrollsQuery->get();

            return (new FastExcel($softEnrolls))->download('rapport_formation_sur_mesure.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Module' => $enroll->module->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        } elseif ($rapport == "mooc") {
            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $moocEnrolls = Enrollmooc::where(function ($query) use ($dateDebut, $dateFin) {
                        $query->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($dateDebut, $dateFin) {
                            $query->whereNull('enrollment_updated_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $moocEnrolls = Enrollmooc::where(function ($query) use ($learnersIds, $dateDebut, $dateFin) {
                        $query->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($learnersIds, $dateDebut, $dateFin) {
                            $query->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_updated_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $moocEnrolls = Enrollmooc::get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $moocEnrolls = Enrollmooc::whereIn('learner_docebo_id', $learnersIds)->get();
                }
            }


            return (new FastExcel($moocEnrolls))->download('rapport_formation_moocs.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Mooc' => $enroll->mooc->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        } elseif ($rapport == "connexion") {
            $groups = Group::where('status', 1)->get();
            $statsConnexions = [];
            if ($dateDebut != null && $dateFin != null) {
                foreach ($groups as $group) {
                    $actives = $group->learners()->where('statut', 'active')->whereBetween('last_access_date', [$dateDebut, $dateFin])->count();
                    $inactives = $group->learners()->where('statut', 'inactive')->whereBetween('creation_date', [$dateDebut, $dateFin])->count();
                    $total = $actives + $inactives;
                    $pourcentage = ($total != 0) ? $actives * 100 / $total : 0;
                    $statsConnexions[] = [
                        'filiale' => $group->name,
                        'Nombre de connexions' => $actives,
                        'total' => $total,
                        'pourcentage' => round($pourcentage, 2) . " %"
                    ];
                }
            } else {
                foreach ($groups as $group) {
                    $actives = $group->learners()->where('statut', 'active')->count();
                    $total = $group->learners()->whereIn('statut', ['active', 'inactive'])->count();
                    $pourcentage = ($total != 0) ? $actives * 100 / $total : 0;
                    $statsConnexions[] = [
                        'filiale' => $group->name,
                        'Nombre de connexions' => $actives,
                        'total' => $total,
                        'pourcentage' => round($pourcentage, 2) . " %"
                    ];
                }
            }
            Storage::put('rapport_connexion.xlsx', '');
            (new FastExcel($statsConnexions))->export('rapport_connexion.xlsx');
        } elseif ($rapport == "active") {
            $softModules = Module::where(['category' => 'CEGOS', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $softEnrollsQuery = Enrollmodule::whereIn('module_docebo_id', $softModules);

            if ($dateDebut != null && $dateFin != null) {
                $softEnrollsQuery->where(function ($query) use ($dateDebut, $dateFin, $archive) {
                    $query->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    if (!$archive) {
                        $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                        $query->whereIn('learner_docebo_id', $learnersIds);
                    }
                })
                    ->orWhere(function ($query) use ($dateDebut, $dateFin, $archive) {
                        $query->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        if (!$archive) {
                            $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                            $query->whereIn('learner_docebo_id', $learnersIds);
                        }
                    });
            } else {
                if (!$archive) {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrollsQuery->whereIn('learner_docebo_id', $learnersIds);
                }
            }

            $softEnrolls = $softEnrollsQuery->get();

            (new FastExcel($softEnrolls))->download('rapport_formation_softskills.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Module' => $enroll->module->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        } elseif ($rapport == "inactive") {
            $softModules = Module::where(['category' => 'CEGOS', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $softEnrollsQuery = Enrollmodule::whereIn('module_docebo_id', $softModules);

            if ($dateDebut != null && $dateFin != null) {
                $softEnrollsQuery->where(function ($query) use ($dateDebut, $dateFin, $archive) {
                    $query->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    if (!$archive) {
                        $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                        $query->whereIn('learner_docebo_id', $learnersIds);
                    }
                })
                    ->orWhere(function ($query) use ($dateDebut, $dateFin, $archive) {
                        $query->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        if (!$archive) {
                            $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                            $query->whereIn('learner_docebo_id', $learnersIds);
                        }
                    });
            } else {
                if (!$archive) {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrollsQuery->whereIn('learner_docebo_id', $learnersIds);
                }
            }

            $softEnrolls = $softEnrollsQuery->get();

            (new FastExcel($softEnrolls))->download('rapport_formation_softskills.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Module' => $enroll->module->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        } elseif ($rapport == "tickets") {
            $softModules = Module::where(['category' => 'CEGOS', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $softEnrollsQuery = Enrollmodule::whereIn('module_docebo_id', $softModules);

            if ($dateDebut != null && $dateFin != null) {
                $softEnrollsQuery->where(function ($query) use ($dateDebut, $dateFin, $archive) {
                    $query->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    if (!$archive) {
                        $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                        $query->whereIn('learner_docebo_id', $learnersIds);
                    }
                })
                    ->orWhere(function ($query) use ($dateDebut, $dateFin, $archive) {
                        $query->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        if (!$archive) {
                            $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                            $query->whereIn('learner_docebo_id', $learnersIds);
                        }
                    });
            } else {
                if (!$archive) {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrollsQuery->whereIn('learner_docebo_id', $learnersIds);
                }
            }

            $softEnrolls = $softEnrollsQuery->get();

            (new FastExcel($softEnrolls))->download('rapport_formation_softskills.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Module' => $enroll->module->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        } elseif ($rapport == "calls") {
            $softModules = Module::where(['category' => 'CEGOS', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            $softEnrollsQuery = Enrollmodule::whereIn('module_docebo_id', $softModules);

            if ($dateDebut != null && $dateFin != null) {
                $softEnrollsQuery->where(function ($query) use ($dateDebut, $dateFin, $archive) {
                    $query->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    if (!$archive) {
                        $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                        $query->whereIn('learner_docebo_id', $learnersIds);
                    }
                })
                    ->orWhere(function ($query) use ($dateDebut, $dateFin, $archive) {
                        $query->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        if (!$archive) {
                            $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                            $query->whereIn('learner_docebo_id', $learnersIds);
                        }
                    });
            } else {
                if (!$archive) {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrollsQuery->whereIn('learner_docebo_id', $learnersIds);
                }
            }

            $softEnrolls = $softEnrollsQuery->get();

            (new FastExcel($softEnrolls))->download('rapport_formation_softskills.xlsx', function ($enroll) {
                $userfields = config('tenantconfigfields.userfields');
                $enrollfields = config('tenantconfigfields.enrollmentfields');
                $data = [
                    'Branche' => $enroll->project->name ?? '******',
                    'Filiale' => $enroll->group->name ?? '******',
                    'Module' => $enroll->module->name ?? '******',
                    'Username' => $enroll->module->name ?? '******',
                ];

                if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                    $data['Matricule'] = $enroll->learner->matricule ?? 'Matricule';
                }

                $data['Date d\'inscription'] = $enroll->enrollment_created_at ?? 'Date d\'inscription';
                $data['Statut'] = $enroll->status ?? '******';
                $data['Date du dernière modification'] = $enroll->enrollment_updated_at ?? '******';
                $data['Date d\'achèvement'] = $enroll->enrollment_completed_at ?? '******';
                $data['Temps de session'] = $enroll->session_time ?? '******';

                if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
                    $data['Temps d\'engagement'] = $enroll->cmi_time ?? '******';
                }

                if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
                    $data['Temps calculé'] = $enroll->calculated_time ?? '******';
                }

                if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
                    $data['Temps pédagogique recommandé'] = $enroll->recommended_time ?? '******';
                }

                return $data;
            });
        }


    }
}