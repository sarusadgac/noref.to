<?php

use App\Models\Link;
use App\Models\Report;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    User::factory()->system()->create();
});

test('report page is accessible', function () {
    $this->get(route('report'))->assertOk();
});

test('user can submit a valid report for a short URL', function () {
    $link = Link::factory()->create();

    Livewire::test('pages::report')
        ->set('linkUrl', url('/'.$link->hash))
        ->set('email', 'reporter@example.com')
        ->set('comment', 'This is a spam link')
        ->set('captchaToken', 'test-token')
        ->call('submitReport')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    expect(Report::count())->toBe(1);
    expect(Report::first()->link_id)->toBe($link->id);
});

test('user can submit a valid report for a destination URL', function () {
    $link = Link::factory()->withUrl('https://spam-site.com/bad-page')->create();

    Livewire::test('pages::report')
        ->set('linkUrl', 'https://spam-site.com/bad-page')
        ->set('email', 'reporter@example.com')
        ->set('comment', 'Malicious content')
        ->set('captchaToken', 'test-token')
        ->call('submitReport')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    expect(Report::count())->toBe(1);
});

test('duplicate report for same link is rejected', function () {
    $link = Link::factory()->create();
    Report::factory()->create(['link_id' => $link->id]);

    Livewire::test('pages::report')
        ->set('linkUrl', url('/'.$link->hash))
        ->set('email', 'reporter@example.com')
        ->set('comment', 'Already reported')
        ->set('captchaToken', 'test-token')
        ->call('submitReport')
        ->assertHasErrors(['linkUrl']);

    expect(Report::count())->toBe(1);
});

test('report submission is rate limited', function () {

    for ($i = 0; $i < 5; $i++) {
        $link = Link::factory()->create();

        Livewire::test('pages::report')
            ->set('linkUrl', url('/'.$link->hash))
            ->set('email', 'reporter@example.com')
            ->set('comment', 'Report '.$i)
            ->set('captchaToken', 'test-token')
            ->call('submitReport')
            ->assertHasNoErrors();
    }

    $extraLink = Link::factory()->create();

    Livewire::test('pages::report')
        ->set('linkUrl', url('/'.$extraLink->hash))
        ->set('email', 'reporter@example.com')
        ->set('comment', 'Should be rate limited')
        ->set('captchaToken', 'test-token')
        ->call('submitReport')
        ->assertHasErrors(['linkUrl']);
});

test('report with unknown URL is rejected', function () {
    Livewire::test('pages::report')
        ->set('linkUrl', 'https://unknown-nonexistent-site.com')
        ->set('email', 'reporter@example.com')
        ->set('comment', 'Does not exist')
        ->set('captchaToken', 'test-token')
        ->call('submitReport')
        ->assertHasErrors(['linkUrl']);

    expect(Report::count())->toBe(0);
});
