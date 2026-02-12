<?php

test('integrate page is accessible', function () {
    $this->get(route('integrate'))->assertOk();
});

test('integrate page contains snippet content', function () {
    $this->get(route('integrate'))
        ->assertSee(config('app.url').'/?')
        ->assertSee('anonymizeLinks');
});
