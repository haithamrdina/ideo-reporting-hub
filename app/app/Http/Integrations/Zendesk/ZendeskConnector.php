<?php

namespace App\Http\Integrations\Zendesk;

use Illuminate\Support\Facades\Cache;
use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\CursorPaginator;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\Traits\Plugins\AcceptsJson;

use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

class ZendeskConnector extends Connector implements HasPagination
{
    use AcceptsJson;
    use HasRateLimits;

    public ?int $tries = 3;
    public function __construct()
    {
        $this->detectTooManyAttempts = false;
    }
    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://ideolearninghelp.zendesk.com/api/v2';
    }

    protected function resolveLimits(): array
    {
        return [
            Limit::allow(100000)->everyMinute(),
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

    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator('midrissi@ideolearning.com/token', 'u3VzAMoFa2JajiFPEoPz9SVaVQFVcHIXpX5sK9OC');
    }


    public function paginate(Request $request): CursorPaginator
    {
        return new class($this,$request) extends CursorPaginator
        {
            protected bool $detectInfiniteLoop = false;
            protected function getNextCursor(Response $response): string
            {
                return $response->json('meta.after_cursor');
            }

            protected function isLastPage(Response $response): bool
            {
                // return is_null($response->json('meta.after_cursor'));
                return !($response->json('meta.has_more'));
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('results');
            }

            protected function applyPagination(Request $request): Request
            {
                if ($this->currentResponse instanceof Response) {
                    $request->query()->add('page[after]', $this->getNextCursor($this->currentResponse));
                }
                return $request;
            }
        };
    }

}
