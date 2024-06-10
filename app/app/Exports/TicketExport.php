<?php

namespace App\Exports;

use App\Models\Group;
use App\Models\Learner;
use App\Models\Project;
use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison, WithTitle, ShouldAutoSize, WithStyles
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
        return 'Tickets';
    }

    public function collection()
    {
        $archive = config('tenantconfigfields.archive');
        if ($this->datedebut != null && $this->datefin != null) {
            if ($archive == true) {
                $tickets = Ticket::whereBetween('ticket_created_at', [$this->datedebut, $this->datefin])->get();
            } else {
                $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                $tickets = Ticket::whereIn('learner_docebo_id', $learnersIds)->whereBetween('ticket_created_at', [$this->datedebut, $this->datefin])->get();
            }
        } else {
            if ($archive == true) {
                $tickets = Ticket::get();
            } else {
                $learnersIds = Learner::where('statut', '!=', 'archive')->pluck('docebo_id')->toArray();
                $tickets = Ticket::whereIn('learner_docebo_id', $learnersIds)->get();
            }
        }
        return $tickets;
    }

    public function headings(): array
    {
        return [
            'Branche',
            'Filiale',
            'Username',
            'Sujet',
            'Statut',
            'Date de création',
            'Date du dernière modification'
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
            $row['ticket_created_at'],
            $row['ticket_updated_at']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            '1' => ['font' => ['bold' => true]]
        ];
    }
}
