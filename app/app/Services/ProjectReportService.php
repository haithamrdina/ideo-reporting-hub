<?php


namespace App\Services;

use App\Enums\CourseStatusEnum;
use App\Models\Call;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Lpenroll;
use App\Models\Project;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProjectReportService
{

    public function getLearnersInscriptionsPerStatDate($contract_start_date_conf, $project)
    {

        if ($contract_start_date_conf != null) {
            $date = Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
            $dayMonth = $date->format('-m-d');
            $currentYear = now()->year;

            // Créer un objet Carbon pour la date du contrat dans l'année courante
            $contractDateThisYear = Carbon::createFromFormat('Y-m-d', $currentYear . $dayMonth);
            $now = Carbon::now();

            // Si la date du contrat dans l'année courante est dans le futur
            if ($contractDateThisYear->gt($now)) {
                // Utiliser l'année précédente
                $startDate = ($currentYear - 1) . $dayMonth;
            } else {
                // Utiliser l'année courante
                $startDate = $currentYear . $dayMonth;
            }
            $endDate = Carbon::now()->format('Y-m-d');
            $total_learners = Learner::whereBetween('creation_date', [$startDate, $endDate])->where('project_id', $project->id)->count();
            $active_learners = Learner::whereBetween('last_access_date', [$startDate, $endDate])->where('statut', 'active')->where('project_id', $project->id)->count();
            $inactive_learners = Learner::whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'inactive')->where('project_id', $project->id)->count();
            $statsLearners = [
                'total' => $total_learners,
                'active' => $active_learners,
                'inactive' => $inactive_learners,
            ];
        } else {
            $statsLearners = [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
            ];
        }

        return $statsLearners;
    }

    public function getTimingDetailsPerStatDate($contract_start_date_conf, $enrollfields, $project)
    {

        if ($contract_start_date_conf != null) {
            $date = Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
            $dayMonth = $date->format('-m-d');
            $currentYear = now()->year;

            // Créer un objet Carbon pour la date du contrat dans l'année courante
            $contractDateThisYear = Carbon::createFromFormat('Y-m-d', $currentYear . $dayMonth);
            $now = Carbon::now();

            // Si la date du contrat dans l'année courante est dans le futur
            if ($contractDateThisYear->gt($now)) {
                // Utiliser l'année précédente
                $startDate = ($currentYear - 1) . $dayMonth;
            } else {
                // Utiliser l'année courante
                $startDate = $currentYear . $dayMonth;
            }

            $endDate = Carbon::now()->format('Y-m-d');
            $archive = config('tenantconfigfields.archive');
            if ($archive == true) {
                $learners = Learner::whereIn('statut', ['active', 'archive'])->whereBetween('last_access_date', [$startDate, $endDate])->where('project_id', $project->id)->count();
                $learnersIds = Learner::whereIn('statut', ['active', 'archive'])->whereBetween('last_access_date', [$startDate, $endDate])->where('project_id', $project->id)->pluck('docebo_id')->toArray();
            } else {
                $learners = Learner::where('statut', 'active')->whereBetween('last_access_date', [$startDate, $endDate])->where('project_id', $project->id)->count();
                $learnersIds = Learner::where('statut', 'active')->whereBetween('last_access_date', [$startDate, $endDate])->where('project_id', $project->id)->pluck('docebo_id')->toArray();
            }

            $moduleDataTimes = Enrollmodule::calculateModuleDataTimesBetweenDatePerProject($startDate, $endDate, $project->id, $learnersIds);
            $moocDataTimes = Enrollmooc::calculateMoocDataTimesBetweenDatePerProject($startDate, $endDate, $project->id, $learnersIds);
            $speexDataTimes = Langenroll::calculateSpeexDataTimesBetweenDatePerProject($startDate, $endDate, $project->id, $learnersIds);

            $timeConversionService = new TimeConversionService();
            $total_session_time = intval($moduleDataTimes->total_session_time) + intval($moocDataTimes->total_session_time) + intval($speexDataTimes->total_session_time);
            $avg_session_time = $learners != 0 ? intval($total_session_time / $learners) : 0;
            $total_session_time = $timeConversionService->convertSecondsToTime($total_session_time);
            $avg_session_time = $timeConversionService->convertSecondsToTime($avg_session_time);


            if ($enrollfields['cmi_time'] == true) {
                $total_cmi_time = intval($moduleDataTimes->total_cmi_time) + intval($moocDataTimes->total_cmi_time) + intval($speexDataTimes->total_cmi_time);
                $avg_cmi_time = $learners != 0 ? intval($total_cmi_time / $learners) : 0;
                $total_cmi_time = $timeConversionService->convertSecondsToTime($total_cmi_time);
                $avg_cmi_time = $timeConversionService->convertSecondsToTime($avg_cmi_time);
            } else {
                $total_cmi_time = "**h **min **s";
                $avg_cmi_time = "**h **min **s";
            }

            if ($enrollfields['calculated_time'] == true) {
                $total_calculated_time = intval($moduleDataTimes->total_calculated_time) + intval($moocDataTimes->total_calculated_time) + intval($speexDataTimes->total_calculated_time);
                $avg_calculated_time = $learners != 0 ? intval($total_calculated_time / $learners) : 0;
                $total_calculated_time = $timeConversionService->convertSecondsToTime($total_calculated_time);
                $avg_calculated_time = $timeConversionService->convertSecondsToTime($avg_calculated_time);
            } else {
                $total_calculated_time = "**h **min **s";
                $avg_calculated_time = "**h **min **s";
            }

            if ($enrollfields['recommended_time'] == true) {
                $total_recommended_time = intval($moduleDataTimes->total_recommended_time) + intval($moocDataTimes->total_recommended_time) + intval($speexDataTimes->total_recommended_time);
                $avg_recommended_time = $learners != 0 ? intval($total_recommended_time / $learners) : 0;
                $total_recommended_time = $timeConversionService->convertSecondsToTime($total_recommended_time);
                $avg_recommended_time = $timeConversionService->convertSecondsToTime($avg_recommended_time);
            } else {
                $total_recommended_time = "**h **min **s";
                $avg_recommended_time = "**h **min **s";
            }
            $statsTimes = [
                'total_session_time' => $total_session_time,
                'avg_session_time' => $avg_session_time,
                'total_cmi_time' => $total_cmi_time,
                'avg_cmi_time' => $avg_cmi_time,
                'total_calculated_time' => $total_calculated_time,
                'avg_calculated_time' => $avg_calculated_time,
                'total_recommended_time' => $total_recommended_time,
                'avg_recommended_time' => $avg_recommended_time,

            ];
        } else {
            $statsTimes = [
                'total_session_time' => "**h **min **s",
                'avg_session_time' => "**h **min **s",
                'total_cmi_time' => "**h **min **s",
                'avg_cmi_time' => "**h **min **s",
                'total_calculated_time' => "**h **min **s",
                'avg_calculated_time' => "**h **min **s",
                'total_recommended_time' => "**h **min **s",
                'avg_recommended_time' => "**h **min **s",

            ];
        }
        return $statsTimes;
    }

    public function getLearnersInscriptions($project)
    {
        $total_learners = Learner::where('project_id', $project->id)->count();
        $active_learners = Learner::where('statut', 'active')->where('project_id', $project->id)->count();
        $inactive_learners = Learner::where('statut', 'inactive')->where('project_id', $project->id)->count();
        return [
            'total' => $total_learners,
            'active' => $active_learners,
            'inactive' => $inactive_learners
        ];
    }

    public function getTimingDetails($enrollfields, $project)
    {
        $archive = config('tenantconfigfields.archive');

        if ($archive == true) {
            $learners = Learner::whereIn('statut', ['active', 'archive'])->where('project_id', $project->id)->count();
            $learnersIds = Learner::whereIn('statut', ['active', 'archive'])->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learners = Learner::where('statut', 'active')->count();
            $learnersIds = Learner::where('statut', 'active')->pluck('docebo_id')->toArray();
        }

        $enrollModules = EnrollModule::whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
        $enrollMoocs = Enrollmooc::whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
        $enrollSpeex = Langenroll::whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();

        $timeConversionService = new TimeConversionService();
        $total_session_time = intval($enrollModules->sum('session_time')) + intval($enrollMoocs->sum('session_time')) + intval($enrollSpeex->sum('session_time'));
        $avg_session_time = $learners != 0 ? intval($total_session_time / $learners) : 0;
        $total_session_time = $timeConversionService->convertSecondsToTime($total_session_time);
        $avg_session_time = $timeConversionService->convertSecondsToTime($avg_session_time);


        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = intval($enrollModules->sum('cmi_time')) + intval($enrollMoocs->sum('cmi_time')) + intval($enrollSpeex->sum('cmi_time'));
            $avg_cmi_time = $learners != 0 ? intval($total_cmi_time / $learners) : 0;
            $total_cmi_time = $timeConversionService->convertSecondsToTime($total_cmi_time);
            $avg_cmi_time = $timeConversionService->convertSecondsToTime($avg_cmi_time);
        } else {
            $total_cmi_time = "**h **min **s";
            $avg_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = intval($enrollModules->sum('calculated_time')) + intval($enrollMoocs->sum('calculated_time')) + intval($enrollSpeex->sum('calculated_time'));;
            $avg_calculated_time = $learners != 0 ? intval($total_calculated_time / $learners) : 0;
            $total_calculated_time = $timeConversionService->convertSecondsToTime($total_calculated_time);
            $avg_calculated_time = $timeConversionService->convertSecondsToTime($avg_calculated_time);
        } else {
            $total_calculated_time = "**h **min **s";
            $avg_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = intval($enrollModules->sum('recommended_time')) + intval($enrollMoocs->sum('recommended_time')) + intval($enrollSpeex->sum('recommended_time'));;
            $avg_recommended_time = $learners != 0 ? intval($total_recommended_time / $learners) : 0;
            $total_recommended_time = $timeConversionService->convertSecondsToTime($total_recommended_time);
            $avg_recommended_time = $timeConversionService->convertSecondsToTime($avg_recommended_time);
        } else {
            $total_recommended_time = "**h **min **s";
            $avg_recommended_time = "**h **min **s";
        }


        return [
            'total_session_time' => $total_session_time,
            'avg_session_time' => $avg_session_time,
            'total_cmi_time' => $total_cmi_time,
            'avg_cmi_time' => $avg_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'avg_calculated_time' => $avg_calculated_time,
            'total_recommended_time' => $total_recommended_time,
            'avg_recommended_time' => $avg_recommended_time,

        ];
    }

    public function getLearnersCharts($categorie, $project)
    {
        $archive = config('tenantconfigfields.archive');
        if ($categorie) {
            if ($archive == true) {
                $learnerCounts = DB::table('learners')
                    ->select('categorie', DB::raw('count(*) as total'))
                    ->where('project_id', $project->id)
                    ->groupBy('categorie')
                    ->get();
                $totalLearners = DB::table('learners')->where('project_id', $project->id)->count();
            } else {
                $learnerCounts = DB::table('learners')
                    ->select('categorie', DB::raw('count(*) as total'))
                    ->where('project_id', $project->id)
                    ->where('statut', '!=', 'archive')
                    ->groupBy('categorie')
                    ->get();
                $totalLearners = DB::table('learners')->where('statut', '!=', 'archive')->where('project_id', $project->id)->count();
            }
            $data = [];
            $labels = [];

            foreach ($learnerCounts as $count) {
                $percentage = round(($count->total / $totalLearners) * 100, 2);
                $data[] = $count->total;
                $labels[] = $count->categorie !== null ? ucfirst($count->categorie) . ' ' . $count->total . ' - (' . $percentage . '%)' : ' Indéterminé' . ' ' . $count->total . ' - (' . $percentage . '%)';
            }

            $chartInscritPerCategorie = [
                'labels' => $labels,
                'data' => $data
            ];
            $categories = Learner::where('project_id', $project->id)->distinct()->pluck('categorie')->filter();

            $counts = [
                'Active' => [],
                'Inactive' => []
            ];

            foreach ($categories as $category) {
                $counts['Active'][] = Learner::where('categorie', $category)->where('statut', 'active')->where('project_id', $project->id)->count();
                $counts['Inactive'][] = Learner::where('categorie', $category)->where('statut', 'inactive')->where('project_id', $project->id)->count();
            }

            $chartInscritPerCategoryAndStatus = [
                'labels' => $categories->toArray(),
                'actives' => $counts['Active'],
                'inactives' => $counts['Inactive'],
            ];
        } else {
            $chartInscritPerCategorie = null;
            $chartInscritPerCategoryAndStatus = null;
        }

        return [
            'chartInscritPerCategorie' => $chartInscritPerCategorie,
            'chartInscritPerCategoryAndStatus' => $chartInscritPerCategoryAndStatus
        ];
    }

    public function getStatSoftskills($enrollfields, $project)
    {

        $softModules = $project->modules->filter(function ($module) {
            return $module->category === 'CEGOS' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();

        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
        $softEnrollsInEnrolled = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->where('project_id', $project->id)->count();
        $softEnrollsInProgress = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->where('project_id', $project->id)->count();
        $softEnrollsInCompleted = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->where('project_id', $project->id)->count();

        $statSoftskills = [
            'enrolled' => $softEnrollsInEnrolled,
            'in_progress' => $softEnrollsInProgress,
            'completed' => $softEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time = $timeConversionService->convertSecondsToTime($softEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = $timeConversionService->convertSecondsToTime($softEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = $timeConversionService->convertSecondsToTime($softEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = $timeConversionService->convertSecondsToTime($softEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time = "**h **min **s";
        }

        $statSoftTimes = [
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $softCharts = [
            'labels' => ['Non démarré', 'En cours', 'Terminé'],
            'data' => [$softEnrollsInEnrolled, $softEnrollsInProgress, $softEnrollsInCompleted]
        ];


        return [
            'statSoftskills' => $statSoftskills,
            'statSoftTimes' => $statSoftTimes,
            'softCharts' => $softCharts,
        ];
    }

    public function getStatDigital($enrollfields, $project)
    {
        $digitalModules = $project->modules->filter(function ($module) {
            return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();

        $moduleDigitals = $project->modules->filter(function ($module) {
            return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
        })->values();

        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $digitalEnrolls = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();

        $digitalEnrollsInEnrolled = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->where('project_id', $project->id)->count();
        $digitalEnrollsInProgress = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->where('project_id', $project->id)->count();
        $digitalEnrollsInCompleted = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->where('project_id', $project->id)->count();

        $statDigital = [
            'enrolled' => $digitalEnrollsInEnrolled,
            'in_progress' => $digitalEnrollsInProgress,
            'completed' => $digitalEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time = $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time = "**h **min **s";
        }

        $statDigitalTimes = [
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $digitalCharts = [
            'labels' => ['Non démarré', 'En cours', 'Terminé'],
            'data' => [$digitalEnrollsInEnrolled, $digitalEnrollsInProgress, $digitalEnrollsInCompleted]
        ];


        return [
            'statDigital' => $statDigital,
            'statDigitalTimes' => $statDigitalTimes,
            'digitalCharts' => $digitalCharts,
            'modulesDigital' => $moduleDigitals
        ];
    }

    public function getStatDigitalPerModule($enrollfields, $selectedDigital, $projectId)
    {
        $project = Project::find($projectId);

        $moduleDigitals = $project->modules->filter(function ($module) {
            return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
        })->values();

        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }


        $digitalEnrolls = Enrollmodule::where('module_docebo_id', $selectedDigital)->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();

        $digitalEnrollsInEnrolled = Enrollmodule::where('module_docebo_id', $selectedDigital)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->where('project_id', $projectId)->count();
        $digitalEnrollsInProgress = Enrollmodule::where('module_docebo_id', $selectedDigital)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->where('project_id', $projectId)->count();
        $digitalEnrollsInCompleted = Enrollmodule::where('module_docebo_id', $selectedDigital)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->where('project_id', $projectId)->count();

        $statDigital = [
            'enrolled' => $digitalEnrollsInEnrolled,
            'in_progress' => $digitalEnrollsInProgress,
            'completed' => $digitalEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time = $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time = "**h **min **s";
        }

        $statDigitalTimes = [
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $digitalCharts = [
            'labels' => ['Non démarré', 'En cours', 'Terminé'],
            'data' => [$digitalEnrollsInEnrolled, $digitalEnrollsInProgress, $digitalEnrollsInCompleted]
        ];

        return [
            'statDigital' => $statDigital,
            'statDigitalTimes' => $statDigitalTimes,
            'digitalCharts' => $digitalCharts,
            'modulesDigital' => $moduleDigitals
        ];
    }

    public function getStatSM($enrollfields, $project)
    {
        $smModules = $project->modules->filter(function ($module) {
            return $module->category === 'SM' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();
        $moduleSms = $project->modules->filter(function ($module) {
            return $module->category === 'SM' && $module->status === CourseStatusEnum::ACTIVE;
        })->values();

        $smEnrolls = Enrollmodule::whereIn('module_docebo_id', $smModules)->get();

        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $smEnrollsInEnrolled = Enrollmodule::whereIn('module_docebo_id', $smModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->where('project_id', $project->id)->count();
        $smEnrollsInProgress = Enrollmodule::whereIn('module_docebo_id', $smModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->where('project_id', $project->id)->count();
        $smEnrollsInCompleted = Enrollmodule::whereIn('module_docebo_id', $smModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->where('project_id', $project->id)->count();

        $statSM = [
            'enrolled' => $smEnrollsInEnrolled,
            'in_progress' => $smEnrollsInProgress,
            'completed' => $smEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time = $timeConversionService->convertSecondsToTime($smEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = $timeConversionService->convertSecondsToTime($smEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = $timeConversionService->convertSecondsToTime($smEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = $timeConversionService->convertSecondsToTime($smEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time = "**h **min **s";
        }

        $statSMTimes = [
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $smCharts = [
            'labels' => ['Non démarré', 'En cours', 'Terminé'],
            'data' => [$smEnrollsInEnrolled, $smEnrollsInProgress, $smEnrollsInCompleted]
        ];

        return [
            'statSM' => $statSM,
            'statSMTimes' => $statSMTimes,
            'smCharts' => $smCharts,
            'modulesSms' => $moduleSms
        ];
    }

    public function getStatSMPerModule($enrollfields, $selectedSm, $projectId)
    {
        $project = Project::find($projectId);
        $moduleSms = $project->modules->filter(function ($module) {
            return $module->category === 'SM' && $module->status === CourseStatusEnum::ACTIVE;
        })->values();

        $smEnrolls = Enrollmodule::where('module_docebo_id', $selectedSm)->where('project_id', $project->id)->get();

        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $smEnrollsInEnrolled = Enrollmodule::where('module_docebo_id', $selectedSm)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->where('project_id', $project->id)->count();
        $smEnrollsInProgress = Enrollmodule::where('module_docebo_id', $selectedSm)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->where('project_id', $project->id)->count();
        $smEnrollsInCompleted = Enrollmodule::where('module_docebo_id', $selectedSm)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->where('project_id', $project->id)->count();

        $statSM = [
            'enrolled' => $smEnrollsInEnrolled,
            'in_progress' => $smEnrollsInProgress,
            'completed' => $smEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time = $timeConversionService->convertSecondsToTime($smEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = $timeConversionService->convertSecondsToTime($smEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = $timeConversionService->convertSecondsToTime($smEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = $timeConversionService->convertSecondsToTime($smEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time = "**h **min **s";
        }

        $statSMTimes = [
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $smCharts = [
            'labels' => ['Non démarré', 'En cours', 'Terminé'],
            'data' => [$smEnrollsInEnrolled, $smEnrollsInProgress, $smEnrollsInCompleted]
        ];

        return [
            'statSM' => $statSM,
            'statSMTimes' => $statSMTimes,
            'smCharts' => $smCharts,
            'modulesSms' => $moduleSms
        ];
    }


    public function getStatSpeex($enrollfields, $project)
    {
        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }


        $langEnrollsInEnrolled = Langenroll::whereIn('status', ['enrolled', 'waiting'])->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();
        $langEnrollsInProgress = Langenroll::where('status', 'in_progress')->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();
        $langEnrollsInCompleted = Langenroll::where('status', 'completed')->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();

        $statSpeex = [
            'enrolled' => $langEnrollsInEnrolled,
            'in_progress' => $langEnrollsInProgress,
            'completed' => $langEnrollsInCompleted,
        ];

        $langEnrolls = Langenroll::where('project_id', $project->id)->get();

        $timeConversionService = new TimeConversionService();
        $total_session_time = $timeConversionService->convertSecondsToTime($langEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = $timeConversionService->convertSecondsToTime($langEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = $timeConversionService->convertSecondsToTime($langEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = $timeConversionService->convertSecondsToTime($langEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time = "**h **min **s";
        }

        $statSpeexTimes = [
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $speexLangues = Langenroll::distinct()->where('project_id', $project->id)->pluck('language');
        return [
            'statSpeex' => $statSpeex,
            'statSpeexTimes' => $statSpeexTimes,
            'speexLangues' => $speexLangues,
        ];
    }

    public function getStatSpeexChart($projectId, $selectedLanguage)
    {
        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $projectId)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $projectId)->pluck('docebo_id')->toArray();
        }

        $timeConversionService = new TimeConversionService();
        $niveaux = ['A1', 'A2', 'B1.1', 'B1.2', 'B2.1', 'B2.2', 'C1.1', 'C1.2', 'Indéterminé'];

        $statistiques = Langenroll::selectRaw('niveau, COUNT(*) AS nombre_total, SUM(cmi_time) AS temps_total_cmi')
            ->where('language', $selectedLanguage)
            ->whereIn('learner_docebo_id', $learnersIds)
            ->where('project_id', $projectId)
            ->groupBy('niveau')
            ->get();

        $nombreTotalArray = [];
        $tempsTotalCmiArray = [];

        // Initialiser toutes les valeurs à 0
        foreach ($niveaux as $niveau) {
            $nombreTotalArray[$niveau] = 0;
            $tempsTotalCmiArray[$niveau] = 0;
        }

        // Remplacer les valeurs par celles obtenues dans la requête
        foreach ($statistiques as $statistique) {
            $niveau = $statistique->niveau == '' ? 'Indéterminé' : $statistique->niveau;
            $nombreTotalArray[$niveau] = $statistique->nombre_total;
            $tempsTotalCmiArray[$niveau] = $timeConversionService->convertSecondsToHours($statistique->temps_total_cmi);
        }

        return [
            'labels' => $niveaux,
            'inscrits' => array_values($nombreTotalArray),
            'heures' => array_values($tempsTotalCmiArray)
        ];
    }

    public function getStatMooc($enrollfields, $project)
    {
        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $moocEnrolls = Enrollmooc::where('project_id', $project->id)->whereIn('learner_docebo_id', $learnersIds)->get();
        $moocEnrollsInWaiting = Enrollmooc::where('status', 'enrolled')->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();
        $moocEnrollsInEnrolled = Enrollmooc::where('status', 'enrolled')->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();
        $moocEnrollsInProgress = Enrollmooc::where('status', 'in_progress')->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();
        $moocEnrollsInCompleted = Enrollmooc::where('status', 'completed')->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();

        $statMooc = [
            'waiting' => $moocEnrollsInWaiting,
            'enrolled' => $moocEnrollsInEnrolled,
            'in_progress' => $moocEnrollsInProgress,
            'completed' => $moocEnrollsInCompleted,
        ];



        $timeConversionService = new TimeConversionService();
        $total_session_time = $timeConversionService->convertSecondsToTime($moocEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = $timeConversionService->convertSecondsToTime($moocEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = $timeConversionService->convertSecondsToTime($moocEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = $timeConversionService->convertSecondsToTime($moocEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time = "**h **min **s";
        }

        $statMoocTimes = [
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $moocCharts = [
            'labels' => ['en attente', 'Non démarré', 'En cours', 'Terminé'],
            'data' => [$moocEnrollsInWaiting, $moocEnrollsInEnrolled, $moocEnrollsInProgress, $moocEnrollsInCompleted]
        ];

        return [
            'statMooc' => $statMooc,
            'statMoocTimes' => $statMoocTimes,
            'moocCharts' => $moocCharts
        ];
    }

    public function getTimingStats($enrollfields, $project)
    {
        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $digitalModules = $project->modules->filter(function ($module) {
            return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();
        $softModules = $project->modules->filter(function ($module) {
            return $module->category === 'CEGOS' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();


        $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
        $digitalEnrolls = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
        $langEnrolls = Langenroll::where('project_id', $project->id)->whereIn('learner_docebo_id', $learnersIds)->get();
        $moocEnrolls = Enrollmooc::where('project_id', $project->id)->whereIn('learner_docebo_id', $learnersIds)->get();

        $timeConversionService = new TimeConversionService();

        $total_session_time_mooc = $timeConversionService->convertSecondsToHours($moocEnrolls->sum('session_time'));
        $total_session_time_speex = $timeConversionService->convertSecondsToHours($langEnrolls->sum('session_time'));
        $total_session_time_cegos = $timeConversionService->convertSecondsToHours($softEnrolls->sum('session_time'));
        $total_session_time_eni = $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time_mooc = $timeConversionService->convertSecondsToHours($moocEnrolls->sum('cmi_time'));
            $total_cmi_time_speex = $timeConversionService->convertSecondsToHours($langEnrolls->sum('cmi_time'));
            $total_cmi_time_cegos = $timeConversionService->convertSecondsToHours($softEnrolls->sum('cmi_time'));
            $total_cmi_time_eni = $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time_mooc = null;
            $total_cmi_time_speex = null;
            $total_cmi_time_cegos = null;
            $total_cmi_time_eni = null;
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time_mooc = $timeConversionService->convertSecondsToHours($moocEnrolls->sum('calculated_time'));
            $total_calculated_time_speex = $timeConversionService->convertSecondsToHours($langEnrolls->sum('calculated_time'));
            $total_calculated_time_cegos = $timeConversionService->convertSecondsToHours($softEnrolls->sum('calculated_time'));
            $total_calculated_time_eni = $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time_mooc = null;
            $total_calculated_time_speex = null;
            $total_calculated_time_cegos = null;
            $total_calculated_time_eni = null;
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time_mooc = $timeConversionService->convertSecondsToHours($moocEnrolls->sum('recommended_time'));
            $total_recommended_time_speex = $timeConversionService->convertSecondsToHours($langEnrolls->sum('recommended_time'));
            $total_recommended_time_cegos = $timeConversionService->convertSecondsToHours($softEnrolls->sum('recommended_time'));
            $total_recommended_time_eni = $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time_mooc = null;
            $total_recommended_time_speex = null;
            $total_recommended_time_cegos = null;
            $total_recommended_time_eni = null;
        }

        $timingChart = [
            'labels' => ['Modules softskills', 'Modules digitals', 'Modules langue', 'Mooc'],
            'session' => [$total_session_time_cegos, $total_session_time_eni, $total_session_time_speex, $total_session_time_mooc],
            'cmi' => $enrollfields['cmi_time'] == true ? [$total_cmi_time_cegos, $total_cmi_time_eni, $total_cmi_time_speex, $total_cmi_time_mooc] : [],
            'calculated' => $enrollfields['calculated_time'] == true ? [$total_calculated_time_cegos, $total_calculated_time_eni, $total_calculated_time_speex, $total_calculated_time_mooc] : [],
            'recommended' => $enrollfields['recommended_time'] == true ? [$total_recommended_time_cegos, $total_recommended_time_eni, $total_recommended_time_speex, $total_recommended_time_mooc] : []
        ];

        return $timingChart;
    }

    public function getCalculatedTimingStats($enrollfields, $project)
    {

        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $digitalModules = $project->modules->filter(function ($module) {
            return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();
        $softModules = $project->modules->filter(function ($module) {
            return $module->category === 'CEGOS' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();


        $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
        $digitalEnrolls = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->get();
        $langEnrolls = Langenroll::where('project_id', $project->id)->whereIn('learner_docebo_id', $learnersIds)->get();
        $moocEnrolls = Enrollmooc::where('project_id', $project->id)->whereIn('learner_docebo_id', $learnersIds)->get();


        $timeConversionService = new TimeConversionService();



        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time_mooc = $timeConversionService->convertSecondsToHours($moocEnrolls->sum('calculated_time'));
            $total_calculated_time_speex = $timeConversionService->convertSecondsToHours($langEnrolls->sum('calculated_time'));
            $total_calculated_time_cegos = $timeConversionService->convertSecondsToHours($softEnrolls->sum('calculated_time'));
            $total_calculated_time_eni = $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('calculated_time'));
            $total_calculated_time_total = $total_calculated_time_cegos + $total_calculated_time_eni + $total_calculated_time_speex + $total_calculated_time_mooc;

            $pr_calculated_time_mooc = $total_calculated_time_total != 0 ? ($total_calculated_time_mooc / $total_calculated_time_total) * 100 : 0;
            $pr_calculated_time_speex = $total_calculated_time_total != 0 ? ($total_calculated_time_speex / $total_calculated_time_total) * 100 : 0;
            $pr_calculated_time_cegos = $total_calculated_time_total != 0 ? ($total_calculated_time_cegos / $total_calculated_time_total) * 100 : 0;
            $pr_calculated_time_eni = $total_calculated_time_total != 0 ? ($total_calculated_time_eni / $total_calculated_time_total) * 100 : 0;
        } else {
            $total_calculated_time_mooc = $timeConversionService->convertSecondsToHours($moocEnrolls->sum('session_time'));
            $total_calculated_time_speex = $timeConversionService->convertSecondsToHours($langEnrolls->sum('session_time'));
            $total_calculated_time_cegos = $timeConversionService->convertSecondsToHours($softEnrolls->sum('session_time'));
            $total_calculated_time_eni = $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('session_time'));
            $total_calculated_time_total = $total_calculated_time_cegos + $total_calculated_time_eni + $total_calculated_time_speex + $total_calculated_time_mooc;

            $pr_calculated_time_mooc = $total_calculated_time_total != 0 ? ($total_calculated_time_mooc / $total_calculated_time_total) * 100 : 0;
            $pr_calculated_time_speex = $total_calculated_time_total != 0 ? ($total_calculated_time_speex / $total_calculated_time_total) * 100 : 0;
            $pr_calculated_time_cegos = $total_calculated_time_total != 0 ? ($total_calculated_time_cegos / $total_calculated_time_total) * 100 : 0;
            $pr_calculated_time_eni = $total_calculated_time_total != 0 ? ($total_calculated_time_eni / $total_calculated_time_total) * 100 : 0;
        }

        $softLabel = 'Modules softskills ( ' . $total_calculated_time_cegos . ' heures - ' . round($pr_calculated_time_cegos, 2) . ' %)';
        $eniLabel = 'Modules digitals ( ' . $total_calculated_time_eni . ' heures - ' . round($pr_calculated_time_eni, 2) . ' %)';
        $speexLabel = 'Modules langue ( ' . $total_calculated_time_speex . ' heures - ' . round($pr_calculated_time_speex, 2) . ' %)';
        $moocLabel = 'Mooc ( ' . $total_calculated_time_mooc . ' heures - ' . round($pr_calculated_time_mooc, 2) . ' %)';

        $timingChart = [
            'labels' => [$softLabel, $eniLabel, $speexLabel, $moocLabel],
            'data' => [round($pr_calculated_time_cegos, 2), round($pr_calculated_time_eni, 2), round($pr_calculated_time_speex, 2), round($pr_calculated_time_mooc, 2)]
        ];

        return $timingChart;
    }

    public function getLpStats($enrollfields, $project)
    {
        $lps = $project->lps;

        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $lpEnrolls = Lpenroll::where('project_id', $project->id)->whereIn('learner_docebo_id', $learnersIds)->get();

        $lpEnrollsInEnrolled = Lpenroll::where('status', 'not_started')->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();
        $lpEnrollsInProgress = Lpenroll::where('status', 'in_progress')->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();
        $lpEnrollsInProgressMax = Lpenroll::where('status', 'in_progress')->whereIn('learner_docebo_id', $learnersIds)->where('enrollment_completion_percentage', '>=', '50')->where('project_id', $project->id)->count();
        $lpEnrollsInProgressMin = Lpenroll::where('status', 'in_progress')->whereIn('learner_docebo_id', $learnersIds)->where('enrollment_completion_percentage', '<', '50')->where('project_id', $project->id)->count();
        $lpEnrollsInCompleted = Lpenroll::where('status', 'completed')->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $project->id)->count();

        $statLps = [
            'enrolled' => $lpEnrollsInEnrolled,
            'in_progress' => $lpEnrollsInProgress,
            'in_progress_max' => $lpEnrollsInProgressMax,
            'in_progress_min' => $lpEnrollsInProgressMin,
            'completed' => $lpEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time = $timeConversionService->convertSecondsToTime($lpEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = $timeConversionService->convertSecondsToTime($lpEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = $timeConversionService->convertSecondsToTime($lpEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = $timeConversionService->convertSecondsToTime($lpEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time = "**h **min **s";
        }

        $statLpsTimes = [
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $lpCharts = [
            'labels' => ['Non démarré', 'En cours', 'Moins de 50% d\'avancement', 'Plus 50% d\'avancement', 'Terminé'],
            'data' => [$lpEnrollsInEnrolled, $lpEnrollsInProgress, $lpEnrollsInProgressMin, $lpEnrollsInProgressMax, $lpEnrollsInCompleted]
        ];

        return [
            'statLps' => $statLps,
            'statLpsTimes' => $statLpsTimes,
            'lpCharts' => $lpCharts,
            'lps' => $lps
        ];
    }

    public function geStatsPerLp($enrollfields, $selectedLp, $projectId)
    {
        $project = Project::find($projectId);
        $lps = $project->lps;

        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $lpEnrolls = Lpenroll::where('project_id', $projectId)->whereIn('learner_docebo_id', $learnersIds)->where('lp_docebo_id', $selectedLp)->get();

        $lpEnrollsInEnrolled = Lpenroll::where('project_id', $projectId)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInProgress = Lpenroll::where('project_id', $projectId)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInProgressMax = Lpenroll::where('project_id', $projectId)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->where('enrollment_completion_percentage', '>=', '50')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInProgressMin = Lpenroll::where('project_id', $projectId)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->where('enrollment_completion_percentage', '<', '50')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInCompleted = Lpenroll::where('project_id', $projectId)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->where('lp_docebo_id', $selectedLp)->count();

        $statLps = [
            'enrolled' => $lpEnrollsInEnrolled,
            'in_progress' => $lpEnrollsInProgress,
            'in_progress_max' => $lpEnrollsInProgressMax,
            'in_progress_min' => $lpEnrollsInProgressMin,
            'completed' => $lpEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time = $timeConversionService->convertSecondsToTime($lpEnrolls->sum('session_time'));

        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = $timeConversionService->convertSecondsToTime($lpEnrolls->sum('cmi_time'));
        } else {
            $total_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = $timeConversionService->convertSecondsToTime($lpEnrolls->sum('calculated_time'));
        } else {
            $total_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = $timeConversionService->convertSecondsToTime($lpEnrolls->sum('recommended_time'));
        } else {
            $total_recommended_time = "**h **min **s";
        }

        $statLpsTimes = [
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];
        $lpCharts = [
            'labels' => ['Non démarré', 'En cours', 'Moins de 50% d\'avancement', 'Plus 50% d\'avancement', 'Terminé'],
            'data' => [$lpEnrollsInEnrolled, $lpEnrollsInProgress, $lpEnrollsInProgressMin, $lpEnrollsInProgressMax, $lpEnrollsInCompleted]
        ];

        return [
            'statLps' => $statLps,
            'statLpsTimes' => $statLpsTimes,
            'lpCharts' => $lpCharts,
            'lps' => $lps
        ];
    }

    public function getLscStats($project)
    {
        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $project->id)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $project->id)->pluck('docebo_id')->toArray();
        }

        $totalTickets = Ticket::where('project_id', $project->id)->whereIn('learner_docebo_id', $learnersIds)->count();
        $totalCalls = Call::where('project_id', $project->id)->whereIn('learner_docebo_id', $learnersIds)->count();
        $ticketDistribution = Ticket::select('status', DB::raw('count(*) as count'))
            ->where('project_id', $project->id)
            ->whereIn('learner_docebo_id', $learnersIds)
            ->groupBy('status')
            ->get();
        $ticketsLabels = [];
        $ticketsData = [];
        foreach ($ticketDistribution as $distribution) {
            $ticketsLabels[] = $distribution->status;
            $ticketsData[] = $distribution->count;
        }

        $ticketsCharts = [
            'labels' => $ticketsLabels,
            'data' => $ticketsData
        ];


        $callStatisticsSubject = Call::select('subject', 'type', DB::raw('COUNT(*) as call_count'))
            ->where('project_id', $project->id)
            ->whereIn('learner_docebo_id', $learnersIds)
            ->groupBy('subject', 'type')
            ->get();
        $groupedStatisticsSubject = [];
        foreach ($callStatisticsSubject as $stat) {
            $groupedStatisticsSubject[$stat->subject][$stat->type] = $stat->call_count;
        }
        $labelsCallsSubject = [];
        $dataCallsSubjectEntrantes = [];
        $dataCallsSubjectSortantes = [];
        foreach ($groupedStatisticsSubject as $key => $value) {
            $labelsCallsSubject[] = $key;
            $dataCallsSubjectEntrantes[] = isset($groupedStatisticsSubject[$key]['entrante']) ? $groupedStatisticsSubject[$key]['entrante'] : 0;
            $dataCallsSubjectSortantes[] = isset($groupedStatisticsSubject[$key]['sortante']) ? $groupedStatisticsSubject[$key]['sortante'] : 0;
        }

        $callStatisticsStatus = Call::select('status', 'type', DB::raw('COUNT(*) as call_count'))
            ->where('project_id', $project->id)
            ->whereIn('learner_docebo_id', $learnersIds)
            ->groupBy('status', 'type')
            ->get();
        $groupedStatisticsStatus = [];
        foreach ($callStatisticsStatus as $stat) {
            $groupedStatisticsStatus[$stat->status][$stat->type] = $stat->call_count;
        }

        $labelsCallsStatus = [];
        $dataCallsStatusEntrantes = [];
        $dataCallsStatusSortantes = [];
        foreach ($groupedStatisticsStatus as $key => $value) {
            $labelsCallsStatus[] = $key;
            $dataCallsStatusEntrantes[] = isset($groupedStatisticsStatus[$key]['entrante']) ? $groupedStatisticsStatus[$key]['entrante'] : 0;
            $dataCallsStatusSortantes[] = isset($groupedStatisticsStatus[$key]['sortante']) ? $groupedStatisticsStatus[$key]['sortante'] : 0;
        }

        $callsPerStatutAndTypeChart = [
            'labels' => $labelsCallsStatus,
            'reçu' => $dataCallsStatusEntrantes,
            'emis' => $dataCallsStatusSortantes
        ];

        $callsPerSubjectAndTypeChart = [
            'labels' => $labelsCallsSubject,
            'reçu' => $dataCallsSubjectEntrantes,
            'emis' => $dataCallsSubjectSortantes
        ];

        return [
            'totalTickets' => $totalTickets,
            'totalCalls' => $totalCalls,
            'ticketsCharts' => $ticketsCharts,
            'callsPerSubjectAndTypeChart' => $callsPerSubjectAndTypeChart,
            'callsPerStatutAndTypeChart' => $callsPerStatutAndTypeChart,
        ];
    }

    public function getLearnersInscriptionsPerDate($startDate, $endDate, $projectId)
    {
        $total_learners = Learner::whereBetween('creation_date', [$startDate, $endDate])->where('project_id', $projectId)->count();
        $active_learners = Learner::whereNotNull('last_access_date')->whereBetween('last_access_date', [$startDate, $endDate])->where('statut', 'active')->where('project_id', $projectId)->count();
        $inactive_learners = Learner::whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'inactive')->where('project_id', $projectId)->count();
        return [
            'total' => $total_learners,
            'active' => $active_learners,
            'inactive' => $inactive_learners,
        ];
    }

    public function getTimingDetailsPerDate($enrollfields, $startDate, $endDate, $projectId)
    {
        $archive = config('tenantconfigfields.archive');
        if ($archive == true) {
            $learners = Learner::whereIn('statut', ['active', 'archive'])->whereBetween('last_access_date', [$startDate, $endDate])->where('project_id', $projectId)->count();
            $learnersIds = Learner::whereIn('statut', ['active', 'archive'])->whereBetween('last_access_date', [$startDate, $endDate])->where('project_id', $projectId)->pluck('docebo_id')->toArray();
        } else {
            $learners = Learner::where('statut', 'active')->whereBetween('last_access_date', [$startDate, $endDate])->count();
            $learnersIds = Learner::where('statut', 'active')->whereBetween('last_access_date', [$startDate, $endDate])->pluck('docebo_id')->toArray();
        }

        $moduleDataTimes = Enrollmodule::calculateModuleDataTimesBetweenDatePerProject($startDate, $endDate, $projectId, $learnersIds);
        $moocDataTimes = Enrollmooc::calculateMoocDataTimesBetweenDatePerProject($startDate, $endDate, $projectId, $learnersIds);
        $speexDataTimes = Langenroll::calculateSpeexDataTimesBetweenDatePerProject($startDate, $endDate, $projectId, $learnersIds);

        $timeConversionService = new TimeConversionService();
        $total_session_time = intval($moduleDataTimes->total_session_time) + intval($moocDataTimes->total_session_time) + intval($speexDataTimes->total_session_time);
        $avg_session_time = $learners != 0 ? intval($total_session_time / $learners) : 0;
        $total_session_time = $timeConversionService->convertSecondsToTime($total_session_time);
        $avg_session_time = $timeConversionService->convertSecondsToTime($avg_session_time);


        if ($enrollfields['cmi_time'] == true) {
            $total_cmi_time = intval($moduleDataTimes->total_cmi_time) + intval($moocDataTimes->total_cmi_time) + intval($speexDataTimes->total_cmi_time);
            $avg_cmi_time = $learners != 0 ? intval($total_cmi_time / $learners) : 0;
            $total_cmi_time = $timeConversionService->convertSecondsToTime($total_cmi_time);
            $avg_cmi_time = $timeConversionService->convertSecondsToTime($avg_cmi_time);
        } else {
            $total_cmi_time = "**h **min **s";
            $avg_cmi_time = "**h **min **s";
        }

        if ($enrollfields['calculated_time'] == true) {
            $total_calculated_time = intval($moduleDataTimes->total_calculated_time) + intval($moocDataTimes->total_calculated_time) + intval($speexDataTimes->total_calculated_time);
            $avg_calculated_time = $learners != 0 ? intval($total_calculated_time / $learners) : 0;
            $total_calculated_time = $timeConversionService->convertSecondsToTime($total_calculated_time);
            $avg_calculated_time = $timeConversionService->convertSecondsToTime($avg_calculated_time);
        } else {
            $total_calculated_time = "**h **min **s";
            $avg_calculated_time = "**h **min **s";
        }

        if ($enrollfields['recommended_time'] == true) {
            $total_recommended_time = intval($moduleDataTimes->total_recommended_time) + intval($moocDataTimes->total_recommended_time) + intval($speexDataTimes->total_recommended_time);
            $avg_recommended_time = $learners != 0 ? intval($total_recommended_time / $learners) : 0;
            $total_recommended_time = $timeConversionService->convertSecondsToTime($total_recommended_time);
            $avg_recommended_time = $timeConversionService->convertSecondsToTime($avg_recommended_time);
        } else {
            $total_recommended_time = "**h **min **s";
            $avg_recommended_time = "**h **min **s";
        }
        return [
            'total_session_time' => $total_session_time,
            'avg_session_time' => $avg_session_time,
            'total_cmi_time' => $total_cmi_time,
            'avg_cmi_time' => $avg_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'avg_calculated_time' => $avg_calculated_time,
            'total_recommended_time' => $total_recommended_time,
            'avg_recommended_time' => $avg_recommended_time,

        ];
    }

    public function getLearnersChartsPerDate($categorie, $startDate, $endDate, $projectId)
    {
        if ($categorie) {
            $archive = config('tenantconfigfields.archive');
            if ($archive == true) {
                $learnerCounts = DB::table('learners')
                    ->select('categorie', DB::raw('count(*) as total'))
                    ->whereBetween('creation_date', [$startDate, $endDate])
                    ->where('project_id', $projectId)
                    ->groupBy('categorie')
                    ->get();

                $totalLearners = DB::table('learners')->whereBetween('creation_date', [$startDate, $endDate])->where('project_id', $projectId)->count();
            } else {
                $learnerCounts = DB::table('learners')
                    ->select('categorie', DB::raw('count(*) as total'))
                    ->where('statut', '!=', 'archive')
                    ->whereBetween('creation_date', [$startDate, $endDate])
                    ->where('project_id', $projectId)
                    ->groupBy('categorie')
                    ->get();

                $totalLearners = DB::table('learners')->whereBetween('creation_date', [$startDate, $endDate])->where('statut', '!=', 'archive')->where('project_id', $projectId)->count();
            }



            $data = [];
            $labels = [];

            foreach ($learnerCounts as $count) {
                $percentage = round(($count->total / $totalLearners) * 100, 2);
                $data[] = $count->total;
                $labels[] = $count->categorie !== null ? ucfirst($count->categorie) . ' ' . $count->total . ' - (' . $percentage . '%)' : 'Indéterminé' . ' ' . $count->total . ' - (' . $percentage . '%)';
            }

            $chartInscritPerCategorie = [
                'labels' => $labels,
                'data' => $data
            ];

            $categories = Learner::distinct()->where('project_id', $projectId)->pluck('categorie')->filter();

            $counts = [
                'Active' => [],
                'Inactive' => [],
            ];

            foreach ($categories as $category) {
                $counts['Active'][] = Learner::where('categorie', $category)->whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'active')->where('project_id', $projectId)->count();
                $counts['Inactive'][] = Learner::where('categorie', $category)->whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'inactive')->where('project_id', $projectId)->count();
            }
            $chartInscritPerCategoryAndStatus = [
                'labels' => $categories->toArray(),
                'actives' => $counts['Active'],
                'inactives' => $counts['Inactive'],
            ];
        } else {
            $chartInscritPerCategorie = null;
            $chartInscritPerCategoryAndStatus = null;
        }

        return [
            'chartInscritPerCategorie' => $chartInscritPerCategorie,
            'chartInscritPerCategoryAndStatus' => $chartInscritPerCategoryAndStatus
        ];
    }

    public function getLscStatsPerDate($startDate, $endDate, $projectId)
    {
        $archive = config('tenantconfigfields.archive');
        if ($archive != true) {
            $learnersIds = Learner::where('statut', '!=', 'archive')->where('project_id', $projectId)->pluck('docebo_id')->toArray();
        } else {
            $learnersIds = Learner::where('project_id', $projectId)->pluck('docebo_id')->toArray();
        }

        $totalTickets = Ticket::whereBetween('ticket_created_at', [$startDate, $endDate])->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $projectId)->count();
        $totalCalls = Call::whereBetween('date_call', [$startDate, $endDate])->whereIn('learner_docebo_id', $learnersIds)->where('project_id', $projectId)->count();
        $ticketDistribution = Ticket::select('status', DB::raw('count(*) as count'))
            ->whereBetween('ticket_created_at', [$startDate, $endDate])
            ->where('project_id', $projectId)
            ->whereIn('learner_docebo_id', $learnersIds)
            ->groupBy('status')
            ->get();
        $ticketsLabels = [];
        $ticketsData = [];
        foreach ($ticketDistribution as $distribution) {
            $ticketsLabels[] = $distribution->status . '- (' . $distribution->count . ')';
            $ticketsData[] = $distribution->count;
        }
        $ticketsCharts = [
            'labels' => $ticketsLabels,
            'data' => $ticketsData
        ];

        $callStatisticsSubject = Call::select('subject', 'type', DB::raw('COUNT(*) as call_count'))
            ->whereBetween('date_call', [$startDate, $endDate])
            ->whereIn('learner_docebo_id', $learnersIds)
            ->where('project_id', $projectId)
            ->groupBy('subject', 'type')
            ->get();
        $groupedStatisticsSubject = [];
        foreach ($callStatisticsSubject as $stat) {
            $groupedStatisticsSubject[$stat->subject][$stat->type] = $stat->call_count;
        }
        $labelsCallsSubject = [];
        $dataCallsSubjectEntrantes = [];
        $dataCallsSubjectSortantes = [];
        foreach ($groupedStatisticsSubject as $key => $value) {
            $labelsCallsSubject[] = $key;
            $dataCallsSubjectEntrantes[] = isset($groupedStatisticsSubject[$key]['entrante']) ? $groupedStatisticsSubject[$key]['entrante'] : 0;
            $dataCallsSubjectSortantes[] = isset($groupedStatisticsSubject[$key]['sortante']) ? $groupedStatisticsSubject[$key]['sortante'] : 0;
        }

        $callStatisticsStatus = Call::select('status', 'type', DB::raw('COUNT(*) as call_count'))
            ->whereBetween('date_call', [$startDate, $endDate])
            ->whereIn('learner_docebo_id', $learnersIds)
            ->where('project_id', $projectId)
            ->groupBy('status', 'type')
            ->get();
        $groupedStatisticsStatus = [];
        foreach ($callStatisticsStatus as $stat) {
            $groupedStatisticsStatus[$stat->status][$stat->type] = $stat->call_count;
        }

        $labelsCallsStatus = [];
        $dataCallsStatusEntrantes = [];
        $dataCallsStatusSortantes = [];
        foreach ($groupedStatisticsStatus as $key => $value) {
            $labelsCallsStatus[] = $key;
            $dataCallsStatusEntrantes[] = isset($groupedStatisticsStatus[$key]['entrante']) ? $groupedStatisticsStatus[$key]['entrante'] : 0;
            $dataCallsStatusSortantes[] = isset($groupedStatisticsStatus[$key]['sortante']) ? $groupedStatisticsStatus[$key]['sortante'] : 0;
        }
        $callsPerStatutAndTypeChart = [
            'labels' => $labelsCallsStatus,
            'reçu' => $dataCallsStatusEntrantes,
            'emis' => $dataCallsStatusSortantes
        ];

        $callsPerSubjectAndTypeChart = [
            'labels' => $labelsCallsSubject,
            'reçu' => $dataCallsSubjectEntrantes,
            'emis' => $dataCallsSubjectSortantes
        ];
        return [
            'totalTickets' => $totalTickets,
            'totalCalls' => $totalCalls,
            'ticketsCharts' => $ticketsCharts,
            'callsPerSubjectAndTypeChart' => $callsPerSubjectAndTypeChart,
            'callsPerStatutAndTypeChart' => $callsPerStatutAndTypeChart,
        ];
    }
}
