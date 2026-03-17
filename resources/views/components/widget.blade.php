<form>
    <script
        src="https://checkout.wompi.co/widget.js"
        data-render="button"
        data-public-key="{{ $publicKey }}"
        data-currency="{{ $currency }}"
        data-amount-in-cents="{{ $amountInCents }}"
        data-reference="{{ $reference }}"
        data-signature:integrity="{{ $integritySignature }}"
        @if($redirectUrl) data-redirect-url="{{ $redirectUrl }}" @endif
        @if($expirationTime) data-expiration-time="{{ $expirationTime }}" @endif
        @if($customerEmail) data-customer-data:email="{{ $customerEmail }}" @endif
        @if($customerFullName) data-customer-data:full-name="{{ $customerFullName }}" @endif
        @if($customerPhoneNumber) data-customer-data:phone-number="{{ $customerPhoneNumber }}" @endif
        @if($taxInCentsVat) data-tax-in-cents:vat="{{ $taxInCentsVat }}" @endif
        @if($taxInCentsConsumption) data-tax-in-cents:consumption="{{ $taxInCentsConsumption }}" @endif
    ></script>
</form>
