<?php

namespace App\Services;

use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\DoceboGetLoCmiData;
use App\Interfaces\EnrollmentInterface;
use App\Models\Enrollmodule;
use App\Models\Learner;
use App\Models\Module;
use Illuminate\Support\Facades\DB;

class ModuleEnrollmentsService implements EnrollmentInterface
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

    public function getEnrollmentsList($items, $fields): array
    {
        $result = array_map(function ($item) use($fields){
            $learner = Learner::where('docebo_id' , $item['learner_docebo_id'])->first();

            $item['group_id'] = $learner->group->id;
            $item['project_id'] = $learner->project->id;

            $item['session_time'] = $this->getSessionTime($item);

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

        }, $items);
        return $result;
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
            $doceboConnector = new DoceboConnector();
            $module = Module::where('docebo_id', $item['module_docebo_id'])->first();
            $cmi_time = 0;
            foreach($module->los as $lo){
                $cmiRequest = new DoceboGetLoCmiData($lo,$item['module_docebo_id'],$item['learner_docebo_id']);
                $cmiResponse = $doceboConnector->send($cmiRequest);
                if($cmiResponse->status() === 200){
                    $cmi_time += getCmiTime($cmiResponse->body());
                }else{
                    $cmi_time += 0;
                }
            }
        }else{
            $cmi_time = 0;
        }

        return $cmi_time;
    }

    public function getCalculatedTime($item): string
    {
        $module = Module::where('docebo_id' , $item['module_docebo_id'])->first();
        if($item['status'] != 'enrolled' || $item['status'] != 'waiting' ){
            if($item['status'] == 'completed'){
                $calculated_time = $module->recommended_time;
            }elseif($item['status'] == 'in_progress' && $item['cmi_time'] > $module->recommended_time){
                $calculated_time = $module->recommended_time;
            }else{
                $calculated_time = $item['cmi_time'];
            }
        }else{
            $calculated_time = 0;
        }
        return $calculated_time;
    }

    public function getRecommendedTime($item): string
    {
        $module = Module::where('docebo_id' , $item['module_docebo_id'])->first();
        $recommended_time = $module->recommended_time;;
        return $recommended_time;
    }

    public function batchInsert($data, $fields){
        DB::transaction(function () use ($data, $fields) {
            Enrollmodule::upsert(
                $data,
                [
                    'learner_docebo_id',
                    'module_docebo_id',
                ],
                $fields
            );
        });
    }
}
