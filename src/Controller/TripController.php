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

            // TODO: fix status check here, cause function returns false if error exists
            $havanaFerries = new HavanaFerries();
            $havanaFerriesTrips = $havanaFerries->getTrips();
            if($havanaFerriesTrips['status']) $trips = array_merge($trips, $havanaFerriesTrips['data']);

            // TODO: fix status check here, cause function returns false if error exists
            $bananaLines = new BananaLines();
            $bananaLinesTrips = $bananaLines->getTrips();
            if($bananaLinesTrips['status']) $trips = array_merge($trips, $bananaLinesTrips['data']);

            $data = array();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');

            // Check if the array that containts the trip instances is empty
            if(empty($trips)) {
                $response->setStatusCode(Response::HTTP_NO_CONTENT);
                $response->setContent( json_encode(array('status'=>false, 'errorCode'=>'NO_DATA', 'errorDescription'=>'There are no trips at the moment...')) );     
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
            $allowedPassTypes = array('AD', 'CH', 'IN');

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

            // Check if request passenger types are correct
            $flag = true;
            $passTypesList = array_column($reqData['pricePerPassenger'], 'passengerType');
            foreach($allowedPassTypes as $allowedType) {
                if(!in_array($allowedType, $passTypesList)) {
                    $flag = false;
                    break;
                }
            }

            if(!$flag) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setContent( json_encode(array('status'=>false, 'errorCode'=>'WRONG_PASS_TYPE', 'errorDescription'=>'One or more passengers in the request has invalid type...')) );
                return $response;
            }

            // Fetching request & sanitizing them.
            $tmpTripsObj = new TripModel();
            $tmpTripsObj->setItinerary(intval($reqData['itineraryId']));
            $tmpTripsObj->setCompanyPrefix(htmlspecialchars($reqData['operatorCode']));
            $expectedOverallPrice = intval($reqData['expectedOverallPrice']);
            $pricePerPassenger = $reqData['pricePerPassenger'];

            // Fetch ferries company data from database to check the request search criteria
            $operatorData = $tmpTripsObj->getTripsDataFromDatabase();
            if(!$operatorData) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setContent( json_encode(array('status'=>false, 'errorCode'=>'TRIP_NOT_EXIST', 'errorDescription'=>'The requested trip does not exist...')) );
                return $response;
            }

            switch( $tmpTripsObj->getCompanyPrefix() ) {
                case "HVF": $operatorInst = new HavanaFerries(); break;
                case "BLS": $operatorInst = new BananaLines(); break;   
            }

            // Fetch the specific trip from the operator.
            $companyTripsList = $operatorInst->getTrips( $tmpTripsObj->getItinerary() );
            if(!$companyTripsList) {
                $response->setStatusCode(Response::HTTP_NO_CONTENT);
                $response->setContent( json_encode(array('status'=>false, 'errorCode'=>'NO_DATA', 'errorDescription'=>'Could not fetch trip data from operator...')) );
                return $response;
            }

            // Iterate through pricePerPassenger and count their numbers to check the vacancies later, 
            // make a total cost of all the tickets combined to check later with expectedOverallPrice
            // build the pricePerPassenger portion of the response
            $totalCost = 0;
            $pppRespPortion = array();
            $passengerCounters = array('AD'=>0, 'CH'=>0, 'IN'=>0);
            foreach($pricePerPassenger as $ppp) {
                switch($ppp['passengerType']) {
                    case 'AD': 
                        $passengerPrice = $companyTripsList['data'][0]->getAdultPrice();
                    break;
                    case 'CH': 
                        $passengerPrice = $companyTripsList['data'][0]->getChildPrice();
                    break;
                    case 'IN': 
                        $passengerPrice = $companyTripsList['data'][0]->getInfantPrice();
                    break;
                }

                $totalCost += $passengerPrice;
                $passengerCounters[ $ppp['passengerType'] ]++;
                $pppRespPortion[] = array(
                    'passengerId'=>intval($ppp['passengerId']),
                    'passengerType'=>htmlspecialchars($ppp['passengerType']),
                    'passengerPrice'=>$passengerPrice  
                );
            }

            // Check if the expectedOverallPrice is different from the calculated one.
            if($expectedOverallPrice != $totalCost) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setContent( json_encode(array('status'=>false, 'errorCode'=>'DIF_IN_PRICE', 'errorDescription'=>'Expected total cost is different from the calculated one...')) );
                return $response;
            }

            // Check if the number of passengers exceed the vacancies
            if(
                $passengerCounters['AD'] > $companyTripsList['data'][0]->getAdultVacancies() ||
                $passengerCounters['CH'] > $companyTripsList['data'][0]->getChildVacancies() ||
                $passengerCounters['IN'] > $companyTripsList['data'][0]->getInfantVacancies()
            ) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setContent( json_encode(array('status'=>false, 'errorCode'=>'EXCEED_VACAN', 'errorDescription'=>'The requested passenger number exceeds the vacancies limit...')) );
                return $response;
            }

            // Finally everything is ok and need to send response.
            $respData = array();
            $respData['status'] = true;
            $respData['pricePerPassenger'] = $pppRespPortion;
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent( json_encode( $respData ) );
            return $response;
        }
        
    }
