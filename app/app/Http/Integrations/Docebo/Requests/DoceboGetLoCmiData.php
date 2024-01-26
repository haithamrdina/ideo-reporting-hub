<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DoceboGetLoCmiData extends Request
{
    /**
     * The HTTP method of the request
     */
    protected $lo;
    protected $course;
    protected $user;
    protected Method $method = Method::GET;
    public function __construct(string $lo, string $course, string $user) {
        $this->lo = $lo;
        $this->course = $course;
        $this->user = $user;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/lms/index.php';
    }

    protected function defaultQuery(): array
    {

        return [
            'r' => 'player/report/scoXmlReport',
            'objectId' => $this->lo,
            'course_id' => $this->course,
            'user_id' => $this->user
        ];
    }
}
