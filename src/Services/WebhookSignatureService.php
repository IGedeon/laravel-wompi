<?php

namespace IGedeon\WompiLaravel\Services;

use IGedeon\WompiLaravel\Exceptions\InvalidConfigurationException;

class WebhookSignatureService
{
    public function __construct(private readonly array $config) {}

    public function verify(array $payload): bool
    {
        $secret = $this->config['keys']['events']
            ?? throw new InvalidConfigurationException('WOMPI_EVENTS_SECRET is not configured.');

        $properties = $payload['signature']['properties'] ?? [];
        $checksum = $payload['signature']['checksum'] ?? '';
        $timestamp = $payload['timestamp'] ?? '';

        if (empty($properties) || empty($checksum)) {
            return false;
        }

        $values = '';
        foreach ($properties as $property) {
            $values .= data_get($payload['data'], $property, '');
        }

        $values .= $timestamp;
        $values .= $secret;

        $computedHash = hash('sha256', $values);

        return hash_equals($computedHash, $checksum);
    }
}
