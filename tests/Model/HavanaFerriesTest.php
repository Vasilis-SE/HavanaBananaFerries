<?php
    namespace App\Tests\Model;

    use App\Model\HavanaFerries;
    use PHPUnit\Framework\TestCase;

    class HavanaFerriesTest extends TestCase {
        
        public function testHavanaTripsNotEmpty() {
            $havanaFerries = new HavanaFerries();
            $response = $havanaFerries->getTrips();

            $this->assertEquals(true, ($response['status'] && count($response['data']) > 0) );
        }

    }