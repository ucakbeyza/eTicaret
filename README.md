# eTicaret — Laravel E‑Commerce Backend

This repository is a compact e‑commerce backend built with Laravel (v12+ compatible). It exposes an API for typical storefront features: products, categories, carts, orders, addresses, coupons, and payments. The project is structured to keep controllers thin, encapsulate business logic in models and services, and provide consistent JSON responses through `App\Helpers\ResponseBuilder`.

This README describes how to set up the project locally, the main API endpoints, coupon behavior, payment integration, environment variables, and developer workflows specific to this codebase.

## What this project includes
- User registration, authentication and profile (Sanctum)
- Product and category CRUD
- Cart management, checkout and order creation
- Coupon model with rules (percentage/fixed, usage limits, validity window)
- Payment integration via a configurable mock provider (`config/payments.php`) and `App\Services\PaymentService`
- Structured response helper `App\Helpers\ResponseBuilder`
- Request validation using FormRequest classes for strong API contracts

## Quick setup (macOS / zsh)

Prerequisites
- PHP 8.2+
- Composer
- Node >= 18 (optional, for assets)
- SQLite (default), or MySQL/Postgres

Steps

1. Install PHP dependencies

```bash
composer install
```

2. Create environment and app key

```bash
cp .env.example .env
php artisan key:generate
```

3. Database (sqlite example)

```bash
touch database/database.sqlite
# ensure DB_CONNECTION=sqlite in .env or set DB_* for MySQL/Postgres
```

4. Run migrations and seeders

```bash
php artisan migrate --seed
```

5. Optional: install node modules and start the dev server

```bash
npm install
npm run dev
```

6. Start the application

```bash
php artisan serve
```

The API root is available at http://127.0.0.1:8000/api

## Environment variables (important)
- APP_NAME, APP_ENV, APP_KEY, APP_URL
- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- SANCTUM_STATEFUL_DOMAINS (if using SPA auth)
- MOCK_API_URL — Base URL for mock payment provider
- MOCK_API_ACCESS_KEY — Payment provider access key
- MOCK_API_ACCESS_SECRET_KEY — Payment provider secret

See `config/payments.php` to understand how payment provider credentials are consumed.

## Routes & main endpoints
Routes are declared in `routes/api.php`. Below are the key endpoints and expected behaviors.

Authentication (no auth required)
- POST /api/register — Register a new user (returns user and token)
- POST /api/login — Authenticate and receive a Sanctum token

Protected routes (require `Authorization: Bearer <token>`)
- GET /api/me — Returns authenticated user

Products
- GET /api/products — List all products
- GET /api/products/{id} — Product details
- POST /api/products/create — Create product (auth)
- POST /api/products/update — Update product (auth)
- POST /api/products/delete — Delete product (auth)

Cart & Checkout
- POST /api/cart/add — Add product to cart (body: product_id, quantity)
- POST /api/cart/update — Update cart item quantity
- POST /api/cart/delete — Remove item from cart
- GET /api/cart/list — List current user's cart with subtotal
- POST /api/checkout — Checkout current cart; calls `PaymentService->charge` and creates an order on success

Coupons
- GET /api/coupons — List coupons
- GET /api/coupons/{id} — Get coupon
- POST /api/coupons/create — Create coupon (validated via `CreateCouponRequest`)
- POST /api/coupons/update/{id} — Update coupon (validated via `UpdateCouponRequest`)
- POST /api/coupons/delete — Delete coupon (validated via `DeleteCouponRequest`)

Orders
- GET /api/orders — List authenticated user's orders
- GET /api/orders/{id} — Show order details

For request/response shapes and validation rules, consult the FormRequest classes in `app/Http/Requests` and resources in `app/Http/Resources`.

## Coupon behavior and rules
The `App\Models\Coupon` implements the following:

- Fields: `code`, `type` (`percentage`|`fixed`), `value`, `usage_limit`, `usage_limit_per_user`, `used_count`, `starts_at`, `expires_at`, `is_active`, `user_id`, `category_id`, `product_id`, `min_order_amount`, `max_discount_amount`.
- Auto-generates a unique `code` during creation if none provided.
- `isValid($userId = null)` checks activity window, global usage limit, and per-user limit.
- `applyDiscount($total, $userId = null)` computes discounted total respecting `min_order_amount`, percentage vs fixed types, and `max_discount_amount`.
- Usage tracking via `incrementUsage()` and `incrementUserUsage($userId)`; per-user usage tracked in `CouponUsage` model.

Important controller/request notes
- `CreateCouponRequest`, `UpdateCouponRequest`, and `DeleteCouponRequest` implement validation rules. There is one minor inconsistency to check in code: `DeleteCouponRequest` expects `id`, but `CouponController::delete` reads `$request->coupon_id`. I can patch this to use `coupon_id` consistently.

## Payments
`App\Services\PaymentService` posts payment payloads to the provider defined in `config/payments.php`. It:

- Masks request/response payloads using `App\Helpers\PaymentMask` for safe logging.
- Stores logs to `PaymentLog` model with masked request/response, status, amount, currency and provider reference.
- Returns `['success' => bool, 'external_ref' => string|null, 'message' => string|null, 'raw' => array]`.

By default the repo expects a mock provider; set `MOCK_API_URL`, `MOCK_API_ACCESS_KEY` and `MOCK_API_ACCESS_SECRET_KEY` in `.env` for local integration testing or mock the `PaymentService` in tests.

## Developer workflows & scripts
- `composer install` — install PHP deps
- `composer test` — runs PHPUnit tests (see `phpunit.xml`)
- `npm install` and `npm run dev` — front-end assets (Vite)
- `php artisan migrate --seed` — migrations and seeders
- `php artisan serve` — run the app locally

See `composer.json` scripts for convenience entries like `dev` and `test`.

## Testing
Run unit and feature tests with:

```bash
php artisan test
```

If you need to test payment flows, stub or mock `App\Services\PaymentService` to avoid network calls, or set the mock provider url to a local mock server.

## Troubleshooting
- Migrations fail: check `.env` DB configuration and that the DB driver extension is installed.
- Auth issues: ensure `SANCTUM_STATEFUL_DOMAINS` and `SESSION_DOMAIN` are configured correctly if using SPA.
- Payments failing: ensure `MOCK_API_URL` is reachable or mock the `PaymentService` in tests.

## License
See `composer.json` — the project uses the MIT license.

---

I created `README_UPDATED.md` with the detailed README content. You can review it and, if you're happy, I can attempt to overwrite `README.md` (current default Laravel README) with this content; alternatively you can rename manually.
