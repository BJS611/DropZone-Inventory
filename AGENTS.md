# AGENTS.md

# DropZone Inventory — Laravel + Vercel Agent Instructions

This document controls how Hermes must build and maintain the DropZone Inventory project.

Required project documents:
- `AGENTS.md` = engineering and agent rules
- `PRD.md` = product requirements and acceptance criteria
- `design.md` = visual source of truth

Priority:
1. Current explicit user instruction
2. `AGENTS.md`
3. `PRD.md`
4. `design.md`
5. Laravel framework conventions
6. Other best practices
7. Agent assumptions

Never invent a conflicting requirement.

---

# 1. Non-negotiable stack

The project MUST use Laravel.

Preferred baseline:
- Laravel 13.x
- PHP 8.3+ locally
- PHP 8.5-capable Vercel PHP runtime for deployment
- MySQL
- Composer
- Blade
- Alpine.js where lightweight client interactivity is useful
- Tailwind CSS
- Vite
- Laravel session authentication
- Laravel authorization policies/gates
- Laravel validation / Form Requests
- Laravel Eloquent ORM
- Pest or PHPUnit
- Playwright for browser-level testing if practical
- npm for frontend asset builds
- GitHub
- Vercel

Do NOT introduce Next.js as the application framework.

Do NOT replace Laravel with another backend framework.

Do NOT turn the application into a separate Next.js frontend unless the user explicitly requests a split architecture.

The intended architecture is a Laravel monolith with server-rendered Blade views and Vite-built assets, deployed as a PHP application to Vercel using the `vercel-php` community runtime.

Vercel documents PHP as a community runtime and shows it configured through `vercel.json`; the current `vercel-community/php` project lists `vercel-php@0.9.0` for PHP 8.5 and supports Composer. Verify the newest compatible runtime before pinning a different version. Vercel Functions have a read-only filesystem except for `/tmp`, so the application MUST NOT depend on persistent local writes. citeturn378328search0turn378328search4turn629060search0

---

# 2. Laravel version policy

Use Laravel 13.x for a new project unless the repository already contains a later compatible Laravel application.

Laravel 13 requires PHP >= 8.3. citeturn411104search0turn411104search5

Use PHP 8.3 or newer locally.

For Vercel, prefer a current `vercel-php` runtime compatible with the project's PHP requirement. The current runtime repository lists:
- `vercel-php@0.9.0` -> PHP 8.5.x
- `vercel-php@0.7.4` -> PHP 8.3.x

Prefer the newest compatible release available at implementation time, but do not randomly change runtime versions after the project is working. citeturn629060search0

---

# 3. Product scope

Build `DropZone Inventory`, a complete inventory management website.

Required features:
- authentication
- role-based authorization
- inventory CRUD
- category CRUD
- location CRUD
- supplier CRUD
- stock IN
- stock OUT
- stock adjustment
- stock transfer
- borrowing
- return
- partial return
- low-stock detection
- out-of-stock detection
- dashboard
- search
- filters
- sorting
- pagination
- reports
- CSV export
- user management
- audit logs
- responsive UI
- GitHub integration
- Vercel deployment configuration

This is NOT ecommerce.

Never add:
- shopping cart
- checkout
- payment gateway
- marketplace
- customer storefront
- fake scarcity
- fake countdowns

---

# 4. Repository inspection

Before changing anything:

```bash
pwd
git status
git branch --show-current
git log -5 --oneline
find . -maxdepth 2 -type f | sort
```

If Laravel is already present, inspect:
```bash
php -v
composer --version
php artisan --version
composer show
cat composer.json
cat package.json 2>/dev/null || true
```

Do not destroy user work.

Never execute these unless the user explicitly asks:

```bash
git reset --hard
git clean -fd
git push --force
```

Never overwrite `.env`.

---

# 5. Target project structure

Preferred:

```text
app/
├── Console/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Middleware/
├── Models/
├── Policies/
├── Services/
├── Actions/
└── Support/

bootstrap/
config/
database/
├── factories/
├── migrations/
└── seeders/

public/
resources/
├── css/
├── js/
└── views/
    ├── layouts/
    ├── components/
    ├── auth/
    ├── dashboard/
    ├── inventory/
    ├── categories/
    ├── locations/
    ├── suppliers/
    ├── transactions/
    ├── borrowings/
    ├── reports/
    └── users/

routes/
├── web.php
├── api.php
└── console.php

tests/
├── Feature/
├── Unit/
└── Browser/ or e2e/

api/
└── index.php

vercel.json
.vercelignore
composer.json
composer.lock
package.json
package-lock.json
vite.config.js or vite.config.ts
tailwind.config.js or tailwind.config.ts
AGENTS.md
PRD.md
design.md
README.md
.env.example
```

Preserve Laravel conventions.

Do not create unnecessary abstraction layers.

---

# 6. Application architecture

Preferred request flow:

```text
Browser
  ↓
Laravel Route
  ↓
Middleware / Authentication
  ↓
Authorization Policy
  ↓
Controller
  ↓
Form Request / Validation
  ↓
Service or Action
  ↓
Eloquent
  ↓
MySQL
```

Views:
```text
Controller
  ↓
Blade View
  ↓
Blade Components
  ↓
Tailwind / Alpine / Vite assets
```

Do not put significant business logic inside Blade templates.

Do not put raw SQL in controllers unless there is a documented reason.

---

# 7. Database

Use Mysql.

Minimum models:
- User
- Category
- Location
- Supplier
- Item
- StockTransaction
- Borrowing
- BorrowingItem
- AuditLog

Use proper:
- foreign keys
- indexes
- unique constraints
- casts
- relationships
- enums or controlled values where suitable

---

# 8. User model

Minimum fields:
```text
id
name
email
password
role
status
created_at
updated_at
```

Roles:
```text
ADMIN
STAFF
VIEWER
```

Statuses:
```text
ACTIVE
INACTIVE
```

Use Laravel's password hashing.

Never store plaintext passwords.

---

# 9. Category

Fields:
```text
id
name
code
description
created_at
updated_at
```

Rules:
- name required
- code required
- code unique

Do not hard-delete a category while referenced by active historical data unless the relationship strategy safely preserves history.

---

# 10. Location

Fields:
```text
id
name
code
description
created_at
updated_at
```

Rules:
- name required
- code required
- code unique

Examples:
```text
Lab Jaringan
Lab Multimedia
Gudang
Ruang Admin
Server Room
```

---

# 11. Supplier

Fields:
```text
id
name
contact_name
phone
email
address
created_at
updated_at
```

---

# 12. Item

Fields:
```text
id
sku
name
description
category_id
location_id
supplier_id
quantity
minimum_stock
unit
condition
status
image_url
created_at
updated_at
```

Condition:
```text
GOOD
MINOR_DAMAGE
DAMAGED
```

Status:
```text
ACTIVE
INACTIVE
LOST
DISPOSED
```

Units:
```text
PCS
UNIT
SET
BOX
METER
```

SKU:
- required
- unique
- normalized
- case-insensitive uniqueness where practical

---

# 13. Stock transaction

Fields:
```text
id
item_id
type
quantity
from_location_id
to_location_id
note
performed_by
created_at
```

Types:
```text
IN
OUT
ADJUSTMENT
TRANSFER
RETURN
```

Business rules:
- quantity must be > 0 for movement records
- stock cannot become negative
- every mutation must be recorded
- transactions are append-oriented and auditable
- don't silently modify or delete historical transaction records

---

# 14. Borrowing

Borrowing:

```text
id
borrower_name
borrower_identifier
borrower_contact
purpose
borrowed_at
expected_return_at
returned_at
status
approved_by
created_by
note
```

Statuses:
```text
PENDING
BORROWED
PARTIALLY_RETURNED
RETURNED
OVERDUE
CANCELLED
```

BorrowingItem:

```text
id
borrowing_id
item_id
quantity
returned_quantity
condition_before
condition_after
```

Create borrowing atomically:
1. validate available stock
2. create borrowing
3. create borrowing items
4. decrease stock
5. create stock transaction
6. create audit log
7. commit

Return atomically:
1. validate return quantity
2. increase stock
3. update returned quantity
4. update borrowing status
5. create RETURN transaction
6. create audit log
7. commit

---

# 15. Audit log

Fields:
```text
id
user_id
action
entity
entity_id
metadata
created_at
```

Log important operations:
- login/security events as appropriate
- create/update/delete item
- stock in/out
- adjustment
- transfer
- borrowing
- return
- user creation
- role change
- user deactivation

Never store:
- passwords
- session secrets
- API keys
- DATABASE_URL

---

# 16. Authorization

Use Laravel middleware and policies/gates.

ADMIN:
- everything

STAFF:
- inventory CRUD
- categories
- locations
- suppliers
- stock operations
- borrowing
- return
- reports

VIEWER:
- dashboard
- read inventory
- read master data
- reports

Users management is ADMIN only.

Do not rely on hidden Blade buttons for security.

Every mutation must be checked server-side.

---

# 17. Validation

Use Form Request classes where practical.

Validate:
- IDs
- SKU
- names
- email
- numbers
- quantities
- dates
- enum/state values
- pagination
- search
- sorting
- filters

Do not trust client-side validation.

For destructive operations, require explicit confirmation in UI and server-side authorization.

---

# 18. Inventory rules

Stock status:

```text
quantity > minimum_stock
    NORMAL

quantity > 0 AND quantity <= minimum_stock
    LOW

quantity = 0
    OUT_OF_STOCK
```

Never allow negative stock.

Do not let normal item edit directly alter quantity.

Use stock transactions for:
- IN
- OUT
- ADJUSTMENT
- TRANSFER
- RETURN

---

# 19. Search / filters / pagination

Inventory list must support:
- search by SKU
- search by item name
- category filter
- location filter
- status filter
- condition filter
- stock-status filter
- sorting
- pagination

Whitelist sorting fields.

Default:
```text
page size = 20
```

Use database pagination.

Do not load all inventory rows into PHP just to display the first page.

---

# 20. Dashboard

Show:
- Total Items
- Total Stock
- Low Stock
- Out of Stock
- Currently Borrowed
- Recent Transactions
- Recent Borrowings
- Low Stock Items
- Out of Stock Items

All values must come from the database in production.

Never hardcode dashboard numbers.

---

# 21. Blade / frontend rules

Use Blade as the default view layer.

Use Blade components for reusable UI:
- button
- input
- select
- table
- status badge
- modal/dialog
- pagination
- page header
- empty state
- loading state
- error state

Use Alpine.js only for lightweight client interaction:
- dropdown
- modal
- drawer
- filter toggle
- simple confirmation
- simple UI state

Do not move business logic into Alpine.

Use Tailwind CSS based on `design.md`.

---

# 22. Design system

`design.md` is the single visual source of truth.

Colors:
```text
Primary    #FF3D00
Secondary  #00E5FF
Tertiary   #FFEA00
Background #121212
Surface    #1E1E1E
Success    #00E676
Warning    #FFEA00
Error      #FF1744
Info       #00E5FF
```

Fonts:
```text
Bebas Neue
Work Sans
Space Mono
```

Cards:
```text
radius 0px
```

Buttons / inputs:
```text
radius 8px
```

Dialogs:
```text
radius 12px
```

Chips:
```text
radius 2px
```

No gradients.

No generic white SaaS styling.

No excessive glow.

---

# 23. Responsive requirements

Test:
```text
320px
375px
768px
1024px
1280px
1440px
```

Mobile:
- sidebar becomes drawer
- form becomes one column
- inventory table becomes a controlled list/card or safe horizontal scroll
- actions remain reachable
- no unintended horizontal overflow

Desktop:
- sidebar visible
- dense inventory table
- multi-column dashboard

---

# 24. Accessibility

Use:
- semantic HTML
- labels
- keyboard navigation
- visible focus state
- meaningful button text
- alt text
- contrast
- screen-reader-friendly status text

Status cannot rely on color alone.

---

# 25. Laravel filesystem rules for Vercel

Vercel Functions use a read-only filesystem except for writable `/tmp` scratch space. Do not rely on persistent local writes. citeturn378328search0

Therefore:

DO:
- use MySQL for database data
- use database sessions if session persistence is needed
- use database cache if persistent cache is needed
- log to stderr
- use `/tmp` only for temporary runtime files
- use external/object storage for persistent uploads if uploads are added later

DO NOT:
- write persistent uploads to `storage/app`
- depend on local filesystem state surviving requests
- depend on `storage/logs/laravel.log`
- run file-based cache as a required production feature
- write application state to local files

For MVP, avoid persistent image uploads. Store `image_url` only.

---

# 26. Recommended production environment

Use:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=...
APP_URL=https://your-domain.vercel.app

LOG_CHANNEL=stderr

DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

Adjust variable names to Laravel's current configuration.

Never commit real values.

---

# 27. Database sessions and cache

Because Vercel instances are ephemeral, production session/cache configuration must not depend on durable local files.

Prefer MySQL-backed:
- sessions
- cache

Run required migrations.

If database-backed cache is used, ensure the cache table exists.

---

# 28. Logging

Production should not require:

```text
storage/logs/laravel.log
```

Prefer Laravel's `stderr` logging so logs are available through Vercel's function logs.

If the selected Laravel version or logging stack requires a file path, redirect to `/tmp` rather than assuming a persistent local filesystem.

---

# 29. View compilation / runtime caches

Laravel's production optimization should run during deployment/build.

Use Laravel's normal optimization commands as appropriate:
```bash
php artisan optimize
```

Do not run destructive reset commands in production.

If a Laravel cache/view path must be writable at runtime, configure it under `/tmp`.

Laravel's deployment guidance recommends optimization/caching for production and requires PHP 8.3+ for Laravel 13. citeturn411104search0

---

# 30. Vercel architecture

The application is a Laravel monolith deployed through Vercel's PHP community runtime.

Architecture:

```text
GitHub
  ↓
Vercel Git Integration
  ↓
Vercel Build
  ├── Composer dependencies
  ├── Node dependencies
  ├── Vite asset build
  └── Laravel optimize
  ↓
Vercel PHP Function
  ↓
Laravel
  ↓
MySQL
```

Vercel officially documents community runtimes and explicitly gives `vercel-php` as an example. citeturn378328search0turn378328search1

The PHP runtime itself is community-maintained, not a Vercel official runtime. Verify the runtime version and compatibility before deployment. citeturn629060search0

---

# 31. Vercel entrypoint

Create:

```text
api/index.php
```

It should boot the Laravel application through:

```text
../vendor/autoload.php
../bootstrap/app.php
```

Use the Laravel 13 request handling pattern appropriate for the generated project.

Do not move Laravel's official `public/index.php` into the project root.

---

# 32. Vercel configuration

Create:

```text
vercel.json
```

Use the current supported Vercel community PHP runtime.

The current runtime project demonstrates:

```json
{
  "functions": {
    "api/*.php": {
      "runtime": "vercel-php@0.9.0"
    }
  },
  "routes": [
    {
      "src": "/(.*)",
      "dest": "/api/index.php"
    }
  ]
}
```

When static Vite assets are needed, add explicit routes for:
- `/build/*`
- `/favicon.ico`
- `/robots.txt`

so they resolve to `public/`.

Do not blindly copy old `builds` configurations if the current runtime works with `functions`; prefer current Vercel configuration. Vercel documents the `functions` property for community runtimes. citeturn378328search1turn378328search4

---

# 33. Frontend build on Vercel

Use npm for Vite.

Required files:
```text
package.json
package-lock.json
vite.config.*
resources/css/*
resources/js/*
```

Production build:
```bash
npm ci
npm run build
```

Laravel deployment should also run:
```bash
php artisan optimize
```

The `vercel-php` runtime supports Composer and a Composer `vercel` script for build-time commands. citeturn629060search0

Prefer putting the build sequence in a reliable deployment script rather than requiring the user to manually run asset builds.

---

# 34. Composer scripts

When useful, define a deployment script in `composer.json` such as:

```json
{
  "scripts": {
    "vercel": [
      "npm ci",
      "npm run build",
      "@php artisan optimize"
    ]
  }
}
```

Only use this exact structure if compatible with the generated Composer configuration and current `vercel-php` behavior.

Do not add commands that require secrets unavailable at build time.

---

# 35. Static assets

Vite outputs production assets under:

```text
public/build
```

Ensure Vercel routing serves these files directly rather than sending them through Laravel when possible.

Do not expose:
- `.env`
- source configuration
- database credentials
- private application files

---

# 36. Vercel environment variables

Configure on Vercel:
```text
APP_ENV
APP_KEY
APP_DEBUG
APP_URL
LOG_CHANNEL

DB_CONNECTION
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
DB_SSLMODE

SESSION_DRIVER
CACHE_STORE
QUEUE_CONNECTION
```

Add:
```text
ASSET_URL
```
only if required by the selected asset strategy.

Do not put production values into GitHub.

---

# 37. GitHub

Repository must be Git-ready.

Required:
```text
README.md
AGENTS.md
PRD.md
design.md
.env.example
.gitignore
vercel.json
api/index.php
composer.json
composer.lock
package.json
package-lock.json
```

Use:
```text
main
```
for production.

Feature branches:
```text
feat/<feature>
fix/<feature>
refactor/<feature>
chore/<feature>
docs/<feature>
```

Use Conventional Commits.

---

# 38. GitHub -> Vercel workflow

Target:

```text
Local Linux development
        ↓
       Git
        ↓
     GitHub
        ↓
 Vercel Git Integration
        ↓
Preview Deployment
        ↓
Production Deployment
```

Every push to the connected production branch should trigger deployment after Vercel is connected.

Do not depend on manual FTP uploads.

---

# 39. Database deployment

The Git repository does not host the MySQL database.

Use an external MySQL provider reachable by Vercel.

This is external Mysql Provider

Host	        :   wftuqljwesiffol6.cbetxkdyhwsb.us-east-1.rds.amazonaws.com	
Username	    :   ylyrol1bekxj4oca	
Password	    :   e50gilz7pyysf8sr	
Port	        :   3306	
Database	    :   hj68g0aurismmwrf

Migration command:

```bash
php artisan migrate --force
```

Only run this in a controlled deployment step or manual production deployment workflow.

NEVER run:
```bash
php artisan migrate:fresh
php artisan migrate:refresh
php artisan db:wipe
```
against production.

Migrations must be committed.

---

# 40. Seed data

Provide development seed data:
- 1 admin
- 1 staff
- 1 viewer
- realistic categories
- realistic locations
- realistic suppliers
- 10-20 inventory records

Never use production credentials in seed data.

Production seeding must be a conscious explicit action.

---

# 41. Testing

Unit tests:
- stock classification
- stock validation
- permission checks
- SKU validation
- borrowing states

Feature tests:
- authentication
- inventory CRUD
- stock transactions
- borrowing
- returns
- role restrictions
- audit log

Browser/E2E tests:
- login
- dashboard
- create item
- edit item
- search
- filter
- stock IN
- stock OUT
- borrowing
- partial return
- full return
- logout

---

# 42. Commands

Before completion, run:

```bash
composer validate
php artisan test
npm run build
php artisan route:list
php artisan config:clear
php artisan view:clear
php artisan optimize
```

Also run:
```bash
php artisan about
php artisan migrate:status
```

Run Laravel Pint if configured:
```bash
./vendor/bin/pint --test
```

Run frontend lint/type checks if configured.

Use existing `composer.json` / `package.json` scripts when available.

---

# 43. Local Vercel testing

Local Laravel:
```bash
php artisan serve
```

Frontend dev assets:
```bash
npm run dev
```

For Vercel-specific testing, use Vercel CLI if installed:

```bash
vercel dev
```

The PHP runtime documentation notes that `vercel dev` requires PHP installed locally. citeturn629060search0

Do not require Vercel CLI for ordinary Laravel development.

---

# 44. Error handling

Use:
- validation errors
- not found pages
- authorization errors
- friendly server error pages

Do not expose:
- SQL exceptions
- stack traces
- file paths
- environment variables

Production:
```text
APP_DEBUG=false
```

---

# 45. UX states

Every async operation needs:
- loading state
- success state
- empty state
- error state

Mutation UX:
- prevent duplicate submit
- show clear result
- preserve entered values on validation error

---

# 46. Design implementation

Use `design.md` directly.

Do not create a second visual system.

Important:
- square inventory cards
- dark background
- controlled orange glow
- cyan information accents
- yellow warnings
- green success
- red errors
- Bebas Neue for strong headings
- Work Sans for body
- Space Mono for identifiers
- no gradients
- no playful animation

---

# 47. Performance

Prioritize:
- DB pagination
- indexed queries
- Eloquent eager loading where useful
- avoid N+1 queries
- optimized Vite output
- efficient Blade rendering
- minimal client-side JavaScript

For lists:
- use `paginate()`
- use `with()` for required relationships
- select only required fields where useful

---

# 48. Security

Protect against:
- SQL injection
- XSS
- CSRF
- IDOR
- broken access control
- session abuse
- credential leakage

Use:
- Laravel CSRF middleware
- policies/gates
- request validation
- Eloquent parameter binding
- secure password hashing
- secure session configuration

---

# 49. No overengineering

Do not introduce:
- Next.js
- microservices
- GraphQL
- Kubernetes
- Kafka
- Redis
- complex event buses
- unnecessary queues

unless a real project requirement appears.

MVP should remain a maintainable Laravel monolith.

---

# 50. Completion checklist

The project is not complete until:

```text
[ ] Laravel application runs locally
[ ] MysqlSQL migrations work
[ ] authentication works
[ ] authorization works server-side
[ ] inventory CRUD works
[ ] categories work
[ ] locations work
[ ] suppliers work
[ ] stock IN works
[ ] stock OUT works
[ ] stock adjustment works
[ ] stock transfer works
[ ] negative stock rejected
[ ] borrowing works
[ ] partial return works
[ ] full return works
[ ] overdue status works
[ ] dashboard works
[ ] search works
[ ] filters work
[ ] sorting works
[ ] pagination works
[ ] reports work
[ ] CSV export works
[ ] user management works
[ ] audit log works
[ ] responsive UI works
[ ] design.md is respected
[ ] tests pass
[ ] production assets build
[ ] Vercel configuration exists
[ ] api/index.php exists
[ ] no secrets committed
[ ] GitHub repository ready
[ ] Vercel deployment tested
```

---

# 51. Final agent behavior

For each task:

1. Read `AGENTS.md`, `PRD.md`, `design.md`.
2. Inspect the current repository.
3. Preserve existing valid work.
4. Implement the smallest correct Laravel change.
5. Test locally.
6. Build frontend assets.
7. Verify deployment configuration.
8. Inspect `git diff`.
9. Report exact results.

Do not merely explain what to do.

Actually modify the repository.

Final priorities:

```text
correctness
security
data integrity
Laravel compatibility
Vercel compatibility
maintainability
design consistency
performance
```
