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
            $item['cmi_time'] = !empty($fields['cmi_time']) ? $this->getCmiTime($item) : null;
            $item['calculated_time'] = !empty($fields['calculated_time']) ? $this->getCalculatedTime($item) : null;
            $item['recommended_time'] = !empty($fields['recommended_time']) ? $this->getRecommendedTime($item) : null;

            return $item;

        }, $items);
        return $result;
    }

    public function getSessionTime($item): string
    {
        $session_time = ($item['status'] !== 'enrolled' || $item['status'] !== 'waiting') ? $item['session_time'] : '0';
        return $session_time;
    }

    public function getCmiTime($item): string
    {
        if($item['status'] != 'enrolled' || $item['status'] != 'waiting' ){
            $doceboConnector = new DoceboConnector();
            $timeConverisonService = new TimeConversionService();
            $module = Module::where('docebo_id', $item['module_docebo_id'])->first();
            $cmi_time = 0;
            foreach($module->los as $lo){
                $cmiRequest = new DoceboGetLoCmiData($lo,$item['module_docebo_id'],$item['learner_docebo_id']);
                dd($cmiRequest);
                $cmiResponse = $doceboConnector->send($cmiRequest);
                if($cmiResponse->status() === 200){
                    $cmi_time += $timeConverisonService->getDoceboCmiTime($cmiResponse->body());
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

        if($item['status'] == 'enrolled' || $item['status'] == 'waiting'){
            $calculated_time = 0;
        }elseif ($item['status'] === 'completed' || ($item['status'] === 'in_progress' && $item['cmi_time'] > $module->recommended_time)) {
            $calculated_time = $module->recommended_time;
        }else{
            $calculated_time = $item['cmi_time'];
        }

        return $calculated_time;
    }

    public function getRecommendedTime($item): string
    {
        $module = Module::where('docebo_id', $item['module_docebo_id'])->firstOrFail();
        return $module->recommended_time;
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
