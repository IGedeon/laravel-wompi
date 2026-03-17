<?php

use IGedeon\WompiLaravel\DTOs\PaymentLinkResponse;

it('creates from array with all fields', function () {
    $data = [
        'id' => 'link-001',
        'name' => 'Premium Plan',
        'description' => 'Monthly subscription',
        'single_use' => true,
        'collect_shipping' => false,
        'amount_in_cents' => 5000000,
        'currency' => 'COP',
        'expires_at' => '2025-12-31T23:59:59.000Z',
        'redirect_url' => 'https://example.com/thanks',
        'image_url' => 'https://example.com/image.png',
        'sku' => 'SKU-PREMIUM',
        'active' => true,
    ];

    $response = PaymentLinkResponse::fromArray($data);

    expect($response)
        ->id->toBe('link-001')
        ->name->toBe('Premium Plan')
        ->description->toBe('Monthly subscription')
        ->singleUse->toBeTrue()
        ->collectShipping->toBeFalse()
        ->amountInCents->toBe(5000000)
        ->currency->toBe('COP')
        ->expiresAt->toBe('2025-12-31T23:59:59.000Z')
        ->redirectUrl->toBe('https://example.com/thanks')
        ->imageUrl->toBe('https://example.com/image.png')
        ->sku->toBe('SKU-PREMIUM')
        ->active->toBeTrue();
});

it('defaults nullable fields to null when missing', function () {
    $data = [
        'id' => 'link-minimal',
        'name' => 'Basic Link',
        'description' => 'A basic link',
        'single_use' => false,
        'collect_shipping' => false,
        'currency' => 'COP',
    ];

    $response = PaymentLinkResponse::fromArray($data);

    expect($response)
        ->amountInCents->toBeNull()
        ->expiresAt->toBeNull()
        ->redirectUrl->toBeNull()
        ->imageUrl->toBeNull()
        ->sku->toBeNull();
});

it('defaults active to true when missing', function () {
    $data = [
        'id' => 'link-active',
        'name' => 'Active Link',
        'description' => 'Should be active by default',
        'single_use' => true,
        'collect_shipping' => false,
        'currency' => 'COP',
    ];

    $response = PaymentLinkResponse::fromArray($data);

    expect($response->active)->toBeTrue();
});

it('handles active as false', function () {
    $data = [
        'id' => 'link-inactive',
        'name' => 'Inactive Link',
        'description' => 'An inactive link',
        'single_use' => true,
        'collect_shipping' => false,
        'currency' => 'COP',
        'active' => false,
    ];

    $response = PaymentLinkResponse::fromArray($data);

    expect($response->active)->toBeFalse();
});

it('generates the correct checkout URL', function () {
    $data = [
        'id' => 'link-checkout-abc',
        'name' => 'Checkout Link',
        'description' => 'Test checkout URL',
        'single_use' => true,
        'collect_shipping' => false,
        'currency' => 'COP',
    ];

    $response = PaymentLinkResponse::fromArray($data);

    expect($response->checkoutUrl())->toBe('https://checkout.wompi.co/l/link-checkout-abc');
});

it('generates unique checkout URLs per link id', function () {
    $link1 = PaymentLinkResponse::fromArray([
        'id' => 'link-aaa',
        'name' => 'Link A',
        'description' => 'A',
        'single_use' => true,
        'collect_shipping' => false,
        'currency' => 'COP',
    ]);

    $link2 = PaymentLinkResponse::fromArray([
        'id' => 'link-bbb',
        'name' => 'Link B',
        'description' => 'B',
        'single_use' => true,
        'collect_shipping' => false,
        'currency' => 'COP',
    ]);

    expect($link1->checkoutUrl())->not->toBe($link2->checkoutUrl())
        ->and($link1->checkoutUrl())->toBe('https://checkout.wompi.co/l/link-aaa')
        ->and($link2->checkoutUrl())->toBe('https://checkout.wompi.co/l/link-bbb');
});

it('handles collect_shipping as true', function () {
    $data = [
        'id' => 'link-shipping',
        'name' => 'Shipping Link',
        'description' => 'Collects shipping',
        'single_use' => false,
        'collect_shipping' => true,
        'currency' => 'COP',
    ];

    $response = PaymentLinkResponse::fromArray($data);

    expect($response)
        ->collectShipping->toBeTrue()
        ->singleUse->toBeFalse();
});
