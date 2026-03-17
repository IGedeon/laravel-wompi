<?php

use IGedeon\WompiLaravel\View\Components\RedirectForm;

it('renders the redirect form with required attributes', function () {
    $view = $this->blade(
        '<x-wompi::redirect-form reference="order-123" :amount-in-cents="5000000">
            <button type="submit">Pay</button>
        </x-wompi::redirect-form>',
    );

    $view->assertSee('action="https://checkout.wompi.co/p/"', false)
        ->assertSee('method="GET"', false)
        ->assertSee('name="public-key" value="pub_test_fake_key"', false)
        ->assertSee('name="currency" value="COP"', false)
        ->assertSee('name="amount-in-cents" value="5000000"', false)
        ->assertSee('name="reference" value="order-123"', false)
        ->assertSee('name="signature:integrity"', false)
        ->assertSee('<button type="submit">Pay</button>', false);
});

it('renders with a custom currency', function () {
    $view = $this->blade(
        '<x-wompi::redirect-form reference="order-cur" :amount-in-cents="1000000" currency="COP">
            <button>Pay</button>
        </x-wompi::redirect-form>',
    );

    $view->assertSee('name="currency" value="COP"', false);
});

it('includes redirect URL when provided', function () {
    $view = $this->blade(
        '<x-wompi::redirect-form reference="order-redir" :amount-in-cents="2000000" redirect-url="https://example.com/result">
            <button>Pay</button>
        </x-wompi::redirect-form>',
    );

    $view->assertSee('name="redirect-url" value="https://example.com/result"', false);
});

it('includes expiration time when provided', function () {
    $view = $this->blade(
        '<x-wompi::redirect-form reference="order-exp" :amount-in-cents="3000000" expiration-time="2025-12-31T23:59:59.000Z">
            <button>Pay</button>
        </x-wompi::redirect-form>',
    );

    $view->assertSee('name="expiration-time" value="2025-12-31T23:59:59.000Z"', false);
});

it('does not include optional fields when not provided', function () {
    $view = $this->blade(
        '<x-wompi::redirect-form reference="order-min" :amount-in-cents="1000000">
            <button>Pay</button>
        </x-wompi::redirect-form>',
    );

    $view->assertDontSee('name="redirect-url"')
        ->assertDontSee('name="expiration-time"');
});

it('renders the slot content', function () {
    $view = $this->blade(
        '<x-wompi::redirect-form reference="order-slot" :amount-in-cents="5000000">
            <button class="custom-btn" type="submit">Complete Payment</button>
        </x-wompi::redirect-form>',
    );

    $view->assertSee('Complete Payment');
});

it('generates a valid integrity signature', function () {
    $component = new RedirectForm(
        reference: 'order-sig',
        amountInCents: 5000000,
        currency: 'COP',
    );

    $expected = hash('sha256', 'order-sig5000000COPtest_integrity_fake_key');

    expect($component->integritySignature)->toBe($expected);
});

it('includes expiration time in integrity signature when provided', function () {
    $component = new RedirectForm(
        reference: 'order-exp-sig',
        amountInCents: 3000000,
        currency: 'COP',
        expirationTime: '2025-12-31T23:59:59.000Z',
    );

    $expected = hash('sha256', 'order-exp-sig3000000COP2025-12-31T23:59:59.000Ztest_integrity_fake_key');

    expect($component->integritySignature)->toBe($expected);
});
