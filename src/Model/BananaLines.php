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
            $this->_baseUrl = "https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com";
        }

        public function getTrips() {
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

                die( json_encode($respBody) );

            } catch (RequestException | ClientException | ServerException $e) { // Transfering errors / 400 errors / 500 errors
                return false;
            } catch (ParseException $e) { // Exception that can occure while parsing response from request.
                return false;
            }

            return $response;
        }


        // For prices
        // https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com/prod/prices/banana?tripId=1&adults=2&children=1
        // - tripId: The id of the trip you want to get prices for
        // - adults: Number of adult passengers traveling
        // - children: Number of children or infants traveling

        public function getPrices() {
            $response = array();
            $client = new Client();
            
            try { 
                $havanaTripResp = $client->post($this->_baseUrl.'/prod/prices/banana');
                $response = $havanaTripResp->getBody();
            } catch (RequestException | ClientException | ServerException $e) { // Transfering errors / 400 errors / 500 errors
                return false;
            }

            return $response;
        }


    }
