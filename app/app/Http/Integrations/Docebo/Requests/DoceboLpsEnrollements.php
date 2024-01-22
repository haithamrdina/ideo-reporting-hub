<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboLpsEnrollements extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $lps;
    // protected $users;
    protected Method $method = Method::GET;
    public function __construct(Array $lps) {
        $this->lps = $lps;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        $lps = '';
        foreach ($this->lps as $lp) {
            $lps .= 'learning_plan_id[]=' . $lp['docebo_id'] . "&";
        }
        return '/learningplan/v1/learningplans/enrollments?' . $lps;
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

        $filteredItems = array_map(function ($item) {
            return [
                'learner_docebo_id' => $item['user_id'],
                'lp_docebo_id' => $item['learning_plan_id'],
                'status' => $item['enrollment_status'],
                'enrollment_completion_percentage' => $item['enrollment_completion_percentage'],
                'enrollment_created_at' => $item['enrollment_created_at'],
                'enrollment_updated_at' => $item['enrollment_date_last_updated'],
                'enrollment_completed_at' => $item['enrollment_completion_date'],
            ];
        }, $items);

        return $filteredItems;
    }
}
