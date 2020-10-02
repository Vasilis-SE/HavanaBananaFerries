<?php
    namespace App\Model;

    use GuzzleHttp\Client;

    // Call exceptions
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Exception\ClientException;
    use GuzzleHttp\Exception\ServerException;

    // Parsing response exceptions
    use GuzzleHttp\Exception\ParseException;

    use App\Interfaces\IFerries;
    use App\Model\TripModel;

    class HavanaFerries implements IFerries {
        private $_baseUrl;

        function __construct() {
            $this->_baseUrl = "https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com";
        }

        public function getTrips() {
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
                    $departureDate = substr($trip['date'], 0, 4).'-'.substr($trip['date'], 5, 2).'-'.substr($trip['date'], 7, 2).' '.$trip['departure'];
                    $arrivalDate = substr($trip['date'], 0, 4).'-'.substr($trip['date'], 5, 2).'-'.substr($trip['date'], 7, 2).' '.$trip['arrival'];

                    $tripInstance = new TripModel();
                    $tripInstance->setItinerary(intval($trip['itinerary']));
                    $tripInstance->setVesselName(htmlspecialchars($trip['vesselName']));
                    $tripInstance->setDepartureDate($departureDate);
                    $tripInstance->setArrivalDate($arrivalDate);
                    $tripInstance->setAdultPrice($this->convertCentsToEuros($respBody['prices']['AD']));
                    $tripInstance->setChildPrice($this->convertCentsToEuros($respBody['prices']['CH']));
                    $tripInstance->setInfantPrice($this->convertCentsToEuros($respBody['prices']['IN']));
                    
                    // Fetch company & port data from data base
                    $tripExtraData = $tripInstance->getTripsDataFromDatabase('HavanaFerries');
                    if($tripExtraData) {
                        $tripInstance->setCompanyName($tripExtraData['companyName']);
                        $tripInstance->setCompanyPrefix($tripExtraData['companyPrefix']);
                        $tripInstance->setPortOrigin($tripExtraData['portCodeOrigin']);
                        $tripInstance->setPortDestination($tripExtraData['portCodeDestination']);
                    }

                    $response['data'][] = $tripInstance;
                }
            } catch (RequestException | ClientException | ServerException $e) { // Transfering errors / 400 errors / 500 errors
                return false;
            } catch (ParseException $e) { // Exception that can occure while parsing response from request.
                return false;
            }

            return $response;
        }

        private function convertCentsToEuros($value) {
            return number_format((floatval($value) / 100), 2);
        }

        public function getPrices() {
            $response = array();
            $client = new Client();
            
            try { 
                $havanaTripResp = $client->post($this->_baseUrl.'/prod/prices/havana');
                $response = $havanaTripResp->getBody();
            } catch (RequestException | ClientException | ServerException $e) { // Transfering errors / 400 errors / 500 errors
                return false;
            }

            return $response;
        }

    }
