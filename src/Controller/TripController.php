<?php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    use App\Model\HavanaFerries;

    class TripController {

        /**
         * @Route("/trips/havanaferries", name="havana_ferries_trips")
         */
        public function getHavanaFerriesTrips(Request $request) {
            $havanaFerries = new HavanaFerries();
            $tripsResponse = $havanaFerries->getTrips();

            $response = new Response();
            $response->setContent( $tripsResponse );
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

        /**
         * @Route("/trips/bananalines", name="banana_lines_trips")
         */
        public function getBananaLinesTrips(Request $request) {
            // $trip = new TripModel();
            // $bananaLinesTripsResp = $trip->getBananaLinesTrips();

            // $response = new Response();
            // $response->setContent( $bananaLinesTripsResp );
            // $response->headers->set('Content-Type', 'application/json');
            // $response->setStatusCode(Response::HTTP_OK);
            // return $response;
        }



    }
