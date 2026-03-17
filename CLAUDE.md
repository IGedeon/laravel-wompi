# igedeon/laravel-wompi

Paquete Laravel para Wompi Colombia - Web Checkout.

## Comandos

```bash
# Correr tests
./vendor/bin/pest

# Lint
./vendor/bin/pint

# Validar composer
composer validate
```

## Arquitectura

- `src/Wompi.php` — Manager principal, target del Facade
- `src/Facades/Wompi.php` — Facade (`Wompi::paymentLinks()`, `Wompi::transactions()`, etc.)
- `src/Http/WompiClient.php` — HTTP client que envuelve Laravel Http con auth y retry
- `src/Services/` — Servicios: PaymentLink, Transaction, Merchant, IntegritySignature, WebhookSignature
- `src/DTOs/` — Data Transfer Objects readonly: PaymentLinkData, PaymentLinkResponse, TransactionData, MerchantData
- `src/Enums/` — Environment, TransactionStatus, Currency
- `src/Events/` — Eventos Laravel para webhooks
- `src/Http/Controllers/WebhookController.php` — Recibe y procesa webhooks
- `src/Http/Middleware/VerifyWebhookSignature.php` — Verifica firma SHA-256
- `src/View/Components/` — Blade components: Widget, RedirectForm

## Convenciones

- DTOs son `readonly class` con constructor promotion
- Los DTOs de respuesta tienen `fromArray(array $data): self` como factory estático
- Los montos siempre están en centavos (`amountInCents`): $50,000 COP = 5000000
- Enums usan `string` backed values
- Services reciben `WompiClient` o `array $config` por constructor injection
- Tests usan Pest PHP con Orchestra Testbench
- Feature tests usan `Http::fake()` para mock de la API de Wompi
- Webhooks usan `data_get()` para resolver paths dinámicos de la firma

## API de Wompi

- Base URL sandbox: `https://sandbox.wompi.co/v1`
- Base URL producción: `https://production.wompi.co/v1`
- Auth: Bearer token (public key para GET, private key para POST)
- Firma de integridad: `SHA-256(reference + amountInCents + currency + [expirationTime] + integritySecret)`
- Firma de webhook: leer `signature.properties`, extraer valores de `data`, concatenar + timestamp + eventsSecret, SHA-256
