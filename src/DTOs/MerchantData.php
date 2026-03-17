<?php

namespace IGedeon\WompiLaravel\DTOs;

readonly class MerchantData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $legalName,
        public ?string $acceptanceToken,
        public ?string $acceptancePersonalAuth,
        public array $raw,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            legalName: $data['legal_name'],
            acceptanceToken: $data['presigned_acceptance']['acceptance_token'] ?? null,
            acceptancePersonalAuth: $data['presigned_personal_data_auth']['acceptance_token'] ?? null,
            raw: $data,
        );
    }
}
