<?php

namespace App\Whatagraph;

use App\Exceptions\NotImplemented;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Whatagraph
{
    protected string $baseUrl = 'https://api.whatagraph.com';
    protected string $bearerToken;

    public function __construct(string $bearerToken = null)
    {
        $this->bearerToken = $bearerToken ?? env('WHATAGRAPH_API_KEY');
    }

    protected function get(string $path): \stdClass {
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
            ])
            ->get($this->baseUrl . $path);

        // Throw an exception if a client or server error occurred.
        $response->throw();

        // Otherwise, return the decoded response JSON
        return $response->object();
    }

    protected function post(string $path, mixed $body): void {
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
            ])
            ->post($this->baseUrl . $path, $body);

        // Throw an exception if a client or server error occurred.
        $response->throw();
    }

    protected function put(string $path, mixed $body): void {
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
            ])
            ->put($this->baseUrl . $path, $body);

        // Throw an exception if a client or server error occurred.
        $response->throw();
    }

    protected function delete(string $path): void {
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
            ])
            ->delete($this->baseUrl . $path);

        // Throw an exception if a client or server error occurred.
        $response->throw();
    }

    public function getMetrics(): Collection
    {
        return collect($this->get('/v1/integration-metrics/')->data)
            ->keyBy('external_id')
            ->map(fn (\stdClass $object) => Metric::fromApi($object));
    }

    public function createMetric(Metric $metric): void
    {
        $this->post('/v1/integration-metrics/', $metric);
    }

    public function updateMetric(int $id, Metric $metric): void
    {
        $this->put("/v1/integration-metrics/{$id}", $metric);
    }

    public function deleteMetric(int $id): void
    {
        $this->delete("/v1/integration-metrics/{$id}");
    }

    public function getDimensions(): Collection
    {
        return collect($this->get('/v1/integration-dimensions/')->data)
            ->keyBy('external_id')
            ->map(fn (\stdClass $object) => Dimension::fromApi($object));
    }

    public function createDimension(Dimension $dimension): void
    {
        $this->post('/v1/integration-dimensions/', $dimension);
    }

    public function updateDimension(int $id, Dimension $dimension): void
    {
        $this->put("/v1/integration-dimensions/{$id}", $dimension);
    }

    public function deleteDimension(int $id): void
    {
        $this->delete("/v1/integration-dimensions/{$id}");
    }

    public function getDataPoints(): Collection
    {
        return collect($this->get('/v1/integration-source-data/')->data)
            ->map(fn (\stdClass $object) => DataPoint::fromApi($object));
    }

    public function createDataPoint(DataPoint $dataPoint): void
    {
        $this->post('/v1/integration-source-data/', $dataPoint);
    }

    public function updateDataPoint(string $id, DataPoint $dataPoint): void
    {
        $this->put("/v1/integration-source-data/{$id}", $dataPoint);
    }

    public function deleteDataPoint(string $id): void
    {
        $this->delete("/v1/integration-source-data/{$id}");
    }
}
