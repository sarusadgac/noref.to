<?php

use App\Enums\ReportStatus;
use App\Models\Domain;
use App\Models\Report;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Reports Admin')] class extends Component {
    use WithPagination;

    #[Url]
    public string $filterStatus = '';

    public ?int $selectedReportId = null;

    public function viewReport(int $id): void
    {
        $this->selectedReportId = $id;
        $this->modal('report-detail')->show();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resolveReport(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $report = Report::findOrFail($id);

        if ($report->status !== ReportStatus::Pending) {
            return;
        }

        $report->update([
            'status' => ReportStatus::Resolved,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        session()->flash('message', __('Report resolved.'));
    }

    public function dismissReport(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $report = Report::findOrFail($id);

        if ($report->status !== ReportStatus::Pending) {
            return;
        }

        $report->update([
            'status' => ReportStatus::Dismissed,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        session()->flash('message', __('Report dismissed.'));
    }

    public function deleteReportedLink(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $report = Report::with('link')->findOrFail($id);

        if ($report->status !== ReportStatus::Pending || $report->link === null) {
            return;
        }

        $report->link->clearCache();
        $report->link->delete();

        $report->update([
            'status' => ReportStatus::Resolved,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        session()->flash('message', __('Link deleted and report resolved.'));
    }

    public function blockReportedDomain(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $report = Report::with('link')->findOrFail($id);

        if ($report->status !== ReportStatus::Pending || $report->link === null) {
            return;
        }

        $host = $report->link->host;
        $domain = Domain::firstOrCreate(
            ['host' => $host],
            ['is_allowed' => false],
        );

        if ($domain->is_allowed) {
            $domain->update(['is_allowed' => false]);
        }

        $domain->clearCache();

        $report->update([
            'status' => ReportStatus::Resolved,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        session()->flash('message', __('Domain blocked and report resolved.'));
    }

    public function with(): array
    {
        $query = Report::query()->with(['link', 'resolver']);

        if ($this->filterStatus && ReportStatus::tryFrom($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")->latest();

        return [
            'reports' => $query->paginate(20),
        ];
    }
}; ?>

<div>
    <h1 class="!font-heading !font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Reports Admin') }}</h1>

    @if (session('message'))
        <flux:callout variant="success" class="mb-4">
            <flux:callout.text>{{ session('message') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" class="mb-4">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Status Filter --}}
    <div class="mb-6 flex flex-wrap gap-2">
        <flux:button wire:click="$set('filterStatus', '')" :variant="$filterStatus === '' ? 'primary' : 'subtle'" size="sm">
            {{ __('All') }}
        </flux:button>
        <flux:button wire:click="$set('filterStatus', 'pending')" :variant="$filterStatus === 'pending' ? 'primary' : 'subtle'" size="sm">
            {{ __('Pending') }}
        </flux:button>
        <flux:button wire:click="$set('filterStatus', 'resolved')" :variant="$filterStatus === 'resolved' ? 'primary' : 'subtle'" size="sm">
            {{ __('Resolved') }}
        </flux:button>
        <flux:button wire:click="$set('filterStatus', 'dismissed')" :variant="$filterStatus === 'dismissed' ? 'primary' : 'subtle'" size="sm">
            {{ __('Dismissed') }}
        </flux:button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700/50">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
            <thead class="bg-zinc-100 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Reported URL') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Hash') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Comment') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Reporter') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50">
                @forelse ($reports as $report)
                    <tr wire:key="report-{{ $report->id }}">
                        <td class="px-4 py-3 text-sm max-w-xs truncate">
                            @if ($report->link)
                                <a href="{{ $report->link->destination_url }}" target="_blank" rel="noreferrer nofollow" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors duration-150">
                                    {{ $report->link->destination_url }}
                                </a>
                            @else
                                <span class="text-zinc-500 italic">{{ __('DELETED') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-mono-accent">
                            @if ($report->link)
                                <a href="{{ route('admin.links', ['searchHash' => $report->link->hash]) }}" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors duration-150" wire:navigate>
                                    {{ $report->link->hash }}
                                </a>
                            @else
                                <span class="text-zinc-500 italic">{{ __('DELETED') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-xs truncate cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors duration-150" wire:click="viewReport({{ $report->id }})">
                            {{ $report->comment }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if ($report->status === App\Enums\ReportStatus::Pending)
                                <flux:badge variant="pill" color="yellow" size="sm">{{ __('Pending') }}</flux:badge>
                            @elseif ($report->status === App\Enums\ReportStatus::Resolved)
                                <flux:badge variant="pill" color="lime" size="sm">{{ __('Resolved') }}</flux:badge>
                            @else
                                <flux:badge variant="pill" size="sm">{{ __('Dismissed') }}</flux:badge>
                            @endif
                            @if ($report->resolver && $report->resolved_at)
                                <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                                    {{ $report->resolver->name }} &middot; {{ $report->resolved_at->diffForHumans() }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $report->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $report->email }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if ($report->status === App\Enums\ReportStatus::Pending)
                                <flux:dropdown>
                                    <flux:button variant="subtle" size="xs" icon-trailing="chevron-down">
                                        {{ __('Actions') }}
                                    </flux:button>

                                    <flux:menu>
                                        <flux:menu.item wire:click="resolveReport({{ $report->id }})" icon="check-circle">
                                            {{ __('Resolve') }}
                                        </flux:menu.item>
                                        <flux:menu.item wire:click="dismissReport({{ $report->id }})" icon="x-circle">
                                            {{ __('Dismiss') }}
                                        </flux:menu.item>
                                        @if ($report->link)
                                            <flux:menu.separator />
                                            <flux:menu.item wire:click="deleteReportedLink({{ $report->id }})" wire:confirm="{{ __('Are you sure you want to delete this link?') }}" icon="trash" variant="danger">
                                                {{ __('Delete Link') }}
                                            </flux:menu.item>
                                            <flux:menu.item wire:click="blockReportedDomain({{ $report->id }})" wire:confirm="{{ __('Are you sure you want to block this domain?') }}" icon="no-symbol">
                                                {{ __('Block Domain') }}
                                            </flux:menu.item>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No reports found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <flux:pagination :paginator="$reports" />
    </div>

    {{-- Report Detail Modal --}}
    @php($selectedReport = $selectedReportId ? $reports->firstWhere('id', $selectedReportId) : null)

    <flux:modal name="report-detail">
        @if ($selectedReport)
            <div class="space-y-4">
                <flux:heading size="lg">{{ __('Report Details') }}</flux:heading>

                <div>
                    <div class="text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400 mb-1">{{ __('Reported URL') }}</div>
                    @if ($selectedReport->link)
                        <a href="{{ $selectedReport->link->destination_url }}" target="_blank" rel="noreferrer nofollow" class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 break-all transition-colors duration-150">
                            {{ $selectedReport->link->destination_url }}
                        </a>
                    @else
                        <span class="text-sm text-zinc-500 italic">{{ __('DELETED') }}</span>
                    @endif
                </div>

                <div>
                    <div class="text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400 mb-1">{{ __('Comment') }}</div>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">{{ $selectedReport->comment }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400 mb-1">{{ __('Reporter') }}</div>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $selectedReport->email }}</p>
                    </div>
                    <div>
                        <div class="text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400 mb-1">{{ __('Date') }}</div>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $selectedReport->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                </div>

                @if ($selectedReport->resolver && $selectedReport->resolved_at)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400 mb-1">{{ __('Resolved By') }}</div>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $selectedReport->resolver->name }}</p>
                        </div>
                        <div>
                            <div class="text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400 mb-1">{{ __('Resolved At') }}</div>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $selectedReport->resolved_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </flux:modal>
</div>
