<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Models\Learner;
use App\Models\Mooc;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboMoocsEnrollements extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $course;
    protected Method $method = Method::GET;
    public function __construct(string $course) {
        $this->course = $course;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/course/v1/courses/enrollments?course_id[]='.$this->course;
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

        $filteredItems = array_map(function ($item) {
            $learner = Learner::where('docebo_id' ,  $item['user_id'])->first();
            $mooc = Mooc::where('docebo_id' , $item['course_id'])->first();
            if($learner){
                $status =  $item['enrollment_status'];
                $enrollment_time_spent = $item['enrollment_time_spent'];
                $dataTiming = $this->getTimingData($status, $enrollment_time_spent, $mooc);
                return [
                    'learner_docebo_id' => $learner->docebo_id,
                    'mooc_docebo_id' => $mooc->docebo_id,
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

        return array_filter($filteredItems);
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

    public function getTimingData($status, $enrollment_time_spent, $mooc)
    {
        $fields = config('tenantconfigfields.enrollmentfields');
        if((($status != 'enrolled') && ($status !== 'waiting'))){
            $session_time = $enrollment_time_spent;
            $cmi_time = !empty($fields['cmi_time']) ? $enrollment_time_spent : null;
            $recommended_time = !empty($fields['recommended_time']) ? $mooc->recommended_time : null;
            $calculated_time = !empty($fields['calculated_time']) ? $this->getCalculatedTime($status, $mooc->recommended_time, $cmi_time) : null;
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
