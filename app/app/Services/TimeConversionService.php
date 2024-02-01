<?php

namespace App\Services;


class TimeConversionService
{
    public function convertSecondsToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds - ($hours * 3600)) / 60);
        $remainingSeconds = $seconds - ($hours * 3600) - ($minutes * 60);

        return sprintf('%02dh %02dmin %02ds', $hours, $minutes, $remainingSeconds);
    }

    public function convertSecondsToHours($seconds)
    {
        $hours = floor($seconds / 3600);
        return $hours;
    }
}
