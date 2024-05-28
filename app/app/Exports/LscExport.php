<?php

namespace App\Exports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LscExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;
    protected $datedebut;
    protected $datefin;
    public function __construct($datedebut = null, $datefin = null)
    {
        $this->datedebut = $datedebut;
        $this->datefin = $datefin;
    }
    public function sheets(): array
    {
        $sheets = [
            new TicketExport($this->datedebut, $this->datefin),
            new CallExport($this->datedebut, $this->datefin),
        ];
        return $sheets;
    }
}