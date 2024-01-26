<?php

namespace App\Http\Integrations\Zendesk\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class ZendeskRequesterUsername extends Request
{
    /**
     * The HTTP method of the request
     */
    protected $requester_id;
    protected Method $method = Method::GET;
    public function __construct(string $requester_id)
    {
        $this->requester_id = $requester_id;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return 'users/'. $this->requester_id.'/identities';
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('identities')[0];
        return $items['value'];
    }
}
