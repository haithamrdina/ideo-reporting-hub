<?php

namespace App\Http\Integrations\Zendesk\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class ZendeskOrganizations extends Request
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
            return [
                'zendesk_org_id' => $item['id'],
                'zendesk_org_name' => $item['name'],

            ];
        }, $items);

        return $filteredItems;
    }


}
