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

    function getDoceboCmiTime($responseBody){
        $totalTime = 0;
        $totalTimeRegex = '/<td>cmi\.core\.total_time<\/td><td>(.*?)<\/td>/';
        $sessionTimeRegex = '/<td>cmi\.core\.session_time<\/td><td>(.*?)<\/td>/';
        if (preg_match($totalTimeRegex, $responseBody, $totalTimeMatches)) {
            $totalTimeValue = $totalTimeMatches[1];
            if($totalTimeValue ==  '0000:00:00.00'){
                if(preg_match($sessionTimeRegex, $responseBody, $sessionTimeMatches)) {
                    $sessionTimeValue = $sessionTimeMatches[1];
                    $totalTime += $this->convertTimeToSeconds($sessionTimeValue);
                }
            }else{
                $totalTime += $this->convertTimeToSeconds($totalTimeValue);
            }
        }
        return $totalTime;
    }

    function convertTimeToSeconds($time)
    {
        $timeArray = explode(':', $time);
        if (count($timeArray) !== 3) {
            return 0; // Invalid time format, return 0 seconds
        }

        $hours = intval($timeArray[0]);
        $minutes = intval($timeArray[1]);
        $seconds = intval($timeArray[2]);

        return $hours * 3600 + $minutes * 60 + $seconds;
    }

}
