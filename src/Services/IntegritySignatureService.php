<?php

namespace IGedeon\WompiLaravel\Services;

use IGedeon\WompiLaravel\Exceptions\InvalidConfigurationException;

class IntegritySignatureService
{
    public function __construct(private readonly array $config) {}

    public function generate(
        string $reference,
        int $amountInCents,
        string $currency,
        ?string $expirationTime = null,
    ): string {
        $secret = $this->config['keys']['integrity']
            ?? throw new InvalidConfigurationException('WOMPI_INTEGRITY_SECRET is not configured.');

        $payload = $reference.$amountInCents.$currency;

        if ($expirationTime !== null) {
            $payload .= $expirationTime;
        }

        $payload .= $secret;

        return hash('sha256', $payload);
    }
}
