<?php
    namespace App\Interfaces;

    interface IFerries {
        public function getTrips();
        public function getPrices($params);
        public function convertToCents($val);
        public function convertToUTC($format, $timestamp);
        public function getBaseURL();
        public function setBaseURL($url);
    }