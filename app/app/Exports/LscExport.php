<?php

namespace App\Exports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LscExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    public function sheets(): array{
        $sheets = [
            new TicketExport(),
            new CallExport()
        ];
        return $sheets;
    }
}
