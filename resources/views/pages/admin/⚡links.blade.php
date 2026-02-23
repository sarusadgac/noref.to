<?php

use App\Models\Link;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Links Admin')] class extends Component {
    use WithPagination;

    #[Url]
    public string $searchHash = '';

    #[Url]
    public string $searchDomain = '';

    #[Url]
    public string $searchPath = '';

    #[Url]
    public string $searchCreator = '';

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

    public function updatedSearchCreator(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['searchHash', 'searchDomain', 'searchPath', 'searchCreator']);
        $this->resetPage();
    }

    public function deleteLink(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $link = Link::findOrFail($id);
        $link->clearCache();
        $link->delete();

        session()->flash('message', __('Link deleted successfully.'));
    }

    public function with(): array
    {
        $query = Link::query()->with('creator')->latest();

        if ($this->searchHash) {
            $query->where('hash', $this->searchHash);
        }

        if ($this->searchDomain) {
            $query->where('host', 'like', '%' . addcslashes($this->searchDomain, '%_\\') . '%');
        }

        if ($this->searchPath) {
            $query->where('path', 'like', '%' . addcslashes($this->searchPath, '%_\\') . '%');
        }

        if ($this->searchCreator) {
            if (is_numeric($this->searchCreator)) {
                $query->createdBy((int) $this->searchCreator);
            } else {
                $search = '%' . addcslashes($this->searchCreator, '%_\\') . '%';
                $query->whereHas('creator', fn ($q) => $q->where('name', 'like', $search));
            }
        }

        return [
            'links' => $query->paginate(50),
        ];
    }
}; ?>

<div>
    <h1 class="!font-heading !font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Links Admin') }}</h1>

    @if (session('message'))
        <flux:callout variant="success" class="mb-4">
            <flux:callout.text>{{ session('message') }}</flux:callout.text>
        </flux:callout>
    @endif

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
        <div class="flex-1 min-w-40">
            <flux:input wire:model.live.debounce.300ms="searchCreator" placeholder="{{ __('Creator') }}" size="sm" />
        </div>
        <flux:button wire:click="resetFilters" variant="subtle" size="sm">{{ __('Reset') }}</flux:button>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700/50">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
            <thead class="bg-zinc-100 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Hash') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Destination URL') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Creator') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Created') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}</th>
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
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $link->creator?->name ?? __('System') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $link->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <flux:button
                                wire:click="deleteLink({{ $link->id }})"
                                wire:confirm="{{ __('Are you sure you want to delete this link?') }}"
                                variant="danger"
                                size="xs"
                            >
                                {{ __('Delete') }}
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
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
