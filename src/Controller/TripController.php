<?php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    use App\Model\HavanaFerries;
    use App\Model\BananaLines;

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

            $response = new Response();
            $data = array();

            // There are trip instances.
            if(!empty($trips)) {
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
            } else {
                $data = array('status'=>false, 'message'=>'There are no trips at the moment...');
            }

            $response->setContent( json_encode($data) );
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        
    }
