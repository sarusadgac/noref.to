<?php

use App\Models\Domain;
use App\Models\Link;
use App\Models\User;

beforeEach(function () {
    User::factory()->system()->create();
});

test('valid hash shows interstitial page', function () {
    $link = Link::factory()->withUrl('https://example.com/target')->create();

    $response = $this->get('/'.$link->hash);

    $response->assertOk();
    $response->assertSee('https://example.com/target');
    $response->assertSee('Anonymized Redirect');
    $response->assertHeader('Cache-Control', 'no-store, private');
});

test('interstitial page has referrer stripping meta tag', function () {
    $link = Link::factory()->withUrl('https://example.com/target')->create();

    $response = $this->get('/'.$link->hash);

    $response->assertOk();
    $response->assertSee('no-referrer', false);
    $response->assertSee('noreferrer', false);
});

test('invalid hash returns 404', function () {
    $response = $this->get('/AbCdEf');

    $response->assertNotFound();
});

test('hash route does not match non-6-char strings', function () {
    $response = $this->get('/abc');
    $response->assertNotFound();

    $response = $this->get('/abcdefg');
    $response->assertNotFound();
});

test('interstitial shows continue button instead of auto-redirect', function () {
    $link = Link::factory()->withUrl('https://example.com/auto')->create();

    $response = $this->get('/'.$link->hash);

    $response->assertSee('Continue to site');
    $response->assertDontSee('http-equiv="refresh"', false);
    $response->assertSee('https://example.com/auto', false);
});

test('redirect for allowed domain shows auto-redirect progress bar', function () {
    Domain::factory()->allowed()->create(['host' => 'trusted.com']);
    $link = Link::factory()->withUrl('https://trusted.com/page')->create();

    $response = $this->get('/'.$link->hash);

    $response->assertOk();
    $response->assertSee('Redirecting...');
    $response->assertSee('Click here if not redirected');
    $response->assertDontSee('Continue to site');
});

test('redirect for unknown domain shows manual button', function () {
    $link = Link::factory()->withUrl('https://unknown-domain.com/page')->create();

    $response = $this->get('/'.$link->hash);

    $response->assertOk();
    $response->assertSee('Continue to site');
    $response->assertDontSee('Redirecting...');
});

test('redirect for subdomain of allowed domain shows auto-redirect', function () {
    Domain::factory()->allowed()->create(['host' => 'trusted.com']);
    $link = Link::factory()->withUrl('https://www.trusted.com/page')->create();

    $response = $this->get('/'.$link->hash);

    $response->assertOk();
    $response->assertSee('Redirecting...');
    $response->assertDontSee('Continue to site');
});

test('redirect for subdomain of blocked domain shows blocked message', function () {
    Domain::factory()->blocked()->create(['host' => 'evil.com']);
    $link = Link::factory()->withUrl('https://www.evil.com/bad-page')->create();

    $response = $this->get('/'.$link->hash);

    $response->assertOk();
    $response->assertSee('Domain Blocked');
    $response->assertDontSee('Continue to site');
});

test('redirect for blocked domain shows blocked message', function () {
    Domain::factory()->blocked()->create(['host' => 'evil.com']);
    $link = Link::factory()->withUrl('https://evil.com/bad-page')->create();

    $response = $this->get('/'.$link->hash);

    $response->assertOk();
    $response->assertSee('Domain Blocked');
    $response->assertSee('https://evil.com/bad-page');
    $response->assertDontSee('Continue to site');
    $response->assertDontSee('Redirecting...');
});
