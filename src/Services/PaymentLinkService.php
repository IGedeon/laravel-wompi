<?php

namespace IGedeon\WompiLaravel\Services;

use IGedeon\WompiLaravel\DTOs\PaymentLinkData;
use IGedeon\WompiLaravel\DTOs\PaymentLinkResponse;
use IGedeon\WompiLaravel\Http\WompiClient;

class PaymentLinkService
{
    public function __construct(private readonly WompiClient $client) {}

    public function create(PaymentLinkData $data): PaymentLinkResponse
    {
        $response = $this->client->post('payment_links', $data->toArray());

        return PaymentLinkResponse::fromArray($response['data']);
    }

    public function find(string $id): PaymentLinkResponse
    {
        $response = $this->client->get("payment_links/{$id}");

        return PaymentLinkResponse::fromArray($response['data']);
    }

    public function checkoutUrl(string $id): string
    {
        return "https://checkout.wompi.co/l/{$id}";
    }
}
