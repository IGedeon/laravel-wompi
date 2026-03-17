<?php

use IGedeon\WompiLaravel\DTOs\TransactionData;
use IGedeon\WompiLaravel\Enums\TransactionStatus;
use IGedeon\WompiLaravel\Exceptions\ApiException;
use IGedeon\WompiLaravel\Facades\Wompi;
use Illuminate\Support\Facades\Http;

it('finds a transaction by id', function () {
    Http::fake([
        '*/transactions/txn-abc-123' => Http::response([
            'data' => [
                'id' => 'txn-abc-123',
                'status' => 'APPROVED',
                'amount_in_cents' => 5000000,
                'currency' => 'COP',
                'reference' => 'order-123',
                'payment_method_type' => 'CARD',
                'payment_link_id' => 'link-xyz',
                'redirect_url' => 'https://example.com/thanks',
                'status_message' => null,
            ],
        ], 200),
    ]);

    $result = Wompi::transactions()->find('txn-abc-123');

    expect($result)
        ->toBeInstanceOf(TransactionData::class)
        ->id->toBe('txn-abc-123')
        ->status->toBe(TransactionStatus::Approved)
        ->amountInCents->toBe(5000000)
        ->currency->toBe('COP')
        ->reference->toBe('order-123')
        ->paymentMethodType->toBe('CARD')
        ->paymentLinkId->toBe('link-xyz')
        ->redirectUrl->toBe('https://example.com/thanks');
});

it('finds a pending transaction', function () {
    Http::fake([
        '*/transactions/txn-pending-001' => Http::response([
            'data' => [
                'id' => 'txn-pending-001',
                'status' => 'PENDING',
                'amount_in_cents' => 3000000,
                'currency' => 'COP',
                'reference' => 'order-456',
                'payment_method_type' => 'BANCOLOMBIA_TRANSFER',
            ],
        ], 200),
    ]);

    $result = Wompi::transactions()->find('txn-pending-001');

    expect($result)
        ->toBeInstanceOf(TransactionData::class)
        ->status->toBe(TransactionStatus::Pending)
        ->paymentLinkId->toBeNull()
        ->redirectUrl->toBeNull()
        ->statusMessage->toBeNull();
});

it('throws ApiException when transaction is not found', function () {
    Http::fake([
        '*/transactions/txn-not-found' => Http::response(['error' => 'Not Found'], 404),
    ]);

    Wompi::transactions()->find('txn-not-found');
})->throws(ApiException::class);

it('sends GET request with bearer token to sandbox URL', function () {
    Http::fake([
        'sandbox.wompi.co/v1/transactions/*' => Http::response([
            'data' => [
                'id' => 'txn-001',
                'status' => 'APPROVED',
                'amount_in_cents' => 1000000,
                'currency' => 'COP',
                'reference' => 'ref-001',
                'payment_method_type' => 'CARD',
            ],
        ], 200),
    ]);

    Wompi::transactions()->find('txn-001');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'sandbox.wompi.co/v1/transactions/txn-001')
            && $request->hasHeader('Authorization', 'Bearer pub_test_fake_key');
    });
});
