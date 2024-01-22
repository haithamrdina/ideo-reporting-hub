<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboCourseList extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $search;
    protected Method $method = Method::GET;

    public function __construct(string $search) {
        $this->search = $search;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/course/v1/courses';
    }

    protected function defaultQuery(): array
    {
        return [
            'search_text' => $this->search,
            'type[]' => 'elearning',
            'get_cursor' => '1'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');
        $softCodes = ["CDG-MH","CDG-ME","CDG-MS", "CDG-MFH", "CDG-ME","CDG-MHD"];
        $eniCodes = ["CDG-ENI"];
        $speexCodes = ["SPX-CDG-ENG", "SPX-CDG-ESP", "SPX-CDG-ITL", "SPX-CDG-ALD"];
        $smCodes = ["CDG-SM"];

        $filteredItems = array_map(function ($item) use ($softCodes, $eniCodes, $speexCodes, $smCodes) {
            $category = '';

            if ($this->containsAny($item['code'], $softCodes)) {
                $category = 'CEGOS';
            } elseif ($this->containsAny($item['code'], $eniCodes)) {
                $category = 'eni';
            } elseif ($this->containsAny($item['code'], $speexCodes)) {
                $category = 'SPEEX';
            } elseif ($this->containsAny($item['code'], $smCodes)) {
                $category = 'SM';
            }

            return [
                'docebo_id' => $item['id'],
                'code' => $item['code'],
                'name' => $item['title'],
                'language' => $item['language_label'],
                'recommended_time' => $item['average_completion_time'],
                'category' => $category,
            ];
        }, $items);

        return $filteredItems;
    }

    private function containsAny($str, array $search): bool
    {
        foreach ($search as $s) {
            if (strpos($str, $s) !== false) {
                return true;
            }
        }
        return false;
    }


}
