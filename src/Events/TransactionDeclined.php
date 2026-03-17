<?php

namespace IGedeon\WompiLaravel\Events;

use IGedeon\WompiLaravel\DTOs\TransactionData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionDeclined
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly TransactionData $transaction) {}
}
