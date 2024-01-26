<?php

namespace App\Http\Integrations\Zendesk\Requests;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class ZendeskOrganizationsTickets extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $zendesk_org_id;
    protected Method $method = Method::GET;
    public function __construct(string $zendesk_org_id)
    {
        $this->zendesk_org_id = $zendesk_org_id;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/search/export.json';
    }

    /**
     * Default headers for every request
     */
    protected function defaultQuery(): array
    {
        return [
            'query'=> 'organization_id:' .$this->zendesk_org_id,
            'page[size]' => 100,
            'filter[type]' => 'ticket'

        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('results');
        $filteredItems = array_map(function ($item) {
            return [
                'status' => $item['status'],
                'subject' =>  preg_replace('/\p{Cf}/u', '', $item['subject']),
                'ticket_created_at' => (new DateTime($item['created_at']))->format("Y-m-d H:i:s"),
                'ticket_updated_at' => (new DateTime($item['created_at']))->format("Y-m-d H:i:s"),
                'requester_id' => $item['requester_id'],
            ];
        }, $items);

        return $filteredItems;
    }
}
