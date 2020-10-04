<?php
    namespace App\Tests\Model;

    use App\Model\TripModel;
    use PHPUnit\Framework\TestCase;

    class TripModelTest extends TestCase {
        
        public function testCompanyInfoDoesNotExistWrongCode() {
            $trip = new TripModel();
            $trip->setItinerary(1);
            $trip->setCompanyPrefix("ZHA");
            $data = $trip->getTripsDataFromDatabase();

            $this->assertEquals(false, $data);
        }

        public function testCompanyInfoDoesNotExistWrongItinerary() {
            $trip = new TripModel();
            $trip->setItinerary(15);
            $trip->setCompanyPrefix("HVF");
            $data = $trip->getTripsDataFromDatabase();
            
            $this->assertEquals(false, $data);
        }

        public function testCompanyInfoDoesExist() {
            $trip = new TripModel();
            $trip->setItinerary(1);
            $trip->setCompanyPrefix("HVF");
            $data = $trip->getTripsDataFromDatabase();
            
            $this->assertNotEmpty($data, "Ferrie company data not empty");
        }

        public function testItineraryDatabaseExists() {
            $this->assertFileExists(dirname(__DIR__, 2).'/src/mocks/tripsDB.json');
        }

        public function testItineraryDatabaseIsReadable() {
            $this->assertFileIsReadable(dirname(__DIR__, 2).'/src/mocks/tripsDB.json');
        }


    }