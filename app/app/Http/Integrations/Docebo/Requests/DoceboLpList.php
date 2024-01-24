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
    protected $company_code;
    protected Method $method = Method::GET;

    public function __construct(string $company_code) {
        $this->company_code = $company_code;
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
            'search_text' => $this->company_code,
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
                "courses" => json_encode(array_map(function($course) {
                    return $course["id_course"];
                }, $item["courses"]))

            ];
        }, $items);

        return $filteredItems;
    }

}
