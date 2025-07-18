<?php

namespace App\Http\Integrations\Zendesk\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class ZendeskOrganizations extends Request implements Paginatable
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
        return '/organizations';
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('organizations');
        $filteredItems = array_map(function ($item) {
            return (Object)[
                'zendesk_org_id' => $item['id'],
                'zendesk_org_name' => $item['name'],

            ];
        }, $items);

        return $filteredItems;
    }


}
