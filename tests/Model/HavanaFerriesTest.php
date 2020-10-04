<?php
    namespace App\Tests\Model;

    use App\Model\HavanaFerries;
    use PHPUnit\Framework\TestCase;

    class HavanaFerriesTest extends TestCase {
        
        public function testTripsResponseNotEmpty() {
            $havanaFerries = new HavanaFerries();
            $response = $havanaFerries->getTrips();

            $this->assertEquals(true, ($response['status'] && count($response['data']) > 0) );
        }

        public function testAPIURLIsCorrect() {
            $havanaFerries = new HavanaFerries();
            $this->assertEquals($havanaFerries->getBaseURL(), $_ENV['HAVANA_FERRIES_BASE_URL']);
        }

        public function testWrongURLShouldReturnFalse() {
            $havanaFerries = new HavanaFerries();
            $havanaFerries->setBaseURL('https://fat3lw9sr6.execute-api.eu-west-3.googlecloud.com');
            $response = $havanaFerries->getTrips();
            die($response);
            $this->assertEquals(false, $response);
        }

    }