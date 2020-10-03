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

    class BananaLines implements IFerries {
        private $_baseUrl;

        function __construct() {
            $this->_baseUrl = $_ENV['BANANA_LINES_BASE_URL'];;
        }

        public function convertToCents($val) {
            return $val * 100;
        }

        public function getTrips($tripID = 0) {
            $response = array();
            $client = new Client();
            
            try { 
                // Make the call
                $bananaTripResp = $client->get($this->_baseUrl.'/prod/trips/banana');
                
                // Check if response is ok
                if($bananaTripResp->getStatusCode() != 200) 
                    return false;
                
                $response["status"] = true;

                // Iterate response and convert it to trip class instances
                $respBody = json_decode((string) $bananaTripResp->getBody(), true);
                foreach ($respBody as $trip) {
                    if($tripID != 0 && $tripID != intval($trip['itinerary'])) continue; 

                    $departureDate = htmlspecialchars($trip['date']).' '.htmlspecialchars($trip['departsAt']);

                    // The arrival date is the departure plus whatever the duration is.
                    $arrivalDate = date('Y-m-d H:i', strtotime('+'.$trip['tripDuration'].' minutes', strtotime($departureDate)));
               
                    $tripInstance = new TripModel();
                    $tripInstance->setItinerary(intval($trip['tripId']));
                    $tripInstance->setVesselName(htmlspecialchars($trip['vessel']));
                    $tripInstance->setDepartureDate($departureDate);
                    $tripInstance->setArrivalDate($arrivalDate);
                    $tripInstance->setAdultVacancies(intval($trip['adults']));
                    $tripInstance->setChildVacancies(intval($trip['children']));
                    $tripInstance->setCompanyPrefix('BLS');
            
                    // Fetch company & port data from data base
                    $tripExtraData = $tripInstance->getTripsDataFromDatabase();
                    if($tripExtraData) {
                        $tripInstance->setCompanyName($tripExtraData['companyName']);
                        $tripInstance->setPortOrigin($tripExtraData['portCodeOrigin']);
                        $tripInstance->setPortDestination($tripExtraData['portCodeDestination']);
                    }
                    
                    // Fetch prices per tripid and per passenger type
                    $adultsPriceResp = $this->getPrices(array('tripId'=>$trip['tripId'], 'adults'=>1));
                    if(isset($adultsPriceResp['totalPrice'])) {
                        $adultPriceEUR = floatval($adultsPriceResp['totalPrice']);
                        $tripInstance->setAdultPrice( $this->convertToCents($adultPriceEUR) );
                        
                        // We use both adult param and children beacause the banana API does not allow for children to travel alone.
                        $childrenPriceResp = $this->getPrices(array('tripId'=>$trip['tripId'], 'adults'=>1, 'children'=>1));
                        if(isset($childrenPriceResp['totalPrice'])) {
                           
                            // By subtracting the price of the adults we can get the price of each individual child.
                            $childPriceEUR = floatval($childrenPriceResp['totalPrice']) - $adultPriceEUR;
                            $tripInstance->setChildPrice( $this->convertToCents($childPriceEUR) );
                        }
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

        public function getPrices($params) {
            $response = array();
            $client = new Client();
            
            try { 
                $pricingResponse = $client->get($this->_baseUrl.'/prod/prices/banana?'.http_build_query($params));
                $response = json_decode((string) $pricingResponse->getBody(), true);
            } catch (RequestException | ClientException | ServerException $e) { // Transfering errors / 400 errors / 500 errors
                return false;
            }

            return $response;
        }

    }
