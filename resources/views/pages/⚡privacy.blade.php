<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Privacy Policy')] class extends Component {
}; ?>

<div class="mx-auto max-w-2xl py-8 animate-fade-in">
    <h1 class="font-heading font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Privacy Policy') }}</h1>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-8">{{ __('Your privacy matters. Here\'s how we protect it.') }}</p>

    <div class="space-y-6">
        {{-- Our Privacy Commitment --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Our Privacy Commitment') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('noref.to is built with privacy as its foundation. We do not track you, we do not run analytics, and we do not monetize your data. Our goal is simple: provide a fast, anonymous URL shortening and redirect service.') }}</p>
        </section>

        {{-- Data We Collect --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Data We Collect') }}</h2>
            <ul class="list-disc list-inside text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                <li>{{ __('Shortened links: a URL hash and the destination URL.') }}</li>
                <li>{{ __('Optional accounts: your name and email address, only if you choose to register.') }}</li>
            </ul>
        </section>

        {{-- Data We Do Not Collect --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Data We Do Not Collect') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed mb-2">{{ __('We deliberately do not collect:') }}</p>
            <ul class="list-disc list-inside text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                <li>{{ __('Click counts or link analytics') }}</li>
                <li>{{ __('Visitor IP addresses') }}</li>
                <li>{{ __('Browser fingerprints or user agents') }}</li>
                <li>{{ __('Geographic or location data') }}</li>
                <li>{{ __('Referrer information') }}</li>
            </ul>
        </section>

        {{-- Referrer Stripping --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Referrer Stripping') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('All redirects through noref.to strip the referring page. The destination site will never know where you came from.') }}</p>
        </section>

        {{-- Cookies --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Cookies') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('We only use essential session cookies required for the site to function. We do not use tracking cookies, advertising cookies, or any third-party cookies.') }}</p>
        </section>

        {{-- Third-Party Services --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Third-Party Services') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('We use Cloudflare Turnstile solely on the abuse report page to prevent spam submissions. No other third-party tracking or analytics services are used.') }}</p>
        </section>

        {{-- Data Retention --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Data Retention') }}</h2>
            <ul class="list-disc list-inside text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                <li>{{ __('Shortened links are stored until they are removed by an administrator or the account holder.') }}</li>
                <li>{{ __('Account data is retained while your account exists.') }}</li>
            </ul>
        </section>

        {{-- Your Rights --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Your Rights') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('You may request deletion of your account and any associated links at any time. Contact us and we will remove your data promptly.') }}</p>
        </section>

        {{-- Changes to This Policy --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Changes to This Policy') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('We may update this policy from time to time. Any changes will be reflected on this page.') }}</p>
        </section>
    </div>
</div>
