# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Jagoan Kue** — a full-stack e-commerce web app for a bakery (cake shop), built as a final project for a Web Programming course at Informatika UNP 2025. Laravel 12 + Blade + Tailwind CSS + Alpine.js. Everything is in Indonesian (UI, comments, variable names).

## Commands

```bash
# Install dependencies
composer install && npm install

# Initial setup (migrate, seed, link storage)
composer run setup

# Run all services concurrently (PHP server + queue worker + Vite)
composer run dev

# Run Vite only
npm run dev

# Build frontend assets
npm run build

# Run all tests
composer run test
# or
php artisan test

# Run a single test file
php artisan test tests/Feature/OrderManagementTest.php

# Run a specific test method
php artisan test --filter=test_admin_can_confirm_payment

# Lint/format PHP code
./vendor/bin/pint

# Tinker REPL
php artisan tinker
```

## Demo Credentials (from .env.example / seeders)

- **Admin:** admin@jagoan-kue.test / password
- **Customer:** customer@jagoan-kue.test / password

## Architecture

### Request Flow

Public and authenticated routes are in `routes/web.php` only — there is no API route file. The cart is session-based (not an API). Admin routes are grouped under `/admin` with `auth` + `EnsureUserIsAdmin` middleware.

### Role-Based Access Control

RBAC is enforced via two layers:
1. `app/Http/Middleware/EnsureUserIsAdmin.php` — blocks non-admin users from all `/admin/*` routes
2. `authorizeOwner()` pattern in controllers — ensures customers can only access their own orders, addresses, etc.

The `User` model has a boolean `is_admin` field. No roles/permissions package is used.

### Key Domain Models

| Model | Key relationships |
|---|---|
| `Order` | belongsTo User; hasMany OrderItem, Payment |
| `OrderItem` | belongsTo Order, Product; hasMany OrderItemCustomization |
| `Payment` | belongsTo Order; stores proof upload path + status |
| `Product` | belongsTo Category; hasMany ProductReview, OrderItem |
| `CustomizationOption` | belongsTo Category; used at checkout per product |
| `Voucher` | standalone; applied at checkout to reduce total |
| `ShippingZone` | standalone; selected at checkout for delivery cost |

### Order & Payment State Machine

Orders flow through statuses stored as strings: `menunggu_konfirmasi` → `diproses` → `siap_diambil`/`dikirim` → `selesai`. The `Payment` model has its own status: `menunggu` → `menunggu_verifikasi` → `dikonfirmasi`/`ditolak`.

Payment supports two modes:
- **DP (50%)**: First payment unlocks processing; second payment required before shipment.
- **Full payment**: Single payment.

The `getStatusLabelAttribute()` accessor on `Order` consolidates display-facing labels across both payment modes.

### Cart

`CartController` stores cart data in the PHP session (`session('cart')`). All cart mutations are AJAX and return JSON. Cart is cleared on successful checkout.

### File Uploads

Payment proof images are uploaded to `storage/app/public/payment_proofs/`. Product images go to `storage/app/public/products/`. Review images go to `storage/app/public/reviews/`. Run `php artisan storage:link` if symlink is missing.

Upload validation for payment proofs (MIME type, max size) lives in `OrderController` or `PaymentController` — not just extension checking.

### PDF & Excel Exports

- Invoices: `barryvdh/laravel-dompdf` via `app/Http/Controllers/AdminController` (or OrderController), template at `resources/views/pdf/invoice.blade.php`
- Sales reports: `maatwebsite/excel` via `app/Exports/LaporanPenjualanExport.php`

### Security Headers

`app/Http/Middleware/SecurityHeaders.php` is registered globally and adds: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `X-XSS-Protection`.

### Frontend

- **Tailwind CSS** + **Alpine.js** (CDN or bundled via Vite)
- **Chart.js** for admin analytics charts
- No React/Vue — all interactivity is Alpine.js or vanilla JS in Blade templates
- Layouts: `resources/views/layouts/app.blade.php` (customer), `resources/views/admin/layout.blade.php` (admin)

## Testing

Tests use **Pest** (not PHPUnit directly, though both work). Feature tests use `RefreshDatabase`. Most tests seed via factories or call seeders directly.

Key test areas:
- `tests/Feature/RbacSecurityTest.php` — verifies RBAC enforcement across all routes
- `tests/Feature/SecurityHeadersTest.php` — asserts middleware injects correct headers
- `tests/Feature/PaymentProofUploadTest.php` — upload validation (file type/size)
- `tests/Feature/AuthorizationTest.php` — customer-to-customer data isolation
