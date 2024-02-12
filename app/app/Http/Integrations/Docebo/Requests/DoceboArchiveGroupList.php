<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Enums\GroupStatusEnum;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboArchiveGroupList extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    protected $company_code;

    public function __construct(string $company_code) {
        $this->company_code = $company_code;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/manage/v1/orgchart';
    }

    protected function defaultQuery(): array
    {
        return [
            'search_text' => $this->company_code . ' Archive',
            'search_type' => '3',
            'flattened' => 'true',
            'get_cursor' => '1'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');

        $filteredItems = array_map(function ($item) {
            return [
                'docebo_id' => $item['id'],
                'code' => $item['code'],
                'name' => $item['title'],
                'status' => GroupStatusEnum::ARCHIVE
            ];
        }, $items);

        return $filteredItems;
    }
}
