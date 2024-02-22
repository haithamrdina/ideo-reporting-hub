<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Enums\CourseStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Speex\Requests\SpeexUserArticleResult;
use App\Http\Integrations\Speex\SpeexConnector;
use App\Models\Learner;
use App\Models\Module;
use App\Services\TimeConversionService;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboSpeexEnrollements extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $user;
    protected Method $method = Method::GET;
    public function __construct(string $user) {
        $this->user = $user;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/course/v1/courses/enrollments?user_id[]=' .$this->user ;
    }
    protected function defaultQuery(): array
    {
        return [
            'extra_fields[]' => 'enrollment_time_spent',
            'get_cursor' => '1'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');
        $filteredItems = array_map(function ($item){
            if(!empty($item)){
                $learner = Learner::where('docebo_id' ,  $item['user_id'])->first();
                $module = Module::where('docebo_id' , $item['course_id'])->where('status' , CourseStatusEnum::ACTIVE)->where('category', 'SPEEX')->first();
                if($module){
                    dump($module);
                    /*$status = $item['enrollment_status'];
                    $speexData = $this->getSpeexData($module, $learner);
                    $dataTiming = $this->getTimingData($item, $status, $speexData);
                    return [
                        'learner_docebo_id' => $learner->docebo_id,
                        'module_docebo_id' => $module->docebo_id,
                        'status' => $status,
                        'enrollment_created_at' => $item['enrollment_created_at'],
                        'enrollment_updated_at' => $item['enrollment_date_last_updated'],
                        'enrollment_completed_at' => $item['enrollment_completion_date'],
                        'niveau' => $speexData['niveau'],
                        'language' => $module->language,
                        'session_time' => $dataTiming->session_time,
                        'cmi_time' => $dataTiming->cmi_time,
                        'calculated_time' => $dataTiming->calculated_time,
                        'recommended_time' => $dataTiming->recommended_time,
                        'project_id' => $learner->project->id,
                        'group_id' => $learner->group->id,
                    ];*/
                }
            }
        }, $items);
        return $filteredItems;
    }

    public function getSpeexData($module, $learner){
        $speexConnector = new SpeexConnector();
        $speexResponse = $speexConnector->send(new SpeexUserArticleResult($learner->speex_id, $module->article_id));
        return $speexResponse->dto();

    }

    public function getCalculatedTime($status, $recommended_time, $cmi_time)
    {
        if ($status === 'completed' || ($status === 'in_progress' && $cmi_time > $recommended_time)) {
            $calculated_time =  $recommended_time;
        }else{
            $calculated_time = $cmi_time;
        }
        return $calculated_time;
    }

    public function getRecommendedTime($status, $niveau): string
    {
        $recommended_time = 32400;
        if($status != 'enrolled' || $status != 'waiting' ){

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

            if($niveau != null){
                $recommended_time = 32400 * $multipliers[$niveau];
            }
        }
        return $recommended_time;
    }

    public function getTimingData($item, $status, $speexData)
    {
        $fields = config('tenantconfigfields.enrollmentfields');
        if((($status != 'enrolled') && ($status !== 'waiting'))){
            $session_time = $item['enrollment_time_spent'] + $speexData['time'];
            $recommended_time = !empty($fields['recommended_time']) ? $this->getRecommendedTime($status, $speexData['niveau']) : null;
            $cmi_time = !empty($fields['cmi_time']) ? $speexData['time'] : null;
            $calculated_time = !empty($fields['calculated_time']) ? $this->getCalculatedTime($status, $recommended_time, $cmi_time) : null;
        }else{
            $session_time = "0";
            $recommended_time = !empty($fields['recommended_time']) ? "0" : null;
            $cmi_time = !empty($fields['cmi_time']) ? "0" : null;
            $calculated_time = !empty($fields['calculated_time']) ? "0" : null;
        }

        return (object)[
            'session_time' => $session_time,
            'cmi_time' => $cmi_time,
            'calculated_time' => $calculated_time,
            'recommended_time' => $recommended_time,
        ];
    }

}
