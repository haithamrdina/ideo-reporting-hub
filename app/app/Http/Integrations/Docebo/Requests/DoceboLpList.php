<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboLpList extends Request implements Paginatable
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
        return '/learn/v1/lp';
    }

    protected function defaultQuery(): array
    {
        return [
            'search_text' => $this->search,
            'return_courses' => 'true',
            'get_cursor' => '1'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');

        $filteredItems = array_map(function ($item) {
            return [
                'docebo_id' => $item['id'],
                'code' => $item['code'],
                'name' => $item['name'],
                "courses" => array_map(function($course) {
                    return $course["id_course"];
                }, $item["courses"])

            ];
        }, $items);

        return $filteredItems;
    }

}
