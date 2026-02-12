<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Terms of Service')] class extends Component {
}; ?>

<div class="mx-auto max-w-2xl py-8 animate-fade-in">
    <h1 class="font-heading font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Terms of Service') }}</h1>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-8">{{ __('Please read these terms carefully before using anon.to.') }}</p>

    <div class="space-y-6">
        {{-- Acceptance of Terms --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Acceptance of Terms') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('By accessing or using anon.to, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the service.') }}</p>
        </section>

        {{-- Description of Service --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Description of Service') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('anon.to provides a free, privacy-first URL shortening and anonymous redirect service. We allow users to create shortened links and redirect through URLs without exposing referrer information.') }}</p>
        </section>

        {{-- Acceptable Use --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Acceptable Use') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('You may use anon.to for lawful purposes only. You are responsible for all content you link to through our service.') }}</p>
        </section>

        {{-- Prohibited Content --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Prohibited Content') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed mb-2">{{ __('You may not use anon.to to link to content that:') }}</p>
            <ul class="list-disc list-inside text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                <li>{{ __('Distributes malware, viruses, or other harmful software') }}</li>
                <li>{{ __('Facilitates phishing or fraud') }}</li>
                <li>{{ __('Contains child sexual abuse material (CSAM)') }}</li>
                <li>{{ __('Infringes on copyright or intellectual property rights') }}</li>
                <li>{{ __('Is used for spam or unsolicited bulk messaging') }}</li>
                <li>{{ __('Promotes hate, violence, or discrimination') }}</li>
            </ul>
        </section>

        {{-- Link Removal --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Link Removal') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('We reserve the right to disable or remove any link at any time, for any reason, without prior notice. This includes links that violate these terms or are the subject of an abuse report.') }}</p>
        </section>

        {{-- Account Terms --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Account Terms') }}</h2>
            <ul class="list-disc list-inside text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                <li>{{ __('You are responsible for maintaining the security of your account credentials.') }}</li>
                <li>{{ __('Accounts may be suspended or terminated for violations of these terms.') }}</li>
                <li>{{ __('An account is not required to use the core shortening and redirect features.') }}</li>
            </ul>
        </section>

        {{-- Limitation of Liability --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Limitation of Liability') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('anon.to is provided "as is" without any warranties. We do not guarantee uptime, availability, or the permanence of any shortened link. We are not liable for any damages arising from your use of this service.') }}</p>
        </section>

        {{-- Disclaimer of Warranties --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Disclaimer of Warranties') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('We disclaim all warranties, express or implied, including but not limited to implied warranties of merchantability, fitness for a particular purpose, and non-infringement. We make no warranty that the service will meet your requirements or be uninterrupted, timely, secure, or error-free.') }}</p>
        </section>

        {{-- Changes to Terms --}}
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Changes to Terms') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ __('We reserve the right to modify these terms at any time. Continued use of the service after changes constitutes acceptance of the updated terms.') }}</p>
        </section>
    </div>
</div>
