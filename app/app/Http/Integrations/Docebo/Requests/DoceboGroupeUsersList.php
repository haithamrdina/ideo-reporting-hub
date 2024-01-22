<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboGroupeUsersList extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $branche;
    protected Method $method = Method::GET;

    public function __construct(string $branche) {
        $this->branche = $branche;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/manage/v1/user';
    }

    protected function defaultQuery(): array
    {
        return [
            'branch_id' => $this->branche,
            'get_cursor' => '1'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');

        $filteredItems = array_map(function ($item) {
            return [
                'docebo_id' => $item['user_id'],
                'firstname' => $item['first_name'],
                'lastname' => $item['last_name'],
                'email' => $item['email'],
                'username' => $item['username'],
                'creation_date' => $item['creation_date'],
                'last_access_date' => $item['last_access_date'],
                'status' => $item['last_access_date'] != null ? 'active' : 'inactive',
                'categorie' => $item['field_159']
            ];
        }, $items);

        return $filteredItems;

    }
}
