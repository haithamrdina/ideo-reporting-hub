<?php

namespace App\Http\Integrations\Speex;

use Illuminate\Support\Facades\Cache;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\Connector;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

class SpeexConnector extends Connector
{
    use AcceptsJson;
    use HasRateLimits;

    public ?int $tries = 3;
    public ?int $retryInterval = 1000;
    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://portal.speexx.com/api';
    }

    protected function resolveLimits(): array
    {
        return [
            Limit::allow(100)->everyMinute()
        ];
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        return new LaravelCacheStore(Cache::store('redis'));
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
