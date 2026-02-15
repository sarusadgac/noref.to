<?php

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('decomposeUrl correctly parses a full URL', function () {
    $components = Link::decomposeUrl('https://example.com:8080/path/to/page?q=test#section');

    expect($components)->toBe([
        'scheme' => 'https',
        'host' => 'example.com',
        'port' => 8080,
        'path' => '/path/to/page',
        'query_string' => 'q=test',
        'fragment' => 'section',
    ]);
});

test('decomposeUrl normalizes scheme and host to lowercase', function () {
    $components = Link::decomposeUrl('HTTPS://EXAMPLE.COM/path');

    expect($components['scheme'])->toBe('https');
    expect($components['host'])->toBe('example.com');
});

test('decomposeUrl treats bare slash path as null', function () {
    $components = Link::decomposeUrl('https://example.com/');

    expect($components['path'])->toBeNull();
});

test('decomposeUrl handles URL without path', function () {
    $components = Link::decomposeUrl('https://example.com');

    expect($components['path'])->toBeNull();
    expect($components['port'])->toBeNull();
    expect($components['query_string'])->toBeNull();
    expect($components['fragment'])->toBeNull();
});

test('computeFingerprint returns consistent hash', function () {
    $components = Link::decomposeUrl('https://example.com/path');
    $fp1 = Link::computeFingerprint($components);
    $fp2 = Link::computeFingerprint($components);

    expect($fp1)->toBe($fp2);
    expect($fp1)->toHaveLength(64);
});

test('computeFingerprint differs for different URLs', function () {
    $fp1 = Link::computeFingerprint(Link::decomposeUrl('https://example.com/a'));
    $fp2 = Link::computeFingerprint(Link::decomposeUrl('https://example.com/b'));

    expect($fp1)->not->toBe($fp2);
});

test('destination_url accessor reconstructs full URL', function () {
    $link = new Link([
        'scheme' => 'https',
        'host' => 'example.com',
        'port' => 8080,
        'path' => '/path',
        'query_string' => 'q=1',
        'fragment' => 'top',
    ]);

    expect($link->destination_url)->toBe('https://example.com:8080/path?q=1#top');
});

test('decomposeUrl throws exception for malformed URLs', function () {
    expect(fn () => Link::decomposeUrl('http:///'))
        ->toThrow(\InvalidArgumentException::class);
});

test('decomposeUrl throws exception for non-http schemes', function () {
    expect(fn () => Link::decomposeUrl('javascript:alert(1)'))
        ->toThrow(\InvalidArgumentException::class, 'URL must use http or https scheme.');
});

test('decomposeUrl throws exception for URLs without host', function () {
    expect(fn () => Link::decomposeUrl('http:///path'))
        ->toThrow(\InvalidArgumentException::class);
});

test('destination_url accessor handles minimal URL', function () {
    $link = new Link([
        'scheme' => 'https',
        'host' => 'example.com',
        'port' => null,
        'path' => null,
        'query_string' => null,
        'fragment' => null,
    ]);

    expect($link->destination_url)->toBe('https://example.com');
});

test('resolveHash returns null when cache contains false (negative cache hit)', function () {
    Cache::put('link:AbCdEf', false, 300);

    expect(Link::resolveHash('AbCdEf'))->toBeNull();
});

test('resolveHash returns cached URL on cache hit', function () {
    Cache::put('link:XyZaBc', 'https://example.com/cached', 300);

    expect(Link::resolveHash('XyZaBc'))->toBe('https://example.com/cached');
});

test('findOrCreateByUrl returns existing link on duplicate fingerprint', function () {
    User::factory()->system()->create();

    $link1 = Link::findOrCreateByUrl('https://example.com/dup', null);
    $link2 = Link::findOrCreateByUrl('https://example.com/dup', null);

    expect($link1->id)->toBe($link2->id);
    expect(Link::count())->toBe(1);
});
