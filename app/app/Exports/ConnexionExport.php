<?php

namespace App\Exports;

use App\Models\Group;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConnexionExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithTitle, ShouldAutoSize, WithStyles
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
        return 'rapport des connexions';
    }

    public function collection()
    {
        $groups = Group::where('status', 1)->get();
        $statsConnexions = [];
        if ($this->datedebut != null && $this->datefin != null) {
            foreach ($groups as $group) {
                $actives = $group->learners()->where('statut', 'active')->whereBetween('last_access_date', [$this->datedebut, $this->datefin])->count();
                $inactives = $group->learners()->where('statut', 'inactive')->whereBetween('creation_date', [$this->datedebut, $this->datefin])->count();
                $total = $actives + $inactives;
                $pourcentage = ($total != 0) ? $actives * 100 / $total : 0;
                $statsConnexions[] = [
                    'filiale' => $group->name,
                    'Nombre de connexions' => $actives,
                    'total' => $total,
                    'pourcentage' => round($pourcentage, 2) . " %"
                ];
            }
        } else {
            foreach ($groups as $group) {
                $actives = $group->learners()->where('statut', 'active')->count();
                $total = $group->learners()->whereIn('statut', ['active', 'inactive'])->count();
                $pourcentage = ($total != 0) ? $actives * 100 / $total : 0;
                $statsConnexions[] = [
                    'filiale' => $group->name,
                    'Nombre de connexions' => $actives,
                    'total' => $total,
                    'pourcentage' => round($pourcentage, 2) . " %"
                ];
            }
        }
        return collect($statsConnexions);
    }

    public function headings(): array
    {

        $data = [
            'Filiale',
            'Nombre de connexions',
            'total',
            'pourcentage'
        ];
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            '1' => ['font' => ['bold' => true]]
        ];
    }
}