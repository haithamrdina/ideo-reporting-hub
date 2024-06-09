<?php

namespace App\Exports;

use App\Models\Group;
use App\Models\Learner;
use App\Models\Project;
use App\Services\TimeConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActiveLearnerExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison, WithTitle, ShouldAutoSize, WithStyles, ShouldQueue
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
        return 'Liste des apprenants actifs';
    }

    public function collection()
    {
        if ($this->datedebut != null && $this->datefin != null) {
            $learners = Learner::where('statut', 'active')->whereBetween('last_access_date', [$this->datedebut, $this->datefin])->get();
        } else {
            $learners = Learner::where('statut', 'active')->get();
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
            'Date du dernier accès',
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
        $data[] = 'Heures sessions';
        $data[] = 'Heures d\'engagement';
        $data[] = 'Heures calculé';
        $data[] = 'Heures pédagogique recommandé';
        $data[] = 'Total des tickets';
        $data[] = 'Total des appels';
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
            $row['creation_date'],
            $row['last_access_date'],
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
        $timeConversionService = new TimeConversionService();
        $totalCegosTimes = DB::table('enrollmodules')
            ->selectRaw('SUM(session_time) as total_session_time')
            ->selectRaw('SUM(cmi_time) as total_cmi_time')
            ->selectRaw('SUM(calculated_time) as total_calculated_time')
            ->selectRaw('SUM(recommended_time) as total_recommended_time')
            ->where('learner_docebo_id', '=', $row['docebo_id'])
            ->first();
        $totalMoocTimes = DB::table('enrollmoocs')
            ->selectRaw('SUM(session_time) as total_session_time')
            ->selectRaw('SUM(cmi_time) as total_cmi_time')
            ->selectRaw('SUM(calculated_time) as total_calculated_time')
            ->selectRaw('SUM(recommended_time) as total_recommended_time')
            ->where('learner_docebo_id', '=', $row['docebo_id'])
            ->first();
        $totalSpeexTimes = DB::table('langenrolls')
            ->selectRaw('SUM(session_time) as total_session_time')
            ->selectRaw('SUM(cmi_time) as total_cmi_time')
            ->selectRaw('SUM(calculated_time) as total_calculated_time')
            ->selectRaw('SUM(recommended_time) as total_recommended_time')
            ->where('learner_docebo_id', '=', $row['docebo_id'])
            ->first();
        $totalTickets = DB::table('tickets')->where('learner_docebo_id', '=', $row['docebo_id'])->count();
        $totalCalls = DB::table('calls')->where('learner_docebo_id', '=', $row['docebo_id'])->count();

        $data[] = $timeConversionService->convertSecondsToTime($totalCegosTimes->total_session_time + $totalMoocTimes->total_session_time + $totalSpeexTimes->total_session_time);
        $data[] = $timeConversionService->convertSecondsToTime($totalCegosTimes->total_cmi_time + $totalMoocTimes->total_cmi_time + $totalSpeexTimes->total_cmi_time);
        $data[] = $timeConversionService->convertSecondsToTime($totalCegosTimes->total_calculated_time + $totalMoocTimes->total_calculated_time + $totalSpeexTimes->total_calculated_time);
        $data[] = $timeConversionService->convertSecondsToTime($totalCegosTimes->total_recommended_time + $totalMoocTimes->total_recommended_time + $totalSpeexTimes->total_recommended_time);
        $data[] = $totalTickets;
        $data[] = $totalCalls;
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            '1' => ['font' => ['bold' => true]]
        ];
    }
}