<?php
    namespace App\Tests\Model;

    use App\Model\HavanaFerries;
    use PHPUnit\Framework\TestCase;

    class HavanaFerriesTest extends TestCase {
        
        public function testTripsResponseNotEmpty() {
            $havanaFerries = new HavanaFerries();
            $response = $havanaFerries->getTrips();

            $this->assertTrue(($response['status'] && count($response['data']) > 0));
        }

        public function testAPIURLIsCorrect() {
            $havanaFerries = new HavanaFerries();
            $this->assertEquals($havanaFerries->getBaseURL(), $_ENV['HAVANA_FERRIES_BASE_URL']);
        }

        public function testWrongURLShouldReturnFalse() {
            $havanaFerries = new HavanaFerries();
            $havanaFerries->setBaseURL('https://fat3lw9sr6.execute-api.eu-west-3.googlecloud.com');
            $response = $havanaFerries->getTrips();
            $this->assertFalse($response);
        }

        public function testFetchSpecificTrip() {
            $havanaFerries = new HavanaFerries();
            $response = $havanaFerries->getTrips(1);
            $this->assertEquals(1, count($response['data']));
        }

        public function testTripInstanceIsCreatedCorrectly() {
            $havanaFerries = new HavanaFerries();
            $response = $havanaFerries->getTrips(1);
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
            $havanaFerries = new HavanaFerries();

            $flag = true;
            if(!method_exists($havanaFerries, 'getTrips')) $flag = false;
            if(!method_exists($havanaFerries, 'getPrices')) $flag = false;
            if(!method_exists($havanaFerries, 'convertToCents')) $flag = false;
            if(!method_exists($havanaFerries, 'getBaseURL')) $flag = false;
            if(!method_exists($havanaFerries, 'setBaseURL')) $flag = false;
            
            $this->assertTrue($flag);
        }

        public function testConversionToCents() {
            $havanaFerries = new HavanaFerries();
            $this->assertEquals(500, $havanaFerries->convertToCents(500));
        }

    }