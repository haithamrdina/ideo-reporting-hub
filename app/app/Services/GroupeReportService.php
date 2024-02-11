<?php


namespace App\Services;

use App\Enums\CourseStatusEnum;
use App\Models\Call;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Group;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Lpenroll;
use App\Models\Module;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class GroupeReportService{

    public function getLearnersInscriptionsPerStatDate($contract_start_date_conf , $groupe){

        if($contract_start_date_conf != null){
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
            $yearOfDate = $date->year;
            $currentYear = now()->year;

            if ($yearOfDate > $currentYear) {
                $statDate = now()->year . $date->format('-m-d');
            } else {
                $statDate =  (now()->year - 1) . $date->format('-m-d');
            }

            $total_learners = Learner::where('creation_date', '>=', $statDate)->where('group_id', $groupe->id)->count();
            $active_learners = Learner::where('last_access_date', '>=', $statDate)->where('statut', 'active')->where('group_id', $groupe->id)->count();
            $inactive_learners =  Learner::where('creation_date' , '>=' , $statDate)->where('statut', 'inactive')->where('group_id', $groupe->id)->count();
            $statsLearners = [
                'total' => $total_learners,
                'active' => $active_learners,
                'inactive' => $inactive_learners,
            ];
        } else{
            $statsLearners = null;
        }

        return $statsLearners;

    }

    public function getTimingDetailsPerStatDate($contract_start_date_conf,$enrollfields,$groupe){

        if($contract_start_date_conf != null){
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
            $yearOfDate = $date->year;
            $currentYear = now()->year;

            if ($yearOfDate > $currentYear) {
                $statDate = now()->year . $date->format('-m-d');
            } else {
                $statDate =  (now()->year - 1) . $date->format('-m-d');
            }

            $active_learners = Learner::where('last_access_date', '>=', $statDate)->where('statut', 'active')->where('group_id', $groupe->id)->count();
            $moduleDataTimes = Enrollmodule::calculateModuleDataTimesPerGroup($statDate, $groupe->id);
            $moocDataTimes = Enrollmooc::calculateMoocDataTimesPerGroup($statDate, $groupe->id);
            $speexDataTimes = Langenroll::calculateSpeexDataTimesPerGroup($statDate, $groupe->id);


            $timeConversionService = new TimeConversionService();
            $total_session_time = intval($moduleDataTimes->total_session_time) + intval($moocDataTimes->total_session_time) + intval($speexDataTimes->total_session_time);
            $avg_session_time =  $active_learners != 0 ? intval($total_session_time/$active_learners) : 0;
            $total_session_time = $timeConversionService->convertSecondsToTime($total_session_time);
            $avg_session_time = $timeConversionService->convertSecondsToTime($avg_session_time);


            if($enrollfields['cmi_time'] == true)
            {
                $total_cmi_time = intval($moduleDataTimes->total_cmi_time) + intval($moocDataTimes->total_cmi_time) + intval($speexDataTimes->total_cmi_time);
                $avg_cmi_time =  $active_learners != 0 ?  intval($total_cmi_time/$active_learners) :  0;
                $total_cmi_time = $timeConversionService->convertSecondsToTime($total_cmi_time);
                $avg_cmi_time = $timeConversionService->convertSecondsToTime($avg_cmi_time);
            }else{
                $total_cmi_time = "**h **min **s";
                $avg_cmi_time = "**h **min **s";
            }

            if($enrollfields['calculated_time'] == true)
            {
                $total_calculated_time = intval($moduleDataTimes->total_calculated_time) + intval($moocDataTimes->total_calculated_time) + intval($speexDataTimes->total_calculated_time);
                $avg_calculated_time =  $active_learners != 0 ?  intval($total_calculated_time/$active_learners) : 0;
                $total_calculated_time = $timeConversionService->convertSecondsToTime($total_calculated_time);
                $avg_calculated_time = $timeConversionService->convertSecondsToTime($avg_calculated_time);
            }else{
                $total_calculated_time = "**h **min **s";
                $avg_calculated_time = "**h **min **s";
            }

            if($enrollfields['recommended_time'] == true)
            {
                $total_recommended_time = intval($moduleDataTimes->total_recommended_time) + intval($moocDataTimes->total_recommended_time) + intval($speexDataTimes->total_recommended_time);
                $avg_recommended_time =  $active_learners != 0 ?  intval($total_recommended_time/$active_learners) :  0;
                $total_recommended_time = $timeConversionService->convertSecondsToTime($total_recommended_time);
                $avg_recommended_time = $timeConversionService->convertSecondsToTime($avg_recommended_time);
            }else{
                $total_recommended_time = "**h **min **s";
                $avg_recommended_time = "**h **min **s";
            }


            return [
                'total_session_time' =>$total_session_time ,
                'avg_session_time' =>$avg_session_time ,
                'total_cmi_time' =>$total_cmi_time ,
                'avg_cmi_time' =>$avg_cmi_time ,
                'total_calculated_time' =>$total_calculated_time ,
                'avg_calculated_time' =>$avg_calculated_time ,
                'total_recommended_time' =>$total_recommended_time ,
                'avg_recommended_time' =>$avg_recommended_time ,

            ];

        }else{
            $statsTimes = null;
        }

        return $statsTimes;
    }

    public function getLearnersInscriptions($groupe){
        $total_learners = Learner::where('group_id', $groupe->id)->count();
        $active_learners = Learner::whereNotNull('last_access_date')->where('group_id', $groupe->id)->count();
        $inactive_learners =  Learner::whereNull('last_access_date')->where('group_id', $groupe->id)->count();
        $archive_learners =  Learner::whereNotNull('deleted_at')->where('group_id', $groupe->id)->count();

        return  [
            'total' => $total_learners,
            'active' => $active_learners,
            'inactive' => $inactive_learners,
            'archive' => $archive_learners
        ];
    }

    public function getTimingDetails($enrollfields,$groupe){
        $active_learners = Learner::whereNotNull('last_access_date')->where('group_id', $groupe->id)->count();
        $enrollModules = EnrollModule::where('group_id', $groupe->id)->get();
        $enrollMoocs = Enrollmooc::where('group_id', $groupe->id)->get();
        $enrollSpeex = Langenroll::where('group_id', $groupe->id)->get();

        $timeConversionService = new TimeConversionService();
        $total_session_time =  intval($enrollModules->sum('session_time')) + intval($enrollMoocs->sum('session_time')) + intval($enrollSpeex->sum('session_time'));
        $avg_session_time =  $active_learners != 0 ? intval($total_session_time/$active_learners) : 0;
        $total_session_time = $timeConversionService->convertSecondsToTime($total_session_time);
        $avg_session_time = $timeConversionService->convertSecondsToTime($avg_session_time);


        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time = intval($enrollModules->sum('cmi_time')) + intval($enrollMoocs->sum('cmi_time')) + intval($enrollSpeex->sum('cmi_time'));
            $avg_cmi_time =  $active_learners != 0 ?  intval($total_cmi_time/$active_learners) :  0;
            $total_cmi_time = $timeConversionService->convertSecondsToTime($total_cmi_time);
            $avg_cmi_time = $timeConversionService->convertSecondsToTime($avg_cmi_time);
        }else{
            $total_cmi_time = "**h **min **s";
            $avg_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time = intval($enrollModules->sum('calculated_time')) + intval($enrollMoocs->sum('calculated_time')) + intval($enrollSpeex->sum('calculated_time'));;
            $avg_calculated_time =  $active_learners != 0 ?  intval($total_calculated_time/$active_learners) : 0;
            $total_calculated_time = $timeConversionService->convertSecondsToTime($total_calculated_time);
            $avg_calculated_time = $timeConversionService->convertSecondsToTime($avg_calculated_time);
        }else{
            $total_calculated_time = "**h **min **s";
            $avg_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time = intval($enrollModules->sum('recommended_time')) + intval($enrollMoocs->sum('recommended_time')) + intval($enrollSpeex->sum('recommended_time'));;
            $avg_recommended_time =  $active_learners != 0 ?  intval($total_recommended_time/$active_learners) :  0;
            $total_recommended_time = $timeConversionService->convertSecondsToTime($total_recommended_time);
            $avg_recommended_time = $timeConversionService->convertSecondsToTime($avg_recommended_time);
        }else{
            $total_recommended_time = "**h **min **s";
            $avg_recommended_time = "**h **min **s";
        }


        return [
            'total_session_time' =>$total_session_time ,
            'avg_session_time' =>$avg_session_time ,
            'total_cmi_time' =>$total_cmi_time ,
            'avg_cmi_time' =>$avg_cmi_time ,
            'total_calculated_time' =>$total_calculated_time ,
            'avg_calculated_time' =>$avg_calculated_time ,
            'total_recommended_time' =>$total_recommended_time ,
            'avg_recommended_time' =>$avg_recommended_time ,

        ];

    }

    public function getLearnersCharts($categorie,$groupe)
    {
        if($categorie){
            $learnerCounts = DB::table('learners')
            ->select('categorie', DB::raw('count(*) as total'))
            ->where('group_id', $groupe->id)
            ->groupBy('categorie')
            ->get();

            $totalLearners = DB::table('learners')->where('group_id', $groupe->id)->count();

            $data = [];
            $labels = [];

            foreach ($learnerCounts as $count) {
                $percentage = round(($count->total / $totalLearners) * 100 , 2);
                $data [] = $count->total;
                $labels [] = $count->categorie !== null ?  ucfirst($count->categorie) .' '.  $count->total .  ' - (' . $percentage .'%)' : ' Indéterminé'.' '.  $count->total .  ' - (' . $percentage .'%)' ;
            }

            $chartInscritPerCategorie = [
                'labels' => $labels,
                'data' => $data
            ];



            $categories = Learner::where('group_id', $groupe->id)->distinct()->pluck('categorie')->filter();

            $counts = [
                'Active' => [],
                'Inactive' => [],
                'Archive' => [],
            ];

            foreach ($categories as $category) {
                $counts['Active'][] = Learner::where('categorie', $category)->where('statut', 'active')->where('group_id', $groupe->id)->count();
                $counts['Inactive'][] = Learner::where('categorie', $category)->where('statut', 'inactive')->where('group_id', $groupe->id)->count();
                $counts['Archive'][] = Learner::where('categorie', $category)->where('statut', 'archive')->where('group_id', $groupe->id)->count();
            }

            $chartInscritPerCategoryAndStatus =[
                'labels' => $categories->toArray(),
                'actives' => $counts['Active'],
                'inactives' => $counts['Inactive'],
            ];

        }else{
            $chartInscritPerCategorie = null;
            $chartInscritPerCategoryAndStatus = null;
        }

        return [
           'chartInscritPerCategorie' => $chartInscritPerCategorie,
           'chartInscritPerCategoryAndStatus' => $chartInscritPerCategoryAndStatus
        ];
    }

    public function getStatSoftskills($enrollfields,$groupe){

        $softModules = $groupe->modules->filter(function ($module) {
            return $module->category === 'CEGOS' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();

        $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('group_id', $groupe->id)->get();

        $softEnrollsInEnrolled = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('status', 'enrolled')->where('group_id', $groupe->id)->count();
        $softEnrollsInProgress = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('status', 'in_progress')->where('group_id', $groupe->id)->count();
        $softEnrollsInCompleted = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('status', 'completed')->where('group_id', $groupe->id)->count();

        $statSoftskills = [
            'enrolled' =>  $softEnrollsInEnrolled,
            'in_progress' => $softEnrollsInProgress,
            'completed' => $softEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time =  $timeConversionService->convertSecondsToTime($softEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time =  $timeConversionService->convertSecondsToTime($softEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time =  $timeConversionService->convertSecondsToTime($softEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time =  $timeConversionService->convertSecondsToTime($softEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time = "**h **min **s";
        }

        $statSoftTimes =[
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

    public function getStatDigital($enrollfields,$groupe){
        $digitalModules = $groupe->modules->filter(function ($module) {
            return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();

        $moduleDigitals = $groupe->modules->filter(function ($module) {
            return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
        })->values();

        $digitalEnrolls = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->where('group_id', $groupe->id)->get();

        $digitalEnrollsInEnrolled = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->where('status', 'enrolled')->where('group_id', $groupe->id)->count();
        $digitalEnrollsInProgress = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->where('status', 'in_progress')->where('group_id', $groupe->id)->count();
        $digitalEnrollsInCompleted = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->where('status', 'completed')->where('group_id', $groupe->id)->count();

        $statDigital = [
            'enrolled' =>  $digitalEnrollsInEnrolled,
            'in_progress' => $digitalEnrollsInProgress,
            'completed' => $digitalEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time =  $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time =  $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time =  $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time =  $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time = "**h **min **s";
        }

        $statDigitalTimes =[
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $digitalCharts = [
            'labels' => ['Non démarré', 'En cours', 'Terminé'],
            'data' =>  [$digitalEnrollsInEnrolled, $digitalEnrollsInProgress, $digitalEnrollsInCompleted]
        ];

        return [
            'statDigital' => $statDigital,
            'statDigitalTimes' => $statDigitalTimes,
            'digitalCharts' => $digitalCharts,
            'modulesDigital' => $moduleDigitals
        ];
    }

    public function getStatDigitalPerModule($enrollfields, $selectedDigital, $groupId){
        $groupe = Group::find($groupId);

        $moduleDigitals = $groupe->modules->filter(function ($module) {
            return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
        })->values();

        $digitalEnrolls = Enrollmodule::where('module_docebo_id', $selectedDigital)->where('group_id', $groupe->id)->get();

        $digitalEnrollsInEnrolled = Enrollmodule::where('module_docebo_id', $selectedDigital)->where('status', 'enrolled')->where('group_id', $groupId)->count();
        $digitalEnrollsInProgress = Enrollmodule::where('module_docebo_id', $selectedDigital)->where('status', 'in_progress')->where('group_id', $groupId)->count();
        $digitalEnrollsInCompleted = Enrollmodule::where('module_docebo_id', $selectedDigital)->where('status', 'completed')->where('group_id', $groupId)->count();

        $statDigital = [
            'enrolled' =>  $digitalEnrollsInEnrolled,
            'in_progress' => $digitalEnrollsInProgress,
            'completed' => $digitalEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time =  $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time =  $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time =  $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time =  $timeConversionService->convertSecondsToTime($digitalEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time = "**h **min **s";
        }

        $statDigitalTimes =[
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $digitalCharts = [
            'labels' => ['Non démarré', 'En cours', 'Terminé'],
            'data' =>  [$digitalEnrollsInEnrolled, $digitalEnrollsInProgress, $digitalEnrollsInCompleted]
        ];

        return [
            'statDigital' => $statDigital,
            'statDigitalTimes' => $statDigitalTimes,
            'digitalCharts' => $digitalCharts,
            'modulesDigital' => $moduleDigitals
        ];
    }

    public function getStatSpeex($enrollfields,$groupe){
        $langEnrollsInEnrolled = Langenroll::whereIn('status', ['enrolled', 'waiting'])->where('group_id', $groupe->id)->count();
        $langEnrollsInProgress = Langenroll::where('status', 'in_progress')->where('group_id', $groupe->id)->count();
        $langEnrollsInCompleted = Langenroll::where('status', 'completed')->where('group_id', $groupe->id)->count();

        $statSpeex = [
            'enrolled' =>  $langEnrollsInEnrolled,
            'in_progress' => $langEnrollsInProgress,
            'completed' => $langEnrollsInCompleted,
        ];

        $langEnrolls = Langenroll::where('group_id', $groupe->id)->get();

        $timeConversionService = new TimeConversionService();
        $total_session_time =  $timeConversionService->convertSecondsToTime($langEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time =  $timeConversionService->convertSecondsToTime($langEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time =  $timeConversionService->convertSecondsToTime($langEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time =  $timeConversionService->convertSecondsToTime($langEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time = "**h **min **s";
        }

        $statSpeexTimes =[
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $speexLangues = Langenroll::distinct()->where('group_id', $groupe->id)->pluck('language');
        return [
            'statSpeex' => $statSpeex,
            'statSpeexTimes' => $statSpeexTimes,
            'speexLangues' => $speexLangues,
        ];
    }

    public function getStatSpeexChart($groupId, $selectedLanguage){
        $timeConversionService = new TimeConversionService();
        $niveaux = ['A1', 'A2', 'B1.1', 'B1.2', 'B2.1', 'B2.2', 'C1.1', 'C1.2', 'Indéterminé'];

        $statistiques = Langenroll::selectRaw('niveau, COUNT(*) AS nombre_total, SUM(cmi_time) AS temps_total_cmi')
            ->where('language', $selectedLanguage)
            ->where('group_id', $groupId)
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
            $niveau = $statistique->niveau == '' ? 'Indéterminé': $statistique->niveau;
            $nombreTotalArray[$niveau] = $statistique->nombre_total;
            $tempsTotalCmiArray[$niveau] = $timeConversionService->convertSecondsToHours($statistique->temps_total_cmi);
        }

        return [
            'labels' => $niveaux,
            'inscrits' => array_values($nombreTotalArray),
            'heures' => array_values($tempsTotalCmiArray)
        ];
    }

    public function getStatMooc($enrollfields,$groupe){
        $moocEnrollsInWaiting = Enrollmooc::where('status', 'enrolled')->where('group_id', $groupe->id)->count();
        $moocEnrollsInEnrolled = Enrollmooc::where('status', 'enrolled')->where('group_id', $groupe->id)->count();
        $moocEnrollsInProgress = Enrollmooc::where('status', 'in_progress')->where('group_id', $groupe->id)->count();
        $moocEnrollsInCompleted = Enrollmooc::where('status', 'completed')->where('group_id', $groupe->id)->count();

        $statMooc = [
            'waiting' =>  $moocEnrollsInWaiting,
            'enrolled' =>  $moocEnrollsInEnrolled,
            'in_progress' => $moocEnrollsInProgress,
            'completed' => $moocEnrollsInCompleted,
        ];

        $moocEnrolls = Enrollmooc::where('group_id', $groupe->id)->get();

        $timeConversionService = new TimeConversionService();
        $total_session_time =  $timeConversionService->convertSecondsToTime($moocEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time =  $timeConversionService->convertSecondsToTime($moocEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time =  $timeConversionService->convertSecondsToTime($moocEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time =  $timeConversionService->convertSecondsToTime($moocEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time = "**h **min **s";
        }

        $statMoocTimes =[
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $moocCharts = [
            'labels' => ['en attente', 'Non démarré', 'En cours', 'Terminé'] ,
            'data' => [$moocEnrollsInWaiting, $moocEnrollsInEnrolled, $moocEnrollsInProgress, $moocEnrollsInCompleted]
        ];

        return [
            'statMooc' => $statMooc,
            'statMoocTimes' => $statMoocTimes,
            'moocCharts' => $moocCharts
        ];
    }

    public function getTimingStats($enrollfields,$groupe){

        $digitalModules = $groupe->modules->filter(function ($module) {
            return $module->category === 'ENI' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();
        $softModules = $groupe->modules->filter(function ($module) {
            return $module->category === 'CEGOS' && $module->status === CourseStatusEnum::ACTIVE;
        })->pluck('docebo_id')->toArray();
        $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->where('group_id', $groupe->id)->get();
        $digitalEnrolls = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->where('group_id', $groupe->id)->get();
        $langEnrolls = Langenroll::where('group_id', $groupe->id)->get();
        $moocEnrolls = Enrollmooc::where('group_id', $groupe->id)->get();

        $timeConversionService = new TimeConversionService();

        $total_session_time_mooc =  $timeConversionService->convertSecondsToHours($moocEnrolls->sum('session_time'));
        $total_session_time_speex =  $timeConversionService->convertSecondsToHours($langEnrolls->sum('session_time'));
        $total_session_time_cegos =  $timeConversionService->convertSecondsToHours($softEnrolls->sum('session_time'));
        $total_session_time_eni =  $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time_mooc =  $timeConversionService->convertSecondsToHours($moocEnrolls->sum('cmi_time'));
            $total_cmi_time_speex =  $timeConversionService->convertSecondsToHours($langEnrolls->sum('cmi_time'));
            $total_cmi_time_cegos =  $timeConversionService->convertSecondsToHours($softEnrolls->sum('cmi_time'));
            $total_cmi_time_eni =  $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time_mooc =null;
            $total_cmi_time_speex =null;
            $total_cmi_time_cegos =null;
            $total_cmi_time_eni =null;
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time_mooc =  $timeConversionService->convertSecondsToHours($moocEnrolls->sum('calculated_time'));
            $total_calculated_time_speex =  $timeConversionService->convertSecondsToHours($langEnrolls->sum('calculated_time'));
            $total_calculated_time_cegos =  $timeConversionService->convertSecondsToHours($softEnrolls->sum('calculated_time'));
            $total_calculated_time_eni =  $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time_mooc =null;
            $total_calculated_time_speex =null;
            $total_calculated_time_cegos =null;
            $total_calculated_time_eni =null;
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time_mooc =  $timeConversionService->convertSecondsToHours($moocEnrolls->sum('recommended_time'));
            $total_recommended_time_speex =  $timeConversionService->convertSecondsToHours($langEnrolls->sum('recommended_time'));
            $total_recommended_time_cegos =  $timeConversionService->convertSecondsToHours($softEnrolls->sum('recommended_time'));
            $total_recommended_time_eni =  $timeConversionService->convertSecondsToHours($digitalEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time_mooc =null;
            $total_recommended_time_speex =null;
            $total_recommended_time_cegos =null;
            $total_recommended_time_eni =null;
        }

        $timingChart = [
            'labels' => ['Modules softskills', 'Modules digitals', 'Modules langue', 'Mooc'],
            'session' => [$total_session_time_cegos, $total_session_time_eni, $total_session_time_speex, $total_session_time_mooc],
            'cmi' => $enrollfields['cmi_time'] == true ?  [$total_cmi_time_cegos, $total_cmi_time_eni, $total_cmi_time_speex, $total_cmi_time_mooc] : [],
            'calculated' => $enrollfields['calculated_time'] == true ?  [$total_calculated_time_cegos, $total_calculated_time_eni, $total_calculated_time_speex, $total_calculated_time_mooc] : [],
            'recommended' => $enrollfields['recommended_time'] == true ?  [$total_recommended_time_cegos, $total_recommended_time_eni, $total_recommended_time_speex, $total_recommended_time_mooc] : []
        ];


        return $timingChart;
    }

    public function getLpStats($enrollfields , $groupe){
        $lps = $groupe->lps;
        $lpEnrolls = Lpenroll::where('group_id', $groupe->id)->get();

        $lpEnrollsInEnrolled = Lpenroll::where('status', 'enrolled')->where('group_id', $groupe->id)->count();
        $lpEnrollsInProgress = Lpenroll::where('status', 'in_progress')->where('group_id', $groupe->id)->count();
        $lpEnrollsInProgressMax = Lpenroll::where('status', 'in_progress')->where('enrollment_completion_percentage','>=','50')->where('group_id', $groupe->id)->count();
        $lpEnrollsInProgressMin = Lpenroll::where('status', 'in_progress')->where('enrollment_completion_percentage','<','50')->where('group_id', $groupe->id)->count();
        $lpEnrollsInCompleted = Lpenroll::where('status', 'completed')->where('group_id', $groupe->id)->count();

        $statLps = [
            'enrolled' =>  $lpEnrollsInEnrolled,
            'in_progress' => $lpEnrollsInProgress,
            'in_progress_max' => $lpEnrollsInProgressMax,
            'in_progress_min' => $lpEnrollsInProgressMin,
            'completed' => $lpEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time =  $timeConversionService->convertSecondsToTime($lpEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time =  $timeConversionService->convertSecondsToTime($lpEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time =  $timeConversionService->convertSecondsToTime($lpEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time =  $timeConversionService->convertSecondsToTime($lpEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time = "**h **min **s";
        }

        $statLpsTimes =[
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

    public function geStatsPerLp($enrollfields, $selectedLp, $groupId){
        $groupe = Group::find($groupId);
        $lps = $groupe->lps;
        $lpEnrolls = Lpenroll::where('group_id' , $groupId)->where('lp_docebo_id', $selectedLp)->get();

        $lpEnrollsInEnrolled = Lpenroll::where('group_id' , $groupId)->where('status', 'enrolled')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInProgress = Lpenroll::where('group_id' , $groupId)->where('status', 'in_progress')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInProgressMax = Lpenroll::where('group_id' , $groupId)->where('status', 'in_progress')->where('enrollment_completion_percentage','>=','50')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInProgressMin = Lpenroll::where('group_id' , $groupId)->where('status', 'in_progress')->where('enrollment_completion_percentage','<','50')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInCompleted = Lpenroll::where('group_id' , $groupId)->where('status', 'completed')->where('lp_docebo_id', $selectedLp)->count();

        $statLps = [
            'enrolled' =>  $lpEnrollsInEnrolled,
            'in_progress' => $lpEnrollsInProgress,
            'in_progress_max' => $lpEnrollsInProgressMax,
            'in_progress_min' => $lpEnrollsInProgressMin,
            'completed' => $lpEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time =  $timeConversionService->convertSecondsToTime($lpEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time =  $timeConversionService->convertSecondsToTime($lpEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time =  $timeConversionService->convertSecondsToTime($lpEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time =  $timeConversionService->convertSecondsToTime($lpEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time = "**h **min **s";
        }

        $statLpsTimes =[
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

    public function getLscStats($groupe){
        $totalTickets = Ticket::where('group_id', $groupe->id)->count();
        $totalCalls = Call::where('group_id', $groupe->id)->count();
        $ticketDistribution = Ticket::select('status', DB::raw('count(*) as count'))
                                    ->where('group_id', $groupe->id)
                                    ->groupBy('status')
                                    ->get();
        $ticketsLabels =[];
        $ticketsData = [];
        foreach ($ticketDistribution as $distribution) {
            $ticketsLabels [] = $distribution->status;
            $ticketsData [] = $distribution->count;
        }
        $ticketsCharts = [
            'labels' => $ticketsLabels,
            'data' => $ticketsData
        ];

        $callStatisticsSubject = Call::select('subject', 'type', DB::raw('COUNT(*) as call_count'))
                        ->where('group_id', $groupe->id)
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
            $labelsCallsSubject [] = $key;
            $dataCallsSubjectEntrantes [] =  isset($groupedStatisticsSubject[$key]['entrante']) ? $groupedStatisticsSubject[$key]['entrante'] :  0;
            $dataCallsSubjectSortantes [] =  isset($groupedStatisticsSubject[$key]['sortante']) ? $groupedStatisticsSubject[$key]['sortante'] :  0;
        }

        $callStatisticsStatus = Call::select('status', 'type', DB::raw('COUNT(*) as call_count'))
            ->where('group_id', $groupe->id)
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
            $labelsCallsStatus [] = $key;
            $dataCallsStatusEntrantes [] = isset($groupedStatisticsStatus[$key]['entrante']) ? $groupedStatisticsStatus[$key]['entrante'] :  0;
            $dataCallsStatusSortantes [] = isset($groupedStatisticsStatus[$key]['sortante']) ? $groupedStatisticsStatus[$key]['sortante'] :  0;
        }

        $callsPerStatutAndTypeChart =[
            'labels' => $labelsCallsStatus,
            'reçu' => $dataCallsStatusEntrantes,
            'emis' => $dataCallsStatusSortantes
        ];

        $callsPerSubjectAndTypeChart =[
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

    public function getLearnersInscriptionsPerDate($startDate , $endDate, $groupId){
        $total_learners = Learner::whereBetween('creation_date', [$startDate, $endDate])->where('group_id', $groupId)->count();
        $active_learners = Learner::whereNotNull('last_access_date')->whereBetween('last_access_date', [$startDate, $endDate])->where('statut', 'active')->where('group_id', $groupId)->count();
        $inactive_learners =  Learner::whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'inactive')->where('group_id', $groupId)->count();

        $archive_learners =  Learner::where('statut','archive')->where('group_id', $groupId)->count();

        return  [
            'total' => $total_learners,
            'active' => $active_learners,
            'inactive' => $inactive_learners,
            'archive' => $archive_learners
        ];
    }

    public function getTimingDetailsPerDate($enrollfields, $startDate , $endDate, $groupId){
        $active_learners = Learner::whereNotNull('last_access_date')->whereBetween('last_access_date', [$startDate, $endDate])->where('statut', 'active')->where('group_id',$groupId)->count();

        $moduleDataTimes = Enrollmodule::calculateModuleDataTimesBetweenDatePerGroup($startDate, $endDate, $groupId);
        $moocDataTimes = Enrollmooc::calculateMoocDataTimesBetweenDatePerGroup($startDate, $endDate, $groupId);
        $speexDataTimes = Langenroll::calculateSpeexDataTimesBetweenDatePerGroup($startDate, $endDate, $groupId);

        $timeConversionService = new TimeConversionService();
        $total_session_time = intval($moduleDataTimes->total_session_time) + intval($moocDataTimes->total_session_time) + intval($speexDataTimes->total_session_time);
        $avg_session_time =  $active_learners != 0 ? intval($total_session_time/$active_learners) : 0;
        $total_session_time = $timeConversionService->convertSecondsToTime($total_session_time);
        $avg_session_time = $timeConversionService->convertSecondsToTime($avg_session_time);


        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time = intval($moduleDataTimes->total_cmi_time) + intval($moocDataTimes->total_cmi_time) + intval($speexDataTimes->total_cmi_time);
            $avg_cmi_time =  $active_learners != 0 ?  intval($total_cmi_time/$active_learners) :  0;
            $total_cmi_time = $timeConversionService->convertSecondsToTime($total_cmi_time);
            $avg_cmi_time = $timeConversionService->convertSecondsToTime($avg_cmi_time);
        }else{
            $total_cmi_time = "**h **min **s";
            $avg_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time = intval($moduleDataTimes->total_calculated_time) + intval($moocDataTimes->total_calculated_time) + intval($speexDataTimes->total_calculated_time);
            $avg_calculated_time =  $active_learners != 0 ?  intval($total_calculated_time/$active_learners) : 0;
            $total_calculated_time = $timeConversionService->convertSecondsToTime($total_calculated_time);
            $avg_calculated_time = $timeConversionService->convertSecondsToTime($avg_calculated_time);
        }else{
            $total_calculated_time = "**h **min **s";
            $avg_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time = intval($moduleDataTimes->total_recommended_time) + intval($moocDataTimes->total_recommended_time) + intval($speexDataTimes->total_recommended_time);
            $avg_recommended_time =  $active_learners != 0 ?  intval($total_recommended_time/$active_learners) :  0;
            $total_recommended_time = $timeConversionService->convertSecondsToTime($total_recommended_time);
            $avg_recommended_time = $timeConversionService->convertSecondsToTime($avg_recommended_time);
        }else{
            $total_recommended_time = "**h **min **s";
            $avg_recommended_time = "**h **min **s";
        }
        return [
            'total_session_time' =>$total_session_time ,
            'avg_session_time' =>$avg_session_time ,
            'total_cmi_time' =>$total_cmi_time ,
            'avg_cmi_time' =>$avg_cmi_time ,
            'total_calculated_time' =>$total_calculated_time ,
            'avg_calculated_time' =>$avg_calculated_time ,
            'total_recommended_time' =>$total_recommended_time ,
            'avg_recommended_time' =>$avg_recommended_time ,

        ];

    }

    public function getLearnersChartsPerDate($categorie, $startDate , $endDate, $groupId)
    {
        if($categorie){
            $learnerCounts = DB::table('learners')
            ->select('categorie', DB::raw('count(*) as total'))
            ->whereBetween('creation_date', [$startDate, $endDate])
            ->where('group_id' , $groupId)
            ->groupBy('categorie')
            ->get();

            $totalLearners = DB::table('learners')->whereBetween('creation_date', [$startDate, $endDate])->where('group_id',$groupId)->count();

            $data = [];
            $labels = [];

            foreach ($learnerCounts as $count) {
                $percentage = round(($count->total / $totalLearners) * 100 , 2);
                $data [] = $count->total;
                $labels [] = $count->categorie !== null ?  ucfirst($count->categorie) .' '.  $count->total .  ' - (' . $percentage .'%)' : 'Indéterminé'.' '.  $count->total .  ' - (' . $percentage .'%)' ;
            }

            $chartInscritPerCategorie = [
                'labels' => $labels,
                'data' => $data
            ];

            $categories = Learner::distinct()->where('group_id', $groupId)->pluck('categorie')->filter();

            $counts = [
                'Active' => [],
                'Inactive' => [],
            ];

            foreach ($categories as $category) {
                $counts['Active'][] = Learner::where('categorie', $category)->whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'active')->where('group_id',$groupId)->count();
                $counts['Inactive'][] = Learner::where('categorie', $category)->whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'inactive')->where('group_id',$groupId)->count();
            }
            $chartInscritPerCategoryAndStatus =[
                'labels' => $categories->toArray(),
                'actives' => $counts['Active'],
                'inactives' => $counts['Inactive'],
            ];

        }else{
            $chartInscritPerCategorie = null;
            $chartInscritPerCategoryAndStatus = null;
        }

        return [
           'chartInscritPerCategorie' => $chartInscritPerCategorie,
           'chartInscritPerCategoryAndStatus' => $chartInscritPerCategoryAndStatus
        ];
    }

    public function getLscStatsPerDate($startDate , $endDate, $groupId){
        $totalTickets = Ticket::whereBetween('ticket_created_at', [$startDate, $endDate])->where('group_id', $groupId)->count();
        $totalCalls = Call::whereBetween('date_call', [$startDate, $endDate])->where('group_id', $groupId)->count();
        $ticketDistribution = Ticket::select('status', DB::raw('count(*) as count'))
                                    ->whereBetween('ticket_created_at', [$startDate, $endDate])
                                    ->where('group_id', $groupId)
                                    ->groupBy('status')
                                    ->get();
        $ticketsLabels =[];
        $ticketsData = [];
        foreach ($ticketDistribution as $distribution) {
            $ticketsLabels [] = $distribution->status . '- (' . $distribution->count .')';
            $ticketsData [] = $distribution->count;
        }
        $ticketsCharts = [
            'labels' => $ticketsLabels,
            'data' => $ticketsData
        ];

        $callStatisticsSubject = Call::select('subject', 'type', DB::raw('COUNT(*) as call_count'))
                                    ->whereBetween('date_call', [$startDate, $endDate])
                                    ->where('group_id', $groupId)
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
            $labelsCallsSubject [] = $key;
            $dataCallsSubjectEntrantes [] = isset($groupedStatisticsSubject[$key]['entrante']) ? $groupedStatisticsSubject[$key]['entrante'] :  0;
            $dataCallsSubjectSortantes [] = isset($groupedStatisticsSubject[$key]['sortante']) ? $groupedStatisticsSubject[$key]['sortante'] :  0;
        }

        $callStatisticsStatus = Call::select('status', 'type', DB::raw('COUNT(*) as call_count'))
                                    ->whereBetween('date_call', [$startDate, $endDate])
                                    ->where('group_id', $groupId)
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
            $labelsCallsStatus [] = $key;
            $dataCallsStatusEntrantes [] = isset($groupedStatisticsStatus[$key]['entrante']) ? $groupedStatisticsStatus[$key]['entrante'] :  0;
            $dataCallsStatusSortantes [] = isset($groupedStatisticsStatus[$key]['sortante']) ? $groupedStatisticsStatus[$key]['sortante'] :  0;
        }
        $callsPerStatutAndTypeChart =[
            'labels' => $labelsCallsStatus,
            'reçu' => $dataCallsStatusEntrantes,
            'emis' => $dataCallsStatusSortantes
        ];

        $callsPerSubjectAndTypeChart =[
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

