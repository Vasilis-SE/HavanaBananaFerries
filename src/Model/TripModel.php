<?php
    namespace App\Model;
    
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Exception\ClientException;
    use GuzzleHttp\Exception\ServerException;


    class TripModel {

        public function getHavanaFerriesTrips() {
            $response = array();
            $client = new Client();
            
            try { 
                $havanaTripResp = $client->get('https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com/prod/trips/havanas');
                $response = $havanaTripResp->getBody();
            } catch (RequestException | ClientException | ServerException $e) { // Transfering errors
                // $response['statusCode'] = $havanaTripResp->getStatusCode();
                $response['error'] = $e;
            }

            return $response;
        }

        public function getBananaLinesTrips() {
            $response = array();
            $client = new \GuzzleHttp\Client();
            
            try { 
                $havanaTripResp = $client->get('https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com/prod/trips/havana');
                return $havanaTripResp;

      
            } catch (RequestException $e) { // Transfering errors
                echo $e->getRequest();
                if ($e->hasResponse()) {
                    echo $e->getResponse();
                }
            } catch (ClientException $e) { // 400 level errors
                echo $e->getRequest();
                echo $e->getResponse();
            } catch (ServerException $e) { // 500 level errors
                echo $e->getRequest();
                echo $e->getResponse();
            }

            return $response;
        }

    }