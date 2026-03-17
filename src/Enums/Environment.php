<?php

namespace IGedeon\WompiLaravel\Enums;

enum Environment: string
{
    case Sandbox = 'sandbox';
    case Production = 'production';

    public function baseUrl(): string
    {
        return match ($this) {
            self::Sandbox => 'https://sandbox.wompi.co/v1',
            self::Production => 'https://production.wompi.co/v1',
        };
    }
}
