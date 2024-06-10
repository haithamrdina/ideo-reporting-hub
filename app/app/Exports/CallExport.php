<?php

namespace App\Exports;

use App\Models\Call;
use App\Models\Group;
use App\Models\Learner;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CallExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison, WithTitle, ShouldAutoSize, WithStyles
{

    protected $datedebut;
    protected $datefin;
    public function __construct($datedebut = null, $datefin = null)
    {
        $this->datedebut = $datedebut;
        $this->datefin = $datefin;
    }

    public function title(): string
    {
        return 'Appels téléphoniques';
    }

    public function collection()
    {
        $archive = config('tenantconfigfields.archive');
        if ($this->datedebut != null && $this->datefin != null) {
            if ($archive == true) {
                $calls = Call::whereBetween('date_call', [$this->datedebut, $this->datefin])->get();
            } else {
                $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                $calls = Call::whereIn('learner_docebo_id', $learnersIds)->whereBetween('date_call', [$this->datedebut, $this->datefin])->get();
            }
        } else {
            if ($archive == true) {
                $calls = Call::get();
            } else {
                $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                $calls = Call::whereIn('learner_docebo_id', $learnersIds)->get();
            }
        }

        return $calls;
    }

    public function headings(): array
    {
        return [
            'Branche',
            'Filiale',
            'Username',
            'Sujet',
            'Statut',
            'Date d\'appel'
        ];
    }

    public function map($row): array
    {
        return [
            Project::find($row['project_id'])->name,
            Group::find($row['group_id'])->name,
            Learner::where('docebo_id', $row['learner_docebo_id'])->first()->username,
            $row['subject'],
            $row['status'],
            $row['date_call']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            '1' => ['font' => ['bold' => true]]
        ];
    }
}