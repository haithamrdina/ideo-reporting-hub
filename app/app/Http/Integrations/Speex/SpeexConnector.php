<?php

namespace App\Http\Integrations\Speex;

use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class SpeexConnector extends Connector
{
    use AcceptsJson;

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


    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => 60,
        ];
    }

    protected function defaultAuth(): QueryAuthenticator
    {
        return new QueryAuthenticator('apiKey', 'c170979b0d8786ef3d4b5510b9814913');
    }
}
