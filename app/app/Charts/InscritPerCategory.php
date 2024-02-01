<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\DB;

class InscritPerCategory
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\DonutChart
    {
        $learnerCounts = DB::table('learners')
                            ->select('categorie', DB::raw('count(*) as total'))
                            ->groupBy('categorie')
                            ->get();

        $totalLearners = DB::table('learners')->count();

        $data = [];
        $labels = [];

        foreach ($learnerCounts as $count) {
            $percentage = round(($count->total / $totalLearners) * 100 , 2);
            $data [] = $count->total;
            $labels [] = $count->categorie !== null ?  ucfirst($count->categorie) .' '.  $count->total .  ' - (' . $percentage .'%)' : ' Indéterminé'.' '.  $count->total .  ' - (' . $percentage .'%)' ;
        }

        return $this->chart->donutChart()
            ->addData($data)
            ->setLabels($labels)
            ->setFontFamily('-apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif');
    }
}
