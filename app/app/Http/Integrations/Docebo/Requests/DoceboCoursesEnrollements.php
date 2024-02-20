<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Enums\CourseStatusEnum;
use App\Http\Integrations\Docebo\DoceboConnector;
use App\Models\Learner;
use App\Models\Module;
use App\Services\TimeConversionService;
use Exception;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\Statuses\InternalServerErrorException;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboCoursesEnrollements extends Request implements Paginatable
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
            $learner = Learner::where('docebo_id' ,  $item['user_id'])->first();
            $module = Module::where('docebo_id' , $item['course_id'])->where('status' , CourseStatusEnum::ACTIVE)->whereIn('category', ['SM','CEGOS','ENI'])->first();
            if($module){
                $status = $item['enrollment_status'];
                $dataTiming = $this->getTimingData($item, $module, $learner, $status);
                return [
                    'learner_docebo_id' => $learner->docebo_id,
                    'module_docebo_id' => $module->docebo_id,
                    'status' => $status,
                    'enrollment_created_at' => $item['enrollment_created_at'],
                    'enrollment_updated_at' => $item['enrollment_date_last_updated'],
                    'enrollment_completed_at' => $item['enrollment_completion_date'],
                    'session_time' => $dataTiming->session_time,
                    'cmi_time' => $dataTiming->cmi_time,
                    'calculated_time' => $dataTiming->calculated_time,
                    'recommended_time' => $dataTiming->recommended_time,
                    'project_id' => $learner->project->id,
                    'group_id' => $learner->group->id,

                ];
            }
        }, $items);
        return $filteredItems;
    }

    public function getCmiTime($module,$learner, $status): string
    {
        $cmi_time = 0;
        if($status != 'enrolled' && $status != "waiting"){
            foreach($module->los as $lo){
                try {
                    $doceboConnector = new DoceboConnector();
                    $cmiRequest = new DoceboGetLoCmiData($lo,$module->docebo_id,$learner->docebo_id);
                    $cmiResponse = $doceboConnector->send($cmiRequest);
                    if($cmiResponse->status() === 200){
                        $cmi_time += $cmiResponse->dto();
                    }else{
                        $cmi_time += 0;
                    }
                    // Process $cmiResponse
                } catch (InternalServerErrorException $e) {
                    $cmi_time = 0;
                } catch (Exception $e) {
                    $cmi_time = 0;
                }
            }
        }
        return $cmi_time;
    }

    public function getCalculatedTime($recommended_time, $cmi_time, $status): string
    {
        if ($status === 'completed' || ($status === 'in_progress' && $cmi_time > $recommended_time)) {
            $calculated_time = $recommended_time;
        }else{
            $calculated_time = $cmi_time;
        }
        return $calculated_time;
    }

    public function getTimingData($item, $module, $learner, $status)
    {
        $fields = config('tenantconfigfields.enrollmentfields');
        if((($status != 'enrolled') && ($status !== 'waiting'))){
            $session_time = $item['enrollment_time_spent'];
            $recommended_time = !empty($fields['recommended_time']) ? $module->recommended_time : null;
            $cmi_time = !empty($fields['cmi_time']) ? $this->getCmiTime($module, $learner,$status) : null;
            $calculated_time = !empty($fields['calculated_time']) ? $this->getCalculatedTime($module->recommended_time, $cmi_time, $status) : null;
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
