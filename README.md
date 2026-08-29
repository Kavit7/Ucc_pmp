# UCC Property Management Portal (UCC PMP)

A web-based property management system built on [Yii 2](https://www.yiiframework.com/), for managing properties, leases, tenants, billing, and payments in one place — with Tanzania location data built in so properties can be located by searching (e.g. "Masaki, Dar es Salaam") instead of manual data entry.

## Features

- **Properties** — record any type of property (types and their custom attributes are admin-configurable, not hardcoded), track ownership, usage (Sale/Rented/Storage), and status. Search-as-you-type location entry backed by a seeded Tanzania region/district/ward/street dataset.
- **Leases** — create leases linking a tenant, a property, and a price plan; auto-generates the first bill; renew, terminate, and track lease status.
- **Bills & Payments** — bills are generated per lease; staff record payments against a bill (with an optional receipt upload), which is what actually marks it paid — the Bills and Payments views reflect real, live status rather than a static list.
- **Reports** — Revenue (collected/pending/overdue, filterable by date and status), Occupancy (by property status and usage type), and Lease reports, each with search, print, and CSV export.
- **Dashboard** — at-a-glance stats (properties, active leases, revenue), property analytics charts, recent leases, and a recent-activity feed.
- **Notifications** — in-app notifications for overdue bills, new leases, new users, and recorded payments, with a per-user on/off preference in Settings.
- **User management** — role-based accounts (admin, manager, technician, accountant, tenant) with RBAC-backed permissions, profile pages with photo upload, and password management.
- **Configuration** — admins manage the lookup lists that drive dropdowns across the app (property types, statuses, ownership types, bill/lease statuses, etc.) without touching code.

## Tech stack

- PHP 8.2+, [Yii 2](https://www.yiiframework.com/) framework
- MySQL
- Bootstrap 5, Chart.js, Select2, SweetAlert2, Font Awesome — all vendored locally (`web/lib/`) so the app runs without an internet connection

## Requirements

- PHP 8.2 or later, with the extensions Yii 2 / MySQL normally need (`pdo_mysql`, `mbstring`, `intl`, etc.)
- [Composer](https://getcomposer.org/)
- MySQL 5.7+ / MariaDB

## Setup

1. **Install dependencies**

   ```
   composer install
   ```

2. **Create a database** (e.g. `pmp_db_1`) and point `config/db.php` at it:

   ```php
   return [
       'class' => 'yii\db\Connection',
       'dsn' => 'mysql:host=localhost;dbname=pmp_db_1',
       'username' => 'root',
       'password' => '',
       'charset' => 'utf8',
   ];
   ```

3. **Run migrations** — this creates the schema and seeds RBAC's own tables plus the Tanzania location dataset (regions/districts/wards/streets, ~20,000 rows, bundled under `data/tanzania-locations/`):

   ```
   php yii migrate
   ```

4. **Initialize RBAC roles/permissions:**

   ```
   php yii rbac/init
   ```

5. **Create your first admin account.** There's no public sign-up screen — insert the first user directly (or via `users/create`, see the note below), then log in at `/login/login`.

6. **Run the app:**

   ```
   php yii serve --port=8888
   ```

   or point an Apache/XAMPP vhost at the `web/` directory.

## Directory structure

```
commands/       console commands (e.g. rbac/init)
config/         application configuration (web, console, db, params)
controllers/    web controller classes
data/           bundled seed data (Tanzania locations CSVs)
mail/           e-mail view templates
migrations/     database schema + seed migrations
models/         model classes
views/          view files
web/            entry script, public web assets, and vendored front-end libraries (web/lib/)
```

