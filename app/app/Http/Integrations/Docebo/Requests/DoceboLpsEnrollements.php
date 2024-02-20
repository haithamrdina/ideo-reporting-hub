<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Models\Enrollmodule;
use App\Models\Learner;
use App\Models\Lp;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboLpsEnrollements extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $lp;
    // protected $users;
    protected Method $method = Method::GET;
    public function __construct(string $lp) {
        $this->lp = $lp;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/learningplan/v1/learningplans/enrollments?learning_plan_id[]=' . $this->lp;
    }

    protected function defaultQuery(): array
    {

        return [
            'extra_fields[0]' => 'enrollment_status',
            'extra_fields[1]' => 'enrollment_completion_date',
            'extra_fields[2]' => 'enrollment_completion_percentage',
            'extra_fields[3]' => 'enrollment_time_spent',
            'get_cursor' => '1'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');
        $fields = config('tenantconfigfields.enrollmentfields');
        $filteredItems = array_map(function ($item) use ($fields){
            $learner = Learner::where('docebo_id' ,  $item['user_id'])->first();
            $modulesIds = Lp::where('docebo_id' , $item['learning_plan_id'])->first()->courses;
            if($learner){

                $sumTimesData = Enrollmodule::where('learner_docebo_id', $learner->docebo_id)
                    ->whereIn('module_docebo_id', $modulesIds)
                    ->selectRaw('SUM(session_time) as total_session_time')
                    ->selectRaw('SUM(cmi_time) as total_cmi_time')
                    ->selectRaw('SUM(calculated_time) as total_calculated_time')
                    ->selectRaw('SUM(recommended_time) as total_recommended_time')
                    ->first();

                return [
                    'learner_docebo_id' => $learner->docebo_id,
                    'lp_docebo_id' => $item['learning_plan_id'],
                    'status' => $item['enrollment_status'],
                    'enrollment_completion_percentage' => $item['enrollment_completion_percentage'],
                    'enrollment_created_at' => $item['enrollment_created_at'],
                    'enrollment_updated_at' => $item['enrollment_date_last_updated'],
                    'enrollment_completed_at' => $item['enrollment_completion_date'],
                    'session_time' => $item['enrollment_status'] != 'not_started' ? $sumTimesData->total_session_time : 0,
                    'cmi_time' => !empty($fields['cmi_time']) ? ( $item['enrollment_status'] != 'not_started' ? $sumTimesData->total_cmi_time : 0 ) : null,
                    'calculated_time' => !empty($fields['calculated_time']) ? ( $item['enrollment_status'] != 'not_started' ? $sumTimesData->total_calculated_time : 0 ) : null,
                    'recommended_time' => !empty($fields['recommended_time']) ? ( $item['enrollment_status'] != 'not_started' ? $sumTimesData->total_recommended_time : 0 ) : null,
                    'project_id' => $learner->project->id,
                    'group_id' => $learner->group->id,
                ];
            }
        }, $items);

        return array_filter($filteredItems);
    }
}


