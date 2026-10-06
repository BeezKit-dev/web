# BeezKit

BeezKit is a batteries-included, open-source point-of-sale (POS) system. Merchants add their products and prices, ring up sales, and keep track of what they sold and how much they made.

It starts as a simple POS for a single shop and is designed to grow, through built-in modules, into a full ERP for anything from a market stall to a multi-branch business.

> **Status:** early development. The web MVP is being built and isn't ready for real use yet.

## Features

- **Built for Malaysia first:** MYR currency, English and Bahasa Melayu, and local payment methods (cash and QR).
- **Open to the world:** currency is a setting, translations are file-based, and payment methods are pluggable, so contributors can add their own.
- **Grows with the business:** stock, tax, receipts, branches and ERP features come as built-in modules you switch on.

## Tech stack

- [Laravel](https://laravel.com) with [Livewire](https://livewire.laravel.com) and [Alpine.js](https://alpinejs.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [FrankenPHP](https://frankenphp.dev) with [Laravel Octane](https://laravel.com/docs/octane)
- MySQL

## Running locally

### With Docker

You need [Docker](https://www.docker.com) ([OrbStack](https://orbstack.dev) is recommended on macOS) and [Node.js](https://nodejs.org).

```bash
git clone git@github.com:BeezKit-dev/web.git beezkit
cd beezkit
cp .env.example .env
npm install
npm run build
docker compose up -d --build
```

On its first start, the `app` container installs the Composer dependencies, generates `APP_KEY` and runs the migrations. Follow along with `docker compose logs -f app`.

With OrbStack, the app is served at <https://beezkit.local>.

MySQL is available on `127.0.0.1:3306` with the credentials from your `.env`. The default local password is `secret`, so change it before exposing the database anywhere.

### With Laravel Herd

You need [Laravel Herd](https://herd.laravel.com) (PHP 8.5), Node.js and a MySQL server that matches the `DB_*` settings in `.env`.

```bash
composer run setup
composer run dev
```

`composer run setup` installs dependencies, creates `.env`, generates `APP_KEY`, runs the migrations and builds the frontend. `composer run dev` starts the development processes, including Vite.

## Running the tests

```bash
php artisan test --compact
```

## Translations

UI text lives in `lang/`. Short strings go in `lang/ms.json`, and Laravel's built-in messages are in `lang/ms/*.php`. Contributions for other languages are welcome.

## License

BeezKit is open-source software licensed under the [GNU Affero General Public License v3.0](LICENSE). If you run a modified version of BeezKit as an online service, you must make your source code available to its users.
