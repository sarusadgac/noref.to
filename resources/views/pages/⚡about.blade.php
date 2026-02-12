<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('About')] class extends Component {
}; ?>

<div class="mx-auto max-w-2xl py-8 animate-fade-in">
    <h1 class="font-heading font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-2">{{ __('About') }}</h1>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-8">{{ __('Learn more about anon.to and why it exists.') }}</p>

    <div class="space-y-6">
        {{-- What is anon.to? --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('What is anon.to?') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('anon.to is a free, privacy-first URL shortener and anonymous redirect service. It lets you create short links or redirect through any URL without exposing your identity or browsing habits to the destination site.') }}</p>
        </section>

        {{-- Why anon.to Exists --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Why anon.to Exists') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('Online privacy is eroding. Every link you click can reveal where you came from, what you were reading, and who you are. anon.to breaks that tracking chain. When you redirect through us, the destination sees our server — not you.') }}</p>
        </section>

        {{-- How It Works --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('How It Works') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed mb-2">{{ __('anon.to offers two modes:') }}</p>
            <ul class="list-disc list-inside text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                <li>{{ __('Shorten mode — paste a URL and get a short link. We store the mapping and redirect visitors anonymously.') }}</li>
                <li>{{ __('Direct Redirect mode — use the format anon.to/?url to redirect instantly. Nothing is stored, nothing is logged.') }}</li>
            </ul>
        </section>

        {{-- What Makes Us Different --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('What Makes Us Different') }}</h2>
            <ul class="list-disc list-inside text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                <li>{{ __('No click tracking or analytics — we don\'t count visits or monitor traffic.') }}</li>
                <li>{{ __('No referrer leaking — destination sites never see where you came from.') }}</li>
                <li>{{ __('No third-party trackers — no ads, no pixels, no external scripts besides the abuse report captcha.') }}</li>
                <li>{{ __('Minimal data — we store only what\'s needed to make the redirect work.') }}</li>
            </ul>
        </section>

        {{-- Open Abuse Reporting --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Open Abuse Reporting') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('Privacy doesn\'t mean impunity. Anyone can report a link through our') }} <a href="{{ route('report') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline" wire:navigate>{{ __('report page') }}</a>. {{ __('Reported links are reviewed and removed if they violate our terms.') }}</p>
        </section>
    </div>
</div>
