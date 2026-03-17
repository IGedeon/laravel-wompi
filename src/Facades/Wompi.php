<?php

namespace IGedeon\WompiLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \IGedeon\WompiLaravel\Services\PaymentLinkService paymentLinks()
 * @method static \IGedeon\WompiLaravel\Services\TransactionService transactions()
 * @method static \IGedeon\WompiLaravel\Services\MerchantService merchants()
 * @method static string integrityHash(string $reference, int $amountInCents, string $currency, ?string $expirationTime = null)
 * @method static \IGedeon\WompiLaravel\DTOs\TransactionData|null transactionFromRedirect(\Illuminate\Http\Request $request)
 *
 * @see \IGedeon\WompiLaravel\Wompi
 */
class Wompi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \IGedeon\WompiLaravel\Wompi::class;
    }
}
