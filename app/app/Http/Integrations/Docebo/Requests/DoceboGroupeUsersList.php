<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Services\UserFieldsService;
use Illuminate\Database\Eloquent\Model;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboGroupeUsersList extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $branche;
    protected $userfields;
    protected $userFieldsService;

    protected Method $method = Method::GET;

    public function __construct(UserFieldsService $userFieldsService, string $branche, Array $userfields) {
        $this->branche = $branche;
        $this->userfields = $userfields;
        $this->userFieldsService = $userFieldsService;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/manage/v1/user';
    }

    protected function defaultQuery(): array
    {
        return [
            'branch_id' => $this->branche,
            'get_cursor' => '1'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $filteredItems = $this->userFieldsService->getLearnersFilteredItems($response->json('data.items'),$this->userfields);
        return $filteredItems;
    }
}

