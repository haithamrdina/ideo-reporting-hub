<?php

namespace App\Http\Controllers\Tenant\Plateforme;

use App\Enums\CourseStatusEnum;
use App\Exports\CegosExport;
use App\Exports\EniExport;
use App\Exports\GamificationExport;
use App\Exports\LearnerExport;
use App\Exports\LpExport;
use App\Exports\LscExport;
use App\Exports\ModuleExport;
use App\Exports\MoocExport;
use App\Exports\SmExport;
use App\Exports\SpeexExport;
use App\Http\Controllers\Controller;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\getBadgeData;
use App\Http\Integrations\Docebo\Requests\getLeaderboardsData;
use App\Jobs\ExportActiveJob;
use App\Jobs\ExportCallJob;
use App\Jobs\ExportCegosJob;
use App\Jobs\ExportConnexionJob;
use App\Jobs\ExportEniJob;
use App\Jobs\ExportInactiveJob;
use App\Jobs\ExportMoocJob;
use App\Jobs\ExportSmJob;
use App\Jobs\ExportTicketsJob;
use App\Jobs\ExportTransverseJob;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Models\Badge;
use App\Models\Call;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Group;
use App\Models\Learner;
use App\Models\Lpenroll;
use App\Models\Module;
use App\Models\Ticket;
use App\Services\PlateformeReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        (new LearnerExport())->queue('rapport_inscriptions.xlsx')->chain([
            new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                "name" => "des formations softskills",
                "link" => tenant_asset('rapport_inscriptions.xlsx')
            ]),
        ]);
        return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
    }

    public function exportModules()
    {
        (new ModuleExport())->queue('rapport_formation_softskills.xlsx')->chain([
            new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                "name" => "des formations softskills",
                "link" => tenant_asset('rapport_formation_softskills.xlsx')
            ]),
        ]);
        return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
    }

    public function exportLps()
    {
        (new LpExport())->queue('rapport_formation_transverse.xlsx')->chain([
            new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                "name" => "des formations transverses",
                "link" => tenant_asset('rapport_formation_transverse.xlsx')
            ]),
        ]);
        return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
    }

    public function exportLsc()
    {
        //return Excel::download(new LscExport, 'rapport_learner_success_center.xlsx');
        (new LscExport())->queue('rapport_learner_success.xlsx')->chain([
            new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                "name" => "de learner success center",
                "link" => tenant_asset('rapport_learner_success.xlsx')
            ]),
        ]);
        return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
    }

    public function exportGamification()
    {
        $badgesIDs = Badge::pluck('id')->toArray();
        (new GamificationExport($badgesIDs))->queue('rapport_gamification.xlsx')->chain([
            new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                "name" => "des inscriptions",
                "link" => tenant_asset('rapport_gamification.xlsx')
            ]),
        ]);
        return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
    }

    public function export(Request $request)
    {
        $rapport = $request->input('rapport');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');

        if ($dateDebut && $dateFin) {
            switch ($rapport) {
                case 'inscriptions':
                    (new LearnerExport($dateDebut, $dateFin))->queue('rapport_inscriptions.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des inscriptions",
                            "link" => tenant_asset('rapport_inscriptions.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'transverse':
                    (new LpExport($dateDebut, $dateFin))->queue('rapport_formation_transverse.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations transverses",
                            "link" => tenant_asset('rapport_formation_transverse.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'cegos':
                    (new CegosExport($dateDebut, $dateFin))->queue('rapport_formation_softskills.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations softskills",
                            "link" => tenant_asset('rapport_formation_softskills.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'eni':
                    (new EniExport($dateDebut, $dateFin))->queue('rapport_formation_digital.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations digitals",
                            "link" => tenant_asset('rapport_formation_digital.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'speex':
                    (new SpeexExport($dateDebut, $dateFin))->queue('rapport_formation_langues.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations langues",
                            "link" => tenant_asset('rapport_formation_langues.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'sm':
                    (new SmExport($dateDebut, $dateFin))->queue('rapport_formation_surmesure.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations sur mesure",
                            "link" => tenant_asset('rapport_formation_surmesure.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'mooc':
                    (new MoocExport($dateDebut, $dateFin))->queue('rapport_formation_mooc.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des moocs",
                            "link" => tenant_asset('rapport_formation_mooc.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'lsc':
                    (new LscExport($dateDebut, $dateFin))->queue('rapport_learner_success.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "de learner success center",
                            "link" => tenant_asset('rapport_learner_success.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                default:
                    return response()->json(['error' => 'Type de rapport invalide.'], 400);
            }
        } else {
            switch ($rapport) {
                case 'inscriptions':
                    (new LearnerExport())->queue('rapport_inscriptions.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des inscriptions",
                            "link" => tenant_asset('rapport_inscriptions.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'transverse':
                    (new LpExport())->queue('rapport_formation_transverse.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations transverses",
                            "link" => tenant_asset('rapport_formation_transverse.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'cegos':
                    (new CegosExport())->queue('rapport_formation_softskills.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations softskills",
                            "link" => tenant_asset('rapport_formation_softskills.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'eni':
                    (new EniExport())->queue('rapport_formation_digital.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations digitals",
                            "link" => tenant_asset('rapport_formation_digital.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'speex':
                    (new SpeexExport())->queue('rapport_formation_langues.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations langues",
                            "link" => tenant_asset('rapport_formation_langues.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'sm':
                    (new SmExport())->queue('rapport_formation_surmesure.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations sur mesure",
                            "link" => tenant_asset('rapport_formation_surmesure.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'mooc':
                    (new MoocExport())->queue('rapport_formation_mooc.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des moocs",
                            "link" => tenant_asset('rapport_formation_mooc.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'lsc':
                    (new LscExport())->queue('rapport_learner_success.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "de learner success center",
                            "link" => tenant_asset('rapport_learner_success.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                default:
                    return response()->json(['error' => 'Type de rapport invalide.'], 400);
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

        $userfields = config('tenantconfigfields.userfields');
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $archive = config('tenantconfigfields.archive');


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

            $softModules = Module::where(['category' => 'CEGOS', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $dateDebut, $dateFin) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($softModules, $dateDebut, $dateFin) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->get();
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

            $softModules = Module::where(['category' => 'ENI', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $dateDebut, $dateFin) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($softModules, $dateDebut, $dateFin) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->get();
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

            $softModules = Module::where(['category' => 'SM', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $dateDebut, $dateFin) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($softModules, $dateDebut, $dateFin) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                } else {

                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::where(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin) {
                        $query->whereIn('module_docebo_id', $softModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($softModules, $learnersIds, $dateDebut, $dateFin) {
                            $query->whereIn('module_docebo_id', $softModules)
                                ->whereIn('learner_docebo_id', $learnersIds)
                                ->whereNull('enrollment_completed_at')
                                ->whereBetween('enrollment_updated_at', [$dateDebut, $dateFin]);
                        })
                        ->get();
                }

            } else {
                if ($archive == true) {
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn(
                        'learner_docebo_id',
                        $learnersIds
                    )->get();
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
                    $moocEnrolls = Enrollmooc::where(function ($query) use ($dateDebut, $dateFin) {
                        $query->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($dateDebut, $dateFin) {
                            $query->whereNull('enrollment_completed_at')
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
                                ->whereNull('enrollment_completed_at')
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
                    $lpEnrolls = Lpenroll::where(function ($query) use ($dateDebut, $dateFin) {
                        $query->whereNotNull('enrollment_completed_at')
                            ->whereBetween('enrollment_completed_at', [$dateDebut, $dateFin]);
                    })
                        ->orWhere(function ($query) use ($dateDebut, $dateFin) {
                            $query->whereNull('enrollment_completed_at')
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
                                ->whereNull('enrollment_completed_at')
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
            ExportTransverseJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des formations transverses",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($lpEnrolls, $fields, $filename);
        } elseif ($rapport == 'connexion') {
            $filename = "rapport_des_connexions.csv";
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
            ExportConnexionJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des connexions",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($statsConnexions, ['filiale', 'Nombre de connexions', 'total', 'pourcentage'], $filename);
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
                $learners = Learner::where('statut', 'active')->whereBetween('last_access_date', [$dateDebut, $dateFin])->get();
            } else {
                $learners = Learner::where('statut', 'active')->get();
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
                $learners = Learner::where('statut', 'inactive')->whereBetween('creation_date', [$dateDebut, $dateFin])->get();
            } else {
                $learners = Learner::where('statut', 'inactive')->get();
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
            $fields['ticket_created_at'] = 'Sujet';
            $fields['ticket_udpated_at'] = 'Sujet';

            if ($dateDebut != null && $dateFin != null) {
                if ($archive == true) {
                    $tickets = Ticket::whereBetween('ticket_created_at', [$dateDebut, $dateFin])->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $tickets = Ticket::whereIn('learner_docebo_id', $learnersIds)->whereBetween('ticket_created_at', [$dateDebut, $dateFin])->get();
                }
            } else {
                if ($archive == true) {
                    $tickets = Ticket::get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $tickets = Ticket::whereIn('learner_docebo_id', $learnersIds)->get();
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
                    $calls = Call::whereBetween('date_call', [$dateDebut, $dateFin])->get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $calls = Call::whereIn('learner_docebo_id', $learnersIds)->whereBetween('date_call', [$dateDebut, $dateFin])->get();
                }
            } else {
                if ($archive == true) {
                    $calls = Call::get();
                } else {
                    $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                    $calls = Call::whereIn('learner_docebo_id', $learnersIds)->get();
                }
            }
            ExportCallJob::withChain([
                new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                    "name" => "des appels téléphoniques",
                    "link" => tenant_asset($filename)
                ]),
            ])->dispatch($calls, $fields, $filename);
        }

        return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
    }
}