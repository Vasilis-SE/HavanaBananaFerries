<?php
    namespace App\Model;

    use GuzzleHttp\Client;

    // Call exceptions
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Exception\ClientException;
    use GuzzleHttp\Exception\ServerException;
    use GuzzleHttp\Exception\ConnectException;

    // Parsing response exceptions
    use GuzzleHttp\Exception\ParseException;

    use App\Interfaces\IFerries;
    use App\Model\TripModel;

    class HavanaFerries implements IFerries {
        private $_baseUrl;

        function __construct() {
            $this->_baseUrl = $_ENV['HAVANA_FERRIES_BASE_URL'];
        }

        // API integrations
        public function getTrips($tripID=0) {
            $response = array();
            $client = new Client();
           
            try { 
                // Make the call
                $havanaTripResp = $client->get($this->_baseUrl.'/prod/trips/havana');
                
                // Check if response is ok
                if($havanaTripResp->getStatusCode() != 200) 
                    return false;
                
                $response["status"] = true;

                // Iterate response and convert it to trip class instances
                $respBody = json_decode((string) $havanaTripResp->getBody(), true);
                
                foreach ($respBody['trips'] as $trip) {
                    if($tripID != 0 && $tripID != intval($trip['itinerary'])) continue; 

                    $departureDate = substr($trip['date'], 0, 4).'-'.substr($trip['date'], 5, 2).'-'.substr($trip['date'], 7, 2).' '.$trip['departure'];
                    $arrivalDate = substr($trip['date'], 0, 4).'-'.substr($trip['date'], 5, 2).'-'.substr($trip['date'], 7, 2).' '.$trip['arrival'];

                    $tripInstance = new TripModel();
                    $tripInstance->setItinerary(intval($trip['itinerary']));
                    $tripInstance->setVesselName(htmlspecialchars($trip['vesselName']));
                    $tripInstance->setDepartureDate($departureDate);
                    $tripInstance->setArrivalDate($arrivalDate);
                    $tripInstance->setAdultPrice(intval($respBody['prices']['AD']));
                    $tripInstance->setChildPrice(intval($respBody['prices']['CH']));
                    $tripInstance->setInfantPrice(intval($respBody['prices']['IN']));
                    $tripInstance->setCompanyPrefix('HVF');
                    
                    // Fetch company & port data from data base
                    $tripExtraData = $tripInstance->getTripsDataFromDatabase();
                    if($tripExtraData) {
                        $tripInstance->setCompanyName($tripExtraData['companyName']);
                        $tripInstance->setPortOrigin($tripExtraData['portCodeOrigin']);
                        $tripInstance->setPortDestination($tripExtraData['portCodeDestination']);
                    }

                    $response['data'][] = $tripInstance;
                }
            } catch (ConnectException | RequestException | ClientException | ServerException $e) { // Transfer / Connection errors
                return false;
            } catch (ParseException $e) { // Exception that can occure while parsing response from request.
                return false;
            }

            return $response;
        }

        public function getPrices($params) {}

        // General functions 
        public function convertToCents($val) { return $val; }

        // Getters / Setters
        public function getBaseURL() { return $this->_baseUrl; }
        public function setBaseURL($url) { $this->_baseUrl = $url; }
    }
