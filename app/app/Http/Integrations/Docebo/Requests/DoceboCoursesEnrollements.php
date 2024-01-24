<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboCoursesEnrollements extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $courses;
    protected Method $method = Method::GET;
    public function __construct(Array $courses) {
        $this->courses = $courses;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        $courses = '';
        foreach ($this->courses as $course) {
            $courses .= 'course_id[]=' . $course . "&";
        }
        return '/course/v1/courses/enrollments?'.$courses . 'extra_fields[]=enrollment_time_spent&get_cursor=1' ;
    }


    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');

        $filteredItems = array_map(function ($item) {
            return [
                'learner_docebo_id' => $item['user_id'],
                'module_docebo_id' => $item['course_id'],
                'status' => $item['enrollment_status'],
                'enrollment_created_at' => $item['enrollment_created_at'],
                'enrollment_updated_at' => $item['enrollment_date_last_updated'],
                'enrollment_completed_at' => $item['enrollment_completion_date'],
                'session_time' => $item['enrollment_time_spent'],

            ];
        }, $items);

        return $filteredItems;
    }

}
