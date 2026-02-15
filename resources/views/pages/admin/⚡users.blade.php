<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Users Admin')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteUser(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            session()->flash('error', __('You cannot delete yourself.'));
            return;
        }

        if ($user->email === config('anonto.system_user_email')) {
            session()->flash('error', __('You cannot delete the system user.'));
            return;
        }

        $user->delete();

        session()->flash('message', __('User deleted successfully.'));
    }

    public function with(): array
    {
        $query = User::query()->withCount('links');

        if ($this->search) {
            $search = '%' . addcslashes($this->search, '%_\\') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        $query->latest();

        return [
            'users' => $query->paginate(20),
        ];
    }
}; ?>

<div>
    <h1 class="!font-heading !font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Users Admin') }}</h1>

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

    {{-- Search --}}
    <div class="mb-6 flex gap-3">
        <div class="flex-1 max-w-xs">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or email...') }}" size="sm" />
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700/50">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
            <thead class="bg-zinc-100 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Name') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Email') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Role') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Links') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Joined') }}</th>
                    <th class="px-4 py-3 text-start text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50">
                @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $user->name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if ($user->role === App\Enums\UserRole::Admin)
                                <flux:badge variant="pill" color="purple" size="sm">{{ __('Admin') }}</flux:badge>
                            @else
                                <flux:badge variant="pill" size="sm">{{ __('User') }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('admin.links', ['searchCreator' => $user->id]) }}" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors duration-150" wire:navigate>
                                {{ $user->links_count }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $user->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if ($user->id !== auth()->id() && $user->email !== config('anonto.system_user_email'))
                                <flux:button
                                    wire:click="deleteUser({{ $user->id }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this user? Their links will become unowned.') }}"
                                    variant="danger"
                                    size="xs"
                                >
                                    {{ __('Delete') }}
                                </flux:button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No users found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <flux:pagination :paginator="$users" />
    </div>
</div>
