<?php


namespace App\Services;

use App\Enums\CourseStatusEnum;
use App\Models\Call;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Lpenroll;
use App\Models\Module;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PlateformeReportService{

    public function getLearnersInscriptionsPerStatDate($contract_start_date_conf){

        if($contract_start_date_conf != null){
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
            $yearOfDate = $date->year;
            $currentYear = now()->year;

            if ($yearOfDate > $currentYear) {
                $startDate = now()->year . $date->format('-m-d');
            } else {
                $startDate =  (now()->year - 1) . $date->format('-m-d');
            }
            $endDate = Carbon::now()->format('Y-m-d');
            $total_learners = Learner::whereBetween('creation_date', [$startDate, $endDate])->count();
            $active_learners = Learner::whereBetween('last_access_date', [$startDate, $endDate])->where('statut', 'active')->count();
            $inactive_learners =  Learner::whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'inactive')->count();
            $statsLearners = [
                'total' => $total_learners,
                'active' => $active_learners,
                'inactive' => $inactive_learners,
            ];
        } else{
            $statsLearners = [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
            ];
        }

        return $statsLearners;

    }

    public function getTimingDetailsPerStatDate($contract_start_date_conf,$enrollfields){

        if($contract_start_date_conf != null){
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
            $yearOfDate = $date->year;
            $currentYear = now()->year;

            if ($yearOfDate > $currentYear) {
                $startDate = now()->year . $date->format('-m-d');
            } else {
                $startDate =  (now()->year - 1) . $date->format('-m-d');
            }
            $endDate = Carbon::now()->format('Y-m-d');
            $archive = config('tenantconfigfields.archive');
            if($archive == true){
                $learners = Learner::whereIn('statut' , ['active', 'archive'])->whereBetween('last_access_date', [$startDate, $endDate])->count();
                $learnersIds = Learner::whereIn('statut' , ['active', 'archive'])->whereBetween('last_access_date', [$startDate, $endDate])->pluck('docebo_id')->toArray();
            }else{
                $learners = Learner::where('statut' , 'active')->whereBetween('last_access_date', [$startDate, $endDate])->count();
                $learnersIds = Learner::where('statut' , 'active')->whereBetween('last_access_date', [$startDate, $endDate])->pluck('docebo_id')->toArray();
            }


            $moduleDataTimes = Enrollmodule::calculateModuleDataTimesBetweenDate($startDate, $endDate, $learnersIds);
            $moocDataTimes = Enrollmooc::calculateMoocDataTimesBetweenDate($startDate, $endDate, $learnersIds);
            $speexDataTimes = Langenroll::calculateSpeexDataTimesBetweenDate($startDate, $endDate, $learnersIds);

            $timeConversionService = new TimeConversionService();
            $total_session_time = intval($moduleDataTimes->total_session_time) + intval($moocDataTimes->total_session_time) + intval($speexDataTimes->total_session_time);
            $avg_session_time =  $learners != 0 ? intval($total_session_time/$learners) : 0;
            $total_session_time = $timeConversionService->convertSecondsToTime($total_session_time);
            $avg_session_time = $timeConversionService->convertSecondsToTime($avg_session_time);


            if($enrollfields['cmi_time'] == true)
            {
                $total_cmi_time = intval($moduleDataTimes->total_cmi_time) + intval($moocDataTimes->total_cmi_time) + intval($speexDataTimes->total_cmi_time);
                $avg_cmi_time =  $learners != 0 ?  intval($total_cmi_time/$learners) :  0;
                $total_cmi_time = $timeConversionService->convertSecondsToTime($total_cmi_time);
                $avg_cmi_time = $timeConversionService->convertSecondsToTime($avg_cmi_time);
            }else{
                $total_cmi_time = "**h **min **s";
                $avg_cmi_time = "**h **min **s";
            }

            if($enrollfields['calculated_time'] == true)
            {
                $total_calculated_time = intval($moduleDataTimes->total_calculated_time) + intval($moocDataTimes->total_calculated_time) + intval($speexDataTimes->total_calculated_time);
                $avg_calculated_time =  $learners != 0 ?  intval($total_calculated_time/$learners) : 0;
                $total_calculated_time = $timeConversionService->convertSecondsToTime($total_calculated_time);
                $avg_calculated_time = $timeConversionService->convertSecondsToTime($avg_calculated_time);
            }else{
                $total_calculated_time = "**h **min **s";
                $avg_calculated_time = "**h **min **s";
            }

            if($enrollfields['recommended_time'] == true)
            {
                $total_recommended_time = intval($moduleDataTimes->total_recommended_time) + intval($moocDataTimes->total_recommended_time) + intval($speexDataTimes->total_recommended_time);
                $avg_recommended_time =  $learners != 0 ?  intval($total_recommended_time/$learners) :  0;
                $total_recommended_time = $timeConversionService->convertSecondsToTime($total_recommended_time);
                $avg_recommended_time = $timeConversionService->convertSecondsToTime($avg_recommended_time);
            }else{
                $total_recommended_time = "**h **min **s";
                $avg_recommended_time = "**h **min **s";
            }
            $statsTimes =  [
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
            $statsTimes =  [
                'total_session_time' =>"**h **min **s",
                'avg_session_time' =>"**h **min **s",
                'total_cmi_time' =>"**h **min **s",
                'avg_cmi_time' =>"**h **min **s",
                'total_calculated_time' =>"**h **min **s",
                'avg_calculated_time' =>"**h **min **s",
                'total_recommended_time' =>"**h **min **s",
                'avg_recommended_time' =>"**h **min **s",

            ];
        }

        return $statsTimes;
    }

    public function getLearnersInscriptions(){
        $total_learners = Learner::count();
        $active_learners = Learner::whereNotNull('last_access_date')->count();
        $inactive_learners =  Learner::whereNull('last_access_date')->count();
        $archive_learners =  Learner::where('statut','archive')->count();

        return  [
            'total' => $total_learners,
            'active' => $active_learners,
            'inactive' => $inactive_learners,
            'archive' => $archive_learners
        ];
    }

    public function getTimingDetails($enrollfields){

        $archive = config('tenantconfigfields.archive');

        if($archive == true){
            $learners = Learner::whereIn('statut' , ['active', 'archive'])->count();
            $learnersIds = Learner::whereIn('statut' , ['active', 'archive'])->pluck('docebo_id')->toArray();
        }else{
            $learners = Learner::where('statut' , 'active')->count();
            $learnersIds = Learner::where('statut' , 'active')->pluck('docebo_id')->toArray();
        }

        $enrollModules = EnrollModule::whereIn('learner_docebo_id' , $learnersIds)->get();
        $enrollMoocs = Enrollmooc::whereIn('learner_docebo_id' , $learnersIds)->get();
        $enrollSpeex = Langenroll::whereIn('learner_docebo_id' , $learnersIds)->get();

        $timeConversionService = new TimeConversionService();
        $total_session_time =  intval($enrollModules->sum('session_time')) + intval($enrollMoocs->sum('session_time')) + intval($enrollSpeex->sum('session_time'));
        $avg_session_time =  $learners != 0 ? intval($total_session_time/$learners) : 0;
        $total_session_time = $timeConversionService->convertSecondsToTime($total_session_time);
        $avg_session_time = $timeConversionService->convertSecondsToTime($avg_session_time);


        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time = intval($enrollModules->sum('cmi_time')) + intval($enrollMoocs->sum('cmi_time')) + intval($enrollSpeex->sum('cmi_time'));
            $avg_cmi_time =  $learners != 0 ?  intval($total_cmi_time/$learners) :  0;
            $total_cmi_time = $timeConversionService->convertSecondsToTime($total_cmi_time);
            $avg_cmi_time = $timeConversionService->convertSecondsToTime($avg_cmi_time);
        }else{
            $total_cmi_time = "**h **min **s";
            $avg_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time = intval($enrollModules->sum('calculated_time')) + intval($enrollMoocs->sum('calculated_time')) + intval($enrollSpeex->sum('calculated_time'));;
            $avg_calculated_time =  $learners != 0 ?  intval($total_calculated_time/$learners) : 0;
            $total_calculated_time = $timeConversionService->convertSecondsToTime($total_calculated_time);
            $avg_calculated_time = $timeConversionService->convertSecondsToTime($avg_calculated_time);
        }else{
            $total_calculated_time = "**h **min **s";
            $avg_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time = intval($enrollModules->sum('recommended_time')) + intval($enrollMoocs->sum('recommended_time')) + intval($enrollSpeex->sum('recommended_time'));;
            $avg_recommended_time =  $learners != 0 ?  intval($total_recommended_time/$learners) :  0;
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

    public function getLearnersCharts($categorie)
    {
        $archive = config('tenantconfigfields.archive');
        if($categorie){

            if($archive == true){
                $learnerCounts = DB::table('learners')
                ->select('categorie', DB::raw('count(*) as total'))
                ->groupBy('categorie')
                ->get();
                $totalLearners = DB::table('learners')->count();
            }else{
                $learnerCounts = DB::table('learners')
                ->select('categorie', DB::raw('count(*) as total'))
                ->where('statut','!=', 'archive')
                ->groupBy('categorie')
                ->get();
                $totalLearners = DB::table('learners')->where('statut','!=', 'archive')->count();
            }

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

            $categories = Learner::distinct()->pluck('categorie')->filter();

            $counts = [
                'Active' => [],
                'Inactive' => []
            ];

            foreach ($categories as $category) {
                $counts['Active'][] = Learner::where('categorie', $category)->where('statut', 'active')->count();
                $counts['Inactive'][] = Learner::where('categorie', $category)->where('statut', 'inactive')->count();
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

    public function getStatSoftskills($enrollfields){
        $softModules = Module::where(['category' => 'CEGOS', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();

        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->get();
        $softEnrollsInEnrolled = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->count();
        $softEnrollsInProgress = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->count();
        $softEnrollsInCompleted = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->count();

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

    public function getStatDigital($enrollfields){
        $digitalModules = Module::where(['category' => 'ENI', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
        $moduleDigitals = Module::where(['category' => 'ENI', 'status' => CourseStatusEnum::ACTIVE])->get();

        $digitalEnrolls = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->get();

        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $digitalEnrollsInEnrolled = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->count();
        $digitalEnrollsInProgress = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->count();
        $digitalEnrollsInCompleted = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->count();

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

    public function getStatDigitalPerModule($enrollfields, $selectedDigital){
        $moduleDigitals = Module::where(['category' => 'ENI', 'status' => CourseStatusEnum::ACTIVE])->get();

        $digitalEnrolls = Enrollmodule::where('module_docebo_id', $selectedDigital)->get();

        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $digitalEnrollsInEnrolled = Enrollmodule::where('module_docebo_id', $selectedDigital)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->count();
        $digitalEnrollsInProgress = Enrollmodule::where('module_docebo_id', $selectedDigital)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->count();
        $digitalEnrollsInCompleted = Enrollmodule::where('module_docebo_id', $selectedDigital)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->count();

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

    public function getStatSM($enrollfields){
        $smModules = Module::where(['category' => 'SM', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
        $moduleSms = Module::where(['category' => 'SM', 'status' => CourseStatusEnum::ACTIVE])->get();

        $smEnrolls = Enrollmodule::whereIn('module_docebo_id', $smModules)->get();

        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $smEnrollsInEnrolled = Enrollmodule::whereIn('module_docebo_id', $smModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->count();
        $smEnrollsInProgress = Enrollmodule::whereIn('module_docebo_id', $smModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->count();
        $smEnrollsInCompleted = Enrollmodule::whereIn('module_docebo_id', $smModules)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->count();

        $statSM = [
            'enrolled' =>  $smEnrollsInEnrolled,
            'in_progress' => $smEnrollsInProgress,
            'completed' => $smEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time =  $timeConversionService->convertSecondsToTime($smEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time =  $timeConversionService->convertSecondsToTime($smEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time =  $timeConversionService->convertSecondsToTime($smEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time =  $timeConversionService->convertSecondsToTime($smEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time = "**h **min **s";
        }

        $statSMTimes =[
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $smCharts = [
            'labels' => ['Non démarré', 'En cours', 'Terminé'],
            'data' =>  [$smEnrollsInEnrolled, $smEnrollsInProgress, $smEnrollsInCompleted]
        ];

        return [
            'statSM' => $statSM,
            'statSMTimes' => $statSMTimes,
            'smCharts' => $smCharts,
            'modulesSms' => $moduleSms
        ];
    }

    public function getStatSMPerModule($enrollfields, $selectedSm){
        $moduleSms = Module::where(['category' => 'SM', 'status' => CourseStatusEnum::ACTIVE])->get();

        $smEnrolls = Enrollmodule::where('module_docebo_id', $selectedSm)->get();

        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $smEnrollsInEnrolled = Enrollmodule::where('module_docebo_id', $selectedSm)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'enrolled')->count();
        $smEnrollsInProgress = Enrollmodule::where('module_docebo_id', $selectedSm)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'in_progress')->count();
        $smEnrollsInCompleted = Enrollmodule::where('module_docebo_id', $selectedSm)->whereIn('learner_docebo_id', $learnersIds)->where('status', 'completed')->count();

        $statSM = [
            'enrolled' =>  $smEnrollsInEnrolled,
            'in_progress' => $smEnrollsInProgress,
            'completed' => $smEnrollsInCompleted,
        ];

        $timeConversionService = new TimeConversionService();
        $total_session_time =  $timeConversionService->convertSecondsToTime($smEnrolls->sum('session_time'));

        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time =  $timeConversionService->convertSecondsToTime($smEnrolls->sum('cmi_time'));
        }else{
            $total_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time =  $timeConversionService->convertSecondsToTime($smEnrolls->sum('calculated_time'));
        }else{
            $total_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time =  $timeConversionService->convertSecondsToTime($smEnrolls->sum('recommended_time'));
        }else{
            $total_recommended_time = "**h **min **s";
        }

        $statSMTimes =[
            'total_session_time' => $total_session_time,
            'total_cmi_time' => $total_cmi_time,
            'total_calculated_time' => $total_calculated_time,
            'total_recommended_time' => $total_recommended_time,
        ];

        $smCharts = [
            'labels' => ['Non démarré', 'En cours', 'Terminé'],
            'data' =>  [$smEnrollsInEnrolled, $smEnrollsInProgress, $smEnrollsInCompleted]
        ];

        return [
            'statSM' => $statSM,
            'statSMTimes' => $statSMTimes,
            'smCharts' => $smCharts,
            'modulesSms' => $moduleSms
        ];
    }

    public function getStatSpeex($enrollfields){

        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $langEnrollsInEnrolled = Langenroll::whereIn('status', ['enrolled', 'waiting'])->whereIn('learner_docebo_id', $learnersIds)->count();
        $langEnrollsInProgress = Langenroll::where('status', 'in_progress')->whereIn('learner_docebo_id', $learnersIds)->count();
        $langEnrollsInCompleted = Langenroll::where('status', 'completed')->whereIn('learner_docebo_id', $learnersIds)->count();

        $statSpeex = [
            'enrolled' =>  $langEnrollsInEnrolled,
            'in_progress' => $langEnrollsInProgress,
            'completed' => $langEnrollsInCompleted,
        ];

        $langEnrolls = Langenroll::whereIn('learner_docebo_id', $learnersIds)->get();

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

        $speexLangues = Langenroll::distinct()->pluck('language');

        return [
            'statSpeex' => $statSpeex,
            'statSpeexTimes' => $statSpeexTimes,
            'speexLangues' => $speexLangues,
        ];
    }

    public function getStatSpeexChart($selectedLanguage){
        $timeConversionService = new TimeConversionService();
        $niveaux = ['A1', 'A2', 'B1.1', 'B1.2', 'B2.1', 'B2.2', 'C1.1', 'C1.2', 'Indéterminé'];

        $archive = config('tenantconfigfields.archive');
        if($archive == true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $statistiques = Langenroll::selectRaw('niveau, COUNT(*) AS nombre_total, SUM(cmi_time) AS temps_total_cmi')
            ->whereIn('learner_docebo_id' , $learnersIds)
            ->where('language', $selectedLanguage)
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

    public function getStatMooc($enrollfields){
        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $moocEnrollsInWaiting = Enrollmooc::where('status', 'waiting')->whereIn('learner_docebo_id' , $learnersIds)->count();
        $moocEnrollsInEnrolled = Enrollmooc::where('status', 'enrolled')->whereIn('learner_docebo_id' , $learnersIds)->count();
        $moocEnrollsInProgress = Enrollmooc::where('status', 'in_progress')->whereIn('learner_docebo_id' , $learnersIds)->count();
        $moocEnrollsInCompleted = Enrollmooc::where('status', 'completed')->whereIn('learner_docebo_id' , $learnersIds)->count();

        $statMooc = [
            'waiting' =>  $moocEnrollsInWaiting,
            'enrolled' =>  $moocEnrollsInEnrolled,
            'in_progress' => $moocEnrollsInProgress,
            'completed' => $moocEnrollsInCompleted,
        ];

        $moocEnrolls = Enrollmooc::whereIn('learner_docebo_id' , $learnersIds)->get();

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

    public function getTimingStats($enrollfields){

        $digitalModules = Module::where(['category' => 'ENI', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
        $softModules = Module::where(['category' => 'CEGOS', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $softEnrolls = Enrollmodule::whereIn('module_docebo_id', $softModules)->whereIn('learner_docebo_id' , $learnersIds)->get();
        $digitalEnrolls = Enrollmodule::whereIn('module_docebo_id', $digitalModules)->whereIn('learner_docebo_id' , $learnersIds)->get();
        $langEnrolls = Langenroll::whereIn('learner_docebo_id' , $learnersIds)->get();
        $moocEnrolls = Enrollmooc::whereIn('learner_docebo_id' , $learnersIds)->get();


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

    public function getLpStats($enrollfields){
        $lps = Lp::all();
        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }
        $lpEnrolls = Lpenroll::whereIn('learner_docebo_id' , $learnersIds)->get();

        $lpEnrollsInEnrolled = Lpenroll::where('status', 'enrolled')->whereIn('learner_docebo_id' , $learnersIds)->count();
        $lpEnrollsInProgress = Lpenroll::where('status', 'in_progress')->whereIn('learner_docebo_id' , $learnersIds)->count();
        $lpEnrollsInProgressMax = Lpenroll::where('status', 'in_progress')->whereIn('learner_docebo_id' , $learnersIds)->where('enrollment_completion_percentage','>=','50')->count();
        $lpEnrollsInProgressMin = Lpenroll::where('status', 'in_progress')->whereIn('learner_docebo_id' , $learnersIds)->where('enrollment_completion_percentage','<','50')->count();
        $lpEnrollsInCompleted = Lpenroll::where('status', 'completed')->whereIn('learner_docebo_id' , $learnersIds)->count();

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

    public function geStatsPerLp($enrollfields, $selectedLp){
        $lps = Lp::all();

        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }
        $lpEnrolls = Lpenroll::where('lp_docebo_id', $selectedLp)->whereIn('learner_docebo_id' , $learnersIds)->get();

        $lpEnrollsInEnrolled = Lpenroll::where('status', 'enrolled')->whereIn('learner_docebo_id' , $learnersIds)->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInProgress = Lpenroll::where('status', 'in_progress')->whereIn('learner_docebo_id' , $learnersIds)->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInProgressMax = Lpenroll::where('status', 'in_progress')->whereIn('learner_docebo_id' , $learnersIds)->where('enrollment_completion_percentage','>=','50')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInProgressMin = Lpenroll::where('status', 'in_progress')->whereIn('learner_docebo_id' , $learnersIds)->where('enrollment_completion_percentage','<','50')->where('lp_docebo_id', $selectedLp)->count();
        $lpEnrollsInCompleted = Lpenroll::where('status', 'completed')->whereIn('learner_docebo_id' , $learnersIds)->where('lp_docebo_id', $selectedLp)->count();

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

    public function getLscStats(){

        $archive = config('tenantconfigfields.archive');
        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }
        $totalTickets = Ticket::whereIn('learner_docebo_id' , $learnersIds)->count();
        $totalCalls = Call::whereIn('learner_docebo_id' , $learnersIds)->count();

        $ticketDistribution = Ticket::select('status', DB::raw('count(*) as count'))
                                    ->whereIn('learner_docebo_id' , $learnersIds)
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
                        ->whereIn('learner_docebo_id' , $learnersIds)
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
            ->whereIn('learner_docebo_id' , $learnersIds)
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


    public function getLscStatsPerDate($startDate , $endDate){
        $archive = config('tenantconfigfields.archive');

        if($archive != true){
            $learnersIds = Learner::where('statut', '!=' , 'archive')->pluck('docebo_id')->toArray();
        }else{
            $learnersIds = Learner::pluck('docebo_id')->toArray();
        }

        $totalTickets = Ticket::whereBetween('ticket_created_at', [$startDate, $endDate])->whereIn('learner_docebo_id' , $learnersIds)->count();
        $totalCalls = Call::whereBetween('date_call', [$startDate, $endDate])->whereIn('learner_docebo_id' , $learnersIds)->count();
        $ticketDistribution = Ticket::select('status', DB::raw('count(*) as count'))
                                    ->whereBetween('ticket_created_at', [$startDate, $endDate])
                                    ->whereIn('learner_docebo_id' , $learnersIds)
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
                                    ->whereIn('learner_docebo_id' , $learnersIds)
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
                                    ->whereIn('learner_docebo_id' , $learnersIds)
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


    public function getLearnersInscriptionsPerDate($startDate , $endDate){
        $total_learners = Learner::whereBetween('creation_date', [$startDate, $endDate])->count();
        $active_learners = Learner::whereBetween('last_access_date', [$startDate, $endDate])->where('statut', 'active')->count();
        $inactive_learners =  Learner::whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'inactive')->count();

        $archive_learners =  Learner::where('statut','archive')->count();

        return  [
            'total' => $total_learners,
            'active' => $active_learners,
            'inactive' => $inactive_learners,
            'archive' => $archive_learners
        ];
    }

    public function getTimingDetailsPerDate($enrollfields, $startDate , $endDate){

        $archive = config('tenantconfigfields.archive');
        if($archive == true){
            $learners = Learner::whereIn('statut' , ['active', 'archive'])->whereBetween('last_access_date', [$startDate, $endDate])->count();
            $learnersIds = Learner::whereIn('statut' , ['active', 'archive'])->whereBetween('last_access_date', [$startDate, $endDate])->pluck('docebo_id')->toArray();
        }else{
            $learners = Learner::where('statut' , 'active')->whereBetween('last_access_date', [$startDate, $endDate])->count();
            $learnersIds = Learner::where('statut' , 'active')->whereBetween('last_access_date', [$startDate, $endDate])->pluck('docebo_id')->toArray();
        }


        $moduleDataTimes = Enrollmodule::calculateModuleDataTimesBetweenDate($startDate, $endDate, $learnersIds);
        $moocDataTimes = Enrollmooc::calculateMoocDataTimesBetweenDate($startDate, $endDate, $learnersIds);
        $speexDataTimes = Langenroll::calculateSpeexDataTimesBetweenDate($startDate, $endDate, $learnersIds);

        $timeConversionService = new TimeConversionService();
        $total_session_time = intval($moduleDataTimes->total_session_time) + intval($moocDataTimes->total_session_time) + intval($speexDataTimes->total_session_time);
        $avg_session_time =  $learners != 0 ? intval($total_session_time/$learners) : 0;
        $total_session_time = $timeConversionService->convertSecondsToTime($total_session_time);
        $avg_session_time = $timeConversionService->convertSecondsToTime($avg_session_time);


        if($enrollfields['cmi_time'] == true)
        {
            $total_cmi_time = intval($moduleDataTimes->total_cmi_time) + intval($moocDataTimes->total_cmi_time) + intval($speexDataTimes->total_cmi_time);
            $avg_cmi_time =  $learners != 0 ?  intval($total_cmi_time/$learners) :  0;
            $total_cmi_time = $timeConversionService->convertSecondsToTime($total_cmi_time);
            $avg_cmi_time = $timeConversionService->convertSecondsToTime($avg_cmi_time);
        }else{
            $total_cmi_time = "**h **min **s";
            $avg_cmi_time = "**h **min **s";
        }

        if($enrollfields['calculated_time'] == true)
        {
            $total_calculated_time = intval($moduleDataTimes->total_calculated_time) + intval($moocDataTimes->total_calculated_time) + intval($speexDataTimes->total_calculated_time);
            $avg_calculated_time =  $learners != 0 ?  intval($total_calculated_time/$learners) : 0;
            $total_calculated_time = $timeConversionService->convertSecondsToTime($total_calculated_time);
            $avg_calculated_time = $timeConversionService->convertSecondsToTime($avg_calculated_time);
        }else{
            $total_calculated_time = "**h **min **s";
            $avg_calculated_time = "**h **min **s";
        }

        if($enrollfields['recommended_time'] == true)
        {
            $total_recommended_time = intval($moduleDataTimes->total_recommended_time) + intval($moocDataTimes->total_recommended_time) + intval($speexDataTimes->total_recommended_time);
            $avg_recommended_time =  $learners != 0 ?  intval($total_recommended_time/$learners) :  0;
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

    public function getLearnersChartsPerDate($categorie, $startDate , $endDate)
    {
        $archive = config('tenantconfigfields.archive');
        if($categorie){
            if($archive == true){
                $learnerCounts = DB::table('learners')
                ->select('categorie', DB::raw('count(*) as total'))
                ->whereBetween('creation_date', [$startDate, $endDate])
                ->groupBy('categorie')
                ->get();

                $totalLearners = DB::table('learners')->whereBetween('creation_date', [$startDate, $endDate])->count();

            }else{
                $learnerCounts = DB::table('learners')
                ->select('categorie', DB::raw('count(*) as total'))
                ->where('statut' , '!=', 'archive')
                ->whereBetween('creation_date', [$startDate, $endDate])
                ->groupBy('categorie')
                ->get();

                $totalLearners = DB::table('learners')->whereBetween('creation_date', [$startDate, $endDate])->where('statut' , '!=', 'archive')->count();
            }

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

            $categories = Learner::distinct()->pluck('categorie')->filter();

            $counts = [
                'Active' => [],
                'Inactive' => [],
            ];

            foreach ($categories as $category) {
                $counts['Active'][] = Learner::where('categorie', $category)->whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'active')->count();
                $counts['Inactive'][] = Learner::where('categorie', $category)->whereBetween('creation_date', [$startDate, $endDate])->where('statut', 'inactive')->count();
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

}

