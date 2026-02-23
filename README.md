# noref.to

Privacy-first anonymous URL shortener & referrer stripper. Zero logs, zero tracking.

> Based on [anon.to](https://github.com/bhutanio/anon.to) by [bhutanio](https://github.com/bhutanio) — MIT License.

## Features

- **Short links** — 6-char hashes, URL deduplication, no analytics
- **Referrer stripping** — destination never sees where you came from
- **Direct redirect** — `noref.to/?https://example.com` for instant anonymous redirect
- **Domain management** — allow/block lists with subdomain matching
- **Interstitial page** — preview destination before redirecting
- **Abuse reporting** — with Cloudflare Turnstile protection
- **Admin panel** — links, reports, users, domains + Laravel Pulse monitoring
- **2FA** — TOTP with recovery codes
- **Dark/light mode**

## Tech Stack

Laravel 12 · PHP 8.4 · Livewire 4 · Flux UI 2 · Tailwind CSS 4 · MySQL 8 · Vite 7

## Setup

```bash
git clone https://github.com/sarusadgac/noref.to.git noref.to
cd noref.to
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=SystemUserSeeder
php artisan app:create-admin
npm install && npm run build
```

Configure `.env` with database, Redis, and optionally Cloudflare Turnstile keys.

## Routes

| Path | Description |
|---|---|
| `/` | URL shortener + direct redirect |
| `/{hash}` | Redirect via short link |
| `/?{url}` | Direct anonymous redirect |
| `/report` | Abuse report form |
| `/my` | User's links |
| `/admin` | Admin dashboard |
| `/settings/*` | Profile, password, 2FA, appearance |
| `/admin/pulse` | Laravel Pulse monitoring |

## Integration

Prefix any URL with `https://noref.to/?` to redirect anonymously — no referrer, no tracking, nothing stored.

### JavaScript

Auto-rewrite all external links on your page:

```html
<script>
document.addEventListener('DOMContentLoaded', function () {
  var host = location.hostname;
  document.querySelectorAll('a[href^="http"]').forEach(function (a) {
    try {
      if (new URL(a.href).hostname !== host) {
        a.href = 'https://noref.to/?' + a.href;
      }
    } catch (e) {}
  });
});
</script>
```

### PHP

Rewrite external links in HTML content server-side:

```php
function anonymizeLinks(string $html, string $domain): string
{
    return preg_replace_callback(
        '/<a\s([^>]*?)href=["\']?(https?:\/\/[^"\'\s>]+)["\']?/i',
        function ($m) use ($domain) {
            $host = parse_url($m[2], PHP_URL_HOST);
            if ($host && !str_ends_with($host, $domain)) {
                return '<a ' . $m[1] . 'href="https://noref.to/?' . $m[2] . '"';
            }
            return $m[0];
        },
        $html
    );
}

// Usage:
echo anonymizeLinks($html, 'yourdomain.com');
```
