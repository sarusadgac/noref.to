<?php

use App\Enums\ReportStatus;
use App\Models\Domain;
use App\Models\Link;
use App\Models\Report;
use App\Models\User;
use Livewire\Livewire;

test('guest users are redirected to login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('non-admin users get 403', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('admin can access dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

test('admin sees stat counts', function () {
    $admin = User::factory()->admin()->create();

    Link::factory()->count(3)->create();
    Report::factory()->count(2)->create();
    Domain::factory()->create(['is_allowed' => false]);

    Livewire::actingAs($admin)
        ->test('pages::admin.dashboard')
        ->assertSee('Total Links')
        ->assertSee('Total Users')
        ->assertSee('Pending Reports')
        ->assertSee('Blocked Domains')
        ->assertSee('Links Today')
        ->assertSee('Links This Week');
});

test('admin sees recent links and pending reports', function () {
    $admin = User::factory()->admin()->create();

    $link = Link::factory()->create();
    $report = Report::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.dashboard')
        ->assertSee($link->hash)
        ->assertSee($report->email);
});

test('dashboard shows only pending reports in recent section', function () {
    $admin = User::factory()->admin()->create();

    $pending = Report::factory()->create(['status' => ReportStatus::Pending]);
    $resolved = Report::factory()->create([
        'status' => ReportStatus::Resolved,
        'resolved_by' => $admin->id,
        'resolved_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test('pages::admin.dashboard')
        ->assertSee($pending->email)
        ->assertDontSee($resolved->email);
});

test('pending reports count highlights when greater than zero', function () {
    $admin = User::factory()->admin()->create();
    Report::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.dashboard')
        ->assertSee('text-yellow-600');
});
