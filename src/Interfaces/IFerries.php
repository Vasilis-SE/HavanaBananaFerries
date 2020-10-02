<?php
    namespace App\Interfaces;

    interface IFerries {
        public function getTrips();
        public function getPrices($params);
    }