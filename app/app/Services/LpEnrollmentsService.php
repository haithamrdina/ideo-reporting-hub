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

                $sumTimesData = Enrollmodule::where('learner_docebo_id', $learner->docebo_id)
                            ->whereIn('module_docebo_id', $modulesIds)
                            ->selectRaw('SUM(session_time) as total_session_time')
                            ->selectRaw('SUM(cmi_time) as total_cmi_time')
                            ->selectRaw('SUM(calculated_time) as total_calculated_time')
                            ->selectRaw('SUM(recommended_time) as total_recommended_time')
                            ->first();

                $item['session_time'] = $item['status'] != 'not_started' ? $this->getSessionTime($sumTimesData) : 0;
                $item['cmi_time'] = !empty($fields['cmi_time']) ? ( $item['status'] != 'not_started' ? $this->getCmiTime($sumTimesData): 0 ) : null;
                $item['calculated_time'] = !empty($fields['calculated_time']) ? ( $item['status'] != 'not_started' ? $this->getCalculatedTime($sumTimesData): 0 ) : null;
                $item['recommended_time'] = !empty($fields['recommended_time']) ? ( $item['status'] != 'not_started' ? $this->getRecommendedTime($sumTimesData): 0 ) : null;

                return $item;
            }
        }, $items);

        return $result;
    }

    public function getSessionTime($item): string
    {
        return $item->total_session_time;
    }

    public function getCmiTime($item): string
    {
        return $item->total_cmi_time;
    }
    public function getCalculatedTime($item): string
    {
        return $item->total_calculated_time;
    }
    public function getRecommendedTime($item): string
    {
        return $item->total_recommended_time;
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
