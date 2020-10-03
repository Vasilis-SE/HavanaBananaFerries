<?php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    use App\Model\HavanaFerries;
    use App\Model\BananaLines;
    use App\Model\TripModel;

    class TripController {

        /**
         * Returns the list of all available itineraries from Havana Ferries and Banana Lines
         * @Route("/itineraries", name="itineraries_list")
         */
        public function getItineraries(Request $request) {
            $trips = array();
            $havanaFerries = new HavanaFerries();
            $havanaFerriesTrips = $havanaFerries->getTrips();
            if($havanaFerriesTrips['status']) $trips = array_merge($trips, $havanaFerriesTrips['data']);

            $bananaLines = new BananaLines();
            $bananaLinesTrips = $bananaLines->getTrips();
            if($bananaLinesTrips['status']) $trips = array_merge($trips, $bananaLinesTrips['data']);

            $data = array();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');

            // Check if the array that containts the trip instances is empty
            if(empty($trips)) {
                $response->setStatusCode(Response::HTTP_NO_CONTENT);
                $response->setContent( json_encode(array('status'=>false, 'message'=>'There are no trips at the moment...')) );     
                return $response;
            }
            
            foreach ($trips as $trip) {
                // Build final response for each trip
                $data['itineraries'][] = array(
                    "itineraryId"=>$trip->getItinerary(),
                    "originPortCode"=>$trip->getPortOrigin(),
                    "destinationPortCode"=>$trip->getPortDestination(),
                    "operatorCode"=>$trip->getCompanyPrefix(),
                    "operatorName"=>$trip->getCompanyName(),
                    "vesselName"=>$trip->getVesselName(),
                    "departureDateTime"=>$trip->getDepartureDate(),
                    "arrivalDateTime"=>$trip->getArrivalDate(),
                    "pricePerPassengerType"=>array(
                        array(
                            "passengerType"=>"AD",
                            "passengerPriceInCents"=>$trip->getAdultPrice()                          
                        ),
                        array(
                            "passengerType"=>"CH",
                            "passengerPriceInCents"=>$trip->getChildPrice()
                        ),
                        array(
                            "passengerType"=>"IN",
                            "passengerPriceInCents"=>$trip->getInfantPrice()
                        )
                    )
                );
            }

            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent( json_encode($data) );     
            return $response;
        }

        /**
         * @Route("/prices", name="itinerary_price")
         */
        public function getPrices(Request $request) {
            $allowed = array('itineraryId', 'operatorCode', 'expectedOverallPrice', 'pricePerPassenger', 'passengerId', 'passengerType');
            $reqData = json_decode($request->getContent(), true);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');

            // Check if the request is empty.
            if($reqData == null || empty($reqData)) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setContent( json_encode(array('status'=>false, 'errorCode'=>'EMPTY_REQ', 'errorDescription'=>'There are no data on the request...')) );
                return $response;
            }

            // Check if the request body contains contains all the necessary fields.
            $flag = true;
            $fieldsList = array_keys($reqData);
            if(isset($reqData['pricePerPassenger'])) $fieldsList = array_merge($fieldsList, array_keys($reqData['pricePerPassenger']));
            foreach($allowed as $allowedKey) {
                if(!in_array($allowedKey, $fieldsList)) {
                    $flag = false;
                    break;
                }
            }

            if(!$flag) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setContent( json_encode(array('status'=>false, 'errorCode'=>'MISS_REQ_DATA', 'errorDescription'=>'Missing important request data from post...')) );
                return $response;
            }






        }
        
    }
