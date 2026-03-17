<?php

use IGedeon\WompiLaravel\Enums\TransactionStatus;

it('has the correct values', function () {
    expect(TransactionStatus::Pending->value)->toBe('PENDING')
        ->and(TransactionStatus::Approved->value)->toBe('APPROVED')
        ->and(TransactionStatus::Declined->value)->toBe('DECLINED')
        ->and(TransactionStatus::Voided->value)->toBe('VOIDED')
        ->and(TransactionStatus::Error->value)->toBe('ERROR');
});

it('identifies final statuses', function () {
    expect(TransactionStatus::Pending->isFinal())->toBeFalse()
        ->and(TransactionStatus::Approved->isFinal())->toBeTrue()
        ->and(TransactionStatus::Declined->isFinal())->toBeTrue()
        ->and(TransactionStatus::Voided->isFinal())->toBeTrue()
        ->and(TransactionStatus::Error->isFinal())->toBeTrue();
});
