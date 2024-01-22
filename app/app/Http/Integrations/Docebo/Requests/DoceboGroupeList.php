<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboGroupeList extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */

    protected $node;
    protected Method $method = Method::GET;

    public function __construct(string $node) {
        $this->node = $node;
    }


    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/manage/v1/orgchart';
    }

    protected function defaultQuery(): array
    {
        return [
            'node_id' => $this->node,
            'search_type' => '3',
            'flattened' => 'true',
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
                'name' => $item['title']
            ];
        }, $items);

        return $filteredItems;

    }
}
