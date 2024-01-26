<?php

namespace App\Http\Integrations\Zendesk;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\CursorPaginator;
use Saloon\PaginationPlugin\Paginator;
use Saloon\Traits\Plugins\AcceptsJson;

class ZendeskConnector extends Connector implements HasPagination
{
    use AcceptsJson;
    public ?int $tries = 3;
    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://ideolearninghelp.zendesk.com/api/v2';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [

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

    protected function defaultDelay(): ?int
    {
        return 1000;
    }



}
