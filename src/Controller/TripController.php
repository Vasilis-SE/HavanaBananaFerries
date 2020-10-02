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
         * Its a test route
         * @Route("/trips/havanaferries", name="havana_ferries_trips")
         */
        public function getHavanaFerriesTrips(Request $request) {
            $havanaFerries = new HavanaFerries();
            $tripsResponse = $havanaFerries->getTrips();

            // die($tripsResponse['data'][0]->getVesselName());

            $response = new Response();
            $response->setContent( json_encode($tripsResponse) );
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

        /**
         * Its a test route
         * @Route("/trips/bananalines", name="banana_lines_trips")
         */
        public function getBananaLinesTrips(Request $request) {
            $bananaLines = new BananaLines();
            $tripsResponse = $bananaLines->getTrips();

            // die($tripsResponse['data'][0]->getVesselName());

            $response = new Response();
            $response->setContent( json_encode($tripsResponse) );
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

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

            // There are trip instances.
            if(!empty($trips)) {
                $response = new Response();
                $itinerariesResponse = array();
                
                foreach ($trips as $trip) {
                    // echo $trip->getVesselName().'  |   ';

                    // TODO: Fill all the below empty fields.
                    $itinerariesResponse['itineraries'][] = array(
                        "itineraryId"=>$trip->getItinerary(),
                        "originPortCode"=>"The unique identifier for a port",
                        "destinationPortCode"=>"The unique identifier for a port",
                        "operatorCode"=>"The unique identifier for an operator",
                        "operatorName"=>"The name of the operator",
                        "vesselName"=>$trip->getVesselName(),
                        "departureDateTime"=>$trip->getDepartureDate(),
                        "arrivalDateTime"=>$trip->getArrivalDate()
                    );

                    
                }

                $response->setContent( json_encode($itinerariesResponse) );
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(Response::HTTP_OK);
                return $response;
            } else {
                // TODO: throw error
            }

        }



    }
