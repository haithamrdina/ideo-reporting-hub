<?php

namespace App\Exports\Group;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LscExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;
    protected $datedebut;
    protected $datefin;
    protected $groupId;
    public function __construct(string $groupId, $datedebut = null , $datefin = null)
    {
        $this->groupId = $groupId;
        $this->datedebut = $datedebut;
        $this->datefin = $datefin;
    }
    public function sheets(): array
    {
        $sheets = [
            new TicketExport($this->groupId , $this->datedebut, $this->datefin),
            new CallExport($this->groupId, $this->datedebut, $this->datefin)
        ];
        return $sheets;
    }
}