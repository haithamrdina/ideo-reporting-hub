<?php

namespace App\Exports;

use App\Http\Integrations\Docebo\DoceboConnector;
use App\Http\Integrations\Docebo\Requests\getBadgeData;
use App\Models\Badge;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BadgeExport  implements FromArray, WithHeadings, WithStrictNullComparison ,WithTitle, ShouldAutoSize, WithStyles
{
    protected string $badge_id;
    public function __construct(string $badge_id)
    {
        $this->badge_id = $badge_id;
    }

    public function array(): array
    {
        $badge = Badge::find($this->badge_id);
        $doceboConnector = new DoceboConnector;
        $badgeDataPaginator =$doceboConnector->paginate(new getBadgeData($badge->docebo_id));
        $badgeData = [];
        foreach($badgeDataPaginator as $md){
            $data = $md->dto();
            $badgeData = array_merge($badgeData, $data);
        }
        return  $badgeData;
    }

    public function title(): string{
        $badge = Badge::find($this->badge_id);
        return $badge->code;
    }

    public function headings(): array{
        return  [
            'Branche',
            'Filiale',
            'Username',
            'Nom complet',
            'Categorie',
            'Points',
            'Date du dernier achievement'
        ];
    }

    public function styles(Worksheet $sheet){
        return [
            '1' => ['font' => ['bold' => true]]
        ];
    }
}
