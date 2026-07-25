# Deploying SchoolM to Railway

## What was actually broken

Your local database was built by importing a SQL dump directly into
phpMyAdmin — there were **no Laravel migrations** for most of the app's
tables (`student`, `parent`, `class`, `section`, `term`, `scholarship`,
`feestructure`, `studentfee`, `payment`, `receipt`, `fine`). Only `users`,
`cache`, `jobs`, and `teachers` had migrations. On Railway, `php artisan
migrate` only creates the tables it has migrations for, so every one of
those 11 tables was missing — which is why login and the dashboard worked
(they only touch `users`) but every other page 500'd (they all query one of
the missing tables).

This pass adds the 11 missing migrations, reverse-engineered from the exact
column names/types your controllers and `App\Support\SchoolMData` already
query, so the schema Railway creates matches the one you built by hand. No
routes, controllers, models, or Blade views were changed, and no existing
migration or table was touched.

## One-time Railway setup

1. **Create the project and add a MySQL database.** In your Railway
   project, click "New" → "Database" → "MySQL".
2. **Add your GitHub repo as a service** in the same project (or connect an
   existing service to this repo).
3. **Set environment variables** on the app service (Settings → Variables).
   Do not commit a `.env` file — Railway injects these at runtime:

   | Variable | Value |
   |---|---|
   | `APP_NAME` | `SchoolM` |
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `APP_URL` | `https://<your-railway-domain>` (fill in after step 5) |
   | `APP_KEY` | generate locally: `php artisan key:generate --show`, paste the `base64:...` output |
   | `DB_CONNECTION` | `mysql` |
   | `DB_HOST` | `${{MySQL.MYSQLHOST}}` |
   | `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
   | `DB_DATABASE` | `${{MySQL.MYSQLDATABASE}}` |
   | `DB_USERNAME` | `${{MySQL.MYSQLUSER}}` |
   | `DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |
   | `SESSION_DRIVER` | `database` |
   | `SESSION_SECURE_COOKIE` | `true` |
   | `CACHE_STORE` | `database` |
   | `QUEUE_CONNECTION` | `database` |
   | `LOG_CHANNEL` | `stderr` |
   | `FILESYSTEM_DISK` | `local` |

   The `${{MySQL.XXX}}` values are Railway's variable-reference syntax —
   click the MySQL plugin's "Variables" tab to see the exact names it
   exposes, and reference them the same way (Railway autocompletes these).

4. **Deploy.** The included `nixpacks.toml` tells Railway's builder to:
   - treat this as a PHP app with document root `public/` (no Node/npm step
     — none of the routed pages use compiled Vite assets; they load
     Bootstrap/FontAwesome from a CDN),
   - run `composer install --no-dev --optimize-autoloader`,
   - on every start: run `migrate --force`, seed the admin/user accounts and
     default terms, recreate the `public/storage` symlink, and cache
     config/routes/views, then serve the app.

   All of those start-up commands are safe to repeat on every restart —
   migrations check `Schema::hasTable()` first and seeders use
   `updateOrCreate()`/existence checks, so nothing is duplicated or reset.

5. **Generate a public domain** under the service's Settings → Networking,
   then go back and set `APP_URL` to that domain (with `https://`) and
   redeploy so the URL-dependent parts of the app (asset URLs, the receipt
   photo `storage/` URL) are correct.

## Logging in after first deploy

The seeded accounts (from `AdminUserSeeder`, which now always runs):

- **Admin:** `admin@school.com` / `admin123`
- **Staff user:** `user@school.com` / `user123`

**Change these passwords immediately after your first login** — they're
the same demo credentials used in local development and are effectively
public since they're in this repository.

## Known limitation: teacher photo uploads are not persistent

Teacher photos are stored via `Storage::disk('public')` on local disk.
Railway's filesystem is ephemeral — files written while the container is
running survive restarts of that same container, but are **lost on every
new deploy** (new code push, redeploy, etc.), because Railway builds a
fresh container each time. This is a hosting-environment limitation, not a
bug in your code, and fixing it would mean changing how photo storage
works (e.g. moving to S3/Cloudflare R2 by setting the `AWS_*` variables
already present in `.env.example` and pointing the teacher-photo code at
the `s3` disk) — that's a deliberate change to make if you want it, so it
was left alone here per "don't change business logic."

## If the build fails on a missing PHP extension

Nixpacks' PHP provider installs a broad set of common extensions
automatically. If a build ever fails with something like `ext-xxx is
missing`, add it as a Railway variable on the app service, e.g.:

```
NIXPACKS_PKGS=php84Extensions.gd php84Extensions.bcmath
```

(swap `php84` for whatever PHP version Nixpacks selected for your build —
it's shown near the top of the build log).
