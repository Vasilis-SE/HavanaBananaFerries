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
    
            // $trip = new Trip();
            // $response = $trip->GetHavanaTrips();
            // return json_encode($response);
        }

        /**
         * @Route("/trips/havana", name="havana_trips")
         */
        public function getHavanaFerriesTrips(Request $request) {
            $trip = new TripModel();
            $havanaTripsResp = $trip->getHavanaFerriesTrips();

  
            $response = new Response();
            $response->setContent( $havanaTripsResp->getBody() );
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;

        }

    }
