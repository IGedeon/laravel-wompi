<?php

use IGedeon\WompiLaravel\DTOs\MerchantData;
use IGedeon\WompiLaravel\Exceptions\ApiException;
use IGedeon\WompiLaravel\Facades\Wompi;
use Illuminate\Support\Facades\Http;

it('gets merchant info', function () {
    Http::fake([
        '*/merchants/pub_test_fake_key' => Http::response([
            'data' => [
                'id' => 12345,
                'name' => 'Test Merchant',
                'email' => 'merchant@example.com',
                'legal_name' => 'Test Merchant SAS',
                'presigned_acceptance' => [
                    'acceptance_token' => 'acceptance-token-abc',
                ],
                'presigned_personal_data_auth' => [
                    'acceptance_token' => 'personal-auth-token-xyz',
                ],
            ],
        ], 200),
    ]);

    $result = Wompi::merchants()->get();

    expect($result)
        ->toBeInstanceOf(MerchantData::class)
        ->id->toBe(12345)
        ->name->toBe('Test Merchant')
        ->email->toBe('merchant@example.com')
        ->legalName->toBe('Test Merchant SAS')
        ->acceptanceToken->toBe('acceptance-token-abc')
        ->acceptancePersonalAuth->toBe('personal-auth-token-xyz');
});

it('handles merchant without acceptance tokens', function () {
    Http::fake([
        '*/merchants/pub_test_fake_key' => Http::response([
            'data' => [
                'id' => 99999,
                'name' => 'Simple Merchant',
                'email' => 'simple@example.com',
                'legal_name' => 'Simple Merchant SAS',
            ],
        ], 200),
    ]);

    $result = Wompi::merchants()->get();

    expect($result)
        ->toBeInstanceOf(MerchantData::class)
        ->acceptanceToken->toBeNull()
        ->acceptancePersonalAuth->toBeNull();
});

it('preserves raw data array', function () {
    $rawData = [
        'id' => 12345,
        'name' => 'Test Merchant',
        'email' => 'merchant@example.com',
        'legal_name' => 'Test Merchant SAS',
        'extra_field' => 'extra_value',
    ];

    Http::fake([
        '*/merchants/pub_test_fake_key' => Http::response([
            'data' => $rawData,
        ], 200),
    ]);

    $result = Wompi::merchants()->get();

    expect($result->raw)->toBe($rawData);
});

it('throws ApiException on API failure', function () {
    Http::fake([
        '*/merchants/*' => Http::response(['error' => 'Unauthorized'], 401),
    ]);

    Wompi::merchants()->get();
})->throws(ApiException::class);

it('uses the configured public key in the URL', function () {
    Http::fake([
        '*/merchants/*' => Http::response([
            'data' => [
                'id' => 1,
                'name' => 'M',
                'email' => 'e@e.com',
                'legal_name' => 'L',
            ],
        ], 200),
    ]);

    Wompi::merchants()->get();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'merchants/pub_test_fake_key');
    });
});
