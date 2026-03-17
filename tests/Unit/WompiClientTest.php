<?php

use IGedeon\WompiLaravel\Exceptions\ApiException;
use IGedeon\WompiLaravel\Exceptions\InvalidConfigurationException;
use IGedeon\WompiLaravel\Http\WompiClient;
use Illuminate\Support\Facades\Http;

it('resolves sandbox base URL', function () {
    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_key', 'private' => 'prv_test_key'],
    ]);

    expect($client->baseUrl())->toBe('https://sandbox.wompi.co/v1');
});

it('resolves production base URL', function () {
    $client = new WompiClient([
        'environment' => 'production',
        'keys' => ['public' => 'pub_prod_key', 'private' => 'prv_prod_key'],
    ]);

    expect($client->baseUrl())->toBe('https://production.wompi.co/v1');
});

it('returns the public key', function () {
    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_my_key', 'private' => 'prv_test_key'],
    ]);

    expect($client->publicKey())->toBe('pub_test_my_key');
});

it('returns the private key', function () {
    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_key', 'private' => 'prv_test_my_secret'],
    ]);

    expect($client->privateKey())->toBe('prv_test_my_secret');
});

it('throws InvalidConfigurationException when public key is missing', function () {
    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => null, 'private' => 'prv_test_key'],
    ]);

    $client->publicKey();
})->throws(InvalidConfigurationException::class, 'WOMPI_PUBLIC_KEY is not configured.');

it('throws InvalidConfigurationException when private key is missing', function () {
    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_key', 'private' => null],
    ]);

    $client->privateKey();
})->throws(InvalidConfigurationException::class, 'WOMPI_PRIVATE_KEY is not configured.');

it('throws ValueError for invalid environment', function () {
    new WompiClient([
        'environment' => 'invalid',
        'keys' => ['public' => 'pub_test_key', 'private' => 'prv_test_key'],
    ]);
})->throws(ValueError::class);

it('sends GET request with public key as bearer token', function () {
    Http::fake([
        'sandbox.wompi.co/*' => Http::response(['data' => ['id' => '1']], 200),
    ]);

    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_bearer', 'private' => 'prv_test_key'],
    ]);

    $client->get('transactions/txn-001');

    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer pub_test_bearer')
            && str_contains($request->url(), 'sandbox.wompi.co/v1/transactions/txn-001');
    });
});

it('sends POST request with private key as bearer token', function () {
    Http::fake([
        'sandbox.wompi.co/*' => Http::response(['data' => ['id' => 'link-1']], 200),
    ]);

    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_key', 'private' => 'prv_test_bearer'],
    ]);

    $client->post('payment_links', ['name' => 'Test']);

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request->hasHeader('Authorization', 'Bearer prv_test_bearer')
            && str_contains($request->url(), 'sandbox.wompi.co/v1/payment_links');
    });
});

it('throws ApiException on GET failure', function () {
    Http::fake([
        'sandbox.wompi.co/*' => Http::response(['error' => 'Not Found'], 404),
    ]);

    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_key', 'private' => 'prv_test_key'],
    ]);

    $client->get('transactions/invalid');
})->throws(ApiException::class);

it('throws ApiException on POST failure', function () {
    Http::fake([
        'sandbox.wompi.co/*' => Http::response(['error' => 'Bad Request'], 400),
    ]);

    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_key', 'private' => 'prv_test_key'],
    ]);

    $client->post('payment_links', []);
})->throws(ApiException::class);

it('includes status code and response body in ApiException', function () {
    Http::fake([
        'sandbox.wompi.co/*' => Http::response(['error' => 'Unauthorized'], 401),
    ]);

    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_key', 'private' => 'prv_test_key'],
    ]);

    try {
        $client->get('transactions/txn-001');
    } catch (ApiException $e) {
        expect($e->statusCode)->toBe(401)
            ->and($e->responseBody)->toBe(['error' => 'Unauthorized']);

        return;
    }

    test()->fail('Expected ApiException was not thrown.');
});

it('returns json response on successful GET', function () {
    Http::fake([
        'sandbox.wompi.co/*' => Http::response([
            'data' => ['id' => 'txn-001', 'status' => 'APPROVED'],
        ], 200),
    ]);

    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_key', 'private' => 'prv_test_key'],
    ]);

    $result = $client->get('transactions/txn-001');

    expect($result)->toBe([
        'data' => ['id' => 'txn-001', 'status' => 'APPROVED'],
    ]);
});

it('passes query parameters on GET request', function () {
    Http::fake([
        'sandbox.wompi.co/*' => Http::response(['data' => []], 200),
    ]);

    $client = new WompiClient([
        'environment' => 'sandbox',
        'keys' => ['public' => 'pub_test_key', 'private' => 'prv_test_key'],
    ]);

    $client->get('transactions', ['reference' => 'order-123']);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'reference=order-123');
    });
});
