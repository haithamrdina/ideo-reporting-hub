<?php

namespace App\Exports\Group;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModuleExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;
    protected $groupId;
    public function __construct(string $groupId)
    {
        $this->groupId = $groupId;
    }
    public function sheets(): array{
        $sheets = [
            new CegosExport($this->groupId),
            new EniExport($this->groupId),
            new SpeexExport($this->groupId),
            new MoocExport($this->groupId),
        ];
        return $sheets;
    }

}
