<?php

use App\Enums\Daytime;
use App\Jobs\PushLocation;
use App\Weather\Coords;
use App\Weather\Current;
use App\Weather\DaytimeForecast;
use App\Weather\Forecast;
use App\Weather\Info;
use App\Weather\Weather;
use App\Whatagraph\DataPoint;
use App\Whatagraph\Whatagraph;
use Illuminate\Support\Carbon;

it('pushes weather data to Whatagraph', function () {
    // GIVEN this weather
    $weather = mock(Weather::class)->expect(
        getGeoCoords: fn($location) => Coords::new($location, 50.0, 40.0),
        getInfo: fn($latitude, $longitude) => Info::new('Europe/Vilnius',
            Current::new(
                Carbon::createFromFormat('Y-m-d H:i', '2023-02-12 02:00'),
                13.0,
                15.0,
                1066,
                35,
            ),
            [
                Forecast::new(
                    Carbon::createFromFormat('Y-m-d H:i', '2023-02-12 00:00'),
                    1070,
                    38,
                    [
                        Daytime::Day->value => new DaytimeForecast(14.0, 15.0),
                        Daytime::Night->value => new DaytimeForecast(10.0, 8.0),
                    ],
                ),
                Forecast::new(
                    Carbon::createFromFormat('Y-m-d H:i', '2023-02-13 00:00'),
                    1070,
                    38,
                    [
                        Daytime::Day->value => new DaytimeForecast(14.0, 15.0),
                        Daytime::Night->value => new DaytimeForecast(10.0, 8.0),
                    ],
                ),
            ]),
    );

    // WHEN you run the job for the first time
    // THEN it sends a batch of new data points
    $whatagraph = mock(Whatagraph::class)
        ->expects('createDataPoints')
        ->withArgs(function ($dataPoints) {
            /* @var DataPoint[] $dataPoints */

            // expect it to skip today forecast, otherwise there
            // would be 3 data points
            expect(count($dataPoints))->toBe(2)

                // expect it to use dates as keys
                ->and(array_keys($dataPoints))->toMatchArray(['2023-02-12', '2023-02-13'])

                // expect the first data point to contain real-time data
                ->and($dataPoints['2023-02-12']->data)->toHaveKey('pressure')

                // expect the other data point to contain forecast
                ->and($dataPoints['2023-02-13']->data)->toHaveKey('pressure_forecast');

            return true;
        })
        ->andReturn([
            '2023-02-12' => 'id1',
            '2023-02-13' => 'id2',
        ])->getMock();

    $job = new PushLocation('Vilnius', $weather, $whatagraph);
    $job->handle();

    // WHEN you run the job for the second time
    // THEN it sends individual data point updates
    $whatagraph = mock(Whatagraph::class)
        ->expects('updateDataPoint')
        ->withArgs(function ($id, DataPoint $dataPoint) {
            expect($id)->toBe('id1')
                ->and($dataPoint->data)->toHaveKey('pressure');

            return true;
        })->getMock()
        ->expects('updateDataPoint')
        ->withArgs(function ($id, DataPoint $dataPoint) {
            expect($id)->toBe('id2')
                ->and($dataPoint->data)->toHaveKey('pressure_forecast');

            return true;
        })->getMock();

    $job = new PushLocation('Vilnius', $weather, $whatagraph);
    $job->handle();
});
