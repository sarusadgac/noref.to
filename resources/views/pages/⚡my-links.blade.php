<?php

use App\Models\Link;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.public')] #[Title('My Links')] class extends Component {
    use WithPagination;

    public string $searchHash = '';
    public string $searchDomain = '';
    public string $searchPath = '';

    public function updatedSearchHash(): void
    {
        $this->resetPage();
    }

    public function updatedSearchDomain(): void
    {
        $this->resetPage();
    }

    public function updatedSearchPath(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['searchHash', 'searchDomain', 'searchPath']);
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Link::query()->createdBy(auth()->user())->latest();

        if ($this->searchHash) {
            $query->where('hash', $this->searchHash);
        }

        if ($this->searchDomain) {
            $query->where('host', 'like', '%' . addcslashes($this->searchDomain, '%_\\') . '%');
        }

        if ($this->searchPath) {
            $query->where('path', 'like', '%' . addcslashes($this->searchPath, '%_\\') . '%');
        }

        return [
            'links' => $query->paginate(50),
        ];
    }
}; ?>

<div>
    <h1 class="!font-heading !font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-6">{{ __('My Links') }}</h1>

    <div class="mb-6 flex flex-wrap gap-3">
        <div class="w-32">
            <flux:input wire:model.live.debounce.300ms="searchHash" placeholder="{{ __('Hash') }}" size="sm" />
        </div>
        <div class="flex-1 min-w-40">
            <flux:input wire:model.live.debounce.300ms="searchDomain" placeholder="{{ __('Domain') }}" size="sm" />
        </div>
        <div class="flex-1 min-w-40">
            <flux:input wire:model.live.debounce.300ms="searchPath" placeholder="{{ __('Path') }}" size="sm" />
        </div>
        <flux:button wire:click="resetFilters" variant="subtle" size="sm">{{ __('Reset') }}</flux:button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700/50">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
            <thead class="bg-zinc-100 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Hash') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Destination URL') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Created') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50">
                @forelse ($links as $link)
                    <tr wire:key="link-{{ $link->id }}">
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
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $link->created_at->diffForHumans() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No links found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <flux:pagination :paginator="$links" />
    </div>
</div>
