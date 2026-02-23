<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Integrate')] class extends Component {
}; ?>

<div class="mx-auto max-w-2xl py-8 animate-fade-in">
    <h1 class="font-heading font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Integrate') }}</h1>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-8">{{ __('Anonymize outbound links on your site using noref.to\'s direct redirect.') }}</p>

    <div class="space-y-6">
        <section>
            <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-2">{{ __('How It Works') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                {{ __('Prefix any URL with') }}
                <code class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-emerald-600 dark:text-emerald-400 text-xs font-mono">{{ config('app.url') }}/?</code>
                {{ __('and visitors will be redirected anonymously — no referrer, no tracking. Nothing is stored or logged.') }}
            </p>
        </section>

        {{-- JavaScript Snippet --}}
        <section>
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100">{{ __('JavaScript') }}</h2>
                <button
                    x-data="{ copied: false }"
                    x-on:click="
                        await navigator.clipboard.writeText($refs.jsSnippet.textContent.trim());
                        copied = true;
                        setTimeout(() => copied = false, 1500);
                    "
                    class="flex items-center gap-1 text-xs text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer"
                >
                    <flux:icon.document-duplicate x-show="!copied" variant="micro" />
                    <flux:icon.check x-show="copied" variant="micro" class="text-emerald-500" />
                    <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></span>
                </button>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-3">{{ __('Add this script to your page to automatically rewrite all external links.') }}</p>
            <div class="relative rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 overflow-hidden">
                <pre class="p-4 overflow-x-auto text-sm leading-relaxed" x-ref="jsSnippet"><code class="text-zinc-700 dark:text-zinc-300 font-mono text-xs">&lt;script&gt;
document.addEventListener('DOMContentLoaded', function () {
  var host = location.hostname;
  document.querySelectorAll('a[href^="http"]').forEach(function (a) {
    try {
      if (new URL(a.href).hostname !== host) {
        a.href = '{{ config('app.url') }}/?' + a.href;
      }
    } catch (e) {}
  });
});
&lt;/script&gt;</code></pre>
            </div>
        </section>

        {{-- PHP Snippet --}}
        <section>
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-heading font-bold text-lg text-zinc-900 dark:text-zinc-100">{{ __('PHP') }}</h2>
                <button
                    x-data="{ copied: false }"
                    x-on:click="
                        await navigator.clipboard.writeText($refs.phpSnippet.textContent.trim());
                        copied = true;
                        setTimeout(() => copied = false, 1500);
                    "
                    class="flex items-center gap-1 text-xs text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer"
                >
                    <flux:icon.document-duplicate x-show="!copied" variant="micro" />
                    <flux:icon.check x-show="copied" variant="micro" class="text-emerald-500" />
                    <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></span>
                </button>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-3">{{ __('Use this function to rewrite external links in HTML content server-side.') }}</p>
            <div class="relative rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 overflow-hidden">
                <pre class="p-4 overflow-x-auto text-sm leading-relaxed" x-ref="phpSnippet"><code class="text-zinc-700 dark:text-zinc-300 font-mono text-xs">function anonymizeLinks(string $html, string $domain): string
{
    return preg_replace_callback(
        '/&lt;a\s([^&gt;]*?)href=["\']?(https?:\/\/[^"\'\s&gt;]+)["\']?/i',
        function ($m) use ($domain) {
            $host = parse_url($m[2], PHP_URL_HOST);
            if ($host &amp;&amp; !str_ends_with($host, $domain)) {
                return '&lt;a ' . $m[1] . 'href="{{ config('app.url') }}/?' . $m[2] . '"';
            }
            return $m[0];
        },
        $html
    );
}

// Usage:
echo anonymizeLinks($html, 'yourdomain.com');</code></pre>
            </div>
        </section>
    </div>
</div>
