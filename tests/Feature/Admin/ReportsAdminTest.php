<?php

use App\Enums\ReportStatus;
use App\Models\Domain;
use App\Models\Link;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

test('non-admin users get 403', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.reports'))
        ->assertForbidden();
});

test('guest users are redirected to login', function () {
    $this->get(route('admin.reports'))
        ->assertRedirect(route('login'));
});

test('admin can access reports page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.reports'))
        ->assertOk();
});

test('admin sees reports with link details', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create();
    $report = Report::factory()->create(['link_id' => $link->id]);

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->assertSee($link->hash)
        ->assertSee($report->email);
});

test('deleted link shows DELETED in reports', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create();
    $report = Report::factory()->create(['link_id' => $link->id]);

    $link->delete();

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->assertSee('DELETED')
        ->assertSee($report->email);
});

test('admin can resolve a report', function () {
    $admin = User::factory()->admin()->create();
    $report = Report::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->call('resolveReport', $report->id);

    $report->refresh();
    expect($report->status)->toBe(ReportStatus::Resolved);
    expect($report->resolved_by)->toBe($admin->id);
    expect($report->resolved_at)->not->toBeNull();
});

test('admin can dismiss a report', function () {
    $admin = User::factory()->admin()->create();
    $report = Report::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->call('dismissReport', $report->id);

    $report->refresh();
    expect($report->status)->toBe(ReportStatus::Dismissed);
    expect($report->resolved_by)->toBe($admin->id);
    expect($report->resolved_at)->not->toBeNull();
});

test('admin can delete a reported link', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create();
    $report = Report::factory()->create(['link_id' => $link->id]);

    Link::resolveHash($link->hash);
    expect(Cache::has('link:'.$link->hash))->toBeTrue();

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->call('deleteReportedLink', $report->id);

    expect(Link::find($link->id))->toBeNull();
    expect(Cache::has('link:'.$link->hash))->toBeFalse();

    $report->refresh();
    expect($report->status)->toBe(ReportStatus::Resolved);
});

test('admin can block a reported domain', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create(['host' => 'evil.example.com']);
    $report = Report::factory()->create(['link_id' => $link->id]);

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->call('blockReportedDomain', $report->id);

    $domain = Domain::where('host', 'evil.example.com')->first();
    expect($domain)->not->toBeNull();
    expect($domain->is_allowed)->toBeFalse();

    $report->refresh();
    expect($report->status)->toBe(ReportStatus::Resolved);
});

test('blocking a domain that already exists does not duplicate it', function () {
    $admin = User::factory()->admin()->create();
    Domain::factory()->create(['host' => 'evil.example.com', 'is_allowed' => false]);

    $link = Link::factory()->create(['host' => 'evil.example.com']);
    $report = Report::factory()->create(['link_id' => $link->id]);

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->call('blockReportedDomain', $report->id);

    expect(Domain::where('host', 'evil.example.com')->count())->toBe(1);
});

test('blocking a previously allowed domain sets it to blocked', function () {
    $admin = User::factory()->admin()->create();
    Domain::factory()->create(['host' => 'allowed.example.com', 'is_allowed' => true]);

    $link = Link::factory()->create(['host' => 'allowed.example.com']);
    $report = Report::factory()->create(['link_id' => $link->id]);

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->call('blockReportedDomain', $report->id);

    $domain = Domain::where('host', 'allowed.example.com')->first();
    expect($domain->is_allowed)->toBeFalse();
});

test('non-admin cannot call report actions', function () {
    $user = User::factory()->create();
    $report = Report::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::admin.reports')
        ->call('resolveReport', $report->id)
        ->assertForbidden();

    expect($report->fresh()->status)->toBe(ReportStatus::Pending);
});

test('already resolved report cannot be acted on again', function () {
    $admin = User::factory()->admin()->create();
    $report = Report::factory()->create([
        'status' => ReportStatus::Resolved,
        'resolved_by' => $admin->id,
        'resolved_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->call('resolveReport', $report->id);

    expect($report->fresh()->status)->toBe(ReportStatus::Resolved);
});

test('status filter shows correct reports', function () {
    $admin = User::factory()->admin()->create();

    $pending = Report::factory()->create(['status' => ReportStatus::Pending]);
    $resolved = Report::factory()->create([
        'status' => ReportStatus::Resolved,
        'resolved_by' => $admin->id,
        'resolved_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->set('filterStatus', 'pending')
        ->assertSee($pending->email)
        ->assertDontSee($resolved->email);

    Livewire::actingAs($admin)
        ->test('pages::admin.reports')
        ->set('filterStatus', 'resolved')
        ->assertDontSee($pending->email)
        ->assertSee($resolved->email);
});
