<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class DoceboCourseLosList extends Request
{
    /**
     * The HTTP method of the request
     */
    protected $course_id;
    protected Method $method = Method::GET;
    public function __construct( string $course_id) {
        $this->course_id = $course_id;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/learn/v1/courses/'.$this->course_id.'/los';
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');
        $los = collect($items)->pluck('item_id')->reject(function ($value) {
            return $value === null;
        })->toArray();
        return $los;
    }
}
