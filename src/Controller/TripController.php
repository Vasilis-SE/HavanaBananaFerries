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


    }
