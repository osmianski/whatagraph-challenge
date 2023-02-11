<?php

namespace App\Weather;

use Illuminate\Support\Facades\Http;

class Weather
{
    protected string $baseUrl = 'http://api.openweathermap.org';
    protected string $apiKey;

    public function __construct(string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? env('WEATHER_API_KEY');
    }

    protected function get(string $path, array $query = []): \stdClass|array {
        $query = array_merge($query, ['appid' => $this->apiKey]);

        $response = Http::acceptJson()->get($this->baseUrl . $path, $query);

        // Throw an exception if a client or server error occurred.
        $response->throw();

        // Otherwise, return the decoded response JSON
        return $response->object();
    }

    public function getGeoCoords(string $address): Coords
    {
        $data = $this->get('/geo/1.0/direct', [
            'q' => $address,
            'limit' => 1,
        ]);

        return Coords::fromApi($data[0]);
    }

    public function getForecast(float $latitude, float $longitude): Forecast
    {
        $data = $this->get('/data/3.0/onecall', [
            'lat' => $latitude,
            'long' => $longitude,
            'exclude' => 'hourly,minutely',
        ]);

        return Forecast::fromApi($data);
    }
}
