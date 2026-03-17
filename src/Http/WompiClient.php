<?php

namespace IGedeon\WompiLaravel\Http;

use IGedeon\WompiLaravel\Enums\Environment;
use IGedeon\WompiLaravel\Exceptions\ApiException;
use IGedeon\WompiLaravel\Exceptions\InvalidConfigurationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class WompiClient
{
    private Environment $environment;

    public function __construct(private readonly array $config)
    {
        $this->environment = Environment::from($config['environment']);
    }

    public function get(string $path, array $query = []): array
    {
        $response = $this->baseRequest()
            ->withToken($this->publicKey())
            ->get($this->url($path), $query);

        if ($response->failed()) {
            throw new ApiException(
                "Wompi API GET {$path} failed: {$response->body()}",
                $response->status(),
                $response->json() ?? [],
            );
        }

        return $response->json();
    }

    public function post(string $path, array $data = []): array
    {
        $response = $this->baseRequest()
            ->withToken($this->privateKey())
            ->post($this->url($path), $data);

        if ($response->failed()) {
            throw new ApiException(
                "Wompi API POST {$path} failed: {$response->body()}",
                $response->status(),
                $response->json() ?? [],
            );
        }

        return $response->json();
    }

    public function publicKey(): string
    {
        return $this->config['keys']['public']
            ?? throw new InvalidConfigurationException('WOMPI_PUBLIC_KEY is not configured.');
    }

    public function privateKey(): string
    {
        return $this->config['keys']['private']
            ?? throw new InvalidConfigurationException('WOMPI_PRIVATE_KEY is not configured.');
    }

    public function baseUrl(): string
    {
        return $this->environment->baseUrl();
    }

    private function url(string $path): string
    {
        return $this->baseUrl().'/'.ltrim($path, '/');
    }

    private function baseRequest(): PendingRequest
    {
        return Http::acceptJson()
            ->contentType('application/json')
            ->retry(3, 100, throw: false);
    }
}
