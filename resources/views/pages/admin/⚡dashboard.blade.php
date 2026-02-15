<?php

use App\Enums\ReportStatus;
use App\Models\Domain;
use App\Models\Link;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Admin Dashboard')] class extends Component {
    public function with(): array
    {
        $stats = Cache::remember('admin:dashboard:stats', 60, fn () => [
            'totalLinks' => Link::count(),
            'totalUsers' => User::count(),
            'pendingReports' => Report::pending()->count(),
            'blockedDomains' => Domain::where('is_allowed', false)->count(),
            'linksToday' => Link::whereDate('created_at', today())->count(),
            'linksThisWeek' => Link::where('created_at', '>=', now()->startOfWeek())->count(),
        ]);

        return [
            ...$stats,
            'topReportedDomains' => Report::query()
                ->join('links', 'reports.link_id', '=', 'links.id')
                ->where('reports.status', ReportStatus::Pending)
                ->select('links.host', DB::raw('count(*) as report_count'))
                ->groupBy('links.host')
                ->orderByDesc('report_count')
                ->limit(5)
                ->get(),
            'recentLinks' => Link::query()->with('creator')->latest()->limit(5)->get(),
            'recentReports' => Report::query()->with('link')->pending()->latest()->limit(5)->get(),
        ];
    }
}; ?>

<div>
    <h1 class="!font-heading !font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Dashboard') }}</h1>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.links') }}" class="rounded-lg border border-zinc-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900/50 p-4 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors duration-150" wire:navigate>
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Links') }}</div>
            <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalLinks) }}</div>
        </a>
        <a href="{{ route('admin.users') }}" class="rounded-lg border border-zinc-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900/50 p-4 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors duration-150" wire:navigate>
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Users') }}</div>
            <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalUsers) }}</div>
        </a>
        <a href="{{ route('admin.reports', ['filterStatus' => 'pending']) }}" class="rounded-lg border border-zinc-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900/50 p-4 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors duration-150" wire:navigate>
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Pending Reports') }}</div>
            <div class="mt-1 text-2xl font-bold {{ $pendingReports > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-zinc-900 dark:text-zinc-100' }}">{{ number_format($pendingReports) }}</div>
        </a>
        <a href="{{ route('admin.domains') }}" class="rounded-lg border border-zinc-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900/50 p-4 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors duration-150" wire:navigate>
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Blocked Domains') }}</div>
            <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($blockedDomains) }}</div>
        </a>
    </div>

    {{-- Secondary Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900/50 p-4">
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Links Today') }}</div>
            <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($linksToday) }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900/50 p-4">
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Links This Week') }}</div>
            <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($linksThisWeek) }}</div>
        </div>
    </div>

    {{-- Recent Links --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Recent Links') }}</h2>
            <a href="{{ route('admin.links') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors duration-150" wire:navigate>{{ __('View All') }}</a>
        </div>
        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700/50">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
                <thead class="bg-zinc-100 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Hash') }}</th>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Destination URL') }}</th>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Creator') }}</th>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Created') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50">
                    @forelse ($recentLinks as $link)
                        <tr wire:key="recent-link-{{ $link->id }}">
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ url('/' . $link->hash) }}" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 font-mono-accent transition-colors duration-150">
                                    {{ $link->hash }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm max-w-xs truncate">
                                <a href="{{ $link->destination_url }}" target="_blank" rel="noreferrer nofollow" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors duration-150">
                                    {{ $link->destination_url }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $link->creator?->name ?? __('System') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                {{ $link->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('No links yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Pending Reports --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Recent Pending Reports') }}</h2>
            <a href="{{ route('admin.reports') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors duration-150" wire:navigate>{{ __('View All') }}</a>
        </div>
        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700/50">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
                <thead class="bg-zinc-100 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Reported URL') }}</th>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Comment') }}</th>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Reporter') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50">
                    @forelse ($recentReports as $report)
                        <tr wire:key="recent-report-{{ $report->id }}">
                            <td class="px-4 py-3 text-sm max-w-xs truncate">
                                @if ($report->link)
                                    <a href="{{ $report->link->destination_url }}" target="_blank" rel="noreferrer nofollow" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors duration-150">
                                        {{ $report->link->destination_url }}
                                    </a>
                                @else
                                    <span class="text-zinc-500 italic">{{ __('DELETED') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-xs truncate">
                                {{ $report->comment }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                {{ $report->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $report->email }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('No pending reports.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Reported Domains --}}
    @if ($topReportedDomains->isNotEmpty())
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Top Reported Domains') }}</h2>
                <a href="{{ route('admin.domains') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors duration-150" wire:navigate>{{ __('Manage Domains') }}</a>
            </div>
            <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700/50">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
                    <thead class="bg-zinc-100 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Domain') }}</th>
                            <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Pending Reports') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50">
                        @foreach ($topReportedDomains as $reported)
                            <tr wire:key="top-domain-{{ $reported->host }}">
                                <td class="px-4 py-3 text-sm font-mono-accent text-zinc-900 dark:text-zinc-100">
                                    <a href="{{ route('admin.domains', ['search' => $reported->host]) }}" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors duration-150" wire:navigate>
                                        {{ $reported->host }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $reported->report_count }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
