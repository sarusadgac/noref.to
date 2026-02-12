<?php

use App\Models\Link;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

test('non-admin users get 403', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.links'))
        ->assertForbidden();
});

test('guest users are redirected to login', function () {
    $this->get(route('admin.links'))
        ->assertRedirect(route('login'));
});

test('admin can access links admin page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.links'))
        ->assertOk();
});

test('admin sees all links from all users', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $adminLink = Link::factory()->create(['created_by' => $admin->id]);
    $userLink = Link::factory()->create(['created_by' => $user->id]);

    Livewire::actingAs($admin)
        ->test('pages::admin.links')
        ->assertSee($adminLink->hash)
        ->assertSee($userLink->hash);
});

test('admin can search links by hash', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.links')
        ->set('searchHash', $link->hash)
        ->assertSee($link->hash);
});

test('admin can delete a link and cache is cleared', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create();

    // Prime the cache
    Link::resolveHash($link->hash);
    expect(Cache::has('link:'.$link->hash))->toBeTrue();

    Livewire::actingAs($admin)
        ->test('pages::admin.links')
        ->call('deleteLink', $link->id);

    expect(Link::find($link->id))->toBeNull();
    expect(Cache::has('link:'.$link->hash))->toBeFalse();
});

test('non-admin cannot call deleteLink action', function () {
    $user = User::factory()->create();
    $link = Link::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::admin.links')
        ->call('deleteLink', $link->id)
        ->assertForbidden();

    expect(Link::find($link->id))->not->toBeNull();
});
