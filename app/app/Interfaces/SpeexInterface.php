<?php


namespace App\Interfaces;

interface SpeexInterface {

    public function getEnrollmentsFields($fields): array;
    public function getEnrollmentsList($items, $fields) : array;
    public function getSessionTime($item, $speexdata): string;
    public function getCmiTime($item, $speexdata): string;
    public function getCalculatedTime($item, $speexdata): string;
    public function getRecommendedTime($item, $speexdata) : string;
    public function batchInsert($items,$fields);

}
