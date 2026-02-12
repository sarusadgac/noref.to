<?php

use App\Models\Link;
use App\Models\User;
use Livewire\Livewire;

test('guest users are redirected to login', function () {
    $this->get(route('admin.users'))
        ->assertRedirect(route('login'));
});

test('non-admin users get 403', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.users'))
        ->assertForbidden();
});

test('admin can access users page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.users'))
        ->assertOk();
});

test('admin sees user list with link counts', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    Link::factory()->count(3)->create(['created_by' => $user->id]);

    Livewire::actingAs($admin)
        ->test('pages::admin.users')
        ->assertSee($user->name)
        ->assertSee($user->email)
        ->assertSee('3');
});

test('admin can search users by name', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Smith']);

    Livewire::actingAs($admin)
        ->test('pages::admin.users')
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

test('admin can search users by email', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['email' => 'unique-test-email@example.com']);

    Livewire::actingAs($admin)
        ->test('pages::admin.users')
        ->set('search', 'unique-test-email')
        ->assertSee('unique-test-email@example.com');
});

test('admin can delete a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.users')
        ->call('deleteUser', $user->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('admin cannot delete themselves', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.users')
        ->call('deleteUser', $admin->id);

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('admin cannot delete the system user', function () {
    $admin = User::factory()->admin()->create();
    $systemUser = User::factory()->system()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.users')
        ->call('deleteUser', $systemUser->id);

    $this->assertDatabaseHas('users', ['id' => $systemUser->id]);
});

test('non-admin cannot call deleteUser', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::admin.users')
        ->call('deleteUser', $target->id)
        ->assertForbidden();
});
