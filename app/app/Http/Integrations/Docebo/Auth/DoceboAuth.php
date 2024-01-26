<?php

namespace App\Http\Integrations\Docebo\Auth;

use App\Http\Integrations\Docebo\Requests\DoceboAccess;
use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

class DoceboAuth implements Authenticator
{
    public function __construct()
    {
        //
    }

    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        if ($pendingRequest->getRequest() instanceof DoceboAccess) {
            return;
        }
        $response = $pendingRequest->getConnector()->send(new DoceboAccess);

        $pendingRequest->headers()->add('Authorization', 'Bearer ' . $response->dto());
    }
}
