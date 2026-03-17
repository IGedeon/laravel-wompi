<?php

namespace IGedeon\WompiLaravel\DTOs;

readonly class PaymentLinkData
{
    public function __construct(
        public string $name,
        public string $description,
        public bool $singleUse,
        public bool $collectShipping,
        public ?int $amountInCents = null,
        public string $currency = 'COP',
        public ?string $expiresAt = null,
        public ?string $redirectUrl = null,
        public ?string $imageUrl = null,
        public ?string $sku = null,
        public ?array $taxes = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'name'              => $this->name,
            'description'       => $this->description,
            'single_use'        => $this->singleUse,
            'collect_shipping'  => $this->collectShipping,
            'amount_in_cents'   => $this->amountInCents,
            'currency'          => $this->currency,
            'expires_at'        => $this->expiresAt,
            'redirect_url'      => $this->redirectUrl,
            'image_url'         => $this->imageUrl,
            'sku'               => $this->sku,
            'taxes'             => $this->taxes,
        ], fn ($value) => $value !== null);
    }
}
