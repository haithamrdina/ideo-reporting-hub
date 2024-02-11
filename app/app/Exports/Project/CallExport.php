<?php

namespace App\Exports\Project;

use App\Models\Call;
use App\Models\Group;
use App\Models\Learner;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CallExport implements FromArray, WithMapping, WithHeadings, WithStrictNullComparison ,WithTitle, ShouldAutoSize, WithStyles
{

    public function title(): string{
        return 'Appels téléphoniques';
    }

    protected $projectId;
    public function __construct(string $projectId)
    {
        $this->projectId = $projectId;
    }

    public function array(): array
    {
        $calls = Call::where('project_id', $this->projectId)->get()->toArray();
        return $calls;
    }

    public function headings(): array{
        return  [
            'Branche',
            'Filiale',
            'Username',
            'Sujet',
            'Statut',
            'Date d\'appel'
        ];
    }

    public function map($row): array{
        return  [
            Project::find($row['project_id'])->name,
            Group::find($row['group_id'])->name,
            Learner::where('docebo_id', $row['learner_docebo_id'])->first()->username,
            $row['subject'],
            $row['status'],
            $row['date_call']
        ];
    }

    public function styles(Worksheet $sheet){
        return [
            '1' => ['font' => ['bold' => true]]
        ];
    }
}
