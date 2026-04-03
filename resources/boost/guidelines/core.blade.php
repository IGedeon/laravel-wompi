## Wompi Laravel (igedeon/laravel-wompi)

Integration package for the Wompi Colombia Web Checkout payment gateway. Provides payment links, transaction lookups, webhook handling, and Blade checkout components.

## Key Concepts

- All monetary amounts are in **centavos** (Colombian cents): $50,000 COP = `5000000`.
- The Facade `Wompi` is the main entry point for all operations.
- DTOs are `readonly class` with constructor promotion. Response DTOs use `fromArray()` as static factory.
- Webhook events are dispatched based on transaction status and can be listened to with standard Laravel event listeners.

## Creating Payment Links

@verbatim
<code-snippet name="Create a payment link" lang="php">
use IGedeon\WompiLaravel\DTOs\PaymentLinkData;
use IGedeon\WompiLaravel\Facades\Wompi;

$link = Wompi::paymentLinks()->create(new PaymentLinkData(
    name: 'Order #123',
    description: 'Payment for order #123',
    singleUse: true,
    collectShipping: false,
    amountInCents: 5000000, // $50,000 COP
    currency: 'COP',
    redirectUrl: route('payment.callback'),
));

// $link is a PaymentLinkResponse with ->id, ->name, ->active, ->checkoutUrl(), etc.
$checkoutUrl = $link->checkoutUrl();
</code-snippet>
@endverbatim

## Finding Transactions

@verbatim
<code-snippet name="Find a transaction by ID" lang="php">
use IGedeon\WompiLaravel\Facades\Wompi;

$transaction = Wompi::transactions()->find('trans_abc123');
// $transaction->status is a TransactionStatus enum (Approved, Declined, Voided, Error, Pending)
// $transaction->amountInCents, $transaction->reference, $transaction->currency
</code-snippet>
@endverbatim

## Handling Wompi Redirect After Checkout

When a customer completes checkout, Wompi redirects back with `?id=TRANSACTION_ID`. Use `transactionFromRedirect()` to retrieve the transaction:

@verbatim
<code-snippet name="Handle Wompi redirect" lang="php">
use IGedeon\WompiLaravel\Facades\Wompi;

// In your redirect route controller:
public function callback(Request $request)
{
    $transaction = Wompi::transactionFromRedirect($request);

    if ($transaction && $transaction->status === TransactionStatus::Approved) {
        // Payment approved
    }
}
</code-snippet>
@endverbatim

## Webhook Events

The package auto-registers a webhook route at the URI configured in `config/wompi.php`. Listen for these events:

- `WompiWebhookReceived` — Dispatched for every webhook (receives raw payload array).
- `TransactionApproved` — Transaction was approved.
- `TransactionDeclined` — Transaction was declined.
- `TransactionVoided` — Transaction was voided.
- `TransactionError` — Transaction had an error.

All transaction events receive a `TransactionData` DTO:

@verbatim
<code-snippet name="Listen for webhook events" lang="php">
use IGedeon\WompiLaravel\Events\TransactionApproved;

class HandleApprovedPayment
{
    public function handle(TransactionApproved $event): void
    {
        $transaction = $event->transaction;
        // $transaction->id, $transaction->reference, $transaction->amountInCents, $transaction->status
    }
}
</code-snippet>
@endverbatim

## Blade Components

Two checkout components are available. Both auto-generate the integrity signature:

@verbatim
<code-snippet name="Wompi Widget (JavaScript popup)" lang="blade">
<x-wompi-widget
    :reference="$reference"
    :amount-in-cents="5000000"
    currency="COP"
    :redirect-url="route('payment.callback')"
    :customer-email="$user->email"
/>
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Wompi Redirect Form (full-page redirect)" lang="blade">
<x-wompi-redirect-form
    :reference="$reference"
    :amount-in-cents="5000000"
    currency="COP"
    :redirect-url="route('payment.callback')"
>
    <button type="submit">Pay with Wompi</button>
</x-wompi-redirect-form>
</code-snippet>
@endverbatim

## Best Practices

- Always use the `amountInCents` convention — never pass raw peso values.
- Use `TransactionStatus->isFinal()` to check if a transaction has reached a terminal state (anything except `Pending`).
- Do not trust the redirect callback alone for payment confirmation — always verify via webhooks or `Wompi::transactions()->find()`.
- The webhook signature middleware (`verify-wompi-signature`) is auto-registered; ensure `wompi.keys.events` is configured.
- Generate integrity signatures via `Wompi::integrityHash($reference, $amountInCents, $currency)` when building custom checkout forms.
