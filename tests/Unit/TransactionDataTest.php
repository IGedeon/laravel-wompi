<?php

use IGedeon\WompiLaravel\DTOs\TransactionData;
use IGedeon\WompiLaravel\Enums\TransactionStatus;

it('creates from array with all fields', function () {
    $data = [
        'id' => 'txn-001',
        'status' => 'APPROVED',
        'amount_in_cents' => 5000000,
        'currency' => 'COP',
        'reference' => 'order-123',
        'payment_method_type' => 'CARD',
        'payment_link_id' => 'link-abc',
        'redirect_url' => 'https://example.com/thanks',
        'status_message' => 'Transaction approved',
    ];

    $transaction = TransactionData::fromArray($data);

    expect($transaction)
        ->id->toBe('txn-001')
        ->status->toBe(TransactionStatus::Approved)
        ->amountInCents->toBe(5000000)
        ->currency->toBe('COP')
        ->reference->toBe('order-123')
        ->paymentMethodType->toBe('CARD')
        ->paymentLinkId->toBe('link-abc')
        ->redirectUrl->toBe('https://example.com/thanks')
        ->statusMessage->toBe('Transaction approved')
        ->raw->toBe($data);
});

it('maps PENDING status correctly', function () {
    $transaction = TransactionData::fromArray([
        'id' => 'txn-pending',
        'status' => 'PENDING',
        'amount_in_cents' => 1000000,
        'currency' => 'COP',
        'reference' => 'ref-001',
        'payment_method_type' => 'PSE',
    ]);

    expect($transaction->status)->toBe(TransactionStatus::Pending);
});

it('maps DECLINED status correctly', function () {
    $transaction = TransactionData::fromArray([
        'id' => 'txn-declined',
        'status' => 'DECLINED',
        'amount_in_cents' => 2000000,
        'currency' => 'COP',
        'reference' => 'ref-002',
        'payment_method_type' => 'CARD',
    ]);

    expect($transaction->status)->toBe(TransactionStatus::Declined);
});

it('maps VOIDED status correctly', function () {
    $transaction = TransactionData::fromArray([
        'id' => 'txn-voided',
        'status' => 'VOIDED',
        'amount_in_cents' => 3000000,
        'currency' => 'COP',
        'reference' => 'ref-003',
        'payment_method_type' => 'CARD',
    ]);

    expect($transaction->status)->toBe(TransactionStatus::Voided);
});

it('maps ERROR status correctly', function () {
    $transaction = TransactionData::fromArray([
        'id' => 'txn-error',
        'status' => 'ERROR',
        'amount_in_cents' => 4000000,
        'currency' => 'COP',
        'reference' => 'ref-004',
        'payment_method_type' => 'NEQUI',
    ]);

    expect($transaction->status)->toBe(TransactionStatus::Error);
});

it('defaults payment_method_type to UNKNOWN when missing', function () {
    $transaction = TransactionData::fromArray([
        'id' => 'txn-no-method',
        'status' => 'APPROVED',
        'amount_in_cents' => 1000000,
        'currency' => 'COP',
        'reference' => 'ref-005',
    ]);

    expect($transaction->paymentMethodType)->toBe('UNKNOWN');
});

it('defaults nullable fields to null when missing', function () {
    $transaction = TransactionData::fromArray([
        'id' => 'txn-minimal',
        'status' => 'APPROVED',
        'amount_in_cents' => 1000000,
        'currency' => 'COP',
        'reference' => 'ref-006',
        'payment_method_type' => 'CARD',
    ]);

    expect($transaction)
        ->paymentLinkId->toBeNull()
        ->redirectUrl->toBeNull()
        ->statusMessage->toBeNull();
});

it('preserves the raw data array', function () {
    $data = [
        'id' => 'txn-raw',
        'status' => 'APPROVED',
        'amount_in_cents' => 5000000,
        'currency' => 'COP',
        'reference' => 'ref-007',
        'payment_method_type' => 'CARD',
        'extra_field' => 'extra_value',
        'nested' => ['key' => 'value'],
    ];

    $transaction = TransactionData::fromArray($data);

    expect($transaction->raw)->toBe($data);
});

it('throws ValueError for invalid status', function () {
    TransactionData::fromArray([
        'id' => 'txn-invalid',
        'status' => 'INVALID_STATUS',
        'amount_in_cents' => 1000000,
        'currency' => 'COP',
        'reference' => 'ref-bad',
        'payment_method_type' => 'CARD',
    ]);
})->throws(ValueError::class);
