<?php

namespace IGedeon\WompiLaravel\DTOs;

use IGedeon\WompiLaravel\Enums\TransactionStatus;

readonly class TransactionData
{
    public function __construct(
        public string $id,
        public TransactionStatus $status,
        public int $amountInCents,
        public string $currency,
        public string $reference,
        public string $paymentMethodType,
        public ?string $paymentLinkId,
        public ?string $redirectUrl,
        public ?string $statusMessage,
        public array $raw,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            status: TransactionStatus::from($data['status']),
            amountInCents: $data['amount_in_cents'],
            currency: $data['currency'],
            reference: $data['reference'],
            paymentMethodType: $data['payment_method_type'] ?? 'UNKNOWN',
            paymentLinkId: $data['payment_link_id'] ?? null,
            redirectUrl: $data['redirect_url'] ?? null,
            statusMessage: $data['status_message'] ?? null,
            raw: $data,
        );
    }
}
