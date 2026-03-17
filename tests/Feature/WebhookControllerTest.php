<?php

use IGedeon\WompiLaravel\Events\TransactionApproved;
use IGedeon\WompiLaravel\Events\TransactionDeclined;
use IGedeon\WompiLaravel\Events\WompiWebhookReceived;
use Illuminate\Support\Facades\Event;

function validWebhookPayload(string $status = 'APPROVED'): array
{
    $eventsSecret = config('wompi.keys.events');

    $data = [
        'transaction' => [
            'id' => 'txn-001',
            'status' => $status,
            'amount_in_cents' => 5000000,
            'currency' => 'COP',
            'reference' => 'order-123',
            'payment_method_type' => 'CARD',
        ],
    ];

    $properties = ['transaction.id', 'transaction.status', 'transaction.amount_in_cents'];
    $timestamp = 1530291411;

    $values = '';
    foreach ($properties as $prop) {
        $values .= data_get($data, $prop, '');
    }
    $values .= $timestamp.$eventsSecret;

    return [
        'event' => 'transaction.updated',
        'data' => $data,
        'sent_at' => '2018-07-20T16:45:05.000Z',
        'timestamp' => $timestamp,
        'signature' => [
            'properties' => $properties,
            'checksum' => hash('sha256', $values),
        ],
    ];
}

it('returns 200 for valid webhook and dispatches events', function () {
    Event::fake();

    $this->postJson(route('wompi.webhook'), validWebhookPayload())
        ->assertOk();

    Event::assertDispatched(WompiWebhookReceived::class);
    Event::assertDispatched(TransactionApproved::class);
});

it('dispatches TransactionDeclined for declined status', function () {
    Event::fake();

    $this->postJson(route('wompi.webhook'), validWebhookPayload('DECLINED'))
        ->assertOk();

    Event::assertDispatched(TransactionDeclined::class);
});

it('returns 403 for invalid signature', function () {
    $payload = validWebhookPayload();
    $payload['signature']['checksum'] = 'tampered_checksum';

    $this->postJson(route('wompi.webhook'), $payload)
        ->assertStatus(403);
});
