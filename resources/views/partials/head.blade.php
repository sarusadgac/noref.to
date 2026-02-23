<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>
<meta name="description" content="{{ $description ?? __('Anonymous URL shortener and redirect service. Shorten any URL with referrer hiding, no logging, and full encryption.') }}">
<link rel="canonical" href="{{ $canonical ?? url()->current() }}">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title ?? config('app.name') }}">
<meta property="og:description" content="{{ $description ?? __('Anonymous URL shortener and redirect service. Shorten any URL with referrer hiding, no logging, and full encryption.') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $canonical ?? url()->current() }}">
<meta property="og:site_name" content="{{ config('app.name') }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
<meta name="twitter:description" content="{{ $description ?? __('Anonymous URL shortener and redirect service. Shorten any URL with referrer hiding, no logging, and full encryption.') }}">

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=syne:500,700|dm-sans:400,500|jetbrains-mono:400&display=swap" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
