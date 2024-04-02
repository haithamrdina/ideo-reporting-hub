<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetAllBadges extends Request
{
    /**
     * The HTTP method of the request
     */
    protected $company_code;
    protected Method $method = Method::GET;

    public function __construct( string $company_code) {
        $this->company_code = $company_code;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/share/v1/gamification/badges';
    }

    protected function defaultQuery(): array
    {
        return [
            'search_text' => $this->company_code
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
                'points' => $item['points'],
            ];
        }, $items);
        return $filteredItems;
    }
}
