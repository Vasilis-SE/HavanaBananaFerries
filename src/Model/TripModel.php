<?php
    namespace App\Model;

    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Exception\ClientException;
    use GuzzleHttp\Exception\ServerException;

    class TripModel {

        public function getHavanaFerriesTrips() {
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