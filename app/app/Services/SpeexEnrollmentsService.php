<?php

namespace App\Services;

use App\Http\Integrations\Speex\Requests\SpeexUserArticleResult;
use App\Http\Integrations\Speex\SpeexConnector;
use App\Interfaces\SpeexInterface;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Module;
use Illuminate\Support\Facades\DB;

class SpeexEnrollmentsService implements SpeexInterface
{
    public function getEnrollmentsFields($fields): array
    {
        $principalList =[
            'status',
            'enrollment_created_at',
            'enrollment_updated_at',
            'enrollment_completed_at',
            'niveau',
            'language',
            'session_time',
            'group_id',
            'project_id',
        ];
        return array_merge($principalList, array_keys($fields));
    }

    public function getEnrollmentsList($items, $fields): array
    {
        $result = array_map(function ($item) use ($fields){
            $module = Module::where('docebo_id', $item['module_docebo_id'])->first();
            $learner = Learner::where('docebo_id', $item['learner_docebo_id'])->first();
            $item['language'] = $module->language;
            $item['group_id'] = $learner->group->id;
            $item['project_id'] = $learner->project->id;

            $speexData = $this->getSpeexData($item);
            $item['niveau'] = $speexData['niveau'];

            $item['session_time'] = $this->getSessionTime($item, $speexData);

            if (isset($fields['cmi_time']) && $fields['cmi_time'] === true) {
                $item['cmi_time'] = $this->getCmiTime($item, $speexData);
            }else{
                $item['cmi_time'] = null;
            }

            if (isset($fields['calculated_time']) && $fields['calculated_time'] === true) {
                $item['calculated_time'] = $this->getCalculatedTime($item, $speexData);
            }else{
                $item['calculated_time'] = null;
            }

            if (isset($fields['recommended_time']) && $fields['recommended_time'] === true) {
                $item['recommended_time'] = $this->getRecommendedTime($item, $speexData);
            }else{
                $item['recommended_time'] = null;
            }

            return $item;

        }, $items);

        return $result;

    }

    public function getSessionTime($item, $speexData): string
    {
        if($item['status'] != 'enrolled' || $item['status'] != 'waiting' ){
            $session_time = intval($item['session_time']) +  intval($speexData['time']);
        }else{
            $session_time = 0;
        }
        return $session_time;
    }

    public function getCmiTime($item, $speexData): string
    {
        if($item['status'] != 'enrolled' || $item['status'] != 'waiting' ){
            $cmi_time = $speexData['time'];
        }else{
            $cmi_time = 0;
        }
        return $cmi_time;
    }

    public function getCalculatedTime($item, $speexData): string
    {
        $recommended_time = $this->getRecommendedTime($item,$speexData);
        if($item['status'] == 'completed'){
            $calculated_time = $recommended_time;
        }elseif($item['status'] == 'in_progress' && $speexData['time'] > $recommended_time){
            $calculated_time = $recommended_time;
        }else{
            $calculated_time = $speexData['time'];
        }
        return $calculated_time;
    }

    public function getRecommendedTime($item, $speexData): string
    {
        if($item['status'] != 'enrolled' || $item['status'] != 'waiting' ){

            $multipliers = [
                'A1' => 1,
                'A2' => 2,
                'B1.1' => 3,
                'B1.2' => 4,
                'B2.1' => 5,
                'B2.2' => 6,
                'C1.1' => 7,
                'C1.2' => 8
            ];

            if($speexData['niveau'] != null){
                $recommended_time = 32400 * $multipliers[$item['niveau']];
            }else{
                $recommended_time = 32400;
            }

        }else{
            $recommended_time = 32400;
        }

        return $recommended_time;
    }

    public function batchInsert($data, $fields){
        DB::transaction(function () use ($data, $fields) {
            Langenroll::upsert(
                $data,
                [
                    'learner_docebo_id',
                    'module_docebo_id',
                ],
                $fields
            );
        });
    }

    public function getSpeexData($item){
        $module = Module::where('docebo_id', $item['module_docebo_id'])->first();
        $learner = Learner::where('docebo_id', $item['learner_docebo_id'])->first();
        $articleId = $module->article_id;
        $speexId = $learner->speex_id;
        $speexConnector = new SpeexConnector();
        $speexResponse = $speexConnector->send(new SpeexUserArticleResult($speexId, $articleId));
        $speexReponseData = $speexResponse->dto();
        return $speexReponseData;
    }
}
