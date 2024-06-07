<?php

namespace App\Http\Integrations\Docebo\Requests;

use App\Enums\CourseStatusEnum;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class DoceboCourseList extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected $company_code;
    protected Method $method = Method::GET;

    public function __construct(string $company_code)
    {
        $this->company_code = $company_code;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/course/v1/courses';
    }

    protected function defaultQuery(): array
    {
        return [
            'search_text' => $this->company_code,
            'type[]' => 'elearning',
            'get_cursor' => '1'
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('data.items');
        $softCodes = $this->replaceCompanyCode(["CODE-MH", "CODE-ME", "CODE-MS", "CODE-MFH", "CODE-ME", "CODE-MHD"]);
        $eniCodes = $this->replaceCompanyCode(["CODE-ENI"]);
        $speexCodes = $this->replaceCompanyCode(["SPX-CODE-ENG", "SPX-CODE-ESP", "SPX-CODE-ITL", "SPX-CODE-ALD", "SPX-CODE-SMART", "SPX-CODE-CORE"]);
        $smCodes = $this->replaceCompanyCode(["CODE-SM"]);
        $altissiaCodes = $this->replaceCompanyCode(["CODE-ALTISSIA"]);

        $filteredItems = array_map(function ($item) use ($softCodes, $eniCodes, $speexCodes, $smCodes, $altissiaCodes) {
            $category = null;
            $article = null;
            $niveau = null;
            $language = null;

            if ($this->startsWithAny($item['code'], $softCodes)) {
                $category = 'CEGOS';
                $article = null;
                $niveau = null;
            } elseif ($this->startsWithAny($item['code'], $eniCodes)) {
                $category = 'ENI';
                $article = null;
                $niveau = null;
            } elseif ($this->containsAny($item['code'], $speexCodes)) {
                $category = 'SPEEX';
                $articleSpeex = $this->getArticleDetailsSpeex($item['code']);
                $article = $articleSpeex['articleId'];
                $niveau = $articleSpeex['niveau'];
                $language = $articleSpeex['language'];
            } elseif ($this->startsWithAny($item['code'], $smCodes)) {
                $category = 'SM';
                $article = null;
                $niveau = null;
            } elseif ($this->containsAny($item['code'], $altissiaCodes)) {
                $category = 'ALTISSIA';
                $article = null;
                $niveau = null;
            }

            return [
                'docebo_id' => $item['id'],
                'code' => $item['code'],
                'name' => $item['title'],
                'language' => ($category !== "SPEEX" ? $item['language_label'] : $language),
                'recommended_time' => $item['average_completion_time'],
                'category' => $category,
                'niveau' => $niveau,
                'article_id' => $article,
                'status' => (($category == "SPEEX" || $category == null) && $article == null && $niveau == null) ? 0 : 1
            ];
        }, $items);

        return $filteredItems;
    }

    private function containsAny($str, array $search): bool
    {
        foreach ($search as $s) {
            if (strpos($str, $s) !== false) {
                return true;
            }
        }
        return false;
    }

    private function startsWithAny(string $str, array $search): bool
    {
        foreach ($search as $prefix) {
            if (strpos($str, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }

    private function getArticleDetailsSpeex($str)
    {
        $articleId = null;
        $niveau = null;
        $language = null;

        if (strpos($str, 'ENG-CORE') !== false) {
            $articleId = '388661';
            $niveau = 'BASIC';
            $language = 'English';
        } elseif (strpos($str, 'ENG-SMART') !== false) {
            $articleId = '388662';
            $niveau = 'ACTIVE/SMART';
            $language = 'English';
        } elseif (strpos($str, 'ESP-CORE') !== false) {
            $articleId = '389145';
            $niveau = 'BASIC';
            $language = 'Espagnol';
        } elseif (strpos($str, 'ESP-SMART') !== false) {
            $articleId = '389149';
            $niveau = 'ACTIVE/SMART';
            $language = 'Espagnol';
        } elseif (strpos($str, 'ITL-CORE') !== false) {
            $articleId = '389144';
            $niveau = 'BASIC';
            $language = 'Italiano';
        } elseif (strpos($str, 'ALD-CORE') !== false) {
            $articleId = '389142';
            $niveau = 'BASIC';
            $language = 'Deutsch';
        } elseif (strpos($str, 'ALD-SMART') !== false) {
            $articleId = '389146';
            $niveau = 'ACTIVE/SMART';
            $language = 'Deutsch';
        }

        return [
            'articleId' => $articleId,
            'niveau' => $niveau,
            'language' => $language
        ];
    }

    private function replaceCompanyCode(array $codes): array
    {
        return array_map(function ($code) {
            return str_replace('CODE', $this->company_code, $code);
        }, $codes);
    }


}