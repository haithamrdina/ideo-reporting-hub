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

                $item['session_time'] = $this->getSessionTime($item);
                $item['group_id'] = $learner->group->id;
                $item['project_id'] = $learner->project->id;

                if (isset($fields['cmi_time']) && $fields['cmi_time'] === true) {
                    $item['cmi_time'] = $this->getCmiTime($item);
                }else{
                    $item['cmi_time'] = null;
                }

                if (isset($fields['calculated_time']) && $fields['calculated_time'] === true) {
                    $item['calculated_time'] = $this->getCalculatedTime($item);
                }else{
                    $item['calculated_time'] = null;
                }

                if (isset($fields['recommended_time']) && $fields['recommended_time'] === true) {
                    $item['recommended_time'] = $this->getRecommendedTime($item);
                }else{
                    $item['recommended_time'] = null;
                }

                return $item;
            }
        }, $items);
        return array_filter($result);

    }

    public function getSessionTime($item): string
    {
        if($item['status'] != 'enrolled' || $item['status'] != 'waiting' ){
            $session_time = $item['session_time'];
        }else{
            $session_time = 0;
        }
        return $session_time;
    }

    public function getCmiTime($item): string
    {
        if($item['status'] != 'enrolled' || $item['status'] != 'waiting' ){
            $cmi_time = $item['session_time'];
        }else{
            $cmi_time = 0;
        }

        return $cmi_time;
    }

    public function getCalculatedTime($item): string
    {
        $mooc = Mooc::where('docebo_id' , $item['mooc_docebo_id'])->first();
        if($item['status'] != 'enrolled' || $item['status'] != 'waiting' ){
            if($item['status'] == 'completed'){
                $calculated_time = $mooc->recommended_time;
            }elseif($item['status'] == 'in_progress' && $item['session_time'] > $mooc->recommended_time){
                $calculated_time = $mooc->recommended_time;
            }else{
                $calculated_time = $item['session_time'];
            }
        }else{
            $calculated_time = 0;
        }
        return $calculated_time;
    }

    public function getRecommendedTime($item): string
    {
        $mooc = Mooc::where('docebo_id' , $item['mooc_docebo_id'])->first();
        $recommended_time = $mooc->recommended_time;;
        return $recommended_time;
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
