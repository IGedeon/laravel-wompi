<?php

use IGedeon\WompiLaravel\DTOs\PaymentLinkData;
use IGedeon\WompiLaravel\DTOs\PaymentLinkResponse;
use IGedeon\WompiLaravel\Facades\Wompi;
use Illuminate\Support\Facades\Http;

it('creates a payment link via the API', function () {
    Http::fake([
        '*/payment_links' => Http::response([
            'data' => [
                'id' => 'link-abc-123',
                'name' => 'Test Link',
                'description' => 'A test link',
                'single_use' => true,
                'collect_shipping' => false,
                'amount_in_cents' => 5000000,
                'currency' => 'COP',
                'active' => true,
            ],
        ], 200),
    ]);

    $result = Wompi::paymentLinks()->create(new PaymentLinkData(
        name: 'Test Link',
        description: 'A test link',
        singleUse: true,
        collectShipping: false,
        amountInCents: 5000000,
    ));

    expect($result)
        ->toBeInstanceOf(PaymentLinkResponse::class)
        ->id->toBe('link-abc-123')
        ->checkoutUrl()->toBe('https://checkout.wompi.co/l/link-abc-123');
});

it('finds a payment link by id', function () {
    Http::fake([
        '*/payment_links/link-abc-123' => Http::response([
            'data' => [
                'id' => 'link-abc-123',
                'name' => 'Test Link',
                'description' => 'A test link',
                'single_use' => true,
                'collect_shipping' => false,
                'amount_in_cents' => 5000000,
                'currency' => 'COP',
                'active' => true,
            ],
        ], 200),
    ]);

    $result = Wompi::paymentLinks()->find('link-abc-123');

    expect($result)
        ->toBeInstanceOf(PaymentLinkResponse::class)
        ->id->toBe('link-abc-123');
});

it('throws ApiException on failure', function () {
    Http::fake([
        '*/payment_links' => Http::response(['error' => 'Unauthorized'], 401),
    ]);

    Wompi::paymentLinks()->create(new PaymentLinkData(
        name: 'Test',
        description: 'Test',
        singleUse: true,
        collectShipping: false,
    ));
})->throws(\IGedeon\WompiLaravel\Exceptions\ApiException::class);
