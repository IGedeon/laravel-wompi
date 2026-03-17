<?php

namespace IGedeon\WompiLaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WompiWebhookReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly array $payload) {}
}
