<?php


namespace App\Interfaces;

interface EnrollmentInterface {

    public function getEnrollmentsFields($fields): array;
    public function getEnrollmentsList($items, $fields) : array;
    public function getSessionTime($item): string;
    public function getCmiTime($item): string;
    public function getCalculatedTime($item): string;
    public function getRecommendedTime($item) : string;
    public function batchInsert($items,$fields);

}
