<?php

namespace App\Exports;

use App\Models\Group;
use App\Models\Learner;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InactiveLearnerExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison, WithTitle, ShouldAutoSize, WithStyles, ShouldQueue
{
    use Exportable, Queueable;
    protected $datedebut;
    protected $datefin;
    public function __construct($datedebut = null, $datefin = null)
    {
        $this->datedebut = $datedebut;
        $this->datefin = $datefin;
    }
    public function title(): string
    {
        return 'Liste des apprenants inactifs';
    }

    public function collection()
    {
        if ($this->datedebut != null && $this->datefin != null) {
            $learners = Learner::where('statut', 'inactive')->whereBetween('creation_date', [$this->datedebut, $this->datefin])->get();
        } else {
            $learners = Learner::where('statut', 'inactive')->get();
        }
        return $learners;
    }

    public function headings(): array
    {
        $userfields = config('tenantconfigfields.userfields');
        $data = [
            'Branche',
            'Filiale',
            'Username',
            'Nom',
            'Prénom',
            'Date de création',
        ];

        if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
            $data[] = 'Matricule';
        }

        if (isset($userfields['fonction']) && $userfields['fonction'] === true) {
            $data[] = 'Fonction';
        }

        if (isset($userfields['direction']) && $userfields['direction'] === true) {
            $data[] = 'Direction';
        }

        if (isset($userfields['categorie']) && $userfields['categorie'] === true) {
            $data[] = 'Categorie';
        }

        if (isset($userfields['sexe']) && $userfields['sexe'] === true) {
            $data[] = 'Sexe';
        }
        return $data;
    }

    public function prepareRows($rows)
    {
        $userfields = config('tenantconfigfields.userfields');
        if (isset($userfields['categorie']) && $userfields['categorie'] === true) {
            foreach ($rows as $key => $learner) {
                $rows[$key]['categorie'] = Str::ucfirst($rows[$key]['categorie']);
            }
        }
        return $rows;
    }

    public function map($row): array
    {
        $userfields = config('tenantconfigfields.userfields');

        $data = [
            Project::find($row['project_id'])->name,
            Group::find($row['group_id'])->name,
            $row['username'],
            $row['lastname'],
            $row['firstname'],
            $row['creation_date']
        ];

        if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
            $data[] = $row['matricule'];
        }

        if (isset($userfields['fonction']) && $userfields['fonction'] === true) {
            $data[] = $row['fonction'];
        }

        if (isset($userfields['direction']) && $userfields['direction'] === true) {
            $data[] = $row['direction'];
        }

        if (isset($userfields['categorie']) && $userfields['categorie'] === true) {
            $data[] = $row['categorie'];
        }

        if (isset($userfields['sexe']) && $userfields['sexe'] === true) {
            $data[] = $row['sexe'];
        }
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            '1' => ['font' => ['bold' => true]]
        ];
    }
}
