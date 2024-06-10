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
                case 'modules':
                    (new ModuleExport($dateDebut, $dateFin))->queue('rapport_formation_softskills.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations softskills",
                            "link" => tenant_asset('rapport_formation_softskills.xlsx')
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
                            "name" => "des formations softskills",
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
                case 'modules':
                    (new ModuleExport())->queue('rapport_formation_softskills.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des formations softskills",
                            "link" => tenant_asset('rapport_formation_softskills.xlsx')
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

        if ($dateDebut && $dateFin) {
            switch ($rapport) {
                case 'connexion':
                    return Excel::download(new ConnexionExport($dateDebut, $dateFin), 'rapport_des_connexions.xlsx');
                case 'active':
                    return Excel::download(new ActiveLearnerExport($dateDebut, $dateFin), 'rapport_des_inscrits_actifs.xlsx');
                case 'inactive':
                    return Excel::download(new InactiveLearnerExport($dateDebut, $dateFin), 'rapport_des_inscrits_inactifs.xlsx');
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
                    return Excel::download(new EniExport($dateDebut, $dateFin), 'rapport_formation_digitals.xlsx');
                case 'speex':
                    return Excel::download(new SpeexExport($dateDebut, $dateFin), 'rapport_formation_langue.xlsx');
                case 'sm':
                    return Excel::download(new SmExport($dateDebut, $dateFin), 'rapport_formation_surmesure.xlsx');
                case 'mooc':
                    return Excel::download(new MoocExport($dateDebut, $dateFin), 'rapport_formation_moocs.xlsx');
                case 'tickets':
                    return Excel::download(new TicketExport($dateDebut, $dateFin), 'rapport_lsc_tickets.xlsx');
                case 'calls':
                    return Excel::download(new CallExport($dateDebut, $dateFin), 'rapport_lsc_calls.xlsx');
                default:
                    return response()->json(['error' => 'Type de rapport invalide.'], 400);
            }
        } else {
            switch ($rapport) {
                case 'connexion':
                    return Excel::download(new ConnexionExport, 'rapport_des_connexions.xlsx');
                case 'active':
                    (new LearnerExport())->queue('rapport_des_inscrits.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des inscrits",
                            "link" => tenant_asset('rapport_des_inscrits.xlsx')
                        ]),
                    ]);
                    return response()->json(['message' => 'Le rapport est en cours de génération et vous serez notifié une fois terminé.']);
                case 'inactive':
                    //return Excel::download(new InactiveLearnerExport, 'rapport_des_inscrits_inactifs.xlsx');
                    (new InactiveLearnerExport())->queue('rapport_des_inscrits_inactifs.xlsx')->chain([
                        new NotifyUserOfCompletedExport(Auth::guard('user')->user(), [
                            "name" => "des inscrits actifs",
                            "link" => tenant_asset('rapport_des_inscrits_inactifs.xlsx')
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
                    return Excel::download(new EniExport, 'rapport_formation_digitals.xlsx');
                case 'speex':
                    return Excel::download(new SpeexExport, 'rapport_formation_langue.xlsx');
                case 'sm':
                    return Excel::download(new SmExport, 'rapport_formation_surmesure.xlsx');
                case 'mooc':
                    return Excel::download(new MoocExport, 'rapport_formation_moocs.xlsx');
                case 'tickets':
                    return Excel::download(new TicketExport, 'rapport_lsc_tickets.xlsx');
                case 'calls':
                    return Excel::download(new CallExport, 'rapport_lsc_calls.xlsx');
                default:
                    return response()->json(['error' => 'Type de rapport invalide.'], 400);
            }
        }
    }
}