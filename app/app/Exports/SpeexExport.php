<?php

namespace App\Exports;

use App\Enums\CourseStatusEnum;
use App\Models\Group;
use App\Models\Langenroll;
use App\Models\Learner;
use App\Models\Module;
use App\Models\Project;
use App\Services\TimeConversionService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SpeexExport implements FromArray, WithMapping, WithHeadings, WithStrictNullComparison, WithTitle, ShouldAutoSize, WithStyles
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
        return 'Inscriptions Langues';
    }

    public function array(): array
    {
        $speexModules = Module::where(['category' => 'SPEEX', 'status' => CourseStatusEnum::ACTIVE])->pluck('docebo_id')->toArray();
        $archive = config('tenantconfigfields.archive');

        if ($this->datedebut != null && $this->datefin != null) {
            $startDate = $this->datedebut;
            $endDate = $this->datefin;
            if ($archive == true) {
                $speexEnrolls = Langenroll::where(function ($query) use ($speexModules, $startDate, $endDate) {
                    $query->whereIn('module_docebo_id', $speexModules)
                        ->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$startDate, $endDate]);
                })
                    ->orWhere(function ($query) use ($speexModules, $startDate, $endDate) {
                        $query->whereIn('module_docebo_id', $speexModules)
                            ->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$startDate, $endDate]);
                    })
                    ->get()->toArray();
                ;
            } else {

                $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                $speexEnrolls = Langenroll::where(function ($query) use ($speexModules, $learnersIds, $startDate, $endDate) {
                    $query->whereIn('module_docebo_id', $speexModules)
                        ->whereIn('learner_docebo_id', $learnersIds)
                        ->whereNotNull('enrollment_completed_at')
                        ->whereBetween('enrollment_completed_at', [$startDate, $endDate]);
                })
                    ->orWhere(function ($query) use ($speexModules, $learnersIds, $startDate, $endDate) {
                        $query->whereIn('module_docebo_id', $speexModules)
                            ->whereIn('learner_docebo_id', $learnersIds)
                            ->whereNull('enrollment_updated_at')
                            ->whereBetween('enrollment_updated_at', [$startDate, $endDate]);
                    })
                    ->get()->toArray();
            }

        } else {
            if ($archive == true) {
                $speexEnrolls = Langenroll::whereIn('module_docebo_id', $speexModules)->get()->toArray();
            } else {
                $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                $speexEnrolls = Langenroll::whereIn('module_docebo_id', $speexModules)->whereIn(
                    'learner_docebo_id',
                    $learnersIds
                )->get()->toArray();
            }
        }

        return $speexEnrolls;
    }

    public function headings(): array
    {
        $userfields = config('tenantconfigfields.userfields');
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $data = [
            'Branche',
            'Filiale',
            'Module',
            'Username',
        ];

        if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
            $data[] = 'Matricule';
        }

        $data[] = 'Date d\'inscription';
        $data[] = 'Statut';
        $data[] = 'Date du dernière modification';
        $data[] = 'Date d\'achèvement';
        $data[] = 'Temps de session';

        if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
            $data[] = 'Temps d\'engagement';
        }

        if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
            $data[] = 'Temps calculé';
        }

        if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
            $data[] = 'Temps pédagogique recommandé';
        }

        return $data;
    }

    public function prepareRows($rows)
    {
        $timeConversionService = new TimeConversionService();
        foreach ($rows as $key => $learner) {
            if ($rows[$key]['status'] == 'waiting') {
                $status = "En attente";
            } elseif ($rows[$key]['status'] == 'enrolled') {
                $status = "Inscrit";
            } elseif ($rows[$key]['status'] == 'in_progress') {
                $status = "En cours";
            } elseif ($rows[$key]['status'] == 'completed') {
                $status = "Terminé";
            }
            $rows[$key]['status'] = $status;
            $rows[$key]['session_time'] = $timeConversionService->convertSecondsToTime($rows[$key]['session_time']);
            $rows[$key]['cmi_time'] = $timeConversionService->convertSecondsToTime($rows[$key]['cmi_time']);
            $rows[$key]['calculated_time'] = $timeConversionService->convertSecondsToTime($rows[$key]['calculated_time']);
            $rows[$key]['recommended_time'] = $timeConversionService->convertSecondsToTime($rows[$key]['recommended_time']);
            $rows[$key]['enrollment_updated_at'] = $rows[$key]['enrollment_updated_at'] != null ? $rows[$key]['enrollment_updated_at'] : '******';
            $rows[$key]['enrollment_completed_at'] = $rows[$key]['enrollment_completed_at'] != null ? $rows[$key]['enrollment_completed_at'] : '******';
        }
        return $rows;
    }

    public function map($row): array
    {
        $userfields = config('tenantconfigfields.userfields');
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $data = [
            Project::find($row['project_id'])->name,
            Group::find($row['group_id'])->name,
            Module::where('docebo_id', $row['module_docebo_id'])->first()->name,
            Learner::where('docebo_id', $row['learner_docebo_id'])->first()->username,
        ];

        if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
            $data[] = Learner::where('docebo_id', $row['learner_docebo_id'])->first()->matricule;
        }

        $data[] = $row['enrollment_created_at'];
        $data[] = $row['status'];
        $data[] = $row['enrollment_updated_at'];
        $data[] = $row['enrollment_completed_at'];

        $data[] = $row['session_time'];
        if (isset($enrollfields['cmi_time']) && $enrollfields['cmi_time'] === true) {
            $data[] = $row['cmi_time'];
        }

        if (isset($enrollfields['calculated_time']) && $enrollfields['calculated_time'] === true) {
            $data[] = $row['calculated_time'];
        }

        if (isset($enrollfields['recommended_time']) && $enrollfields['recommended_time'] === true) {
            $data[] = $row['recommended_time'];
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