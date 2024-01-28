<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboMoocsList extends Request implements Paginatable
{
    protected $search_catalog;
    protected Method $method = Method::GET;

    public function __construct( string $search_catalog) {
        $this->search_catalog = $search_catalog;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/learn/v1/catalog?search_catalog_fields=' .$this->search_catalog;
    }

    protected function defaultQuery(): array
    {

        return [
            'show_item_list' => '1',
            'items_per_catalog' => '100000',
            'get_cursor' => '1'
        ];
    }


    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');

        $filteredItems = array_reduce($items, function ($carry, $item) {
            return array_merge($carry, $item['sub_items']);
        }, []);

        $result = array_map(function ($subitem) {
            return [
                'docebo_id' => $subitem['item_id'],
                'code' => $subitem['item_code'],
                'name' => $subitem['item_name'],
                'recommended_time' => $subitem['duration'], // Assuming 'duration' is a key in your data
            ];
        }, $filteredItems);

        return $result;
    }

}
