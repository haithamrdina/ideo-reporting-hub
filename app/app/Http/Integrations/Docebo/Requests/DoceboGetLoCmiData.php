<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

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

     /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => 60,
        ];
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

    public function createDtoFromResponse(Response $response): mixed
    {
        $responseBody = $response->body();

        $totalTime = 0;
        $totalTimeRegex = '/<td>cmi\.core\.total_time<\/td><td>(.*?)<\/td>/';
        $sessionTimeRegex = '/<td>cmi\.core\.session_time<\/td><td>(.*?)<\/td>/';
        if (preg_match($totalTimeRegex, $responseBody, $totalTimeMatches)) {
            $totalTimeValue = $totalTimeMatches[1];
            if($totalTimeValue ==  '0000:00:00.00'){
                if(preg_match($sessionTimeRegex, $responseBody, $sessionTimeMatches)) {
                    $sessionTimeValue = $sessionTimeMatches[1];
                    $totalTime += $this->convertTimeToSeconds($sessionTimeValue);
                }
            }else{
                $totalTime += $this->convertTimeToSeconds($totalTimeValue);
            }
        }
        return $totalTime;
    }


    function convertTimeToSeconds($time)
    {
        $timeArray = explode(':', $time);
        if (count($timeArray) !== 3) {
            return 0; // Invalid time format, return 0 seconds
        }

        $hours = intval($timeArray[0]);
        $minutes = intval($timeArray[1]);
        $seconds = intval($timeArray[2]);

        return $hours * 3600 + $minutes * 60 + $seconds;
    }
}
