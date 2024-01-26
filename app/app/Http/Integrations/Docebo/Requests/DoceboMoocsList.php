<?php

namespace App\Http\Integrations\Docebo\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DoceboMoocsList extends Request
{
    protected $company_code;
    protected Method $method = Method::GET;

    public function __construct( string $company_code) {
        $this->company_code = $company_code;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {

        return '/learn/v1/catalog';
    }
}
