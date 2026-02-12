<?php

test('privacy page is accessible', function () {
    $this->get(route('privacy'))->assertOk();
});

test('terms page is accessible', function () {
    $this->get(route('terms'))->assertOk();
});

test('about page is accessible', function () {
    $this->get(route('about'))->assertOk();
});

test('footer contains links to static pages', function () {
    $this->get(route('home'))
        ->assertSee(route('privacy'))
        ->assertSee(route('terms'))
        ->assertSee(route('about'));
});
