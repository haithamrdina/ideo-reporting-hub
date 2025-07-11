<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboCoursesEnrolls extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $courses;
    protected $users;
    protected Method $method = Method::GET;
    public function __construct(Array $courses, Array $users) {
        $this->courses = $courses;
        $this->users = $users;
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

        $users = '';
        foreach ($this->users as $user) {
            $users .= 'user_id[]=' . $user . "&";
        }
        return '/course/v1/courses/enrollments?'.$courses . $users .'get_cursor=1';
    }
    protected function defaultQuery(): array
    {
        return [
            'extra_fields[]' => 'enrollment_time_spent',
        ];
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
