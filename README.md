# anon.to

Privacy-first anonymous URL shortener. Strips referrers, keeps zero logs, and lets admins manage trusted and blocked domains.

## Features

- **Anonymous short links** — 6-character hashes with URL deduplication via SHA-256 fingerprinting, no tracking, no click analytics
- **Referrer stripping** — destination sites never see where visitors came from
- **Domain allow/block lists** — allowed domains auto-redirect; blocked domains are rejected at creation and show a warning on redirect; supports subdomain hierarchy matching
- **Interstitial page** — shows the destination URL before redirecting, with three states: auto-redirect (allowed), manual button (unknown), blocked warning
- **Direct redirect** — append any URL to `anon.to/?` for instant anonymous redirect without shortening
- **Abuse reporting** — public report form with Cloudflare Turnstile; admins can resolve, dismiss, delete links, or block domains from reports
- **Admin panel** — dashboard with stats, link/report/user/domain management, and Laravel Pulse monitoring
- **Two-factor authentication** — TOTP-based 2FA with recovery codes via Laravel Fortify
- **Dark/light mode** — system preference or manual toggle
- **Rate limiting** — 20 shortens/min, 5 reports/hour per IP
- **Integration guide** — public page with usage examples for developers

## Tech Stack

- **PHP 8.4** / **Laravel 12**
- **Laravel Fortify** for authentication and 2FA
- **Livewire 4** with inline page components
- **Flux UI 2** (free edition)
- **Tailwind CSS 4** via Vite plugin
- **MySQL 8** / **Redis** (cache, sessions, queues)
- **Laravel Pulse** for performance monitoring
- **Pest 4** for testing
- **Laravel Pint** for code formatting
- **Vite 7** for frontend bundling
- **GitHub Actions** CI for tests (PHP 8.4/8.5) and linting

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Redis
- Composer
- Node.js with pnpm

## Setup

```bash
git clone https://github.com/abixalmon/anonto.git
cd anonto
composer setup
```

This runs `composer install`, copies `.env.example`, generates the app key, runs migrations, installs Node dependencies, and builds assets.

Configure `.env` with your database, Redis, and Cloudflare Turnstile credentials.

### Creating an Admin

```bash
php artisan app:create-admin
```

### Local Development

With [Laravel Herd](https://herd.laravel.com), the site is available at `https://anonto.test`. Otherwise:

```bash
composer run dev
```

This starts the dev server, queue worker, log tail, and Vite concurrently.

## Testing

```bash
php artisan test
```

## Project Structure

```
app/
  Actions/Fortify/   CreateNewUser, ResetUserPassword
  Concerns/          CreatedBy, PasswordValidationRules, ProfileValidationRules
  Console/Commands/  CreateAdminUser
  Enums/             UserRole (User, Admin), ReportStatus (Pending, Resolved, Dismissed)
  Http/
    Controllers/     RedirectController (hash-based redirects)
    Middleware/       HandleDirectRedirect, EnsureUserIsAdmin, ValidateTurnstile
  Models/            Domain, Link, Report, User
  Providers/         AppServiceProvider, FortifyServiceProvider
  Rules/             Turnstile (Cloudflare CAPTCHA validation)

resources/views/
  pages/             Inline Livewire components (home, my-links, report, about, privacy, terms, integrate)
  pages/admin/       Admin pages (dashboard, links, reports, users, domains, pulse)
  pages/settings/    Settings pages (profile, password, appearance, two-factor)
  interstitial       Redirect interstitial with 3 states

config/
  anonto.php         App config (cache TTLs, excluded hash words, system user)

routes/
  web.php            Public, auth, and admin routes
  settings.php       Profile and settings routes
```

## Routes

| Path | Description |
|---|---|
| `/` | URL shortening form + direct redirect tool |
| `/{hash}` | Redirect via 6-char hash (interstitial) |
| `/?{url}` | Direct anonymous redirect |
| `/report` | Abuse report form |
| `/about` | About page |
| `/privacy` | Privacy policy |
| `/terms` | Terms of service |
| `/integrate` | Integration guide |
| `/my` | User's shortened links |
| `/settings/profile` | Profile settings |
| `/settings/password` | Password settings |
| `/settings/appearance` | Dark/light mode |
| `/settings/two-factor` | 2FA setup |
| `/admin` | Admin dashboard with stats |
| `/admin/links` | Admin link management |
| `/admin/reports` | Admin abuse reports |
| `/admin/users` | Admin user management |
| `/admin/domains` | Admin domain allow/block lists |
| `/admin/pulse` | Laravel Pulse monitoring |
