<?php

namespace App\Http\Integrations\Speex;

use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class SpeexConnector extends Connector
{
    use AcceptsJson;

    public ?int $tries = 3;
    public ?int $retryInterval = 1000;
    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://portal.speexx.com/api';
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

    protected function defaultAuth(): QueryAuthenticator
    {
        return new QueryAuthenticator('apiKey', 'c170979b0d8786ef3d4b5510b9814913');
    }
}
