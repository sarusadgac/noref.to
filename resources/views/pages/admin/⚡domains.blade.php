<?php

use App\Models\Domain;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Domains Admin')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    public string $newHost = '';
    public bool $newIsAllowed = true;
    public string $newReason = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Add a new domain to the list.
     */
    public function addDomain(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $this->validate([
            'newHost' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9\-]*[a-z0-9])?)*$/i'],
            'newReason' => ['nullable', 'string', 'max:255'],
        ]);

        $host = strtolower(trim($this->newHost));

        if (Domain::where('host', $host)->exists()) {
            $this->addError('newHost', __('This domain already exists.'));
            return;
        }

        $domain = Domain::create([
            'host' => $host,
            'is_allowed' => $this->newIsAllowed,
            'reason' => $this->newReason ?: null,
        ]);

        $domain->clearCache();

        $this->reset(['newHost', 'newIsAllowed', 'newReason']);
        $this->newIsAllowed = true;

        session()->flash('message', __('Domain added successfully.'));
    }

    /**
     * Toggle a domain's is_allowed status.
     */
    public function toggleDomain(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $domain = Domain::findOrFail($id);
        $domain->update(['is_allowed' => ! $domain->is_allowed]);
        $domain->clearCache();

        session()->flash('message', __('Domain status updated.'));
    }

    /**
     * Delete a domain from the list.
     */
    public function deleteDomain(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $domain = Domain::findOrFail($id);
        $domain->clearCache();
        $domain->delete();

        session()->flash('message', __('Domain deleted successfully.'));
    }

    public function with(): array
    {
        $query = Domain::query()->latest();

        if ($this->search) {
            $query->where('host', 'like', '%' . addcslashes($this->search, '%_\\') . '%');
        }

        return [
            'domains' => $query->paginate(20),
        ];
    }
}; ?>

<div>
    <h1 class="!font-heading !font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Domains Admin') }}</h1>

    @if (session('message'))
        <flux:callout variant="success" class="mb-4">
            <flux:callout.text>{{ session('message') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Add Domain Form --}}
    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900/50 p-4 mb-6">
        <h2 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">{{ __('Add Domain') }}</h2>
        <form wire:submit="addDomain" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-48">
                <flux:input wire:model="newHost" placeholder="{{ __('example.com') }}" size="sm" label="{{ __('Host') }}" />
            </div>
            <div class="min-w-40">
                <flux:input wire:model="newReason" placeholder="{{ __('Optional reason') }}" size="sm" label="{{ __('Reason') }}" />
            </div>
            <div class="flex items-center gap-2">
                <flux:switch wire:model="newIsAllowed" label="{{ __('Allowed') }}" />
            </div>
            <flux:button variant="primary" type="submit" size="sm">{{ __('Add') }}</flux:button>
        </form>
        @error('newHost')
            <p class="text-red-600 dark:text-red-400 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    {{-- Search --}}
    <div class="mb-6 flex gap-3">
        <div class="flex-1 max-w-xs">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search domains...') }}" size="sm" />
        </div>
    </div>

    {{-- Domains Table --}}
    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700/50">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
            <thead class="bg-zinc-100 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Host') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Reason') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Created') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50">
                @forelse ($domains as $domain)
                    <tr wire:key="domain-{{ $domain->id }}">
                        <td class="px-4 py-3 text-sm font-mono-accent text-zinc-900 dark:text-zinc-100">
                            {{ $domain->host }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if ($domain->is_allowed)
                                <flux:badge variant="pill" color="lime" size="sm">{{ __('Allowed') }}</flux:badge>
                            @else
                                <flux:badge variant="pill" color="red" size="sm">{{ __('Blocked') }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-xs truncate">
                            {{ $domain->reason ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $domain->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex gap-2">
                                <flux:button
                                    wire:click="toggleDomain({{ $domain->id }})"
                                    variant="subtle"
                                    size="xs"
                                >
                                    {{ $domain->is_allowed ? __('Block') : __('Allow') }}
                                </flux:button>
                                <flux:button
                                    wire:click="deleteDomain({{ $domain->id }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this domain?') }}"
                                    variant="danger"
                                    size="xs"
                                >
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No domains found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <flux:pagination :paginator="$domains" />
    </div>
</div>
