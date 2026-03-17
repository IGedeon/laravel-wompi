<?php

namespace IGedeon\WompiLaravel\Services;

use IGedeon\WompiLaravel\DTOs\MerchantData;
use IGedeon\WompiLaravel\Http\WompiClient;

class MerchantService
{
    public function __construct(private readonly WompiClient $client) {}

    public function get(): MerchantData
    {
        $publicKey = $this->client->publicKey();
        $response = $this->client->get("merchants/{$publicKey}");

        return MerchantData::fromArray($response['data']);
    }
}
