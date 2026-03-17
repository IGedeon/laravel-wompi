<?php

namespace IGedeon\WompiLaravel;

use IGedeon\WompiLaravel\Services\IntegritySignatureService;
use IGedeon\WompiLaravel\Services\MerchantService;
use IGedeon\WompiLaravel\Services\PaymentLinkService;
use IGedeon\WompiLaravel\Services\TransactionService;

class Wompi
{
    public function __construct(
        private readonly PaymentLinkService $paymentLinkService,
        private readonly TransactionService $transactionService,
        private readonly MerchantService $merchantService,
        private readonly IntegritySignatureService $integritySignatureService,
    ) {}

    public function paymentLinks(): PaymentLinkService
    {
        return $this->paymentLinkService;
    }

    public function transactions(): TransactionService
    {
        return $this->transactionService;
    }

    public function merchants(): MerchantService
    {
        return $this->merchantService;
    }

    public function integrityHash(
        string $reference,
        int $amountInCents,
        string $currency,
        ?string $expirationTime = null,
    ): string {
        return $this->integritySignatureService->generate($reference, $amountInCents, $currency, $expirationTime);
    }
}
