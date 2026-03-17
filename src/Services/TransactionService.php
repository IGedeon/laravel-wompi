<?php

namespace IGedeon\WompiLaravel\Services;

use IGedeon\WompiLaravel\DTOs\TransactionData;
use IGedeon\WompiLaravel\Http\WompiClient;

class TransactionService
{
    public function __construct(private readonly WompiClient $client) {}

    public function find(string $id): TransactionData
    {
        $response = $this->client->get("transactions/{$id}");

        return TransactionData::fromArray($response['data']);
    }
}
