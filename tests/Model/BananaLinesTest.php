<?php
    namespace App\Tests\Model;

    use App\Model\BananaLines;
    use PHPUnit\Framework\TestCase;

    class BananaLinesTest extends TestCase {
        
        public function testTripsResponseNotEmpty() {
            $bananaLines = new BananaLines();
            $response = $bananaLines->getTrips();

            $this->assertTrue(($response['status'] && count($response['data']) > 0));
        }

        public function testAPIURLIsCorrect() {
            $bananaLines = new BananaLines();
            $this->assertEquals($bananaLines->getBaseURL(), $_ENV['BANANA_LINES_BASE_URL']);
        }

        public function testWrongURLShouldReturnFalse() {
            $bananaLines = new BananaLines();
            $bananaLines->setBaseURL('https://fat3lw9sr6.execute-api.eu-west-3.googlecloud.com');
            $response = $bananaLines->getTrips();
            $this->assertFalse($response);
        }

        public function testFetchSpecificTrip() {
            $bananaLines = new BananaLines();
            $response = $bananaLines->getTrips(1);
            $this->assertEquals(1, count($response['data']));
        }

        public function testTripInstanceIsCreatedCorrectly() {
            $bananaLines = new BananaLines();
            $response = $bananaLines->getTrips(1);
            $trip = $response['data'][0];

            $flag = true;
            if(
                $trip->getItinerary() == 0 ||
                $trip->getVesselName() == '' ||
                $trip->getDepartureDate() == '' ||
                $trip->getArrivalDate() == '' ||
                $trip->getCompanyPrefix() == '' ||
                $trip->getCompanyName() == '' ||
                $trip->getPortOrigin() == '' ||
                $trip->getPortDestination() == ''
            ) {
                $flag = false;
            }

            $this->assertTrue($flag);
        }

        public function testInterfaceMethodsExist() {
            $bananaLines = new BananaLines();

            $flag = true;
            if(!method_exists($bananaLines, 'getTrips')) $flag = false;
            if(!method_exists($bananaLines, 'getPrices')) $flag = false;
            if(!method_exists($bananaLines, 'convertToCents')) $flag = false;
            if(!method_exists($bananaLines, 'getBaseURL')) $flag = false;
            if(!method_exists($bananaLines, 'setBaseURL')) $flag = false;
            
            $this->assertTrue($flag);
        }

        public function testConversionToCents() {
            $bananaLines = new BananaLines();
            $this->assertEquals(600, $bananaLines->convertToCents(6));
        }

    }