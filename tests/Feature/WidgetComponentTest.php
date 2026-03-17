<?php

use IGedeon\WompiLaravel\View\Components\Widget;

it('renders the widget component with required attributes', function () {
    $view = $this->blade(
        '<x-wompi::widget reference="order-123" :amount-in-cents="5000000" />',
    );

    $view->assertSee('data-public-key="pub_test_fake_key"', false)
        ->assertSee('data-currency="COP"', false)
        ->assertSee('data-amount-in-cents="5000000"', false)
        ->assertSee('data-reference="order-123"', false)
        ->assertSee('data-signature:integrity=', false)
        ->assertSee('https://checkout.wompi.co/widget.js', false);
});

it('renders the widget with a custom currency', function () {
    $view = $this->blade(
        '<x-wompi::widget reference="order-456" :amount-in-cents="1000000" currency="COP" />',
    );

    $view->assertSee('data-currency="COP"', false);
});

it('includes redirect URL when provided', function () {
    $view = $this->blade(
        '<x-wompi::widget reference="order-789" :amount-in-cents="2000000" redirect-url="https://example.com/thanks" />',
    );

    $view->assertSee('data-redirect-url="https://example.com/thanks"', false);
});

it('includes expiration time when provided', function () {
    $view = $this->blade(
        '<x-wompi::widget reference="order-exp" :amount-in-cents="3000000" expiration-time="2025-12-31T23:59:59.000Z" />',
    );

    $view->assertSee('data-expiration-time="2025-12-31T23:59:59.000Z"', false);
});

it('includes customer email when provided', function () {
    $view = $this->blade(
        '<x-wompi::widget reference="order-email" :amount-in-cents="4000000" customer-email="test@example.com" />',
    );

    $view->assertSee('data-customer-data:email="test@example.com"', false);
});

it('includes customer full name when provided', function () {
    $view = $this->blade(
        '<x-wompi::widget reference="order-name" :amount-in-cents="4000000" customer-full-name="John Doe" />',
    );

    $view->assertSee('data-customer-data:full-name="John Doe"', false);
});

it('includes customer phone number when provided', function () {
    $view = $this->blade(
        '<x-wompi::widget reference="order-phone" :amount-in-cents="4000000" customer-phone-number="3001234567" />',
    );

    $view->assertSee('data-customer-data:phone-number="3001234567"', false);
});

it('includes tax fields when provided', function () {
    $view = $this->blade(
        '<x-wompi::widget reference="order-tax" :amount-in-cents="5000000" :tax-in-cents-vat="950000" :tax-in-cents-consumption="400000" />',
    );

    $view->assertSee('data-tax-in-cents:vat="950000"', false)
        ->assertSee('data-tax-in-cents:consumption="400000"', false);
});

it('does not include optional attributes when not provided', function () {
    $view = $this->blade(
        '<x-wompi::widget reference="order-minimal" :amount-in-cents="1000000" />',
    );

    $view->assertDontSee('data-redirect-url')
        ->assertDontSee('data-expiration-time')
        ->assertDontSee('data-customer-data:email')
        ->assertDontSee('data-customer-data:full-name')
        ->assertDontSee('data-customer-data:phone-number')
        ->assertDontSee('data-tax-in-cents:vat')
        ->assertDontSee('data-tax-in-cents:consumption');
});

it('generates a valid integrity signature', function () {
    $component = new Widget(
        reference: 'order-sig',
        amountInCents: 5000000,
        currency: 'COP',
    );

    $expected = hash('sha256', 'order-sig5000000COPtest_integrity_fake_key');

    expect($component->integritySignature)->toBe($expected);
});
