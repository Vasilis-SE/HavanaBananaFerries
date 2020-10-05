# HavanaBananaFerries

This a <b>REST, MVC, OOP</b> implementation of Havana Ferries API & Banana Lines API using the Symfony framework.

## Explanation

There are two main classes that fetch data from both APIs `BananaLines.php` and `HavanaFerries.php`. Those two classes implement the `IFerries.php` interface which provide
a basic blueprint of the APIs implementation.

There is also a mock database (JSON file) which provide some data for each provider and itinerary such as the `provider's full name`, `provider's code`, 
the `prefix of the origin port` and the `prefix of the destination port`.

After fetching those data each and one of those classes creates an instance of the <b> Trip model class </b> (`TripModel.php`) for every itinerary of every provider, thus 
being more flexible and upgradable in the future.

Finally they return an array of trip instances that they are used by the `itineraries` & `prices` routes in the main routes controller (<b> TripController.php </b>)

Most of the crucial data such as the URL endpoint of the providers are located in .env files and are loaded automaticaly by the symfony framework and are accesible by
using the $_ENV global php variable.

There also some <b><u> basic </u></b> unit tests using the PHPUnit library that certify the code's integrity.

## How to use

You need to first clone the repository and run the symfony framework by using the `sumfony server:start` command.

Iam using postman to call my routes feel free to use whatever other method.

## GET Itineraries 

URL: http://127.0.0.1:8000/itineraries

<b> Response </b>

<pre><code>
{
    "itineraries": [
        {
            "itineraryId": 1,
            "originPortCode": "RHD",
            "destinationPortCode": "THS",
            "operatorCode": "HVF",
            "operatorName": "Havana Ferries",
            "vesselName": "Johny",
            "departureDateTime": "2020-06-22 11:30",
            "arrivalDateTime": "2020-06-22 12:00",
            "pricePerPassengerType": [
                {
                    "passengerType": "AD",
                    "passengerPriceInCents": 1000
                },
                {
                    "passengerType": "CH",
                    "passengerPriceInCents": 500
                },
                {
                    "passengerType": "IN",
                    "passengerPriceInCents": 0
                }
            ]
        },
        {
            "itineraryId": 2,
            "originPortCode": "RHD",
            "destinationPortCode": "NZY",
            "operatorCode": "HVF",
            "operatorName": "Havana Ferries",
            "vesselName": "Marky",
            "departureDateTime": "2020-06-22 15:30",
            "arrivalDateTime": "2020-06-22 16:00",
            "pricePerPassengerType": [
                {
                    "passengerType": "AD",
                    "passengerPriceInCents": 1000
                },
                {
                    "passengerType": "CH",
                    "passengerPriceInCents": 500
                },
                {
                    "passengerType": "IN",
                    "passengerPriceInCents": 0
                }
            ]
        },
        {
            "itineraryId": 1,
            "originPortCode": "GFS",
            "destinationPortCode": "TRQ",
            "operatorCode": "BLS",
            "operatorName": "Banana Lines",
            "vesselName": "Joey",
            "departureDateTime": "2020-06-22 11:00",
            "arrivalDateTime": "2020-06-22 11:40",
            "pricePerPassengerType": [
                {
                    "passengerType": "AD",
                    "passengerPriceInCents": 600
                },
                {
                    "passengerType": "CH",
                    "passengerPriceInCents": 500
                },
                {
                    "passengerType": "IN",
                    "passengerPriceInCents": 0
                }
            ]
        },
        {
            "itineraryId": 2,
            "originPortCode": "GFS",
            "destinationPortCode": "AZQ",
            "operatorCode": "BLS",
            "operatorName": "Banana Lines",
            "vesselName": "DeeDee",
            "departureDateTime": "2020-06-22 15:00",
            "arrivalDateTime": "2020-06-22 15:40",
            "pricePerPassengerType": [
                {
                    "passengerType": "AD",
                    "passengerPriceInCents": 600
                },
                {
                    "passengerType": "CH",
                    "passengerPriceInCents": 500
                },
                {
                    "passengerType": "IN",
                    "passengerPriceInCents": 0
                }
            ]
        }
    ]
}
</code></pre>

## POST Prices 

URL: http://127.0.0.1:8000/prices

<b> Request </b>
<pre><code>
{
    "itineraryId": "1",
    "operatorCode": "HVF",
    "expectedOverallPrice": "1500",
    "pricePerPassenger": [
        {
            "passengerId": "1",
            "passengerType": "AD"
        },
        {
            "passengerId": "3",
            "passengerType": "CH"
        }
    ]
}
</code></pre>

<b> Response </b>
<pre><code>
{
    "status": true,
    "pricePerPassenger": [
        {
            "passengerId": 1,
            "passengerType": "AD",
            "passengerPrice": 1000
        },
        {
            "passengerId": 3,
            "passengerType": "CH",
            "passengerPrice": 500
        }
    ]
}
</code></pre>
