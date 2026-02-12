<?php

use App\Models\Domain;
use App\Models\Link;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    User::factory()->system()->create();
});

test('authenticated user can shorten a valid URL', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::home')
        ->set('url', 'https://example.com/some-page')
        ->set('captchaToken', 'test-token')
        ->call('shorten')
        ->assertHasNoErrors()
        ->assertSet('shortUrl', fn ($value) => str_contains($value, '/'));

    expect(Link::count())->toBe(1);
    expect(Link::first()->created_by)->toBe($user->id);
});

test('guest user can shorten a valid URL', function () {
    Livewire::test('pages::home')
        ->set('url', 'https://example.com/some-page')
        ->set('captchaToken', 'test-token')
        ->call('shorten')
        ->assertHasNoErrors()
        ->assertSet('shortUrl', fn ($value) => str_contains($value, '/'));

    $link = Link::first();
    $systemUser = User::where('email', config('anonto.system_user_email'))->first();

    expect($link->created_by)->toBe($systemUser->id);
});

test('shortening rejects invalid URLs', function () {
    Livewire::test('pages::home')
        ->set('url', 'not-a-url')
        ->call('shorten')
        ->assertHasErrors(['url']);
});

test('shortening rejects self-referencing URLs', function () {
    Livewire::test('pages::home')
        ->set('url', config('app.url').'/some-path')
        ->set('captchaToken', 'test-token')
        ->call('shorten')
        ->assertHasErrors(['url']);
});

test('duplicate URLs return same link', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::home')
        ->set('url', 'https://example.com/test')
        ->set('captchaToken', 'test-token')
        ->call('shorten');

    Livewire::actingAs($user)
        ->test('pages::home')
        ->set('url', 'https://example.com/test')
        ->set('captchaToken', 'test-token')
        ->call('shorten');

    expect(Link::count())->toBe(1);
});

test('generated hash is 6 characters alphanumeric', function () {
    $hash = Link::generateUniqueHash();

    expect($hash)->toMatch('/^[A-Za-z0-9]{6}$/');
});

test('generated hash does not match excluded words', function () {
    $excludedWords = array_map('strtolower', config('anonto.excluded_words', []));

    for ($i = 0; $i < 50; $i++) {
        $hash = Link::generateUniqueHash();
        expect(in_array(strtolower($hash), $excludedWords, true))->toBeFalse();
    }
});

test('shortening a URL with a blocked domain returns validation error', function () {
    Domain::factory()->blocked()->create(['host' => 'evil.com']);

    Livewire::test('pages::home')
        ->set('url', 'https://evil.com/some-page')
        ->set('captchaToken', 'test-token')
        ->call('shorten')
        ->assertHasErrors(['url']);

    expect(Link::count())->toBe(0);
});

test('shortening a URL with an allowed domain works normally', function () {
    Domain::factory()->allowed()->create(['host' => 'trusted.com']);

    Livewire::test('pages::home')
        ->set('url', 'https://trusted.com/some-page')
        ->set('captchaToken', 'test-token')
        ->call('shorten')
        ->assertHasNoErrors()
        ->assertSet('shortUrl', fn ($value) => str_contains($value, '/'));

    expect(Link::count())->toBe(1);
});

test('shortening a URL with an unknown domain works normally', function () {
    Livewire::test('pages::home')
        ->set('url', 'https://unknown-domain.com/some-page')
        ->set('captchaToken', 'test-token')
        ->call('shorten')
        ->assertHasNoErrors()
        ->assertSet('shortUrl', fn ($value) => str_contains($value, '/'));

    expect(Link::count())->toBe(1);
});

test('shortening is rate limited', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 20; $i++) {
        Livewire::actingAs($user)
            ->test('pages::home')
            ->set('url', "https://example.com/page-{$i}")
            ->set('captchaToken', 'test-token')
            ->call('shorten')
            ->assertHasNoErrors();
    }

    Livewire::actingAs($user)
        ->test('pages::home')
        ->set('url', 'https://example.com/page-extra')
        ->set('captchaToken', 'test-token')
        ->call('shorten')
        ->assertHasErrors(['url']);
});
