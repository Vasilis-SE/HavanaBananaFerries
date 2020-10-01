<?php
    namespace App\Model;

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Exception\ClientException;
    use GuzzleHttp\Exception\ServerException;

    use App\Interfaces\IFerries;

    class HavanaFerries implements IFerries {
        private $_baseUrl;

        function __construct() {
            $this->_baseUrl = "https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com";
        }

        public function getTrips() {
            $response = array();
            $client = new Client();
            
            try { 
                $havanaTripResp = $client->get($this->_baseUrl.'/prod/trips/havana');
                $response = $havanaTripResp->getBody();
            } catch (RequestException | ClientException | ServerException $e) { // Transfering errors
                return false;
            }

            return $response;
        }




    }
