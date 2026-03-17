<?php

namespace IGedeon\WompiLaravel\Http\Controllers;

use IGedeon\WompiLaravel\DTOs\TransactionData;
use IGedeon\WompiLaravel\Enums\TransactionStatus;
use IGedeon\WompiLaravel\Events\TransactionApproved;
use IGedeon\WompiLaravel\Events\TransactionDeclined;
use IGedeon\WompiLaravel\Events\TransactionError;
use IGedeon\WompiLaravel\Events\TransactionVoided;
use IGedeon\WompiLaravel\Events\WompiWebhookReceived;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class WebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->all();

        WompiWebhookReceived::dispatch($payload);

        $transactionData = $payload['data']['transaction'] ?? null;

        if ($transactionData) {
            $transaction = TransactionData::fromArray($transactionData);

            match ($transaction->status) {
                TransactionStatus::Approved => TransactionApproved::dispatch($transaction),
                TransactionStatus::Declined => TransactionDeclined::dispatch($transaction),
                TransactionStatus::Voided   => TransactionVoided::dispatch($transaction),
                TransactionStatus::Error    => TransactionError::dispatch($transaction),
                default => null,
            };
        }

        return response()->noContent(200);
    }
}
