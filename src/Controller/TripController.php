<?php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    use App\Model\TripModel;

    class TripController {

        /**
         * @Route("/example", name="example")
         */
        public function example(Request $request) {
            $data = array(
                "STATUS"=>true,
                "DATA"=>"lala"
            );

            $response = new Response();
            $response->setContent( json_encode($data) );
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            
            return $response;
        }

        /**
         * @Route("/trips/havanaferries", name="havana_ferries_trips")
         */
        public function getHavanaFerriesTrips(Request $request) {
            $trip = new TripModel();
            $havanaTripsResp = $trip->getHavanaFerriesTrips();
            // die($havanaTripsResp);
            $response = new Response();
            $response->setContent( $havanaTripsResp );
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

        /**
         * @Route("/trips/bananalines", name="banana_lines_trips")
         */
        public function getBananaLinesTrips(Request $request) {
            $trip = new TripModel();
            $bananaLinesTripsResp = $trip->getBananaLinesTrips();

            $response = new Response();
            $response->setContent( $bananaLinesTripsResp );
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }



    }
