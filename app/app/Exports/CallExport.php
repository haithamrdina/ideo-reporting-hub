<?php

namespace App\Exports;

use App\Models\Call;
use Maatwebsite\Excel\Concerns\FromCollection;

class CallExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Call::all();
    }
}
