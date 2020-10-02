<?php
    namespace App\Model;
    
    class TripModel {
        private $_itinerary;
        private $_vesselName;
        private $_departure;
        private $_arrival;
        private $_company;
        private $_companyPref;
        private $_portOrigin;
        private $_portDestination


        // Prices per type
        private $_adultPrice;
        private $_childPrice;
        private $_infantPrice;

        // Passenger vacancies per type
        private $_adultVacancies;
        private $_childVacancies;
        private $_infantVacancies;

        function __construct($it=0, $vsn="", $dep="", $arr="", $com="", $cpref="", $por="", $pdes="" 
            $adpr=0, $chpr=0, $infpr=0, $advc=INF, $chvc=INF, $infvc=INF) {

            $this->_itinerary = $it;
            $this->_vesselName = $vsn;
            $this->_departure = $dep;
            $this->_arrival = $arr;

            $this->_company = $com;
            $this->_companyPref = $cpref;
            $this->_portOrigin = $por;
            $this->_portDestination = $pdes;

            $this->_adultPrice = $adpr;
            $this->_childPrice = $chpr;
            $this->_infantPrice = $infpr;
            $this->_adultVacancies = $advc;
            $this->_childVacancies = $chvc;
            $this->_infantVacancies = $infvc;
        }

        // Getters / Setters
        public function getItinerary() { return $this->_itinerary; }
        public function getVesselName() { return $this->_vesselName; }
        public function getDepartureDate() { return $this->_departure; }
        public function getArrivalDate() { return $this->_arrival; }
        public function getAdultPrice() { return $this->_adultPrice; }
        public function getChildPrice() { return $this->_childPrice; }
        public function getInfantPrice() { return $this->_infantPrice; }
        public function getAdultVacancies() { return $this->_adultVacancies; }
        public function getChildVacancies() { return $this->_childVacancies; }
        public function getInfantVacancies() { return $this->_infantVacancies; }
        public function getCompanyName() { return $this->_company; }
        public function getCompanyPrefix() { return $this->_companyPref; }
        public function getPortOrigin() { return $this->_portOrigin; }
        public function getPortDestination() { return $this->_portDestination; }

        public function setItinerary($it) { $this->_itinerary = $it; }
        public function setVesselName($vsn) { $this->_vesselName = $vsn; }
        public function setDepartureDate($dep) { $this->_departure = $dep; }
        public function setArrivalDate($arr) { $this->_arrival = $arr; }
        public function setAdultPrice($adpr) { $this->_adultPrice = $adpr; }
        public function setChildPrice($chpr) { $this->_childPrice = $chpr; }
        public function setInfantPrice($infpr) { $this->_infantPrice = $infpr; }
        public function setAdultVacancies($advc) { $this->_adultVacancies = $advc; }
        public function setChildVacancies($chvc) { $this->_childVacancies = $chvc; }
        public function setInfantVacancies($infvc) { $this->_infantVacancies = $infvc; }
        public function setCompanyName($com) { $this->_company = $com; }
        public function setCompanyPrefix($cpref) { $this->_companyPref = $cpref; }
        public function setPortOrigin($por) { $this->_portOrigin = $por; }
        public function setPortDestination($pdes) { $this->_portDestination = $pdes; }
    }