<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('creates admin with correct role and verified email', function () {
    $this->artisan('app:create-admin', [
        '--name' => 'Test Admin',
        '--email' => 'admin@example.com',
    ])
        ->expectsQuestion('Password', 'secret-password')
        ->assertSuccessful();

    $user = User::where('email', 'admin@example.com')->first();

    expect($user)
        ->not->toBeNull()
        ->role->toBe(UserRole::Admin)
        ->email_verified_at->not->toBeNull();
});

test('password is not double-hashed', function () {
    $this->artisan('app:create-admin', [
        '--name' => 'Hash Test',
        '--email' => 'hash@example.com',
    ])
        ->expectsQuestion('Password', 'my-secret-pass')
        ->assertSuccessful();

    $user = User::where('email', 'hash@example.com')->first();

    expect(Hash::check('my-secret-pass', $user->password))->toBeTrue();
});

test('rejects duplicate email with failure exit code', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->artisan('app:create-admin', [
        '--name' => 'Duplicate',
        '--email' => 'existing@example.com',
    ])
        ->expectsQuestion('Password', 'any-password')
        ->assertFailed();
});
