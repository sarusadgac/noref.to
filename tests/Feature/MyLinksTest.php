<?php

use App\Models\Link;
use App\Models\User;
use Livewire\Livewire;

test('guests cannot access my links page', function () {
    $response = $this->get(route('my-links'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can access my links page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('my-links'))
        ->assertOk();
});

test('user only sees their own links', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Link::factory()->count(3)->create(['created_by' => $user->id]);
    Link::factory()->count(2)->create(['created_by' => $otherUser->id]);

    Livewire::actingAs($user)
        ->test('pages::my-links')
        ->assertSee($user->links->first()->hash)
        ->assertDontSee($otherUser->links->first()->hash);
});

test('search by hash filters links', function () {
    $user = User::factory()->create();
    $link = Link::factory()->create(['created_by' => $user->id]);

    Livewire::actingAs($user)
        ->test('pages::my-links')
        ->set('searchHash', $link->hash)
        ->assertSee($link->hash);
});

test('search by domain filters links', function () {
    $user = User::factory()->create();
    $link = Link::factory()->withUrl('https://specific-domain.com/page')->create(['created_by' => $user->id]);

    Livewire::actingAs($user)
        ->test('pages::my-links')
        ->set('searchDomain', 'specific-domain')
        ->assertSee($link->hash);
});

test('reset filters clears search', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::my-links')
        ->set('searchHash', 'AbCdEf')
        ->set('searchDomain', 'example')
        ->call('resetFilters')
        ->assertSet('searchHash', '')
        ->assertSet('searchDomain', '')
        ->assertSet('searchPath', '');
});
