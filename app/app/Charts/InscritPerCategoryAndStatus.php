<?php

namespace App\Charts;

use App\Models\Learner;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class InscritPerCategoryAndStatus
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\HorizontalBar
    {
        $categories = Learner::distinct()->pluck('categorie')->filter();

        $counts = [
            'Active' => [],
            'Inactive' => [],
            'Archive' => [],
        ];

        foreach ($categories as $category) {
            $counts['Active'][] = Learner::where('categorie', $category)->where('statut', 'active')->count();
            $counts['Inactive'][] = Learner::where('categorie', $category)->where('statut', 'inactive')->count();
            $counts['Archive'][] = Learner::where('categorie', $category)->where('statut', 'archive')->count();
        }

        return $this->chart->horizontalBarChart()
            ->addData('Active', $counts['Active'])
            ->addData('Inactive', $counts['Inactive'])
            ->addData('Archive', $counts['Archive'])
            ->setXAxis($categories->toArray());
    }
}
