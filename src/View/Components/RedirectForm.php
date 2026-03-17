<?php

namespace IGedeon\WompiLaravel\View\Components;

use IGedeon\WompiLaravel\Services\IntegritySignatureService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RedirectForm extends Component
{
    public string $publicKey;

    public string $integritySignature;

    public function __construct(
        public string $reference,
        public int $amountInCents,
        public string $currency = 'COP',
        public ?string $redirectUrl = null,
        public ?string $expirationTime = null,
    ) {
        $this->publicKey = config('wompi.keys.public');
        $this->integritySignature = app(IntegritySignatureService::class)
            ->generate($reference, $amountInCents, $currency, $expirationTime);
    }

    public function render(): View
    {
        return view('wompi::components.redirect-form');
    }
}
