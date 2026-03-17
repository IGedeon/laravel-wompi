<?php

use IGedeon\WompiLaravel\DTOs\PaymentLinkData;

it('converts to array with snake_case keys', function () {
    $data = new PaymentLinkData(
        name: 'Test Link',
        description: 'A test payment link',
        singleUse: true,
        collectShipping: false,
        amountInCents: 5000000,
    );

    $array = $data->toArray();

    expect($array)->toBe([
        'name' => 'Test Link',
        'description' => 'A test payment link',
        'single_use' => true,
        'collect_shipping' => false,
        'amount_in_cents' => 5000000,
        'currency' => 'COP',
    ]);
});

it('omits null optional fields', function () {
    $data = new PaymentLinkData(
        name: 'Test',
        description: 'Desc',
        singleUse: false,
        collectShipping: false,
    );

    $array = $data->toArray();

    expect($array)->not->toHaveKeys(['amount_in_cents', 'expires_at', 'redirect_url', 'image_url', 'sku', 'taxes']);
});

it('includes all optional fields when provided', function () {
    $data = new PaymentLinkData(
        name: 'Full Link',
        description: 'Full description',
        singleUse: true,
        collectShipping: true,
        amountInCents: 10000000,
        currency: 'COP',
        expiresAt: '2025-12-31T23:59:59.000Z',
        redirectUrl: 'https://example.com/thanks',
        imageUrl: 'https://example.com/image.png',
        sku: 'SKU-001',
        taxes: [['type' => 'VAT', 'amount_in_cents' => 950000]],
    );

    $array = $data->toArray();

    expect($array)
        ->toHaveKey('expires_at', '2025-12-31T23:59:59.000Z')
        ->toHaveKey('redirect_url', 'https://example.com/thanks')
        ->toHaveKey('image_url', 'https://example.com/image.png')
        ->toHaveKey('sku', 'SKU-001')
        ->toHaveKey('taxes');
});
