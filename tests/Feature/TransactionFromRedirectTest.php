<?php

use IGedeon\WompiLaravel\DTOs\TransactionData;
use IGedeon\WompiLaravel\Enums\TransactionStatus;
use IGedeon\WompiLaravel\Facades\Wompi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

function fakeTransactionResponse(string $status = 'APPROVED'): array
{
    return [
        'data' => [
            'id' => 'txn-redirect-001',
            'status' => $status,
            'amount_in_cents' => 5000000,
            'currency' => 'COP',
            'reference' => 'order-123',
            'payment_method_type' => 'CARD',
        ],
    ];
}

it('finds a transaction from a redirect request', function () {
    Http::fake([
        '*/transactions/txn-redirect-001' => Http::response(fakeTransactionResponse(), 200),
    ]);

    $request = Request::create('/thanks', 'GET', ['id' => 'txn-redirect-001']);

    $transaction = Wompi::transactionFromRedirect($request);

    expect($transaction)
        ->toBeInstanceOf(TransactionData::class)
        ->id->toBe('txn-redirect-001')
        ->status->toBe(TransactionStatus::Approved);
});

it('returns null when redirect request has no transaction id', function () {
    $request = Request::create('/thanks', 'GET');

    $transaction = Wompi::transactionFromRedirect($request);

    expect($transaction)->toBeNull();
});

it('also works via transactions()->findFromRedirect()', function () {
    Http::fake([
        '*/transactions/txn-redirect-001' => Http::response(fakeTransactionResponse('DECLINED'), 200),
    ]);

    $request = Request::create('/thanks', 'GET', ['id' => 'txn-redirect-001']);

    $transaction = Wompi::transactions()->findFromRedirect($request);

    expect($transaction)
        ->toBeInstanceOf(TransactionData::class)
        ->status->toBe(TransactionStatus::Declined);
});
