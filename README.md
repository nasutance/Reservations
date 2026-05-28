# Reservations

> A full-stack theatre ticketing web application built with **Laravel 11**, **Vue 3** and **Inertia.js**.

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vuedotjs&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-Pest_3-brightgreen)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?logo=docker&logoColor=white)

---

## Overview

**Reservations** is a full-stack application for managing theatre shows and their online ticketing. Users can browse available shows, view upcoming performances, and complete a booking through a guided multi-step flow. Administrators have a dedicated dashboard to manage all content and a RESTful API secured with Sanctum tokens.

---

## Features

### Public
- Show catalogue with search and filters (keyword, artist, venue, duration, postal code)
- Show detail page: poster, description, cast, upcoming performances
- RSS feed of upcoming performances (Spatie Laravel Feed)

### Authenticated users
- Registration, login, email verification (Laravel Breeze)
- Multi-step reservation flow:
  1. Choose a performance
  2. Select pricing tiers and number of seats
  3. Choose delivery method
  4. Payment (PayPal integration)
  5. Confirmation
- Personalised dashboard: booking history, reservation status
- Reservation management: add pricing lines, cancel a booking
- Profile management: update email, password, delete account

### Administration
- Full CRUD for shows, performances, artists, prices and venues
- User management and role assignment (admin, affiliate, member)
- CSV import / export for shows
- RESTful API secured with Laravel Sanctum

---

## Tech stack

| Layer       | Technologies                                                      |
|-------------|-------------------------------------------------------------------|
| Backend     | PHP 8.2+, Laravel 11, Laravel Sanctum, Laravel Breeze            |
| Frontend    | Vue 3, Inertia.js, Tailwind CSS 3, Vite                          |
| Database    | SQLite (dev) / MySQL–MariaDB (prod), Eloquent ORM, Soft Deletes  |
| Auth & ACL  | Laravel Breeze, resource Policies, role-based middleware          |
| Extras      | Spatie Laravel Feed (RSS), Ziggy (client-side named routes)      |
| Testing     | Pest 3, PHPUnit, DatabaseTransactions                             |
| Deployment  | Docker (PHP 8.3-FPM)                                             |

---

## Architecture

The app follows a **full-stack MVC** pattern with hybrid rendering via Inertia.js — no separate API calls for page navigation, no page reloads.

```
app/
├── Http/
│   ├── Controllers/        # Web controllers (Inertia::render) + API controllers (JsonResponse)
│   └── Middleware/
├── Models/                 # Eloquent models with relationships, soft deletes, casts, factories
├── Policies/               # Per-resource authorisation (ShowPolicy, ReservationPolicy, …)
└── Providers/

resources/js/
├── Components/             # Reusable Vue 3 components (DataTable, Modal, Pagination, …)
├── Layouts/                # AppLayout, AuthenticatedLayout, GuestLayout
└── Pages/
    ├── Dashboard/          # Admin & member dashboards
    ├── Reservation/        # 5-step booking wizard
    └── Show/               # Show catalogue & detail

database/
├── factories/              # Model factories for all entities
├── migrations/
└── seeders/                # Full dataset for local development
```

Key patterns used:
- **Inertia.js** — seamless SPA feel without a separate frontend codebase
- **Laravel Policies** — granular, testable per-resource authorisation
- **Eloquent relationships** — BelongsToMany, HasMany, SoftDeletes, eager loading
- **Sanctum** — stateless token authentication for the `/api` endpoints
- **Ziggy** — named Laravel routes available in Vue components (`route('show.index')`)

---

## Getting started

### Requirements

- PHP 8.2+, Composer 2
- Node.js 18+, npm
- SQLite (default) or MySQL / MariaDB

### Local setup

```bash
git clone https://github.com/nasutance/Reservations.git
cd Reservations

# PHP dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Database (SQLite by default)
touch database/database.sqlite
php artisan migrate --seed

# JS dependencies & dev build
npm install
npm run dev

# In a separate terminal
php artisan serve
```

The app will be available at `http://localhost:8000`.

### Docker

```bash
docker build -t reservations .
docker run -p 8080:80 reservations
```

---

## API

A RESTful API is available under `/api` and secured with Laravel Sanctum.

| Method | Endpoint                  | Description                     |
|--------|---------------------------|---------------------------------|
| GET    | `/api/shows`              | Paginated show list with filters|
| POST   | `/api/shows`              | Create a show (admin)           |
| GET    | `/api/shows/{id}`         | Show detail (with `?include=`)  |
| PUT    | `/api/shows/{id}`         | Update a show (admin)           |
| DELETE | `/api/shows/{id}`         | Delete a show (admin)           |
| GET    | `/api/representations`    | List performances               |
| GET    | `/api/reservations`       | List reservations (own / all)   |

Authenticate by passing a Sanctum token in the `Authorization: Bearer <token>` header.

---

## Testing

```bash
php artisan test
# or
./vendor/bin/pest
```

Tests cover authentication, profile management, show CRUD (including authorisation), representation and reservation controllers, using `DatabaseTransactions` to keep the database clean between runs.

---

## Seeded accounts

After running `php artisan migrate --seed`, two named accounts are available (roles are assigned randomly across all seeded users):

| Login  | Email                    | Password   |
|--------|--------------------------|------------|
| bob    | `bob@sull.com`           | `12345678` |
| anna   | `anna.lyse@sull.com`     | `12345678` |

20 additional accounts are generated via factories.
