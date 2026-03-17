<?php

namespace IGedeon\WompiLaravel\DTOs;

readonly class PaymentLinkResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public bool $singleUse,
        public bool $collectShipping,
        public ?int $amountInCents,
        public string $currency,
        public ?string $expiresAt,
        public ?string $redirectUrl,
        public ?string $imageUrl,
        public ?string $sku,
        public bool $active,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            description: $data['description'],
            singleUse: $data['single_use'],
            collectShipping: $data['collect_shipping'],
            amountInCents: $data['amount_in_cents'] ?? null,
            currency: $data['currency'],
            expiresAt: $data['expires_at'] ?? null,
            redirectUrl: $data['redirect_url'] ?? null,
            imageUrl: $data['image_url'] ?? null,
            sku: $data['sku'] ?? null,
            active: $data['active'] ?? true,
        );
    }

    public function checkoutUrl(): string
    {
        return "https://checkout.wompi.co/l/{$this->id}";
    }
}
