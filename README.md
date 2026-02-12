# anon.to

Privacy-first anonymous URL shortener. Strips referrers, keeps zero logs, and lets admins manage trusted and blocked domains.

## Features

- **Anonymous short links** — 6-character hashes, no tracking, no click analytics
- **Referrer stripping** — destination sites never see where visitors came from
- **Domain allow/block lists** — allowed domains auto-redirect; blocked domains are rejected at creation and show a warning on redirect
- **Interstitial page** — shows the destination URL before redirecting, with three states: auto-redirect (allowed), manual button (unknown), blocked warning
- **Direct redirect** — append any URL to `anon.to/?` for instant anonymous redirect without shortening
- **Abuse reporting** — public report form with Cloudflare Turnstile protection and admin moderation workflow (resolve, dismiss, delete, block domain)
- **Admin panel** — dashboard with stats, link/report/user/domain management, and Laravel Pulse monitoring
- **Two-factor authentication** — TOTP-based 2FA with recovery codes via Laravel Fortify
- **Dark/light mode** — system preference or manual toggle
- **Rate limiting** — 20 shortens/min per user, 5 reports/hour per IP

## Tech Stack

- **PHP 8.4** / **Laravel 12**
- **Livewire 4** with inline page components
- **Flux UI 2** (free edition)
- **Tailwind CSS 4**
- **MySQL 8** / **Redis** (cache, sessions, queues)
- **Laravel Fortify** for authentication and 2FA
- **Laravel Pulse** for application monitoring
- **Cloudflare Turnstile** for bot prevention
- **Pest 4** for testing
- **Vite** for frontend bundling

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

composer install
pnpm install

cp .env.example .env
php artisan key:generate
```

Configure `.env` with your database, Redis, and Turnstile credentials, then:

```bash
php artisan migrate --seed
pnpm run build
```

### Create an Admin User

```bash
php artisan app:create-admin
```

### Local Development

With [Laravel Herd](https://herd.laravel.com), the site is available at `https://anon-to.test`. Otherwise:

```bash
composer run dev
```

## Testing

```bash
php artisan test
```

139 tests covering URL shortening, redirects, domain management, admin authorization, abuse reporting, authentication, and settings.

## Project Structure

```
app/
  Concerns/         CreatedBy trait
  Console/Commands/  CreateAdminUser
  Enums/            UserRole, ReportStatus
  Http/
    Controllers/    RedirectController (hash-based redirects)
    Middleware/      HandleDirectRedirect, EnsureUserIsAdmin, ValidateTurnstile
  Models/           Domain, Link, Report, User
  Rules/            Turnstile (Cloudflare CAPTCHA validation)

resources/views/
  pages/            Inline Livewire page components
  pages/admin/      Admin pages (dashboard, links, reports, users, domains, pulse)
  pages/auth/       Authentication pages (login, register, 2FA, etc.)
  pages/settings/   User settings (profile, password, appearance, 2FA)
  interstitial      Redirect interstitial with 3 states
  layouts/          App, public, and auth layouts

routes/
  web.php           Public, auth, and admin routes
  settings.php      Profile and settings routes
```

## Routes

| Path | Description |
|---|---|
| `/` | URL shortening form |
| `/{hash}` | Redirect via 6-char hash |
| `/?{url}` | Direct anonymous redirect |
| `/report` | Abuse report form |
| `/my` | User's shortened links (auth) |
| `/about` | About page |
| `/privacy` | Privacy policy |
| `/terms` | Terms of service |
| `/integrate` | Integration guide |
| `/admin` | Admin dashboard |
| `/admin/links` | Admin link management |
| `/admin/reports` | Admin abuse reports |
| `/admin/users` | Admin user management |
| `/admin/domains` | Admin domain allow/block lists |
| `/admin/pulse` | Application monitoring |
| `/settings/*` | Profile, password, appearance, 2FA |
