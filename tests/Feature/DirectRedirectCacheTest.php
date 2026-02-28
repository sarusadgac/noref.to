<?php
use Illuminate\Support\Facades\Cache;

test('direct redirect increments cache counters', function () {
    Cache::clear();
    $response = $this->get('/?https://example.com/direct');
    $response->assertOk();
    $this->assertEquals(1, Cache::get('direct_redirects:total'));
});
