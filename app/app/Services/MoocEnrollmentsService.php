<?php

namespace App\Services;

use App\Interfaces\EnrollmentInterface;
use App\Models\Enrollmooc;
use App\Models\Learner;
use App\Models\Mooc;
use Illuminate\Support\Facades\DB;

class MoocEnrollmentsService implements EnrollmentInterface
{
    public function getEnrollmentsFields($fields): array
    {
        $principalList =[
            'status',
            'enrollment_created_at',
            'enrollment_updated_at',
            'enrollment_completed_at',
            'session_time',
            'group_id',
            'project_id',
        ];
        return array_merge($principalList, array_keys($fields));
    }

    public function getEnrollmentsList($items,$fields): array
    {
        $result = array_map(function ($item) use ($fields){
            $learner = Learner::where('docebo_id' , $item['learner_docebo_id'])->first();

            if($learner){

                $item['group_id'] = $learner->group->id;
                $item['project_id'] = $learner->project->id;

                $item['session_time'] = $this->getSessionTime($item);
                $item['cmi_time'] = !empty($fields['cmi_time']) ? $this->getCmiTime($item) : null;
                $item['calculated_time'] = !empty($fields['calculated_time']) ? $this->getCalculatedTime($item) : null;
                $item['recommended_time'] = !empty($fields['recommended_time']) ? $this->getRecommendedTime($item) : null;

                return $item;
            }
        }, $items);
        return array_filter($result);

    }

    public function getSessionTime($item): string
    {
        $session_time = ($item['status'] !== 'enrolled' || $item['status'] !== 'waiting') ? $item['session_time'] : '0';
        return $session_time;
    }

    public function getCmiTime($item): string
    {
        $cmi_time = ($item['status'] !== 'enrolled' || $item['status'] !== 'waiting') ? $item['session_time'] : '0';
        return $cmi_time;
    }

    public function getCalculatedTime($item): string
    {
        $mooc = Mooc::where('docebo_id' , $item['mooc_docebo_id'])->first();

        if($item['status'] == 'enrolled' || $item['status'] == 'waiting'){
            $calculated_time = 0;
        }elseif ($item['status'] === 'completed' || ($item['status'] === 'in_progress' && $item['session_time'] > $mooc->recommended_time)) {
            $calculated_time = $mooc->recommended_time;
        }else{
            $calculated_time = $item['session_time'];
        }

        return $calculated_time;
    }

    public function getRecommendedTime($item): string
    {
        $mooc = Mooc::where('docebo_id', $item['mooc_docebo_id'])->firstOrFail();
        return $mooc->recommended_time;
    }

    public function batchInsert($data, $fields){
        DB::transaction(function () use ($data, $fields) {
            Enrollmooc::upsert(
                $data,
                [
                    'learner_docebo_id',
                    'mooc_docebo_id',
                ],
                $fields
            );
        });
    }
}
