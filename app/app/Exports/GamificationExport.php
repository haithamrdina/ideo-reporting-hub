<?php

namespace App\Exports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GamificationExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    protected $badgeIDs;
    public function __construct(Array $badgeIDs)
    {
        $this->badgeIDs = $badgeIDs;
    }
    public function sheets(): array{
        $sheets = [];
        foreach($this->badgeIDs as $id){
            $sheets [] = new BadgeExport($id);
        }
        return $sheets;
    }
}
