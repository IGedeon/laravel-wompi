<form action="https://checkout.wompi.co/p/" method="GET">
    <input type="hidden" name="public-key" value="{{ $publicKey }}" />
    <input type="hidden" name="currency" value="{{ $currency }}" />
    <input type="hidden" name="amount-in-cents" value="{{ $amountInCents }}" />
    <input type="hidden" name="reference" value="{{ $reference }}" />
    <input type="hidden" name="signature:integrity" value="{{ $integritySignature }}" />
    @if($redirectUrl)
        <input type="hidden" name="redirect-url" value="{{ $redirectUrl }}" />
    @endif
    @if($expirationTime)
        <input type="hidden" name="expiration-time" value="{{ $expirationTime }}" />
    @endif
    {{ $slot }}
</form>
