<?php

namespace IGedeon\WompiLaravel\Enums;

enum TransactionStatus: string
{
    case Pending = 'PENDING';
    case Approved = 'APPROVED';
    case Declined = 'DECLINED';
    case Voided = 'VOIDED';
    case Error = 'ERROR';

    public function isFinal(): bool
    {
        return $this !== self::Pending;
    }
}
