<?php

namespace App\Exports\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModuleExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;
    protected $projectId;
    public function __construct(string $projectId)
    {
        $this->projectId = $projectId;
    }
    public function sheets(): array{
        $sheets = [
            new CegosExport($this->projectId),
            new EniExport($this->projectId),
            new SpeexExport($this->projectId),
            new MoocExport($this->projectId),
        ];
        $sur_mesure = config('tenantconfigfields.sur_mesure');
        if($sur_mesure == true){
            $sheets []= new SmExport($this->projectId);
        }
        return $sheets;
    }

}
