<?php

use IGedeon\WompiLaravel\Services\WebhookSignatureService;

function buildWebhookPayload(string $eventsSecret, array $overrides = []): array
{
    $data = $overrides['data'] ?? [
        'transaction' => [
            'id' => 'txn-001',
            'status' => 'APPROVED',
            'amount_in_cents' => 5000000,
        ],
    ];

    $properties = $overrides['properties'] ?? [
        'transaction.id',
        'transaction.status',
        'transaction.amount_in_cents',
    ];

    $timestamp = $overrides['timestamp'] ?? 1530291411;

    $values = '';
    foreach ($properties as $property) {
        $values .= data_get($data, $property, '');
    }
    $values .= $timestamp;
    $values .= $eventsSecret;

    $checksum = $overrides['checksum'] ?? hash('sha256', $values);

    return [
        'event' => 'transaction.updated',
        'data' => $data,
        'sent_at' => '2018-07-20T16:45:05.000Z',
        'timestamp' => $timestamp,
        'signature' => [
            'properties' => $properties,
            'checksum' => $checksum,
        ],
    ];
}

it('verifies a valid webhook signature', function () {
    $secret = 'test_events_secret';
    $service = new WebhookSignatureService(['keys' => ['events' => $secret]]);

    $payload = buildWebhookPayload($secret);

    expect($service->verify($payload))->toBeTrue();
});

it('rejects an invalid webhook signature', function () {
    $service = new WebhookSignatureService(['keys' => ['events' => 'test_events_secret']]);

    $payload = buildWebhookPayload('test_events_secret', [
        'checksum' => 'invalid_checksum_value',
    ]);

    expect($service->verify($payload))->toBeFalse();
});

it('rejects when signature properties are empty', function () {
    $service = new WebhookSignatureService(['keys' => ['events' => 'test_events_secret']]);

    $payload = buildWebhookPayload('test_events_secret', ['properties' => []]);

    expect($service->verify($payload))->toBeFalse();
});

it('rejects tampered transaction data', function () {
    $secret = 'test_events_secret';
    $service = new WebhookSignatureService(['keys' => ['events' => $secret]]);

    $payload = buildWebhookPayload($secret);
    $payload['data']['transaction']['amount_in_cents'] = 1000;

    expect($service->verify($payload))->toBeFalse();
});
