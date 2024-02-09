<?php

namespace App\Exports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModuleExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    public function sheets(): array{
        $sheets = [
            new CegosExport(),
            new EniExport(),
            new SpeexExport(),
            new MoocExport(),
        ];
        return $sheets;
    }

}
