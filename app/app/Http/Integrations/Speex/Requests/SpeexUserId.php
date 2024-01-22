<?php

namespace App\Http\Integrations\Speex\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class SpeexUserId extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected $username;
    protected Method $method = Method::POST;

    public function __construct(string $username){
        $this->username = $username;
    }

     /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }


    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/user.find';
    }

    protected function defaultBody(): array
    {
        return [
            'userName' => 'IDEO-' .$this->username,
            'projectId' => '502605',
            'apikey' => 'c170979b0d8786ef3d4b5510b9814913'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json();
        return $items['userId'];
    }
}
