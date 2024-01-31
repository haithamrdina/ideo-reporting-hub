<?php

namespace App\Http\Integrations\Speex\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class SpeexUserArticleResult extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected $speexId;
    protected $articleId;
    protected Method $method = Method::GET;
    public function __construct(string $speexId, string $articleId){
        $this->speexId = $speexId;
        $this->articleId = $articleId;
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/user.results';
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

    protected function defaultBody(): array
    {
        return [
            'userId' => $this->speexId,
            'articleId' => $this->articleId,
            'projectId' => '502605',
            'apikey' => 'c170979b0d8786ef3d4b5510b9814913'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json();
        if (array_key_exists("LIVE_ARTICLE_RESULT_LEVEL", $items)) {
            $time =  isset($items['LIVE_ARTICLE_ELAPSED']) ? $items['LIVE_ARTICLE_ELAPSED'] :  0;
            $niveau = isset($items['LIVE_ARTICLE_RESULT_LEVEL']) ? $items['LIVE_ARTICLE_RESULT_LEVEL'] :  null;
        } else {
            $time = isset($items['LIVE_ARTICLE_ELAPSED']) ? $items['LIVE_ARTICLE_ELAPSED'] :  0 ;
            $niveau = NULL;
        }

        return [
            'time' => $time,
            'niveau' => $niveau
        ];

    }
}
