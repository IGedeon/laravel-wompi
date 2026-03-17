<?php

namespace IGedeon\WompiLaravel\Services;

use IGedeon\WompiLaravel\DTOs\TransactionData;
use IGedeon\WompiLaravel\Http\WompiClient;
use Illuminate\Http\Request;

class TransactionService
{
    public function __construct(private readonly WompiClient $client) {}

    public function find(string $id): TransactionData
    {
        $response = $this->client->get("transactions/{$id}");

        return TransactionData::fromArray($response['data']);
    }

    /**
     * Find the transaction from Wompi's redirect request.
     * Wompi appends ?id=TRANSACTION_ID to the redirect_url after checkout.
     * Returns null if the request does not contain a transaction ID.
     */
    public function findFromRedirect(Request $request): ?TransactionData
    {
        $id = $request->query('id');

        if (! $id) {
            return null;
        }

        return $this->find($id);
    }
}
