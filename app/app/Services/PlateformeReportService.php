<?php


namespace App\Services;

use App\Charts\InscritPerCategory;
use App\Charts\InscritPerCategoryAndStatus;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Langenroll;
use App\Models\Learner;
use Illuminate\Support\Facades\DB;

class PlateformeReportService{

    public function getInscritsReportForStatDate($contract_start_date_conf, $enrollfields)
    {
        if($contract_start_date_conf != null){
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
            $yearOfDate = $date->year;
            $currentYear = now()->year;

            if ($yearOfDate > $currentYear) {
                $statDate = now()->year . $date->format('-m-d');
            } else {
                $statDate =  (now()->year - 1) . $date->format('-m-d');
            }

            $timeConversionService = new TimeConversionService();
            $total_learners = Learner::where('creation_date', '>=', $statDate)->count();
            $active_learners = Learner::where('last_access_date', '>=', $statDate)->where('statut', 'active')->count();

            $statsLearners = [
                'total' => $total_learners,
                'active' => $active_learners,
            ];

            $moduleDataTimes = Enrollmodule::calculateModuleDataTimes($statDate);
            $moocDataTimes = Enrollmooc::calculateMoocDataTimes($statDate);
            $speexDataTimes = Langenroll::calculateSpeexDataTimes($statDate);

            $total_session_time = intval($moduleDataTimes->total_session_time) + intval($moocDataTimes->total_session_time) + intval($speexDataTimes->total_session_time);
            $total_cmi_time = intval($moduleDataTimes->total_cmi_time) + intval($moocDataTimes->total_cmi_time) + intval($speexDataTimes->total_cmi_time);
            $total_calculated_time = intval($moduleDataTimes->total_calculated_time) + intval($moocDataTimes->total_calculated_time) + intval($speexDataTimes->total_calculated_time);
            $total_recommended_time = intval($moduleDataTimes->total_recommended_time) + intval($moocDataTimes->total_recommended_time) + intval($speexDataTimes->total_recommended_time);

            $avg_session_time = $active_learners != 0 ? intval($total_session_time/$active_learners) : 0;
            $avg_cmi_time =  $active_learners != 0 ?  intval($total_cmi_time/$active_learners) :  0;
            $avg_calculated_time =  $active_learners != 0 ?  intval($total_calculated_time/$active_learners) : 0;
            $avg_recommended_time =  $active_learners != 0 ?  intval($total_recommended_time/$active_learners) :  0;

            $statsTimes = [
                'total_session_time' => $timeConversionService->convertSecondsToTime($total_session_time),
                'avg_session_time' => $timeConversionService->convertSecondsToTime($avg_session_time) ,
                'total_cmi_time' => $enrollfields['cmi_time'] == true ?  $timeConversionService->convertSecondsToTime($total_cmi_time) : '**h **min **s',
                'avg_cmi_time' => $enrollfields['cmi_time'] == true ?  $timeConversionService->convertSecondsToTime($avg_cmi_time) : '**h **min **s',
                'total_calculated_time' => $enrollfields['calculated_time'] == true ?  $timeConversionService->convertSecondsToTime($total_calculated_time) : '**h **min **s',
                'avg_calculated_time' => $enrollfields['calculated_time'] == true ?  $timeConversionService->convertSecondsToTime($avg_calculated_time) : '**h **min **s',
                'total_recommended_time' => $enrollfields['recommended_time'] == true ?  $timeConversionService->convertSecondsToTime($total_recommended_time) : '**h **min **s',
                'avg_recommended_time' => $enrollfields['recommended_time'] == true ?  $timeConversionService->convertSecondsToTime($avg_recommended_time) : '**h **min **s',

            ];

            $data = [
                'statsLearners' => $statsLearners,
                'statsTimes' => $statsTimes
            ];

        }else{
            $data = null;
        }

        return $data;
    }

    public function getChartsInscrits(){

    }




    public function getLearnersInscriptionsPerStatDate($contract_start_date_conf){

        if($contract_start_date_conf != null){
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
            $yearOfDate = $date->year;
            $currentYear = now()->year;

            if ($yearOfDate > $currentYear) {
                $statDate = now()->year . $date->format('-m-d');
            } else {
                $statDate =  (now()->year - 1) . $date->format('-m-d');
            }

            $total_learners = Learner::where('creation_date', '>=', $statDate)->count();
            $active_learners = Learner::where('last_access_date', '>=', $statDate)->where('statut', 'active')->count();
            $inactive_learners =  Learner::where('last_access_date' , '<' , $statDate)->where('statut', 'active')->count();
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

    public function getTimingDetailsPerStatDate($contract_start_date_conf,$enrollfields){

        if($contract_start_date_conf != null){
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
            $yearOfDate = $date->year;
            $currentYear = now()->year;

            if ($yearOfDate > $currentYear) {
                $statDate = now()->year . $date->format('-m-d');
            } else {
                $statDate =  (now()->year - 1) . $date->format('-m-d');
            }

            $active_learners = Learner::where('last_access_date', '>=', $statDate)->where('statut', 'active')->count();
            $moduleDataTimes = Enrollmodule::calculateModuleDataTimes($statDate);
            $moocDataTimes = Enrollmooc::calculateMoocDataTimes($statDate);
            $speexDataTimes = Langenroll::calculateSpeexDataTimes($statDate);


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

    public function getLearnersInscriptions(){
        $total_learners = Learner::count();
        $active_learners = Learner::whereNotNull('last_access_date')->count();
        $inactive_learners =  Learner::whereNull('last_access_date')->count();
        $archive_learners =  Learner::whereNotNull('deleted_at')->count();

        return  [
            'total' => $total_learners,
            'active' => $active_learners,
            'inactive' => $inactive_learners,
            'archive' => $archive_learners
        ];
    }

    public function getTimingDetails($enrollfields){
        $active_learners = Learner::whereNotNull('last_access_date')->count();
        $enrollModules = EnrollModule::all();
        $enrollMoocs = Enrollmooc::all();
        $enrollSpeex = Langenroll::all();

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

    public function getLearnersCharts(InscritPerCategory $chartInscritPerCategorie, InscritPerCategoryAndStatus $chartInscritPerCategoryAndStatus, $categorie){
        if($categorie){
            $chartInscritPerCategorie = $chartInscritPerCategorie->build();
            $chartInscritPerCategoryAndStatus = $chartInscritPerCategoryAndStatus->build();
        }else{
            $chartInscritPerCategorie = null;
            $chartInscritPerCategoryAndStatus = null;
        }

        return [
            'chartInscritPerCategorie' => $chartInscritPerCategorie,
            'chartInscritPerCategorieAndStatus' => $chartInscritPerCategoryAndStatus
        ];

    }

    public function getLearnersCharts2($categorie)
    {
        if($categorie){
            $learnerCounts = DB::table('learners')
            ->select('categorie', DB::raw('count(*) as total'))
            ->groupBy('categorie')
            ->get();

            $totalLearners = DB::table('learners')->count();

            $data = [];
            $labels = [];

            foreach ($learnerCounts as $count) {
                $percentage = round(($count->total / $totalLearners) * 100 , 2);
                $data [] = $count->total;
                $labels [] = $count->categorie !== null ?  ucfirst($count->categorie) .' '.  $count->total .  ' - (' . $percentage .'%)' : ' Indéterminé'.' '.  $count->total .  ' - (' . $percentage .'%)' ;
            }

            $chartInscritPerCategorie = app()->chartjs
                ->name('chartInscritPerCategorie')
                ->type('doughnut')
                ->size(['width' => 400, 'height' => 200])
                ->labels($labels)
                ->datasets([
                    [
                        'backgroundColor' => ["#1676FB", "#798bff", "#6b5b95", "#b8acff", "#f9db7b", "#1EE0AC", "#ffa9ce"],
                        'hoverBackgroundColor' => ["#1676FB", "#798bff", "#6b5b95", "#b8acff", "#f9db7b", "#1EE0AC", "#ffa9ce"],
                        'data' => $data
                    ]
                ])
                ->options([]);


            $categories = Learner::distinct()->pluck('categorie')->filter();

            $counts = [
                'Active' => [],
                'Inactive' => [],
                'Archive' => [],
            ];

            foreach ($categories as $category) {
                $counts['Active'][] = Learner::where('categorie', $category)->where('statut', 'active')->count();
                $counts['Inactive'][] = Learner::where('categorie', $category)->where('statut', 'inactive')->count();
                $counts['Archive'][] = Learner::where('categorie', $category)->where('statut', 'archive')->count();
            }

            $chartInscritPerCategoryAndStatus = app()->chartjs
            ->name('barChartTest')
            ->type('bar')
            ->size(['width' => 400, 'height' => 200])
            ->labels($categories->toArray())
            ->datasets([
                [
                    "label" => "Actives",
                    'backgroundColor' => ['#206BC4'],
                    'data' => $counts['Active']
                ],
                [
                    "label" => "Inactives",
                    'backgroundColor' => ['#D63939'],
                    'data' => $counts['Inactive']
                ],
                [
                    "label" => "Archives",
                    'backgroundColor' => ['#F59F00'],
                    'data' => $counts['Archive']
                ]
            ])
            ->options([]);
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

