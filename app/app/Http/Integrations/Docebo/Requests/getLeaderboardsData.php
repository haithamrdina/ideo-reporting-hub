<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Models\Learner;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class getLeaderboardsData extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;
    protected string $leaderboard_id;

    public function __construct(string $leaderboard_id)
    {
        $this->leaderboard_id = $leaderboard_id;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/manage/v1/gamification/leaderboards';
    }

    protected function defaultQuery(): array
    {
        return [
            'limit' => 15,
            'show_inactive' => true,
            'id_leaderboard' => $this->leaderboard_id,
            'preview' => true,
            'show_anonymous' => true,
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items')[0]['positions'];
        $filteredItems = array_map(function ($item) {
            $learner = Learner::where('docebo_id' , $item['id_user'])->first();
            if($learner){
                return [
                    'group' => $learner->group->name,
                    'username' => $learner->username,
                    'fullname' => $learner->lastname . ' '. $learner->firstname,
                    'points' => $item['points'],
                    'percentage' => $item['percentage']
                ];
            }
        }, $items);
        $filteredItems = array_slice(array_filter($filteredItems), 0, 10);
        return $filteredItems;
    }
}
