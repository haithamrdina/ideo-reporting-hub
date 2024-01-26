<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class DoceboAccess extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/manage/v1/user/login';
    }

    protected function defaultBody(): array
    {
        return [
            'username' => 'api_ideo',
            'password' => '@idEo_9Fa_cto',
            'issue_refresh_token' => true
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return $response->json('data')["access_token"];
    }
}
