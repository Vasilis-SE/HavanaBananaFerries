<?php
    namespace App\Model;
    
    class TripModel {
        private $_itinerary;
        private $_vesselName;
        private $_departure;
        private $_arrival;

        function __construct($it=0, $vsn="", $dep="", $arr="") {
            $this->_itinerary = $it;
            $this->_vesselName = $vsn;
            $this->_departure = $dep;
            $this->_arrival = $arr;
        }


        // Getters / Setters
        public function getItinerary() { return $this->_itinerary; }
        public function getVesselName() { return $this->_vesselName; }
        public function getDepartureDate() { return $this->_departure; }
        public function getArrivalDate() { return $this->_arrival; }

        public function setItinerary($it) { $this->_itinerary = $it; }
        public function setVesselName($vsn) { $this->_vesselName = $vsn; }
        public function setDepartureDate($dep) { $this->_departure = $dep; }
        public function setArrivalDate($arr) { $this->_arrival = $arr; }
    }