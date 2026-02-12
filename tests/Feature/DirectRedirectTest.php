<?php

use App\Models\Domain;

test('direct redirect with valid URL shows interstitial', function () {
    $response = $this->get('/?https://example.com/direct');

    $response->assertOk();
    $response->assertSee('https://example.com/direct');
    $response->assertSee('Anonymized Redirect');
    $response->assertHeader('Cache-Control', 'no-store, private');
});

test('direct redirect with invalid URL shows homepage', function () {
    $response = $this->get('/?not-a-url');

    $response->assertOk();
    $response->assertSee('Anonymous URL Shortener and Redirect');
});

test('direct redirect with http URL works', function () {
    $response = $this->get('/?http://example.com/http-test');

    $response->assertOk();
    $response->assertSee('http://example.com/http-test');
});

test('direct redirect for allowed domain shows auto-redirect', function () {
    Domain::factory()->allowed()->create(['host' => 'trusted.com']);

    $response = $this->get('/?https://trusted.com/page');

    $response->assertOk();
    $response->assertSee('Redirecting...');
    $response->assertDontSee('Continue to site');
});

test('direct redirect for blocked domain shows blocked message', function () {
    Domain::factory()->blocked()->create(['host' => 'evil.com']);

    $response = $this->get('/?https://evil.com/page');

    $response->assertOk();
    $response->assertSee('Domain Blocked');
    $response->assertDontSee('Continue to site');
});

test('invalid URL does not create cache entry', function () {
    $this->get('/?not-a-url');

    $entry = Cache::get('redirect:'.md5('not-a-url'));
    expect($entry)->toBeNull();
});
