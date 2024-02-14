<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Enums\GroupStatusEnum;
use App\Services\UserFieldsService;
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
    protected $status;

    protected Method $method = Method::GET;

    public function __construct(UserFieldsService $userFieldsService, string $branche, Array $userfields,  GroupStatusEnum $status) {
        $this->branche = $branche;
        $this->userfields = $userfields;
        $this->userFieldsService = $userFieldsService;
        $this->status = $status;
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

    public function createDtoFromResponse(Response $response): mixed{
        if($this->status == GroupStatusEnum::ACTIVE){
            $filteredItems = $this->userFieldsService->getLearnersFilteredItems($response->json('data.items'),$this->userfields);
        }elseif($this->status == GroupStatusEnum::ARCHIVE){
            $filteredItems = $this->userFieldsService->getArchivesFilteredItems($response->json('data.items'),$this->userfields);
        }

        return $filteredItems;
    }
}

