<?php

use App\Models\Domain;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

test('non-admin users get 403', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.domains'))
        ->assertForbidden();
});

test('guest users are redirected to login', function () {
    $this->get(route('admin.domains'))
        ->assertRedirect(route('login'));
});

test('admin can access domains admin page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.domains'))
        ->assertOk();
});

test('admin can add an allowed domain', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.domains')
        ->set('newHost', 'example.com')
        ->set('newIsAllowed', true)
        ->set('newReason', 'Trusted site')
        ->call('addDomain')
        ->assertHasNoErrors();

    expect(Domain::where('host', 'example.com')->first())
        ->not->toBeNull()
        ->is_allowed->toBeTrue()
        ->reason->toBe('Trusted site');
});

test('admin can add a blocked domain', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.domains')
        ->set('newHost', 'evil.com')
        ->set('newIsAllowed', false)
        ->set('newReason', 'Malicious')
        ->call('addDomain')
        ->assertHasNoErrors();

    expect(Domain::where('host', 'evil.com')->first())
        ->not->toBeNull()
        ->is_allowed->toBeFalse();
});

test('admin cannot add duplicate domain', function () {
    $admin = User::factory()->admin()->create();
    Domain::factory()->create(['host' => 'example.com']);

    Livewire::actingAs($admin)
        ->test('pages::admin.domains')
        ->set('newHost', 'example.com')
        ->call('addDomain')
        ->assertHasErrors(['newHost']);

    expect(Domain::where('host', 'example.com')->count())->toBe(1);
});

test('domain host is stored lowercase', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.domains')
        ->set('newHost', 'EXAMPLE.COM')
        ->call('addDomain')
        ->assertHasNoErrors();

    expect(Domain::first()->host)->toBe('example.com');
});

test('admin can toggle a domain status', function () {
    $admin = User::factory()->admin()->create();
    $domain = Domain::factory()->allowed()->create();

    // Prime cache
    Domain::isAllowed($domain->host);
    expect(Cache::has("domain:{$domain->host}"))->toBeTrue();

    Livewire::actingAs($admin)
        ->test('pages::admin.domains')
        ->call('toggleDomain', $domain->id);

    expect($domain->fresh()->is_allowed)->toBeFalse();
    expect(Cache::has("domain:{$domain->host}"))->toBeFalse();
});

test('admin can delete a domain and cache is cleared', function () {
    $admin = User::factory()->admin()->create();
    $domain = Domain::factory()->create();

    // Prime cache
    Domain::isAllowed($domain->host);
    expect(Cache::has("domain:{$domain->host}"))->toBeTrue();

    Livewire::actingAs($admin)
        ->test('pages::admin.domains')
        ->call('deleteDomain', $domain->id);

    expect(Domain::find($domain->id))->toBeNull();
    expect(Cache::has("domain:{$domain->host}"))->toBeFalse();
});

test('non-admin cannot call addDomain action', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::admin.domains')
        ->set('newHost', 'example.com')
        ->call('addDomain')
        ->assertForbidden();

    expect(Domain::count())->toBe(0);
});

test('non-admin cannot call toggleDomain action', function () {
    $user = User::factory()->create();
    $domain = Domain::factory()->allowed()->create();

    Livewire::actingAs($user)
        ->test('pages::admin.domains')
        ->call('toggleDomain', $domain->id)
        ->assertForbidden();

    expect($domain->fresh()->is_allowed)->toBeTrue();
});

test('non-admin cannot call deleteDomain action', function () {
    $user = User::factory()->create();
    $domain = Domain::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::admin.domains')
        ->call('deleteDomain', $domain->id)
        ->assertForbidden();

    expect(Domain::find($domain->id))->not->toBeNull();
});

test('admin can search domains by host', function () {
    $admin = User::factory()->admin()->create();
    Domain::factory()->create(['host' => 'example.com']);
    Domain::factory()->create(['host' => 'other.org']);

    Livewire::actingAs($admin)
        ->test('pages::admin.domains')
        ->set('search', 'example')
        ->assertSee('example.com')
        ->assertDontSee('other.org');
});

test('adding a domain immediately reflects in isAllowed checks', function () {
    $admin = User::factory()->admin()->create();

    // Unknown domain returns null
    expect(Domain::isAllowed('newdomain.com'))->toBeNull();

    // Admin blocks the domain
    Livewire::actingAs($admin)
        ->test('pages::admin.domains')
        ->set('newHost', 'newdomain.com')
        ->set('newIsAllowed', false)
        ->call('addDomain')
        ->assertHasNoErrors();

    // New status should be reflected immediately
    expect(Domain::isAllowed('newdomain.com'))->toBeFalse();
});
