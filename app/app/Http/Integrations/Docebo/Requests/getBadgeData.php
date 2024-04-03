<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Models\Learner;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class getBadgeData extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;
    protected string $badge_id;
    public function __construct(string $badge_id)
    {
        $this->badge_id = $badge_id;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/share/v1/gamification/'.$this->badge_id.'/assigned_users';
    }

    protected function defaultQuery(): array
    {
        return [
            'get_cursor' => '1'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');
        $filteredItems = array_map(function ($item) {
            $learner = Learner::where('docebo_id' , $item['user_id'])->first();
            if($learner){
                return [
                    $learner->project->name,
                    $learner->group->name,
                    $learner->username,
                    $learner->lastname .' '. $learner->firstname,
                    $learner->categorie != null ? $learner->categorie : '******',
                    $item['points_count'],
                    (new \DateTime($item['last_achieved_date']))->format('Y-m-d H:i:s')
                ];
            }
        }, $items);
        return array_filter($filteredItems);
    }

}
