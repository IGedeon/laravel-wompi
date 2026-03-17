<?php

use IGedeon\WompiLaravel\Services\IntegritySignatureService;

it('generates correct integrity hash', function () {
    $service = new IntegritySignatureService([
        'keys' => ['integrity' => 'test_integrity_secret'],
    ]);

    $hash = $service->generate('order-123', 5000000, 'COP');

    $expected = hash('sha256', 'order-1235000000COPtest_integrity_secret');

    expect($hash)->toBe($expected);
});

it('includes expiration time in hash when provided', function () {
    $service = new IntegritySignatureService([
        'keys' => ['integrity' => 'test_integrity_secret'],
    ]);

    $hash = $service->generate('order-123', 5000000, 'COP', '2025-12-31T23:59:59.000Z');

    $expected = hash('sha256', 'order-1235000000COP2025-12-31T23:59:59.000Ztest_integrity_secret');

    expect($hash)->toBe($expected);
});

it('throws exception when integrity secret is missing', function () {
    $service = new IntegritySignatureService([
        'keys' => ['integrity' => null],
    ]);

    $service->generate('order-123', 5000000, 'COP');
})->throws(\IGedeon\WompiLaravel\Exceptions\InvalidConfigurationException::class);
