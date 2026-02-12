<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php($title = config('app.name') . ' — Anonymized Redirect')
    @include('partials.head')
    <meta name="referrer" content="no-referrer">
    <meta name="robots" content="noindex, nofollow">
    @if ($autoRedirect ?? false)
    <style>
        @keyframes progress-fill {
            from { width: 0%; }
            to { width: 100%; }
        }
        .progress-bar-fill {
            animation: progress-fill 1s linear forwards;
        }
    </style>
    @endif
</head>
<body class="min-h-screen flex items-center justify-center bg-zinc-50 dark:bg-zinc-950 bg-noise">
    <div class="bg-mesh-gradient"></div>

    <div class="relative z-10 w-full max-w-lg px-4 py-8 sm:px-6 animate-fade-in-up">
        {{-- Brand --}}
        <div class="flex items-center justify-center gap-2.5 mb-8">
            <x-app-logo-icon class="size-6" />
            <span class="font-heading text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ config('app.name') }}</span>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900/50 backdrop-blur-sm p-6 sm:p-8">
            @if ($blocked ?? false)
                {{-- Blocked state --}}
                <h1 class="font-heading font-bold text-xl text-zinc-900 dark:text-zinc-100 mb-2 text-center">{{ __('Domain Blocked') }}</h1>
                <p class="text-zinc-500 text-sm mb-6 text-center">{{ __('This domain has been blocked due to policy violations.') }}</p>

                <div class="rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-700/40 p-4 mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="size-4 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-red-600 dark:text-red-400 text-[10px] uppercase tracking-widest font-medium">{{ __('Blocked Destination') }}</p>
                    </div>
                    <p class="font-mono-accent text-red-600 dark:text-red-400 text-sm break-all leading-relaxed">{{ $url }}</p>
                </div>

                <p class="text-zinc-400 dark:text-zinc-600 text-xs text-center">{{ __('If you believe this is a mistake, please contact the administrator.') }}</p>
            @else
                {{-- Allowed or unknown state --}}
                <h1 class="font-heading font-bold text-xl text-zinc-900 dark:text-zinc-100 mb-2 text-center">{{ __('Anonymized Redirect') }}</h1>
                <p class="text-zinc-500 text-sm mb-6 text-center">{{ __('Your referrer is hidden and this visit is not logged. Verify the destination below before continuing.') }}</p>

                {{-- Trust indicators --}}
                <div class="flex items-center justify-center gap-4 sm:gap-6 mb-6">
                    <div class="flex items-center gap-1.5">
                        <svg class="size-3.5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Encrypted') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="size-3.5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-1.745-1.745a10.029 10.029 0 0 0 3.3-4.38 1.651 1.651 0 0 0 0-1.185A10.004 10.004 0 0 0 9.999 3a9.956 9.956 0 0 0-4.744 1.194L3.28 2.22ZM7.752 6.69l1.092 1.092a2.5 2.5 0 0 1 3.374 3.373l1.092 1.092a4 4 0 0 0-5.558-5.558Z" clip-rule="evenodd" />
                            <path d="M10.748 13.93 8.07 11.25A2.495 2.495 0 0 0 10 13.5c.256 0 .507-.033.748-.07ZM3.51 9.72a8.527 8.527 0 0 1 1.352-1.97L3.28 6.169A10 10 0 0 0 .458 9.41a1.652 1.652 0 0 0 0 1.186A10.007 10.007 0 0 0 10 16.5c1.15 0 2.263-.195 3.296-.547l-1.57-1.57A4.001 4.001 0 0 1 6.012 8.69l-2.5-2.5A.75.75 0 0 1 3.51 9.72Z" />
                        </svg>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Referrer hidden') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="size-3.5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('No logs') }}</span>
                    </div>
                </div>

                {{-- Full URL display --}}
                <div class="rounded-lg bg-zinc-100 dark:bg-zinc-950/80 border border-zinc-200 dark:border-zinc-700/40 p-4 mb-6">
                    <p class="text-zinc-500 text-[10px] uppercase tracking-widest font-medium mb-2">{{ __('Destination') }}</p>
                    <p id="destination-url" class="font-mono-accent text-emerald-600 dark:text-emerald-400 text-sm break-all leading-relaxed">{{ $url }}</p>
                </div>

                @if ($autoRedirect ?? false)
                    {{-- Auto-redirect with progress bar --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Redirecting...') }}</span>
                            <span id="countdown" class="text-sm font-mono-accent text-emerald-600 dark:text-emerald-400">1s</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-zinc-200 dark:bg-zinc-700/50 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500 progress-bar-fill"></div>
                        </div>
                    </div>

                    <a
                        id="destination-link"
                        href="{{ $url }}"
                        rel="noreferrer nofollow"
                        referrerpolicy="no-referrer"
                        class="group flex items-center justify-center gap-2 w-full rounded-lg bg-zinc-100 dark:bg-zinc-800 px-4 py-3 text-center text-sm text-zinc-500 dark:text-zinc-400 transition-all duration-150 hover:bg-zinc-200 dark:hover:bg-zinc-700"
                    >
                        {{ __('Click here if not redirected') }}
                    </a>
                @else
                    {{-- Manual continue button --}}
                    <a
                        id="destination-link"
                        href="{{ $url }}"
                        rel="noreferrer nofollow"
                        referrerpolicy="no-referrer"
                        class="group flex items-center justify-center gap-2 w-full rounded-lg bg-emerald-500 px-4 py-3 text-center font-medium text-zinc-950 transition-all duration-150 hover:bg-emerald-400 hover:gap-3"
                    >
                        {{ __('Continue to site') }}
                        <svg class="size-4 transition-transform duration-150 group-hover:translate-x-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                <p class="text-zinc-400 dark:text-zinc-600 text-xs text-center mt-4">{{ __('If you don\'t recognize this URL, close this tab.') }}</p>
            @endif
        </div>
    </div>

    <script>
        (function() {
            var hash = window.location.hash;
            var link = document.getElementById('destination-link');
            var urlDisplay = document.getElementById('destination-url');

            if (hash && link) {
                var dest = link.href + hash;
                link.href = dest;
                if (urlDisplay) {
                    urlDisplay.textContent = dest;
                }
            }

            @if ($autoRedirect ?? false)
            var finalUrl = link ? link.href : @json($url);
            var countdown = document.getElementById('countdown');
            var remaining = 1;
            var timer = setInterval(function() {
                remaining--;
                if (countdown) {
                    countdown.textContent = remaining + 's';
                }
                if (remaining <= 0) {
                    clearInterval(timer);
                }
            }, 1000);
            setTimeout(function() {
                window.location.href = finalUrl;
            }, 1000);
            @endif
        })();
    </script>
</body>
</html>
