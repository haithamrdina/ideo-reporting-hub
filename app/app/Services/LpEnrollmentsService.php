<?php

namespace App\Services;

use App\Interfaces\EnrollmentInterface;
use App\Models\Enrollmodule;
use App\Models\Learner;
use App\Models\Lp;
use App\Models\Lpenroll;
use Illuminate\Support\Facades\DB;

class LpEnrollmentsService implements EnrollmentInterface
{
    public function getEnrollmentsFields($fields): array
    {
        $principalList =[
            'status',
            'enrollment_completion_percentage',
            'enrollment_created_at',
            'enrollment_updated_at',
            'enrollment_completed_at',
            'session_time',
            'group_id',
            'project_id',
        ];
        return array_merge($principalList, array_keys($fields));
    }

    public function getEnrollmentsList($items, $fields): array
    {
        $result = array_map(function ($item) use ($fields){

            $modulesIds = Lp::where('docebo_id' , $item['lp_docebo_id'])->first()->courses;
            $learner = Learner::where('docebo_id',$item['learner_docebo_id'])->first();

            if($learner){
                $item['group_id'] = $learner->group->id;
                $item['project_id'] = $learner->project->id;

                $sumData = Enrollmodule::where('learner_docebo_id', $learner->docebo_id)
                            ->whereIn('module_docebo_id', $modulesIds)
                            ->selectRaw('SUM(session_time) as total_session_time')
                            ->selectRaw('SUM(cmi_time) as total_cmi_time')
                            ->selectRaw('SUM(calculated_time) as total_calculated_time')
                            ->selectRaw('SUM(recommended_time) as total_recommended_time')
                            ->first();

                $item['session_time'] = $this->getSessionTime($sumData);

                if (isset($fields['cmi_time']) && $fields['cmi_time'] === true) {
                    $item['cmi_time'] = $this->getCmiTime($sumData);
                }else{
                    $item['cmi_time'] = null;
                }

                if (isset($fields['calculated_time']) && $fields['calculated_time'] === true) {
                    $item['calculated_time'] = $this->getCalculatedTime($sumData);
                }else{
                    $item['calculated_time'] = null;
                }

                if (isset($fields['recommended_time']) && $fields['recommended_time'] === true) {
                    $item['recommended_time'] = $this->getRecommendedTime($sumData);
                }else{
                    $item['recommended_time'] = null;
                }

                return $item;
            }
        }, $items);

        return $result;
    }

    public function getSessionTime($item): string
    {
        if($item['status'] != 'not_started')
        {
            $session_time = $item->total_session_time;
        }else{
            $session_time = 0;
        }

        return $session_time;
    }

    public function getCmiTime($item): string
    {
        if($item['status'] != 'not_started')
        {
            $session_time = $item->total_cmi_time;
        }else{
            $session_time = 0;
        }

        return $session_time;
    }
    public function getCalculatedTime($item): string
    {
        if($item['status'] != 'not_started')
        {
            $session_time = $item->total_calculated_time;
        }else{
            $session_time = 0;
        }

        return $session_time;
    }
    public function getRecommendedTime($item): string
    {
        if($item['status'] != 'not_started')
        {
            $session_time = $item->total_recommended_time;
        }else{
            $session_time = 0;
        }
        return $session_time;
    }

    public function batchInsert($items,$fields){
        DB::transaction(function () use ($items, $fields) {
            Lpenroll::upsert(
                $items,
                [
                    'learner_docebo_id',
                    'lp_docebo_id',
                ],
                $fields
            );
        });
    }
}
