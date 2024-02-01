<?php


namespace App\Services;

use App\Charts\InscritPerCategory;
use App\Charts\InscritPerCategoryAndStatus;
use App\Models\Enrollmodule;
use App\Models\Enrollmooc;
use App\Models\Langenroll;
use App\Models\Learner;

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

    public function getChartsInscrits(InscritPerCategory $chartInscritPerCategorie, InscritPerCategoryAndStatus $chartInscritPerCategoryAndStatus, $categorie){
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

}
