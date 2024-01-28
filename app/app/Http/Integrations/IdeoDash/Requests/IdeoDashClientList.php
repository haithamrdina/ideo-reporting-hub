<?php

namespace App\Http\Integrations\IdeoDash\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class IdeoDashClientList extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/allclients';
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('Clients');
        $filteredItems = array_map(function ($item) {
            return (Object)[
                'client_org_id' => $item['idOrg'],
                'client_org_name' => $item['name'],

            ];
        }, $items);

        return $filteredItems;
    }
}
