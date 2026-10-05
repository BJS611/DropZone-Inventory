# PRD.md

# DropZone Inventory
## Product Requirements Document — Laravel Edition

Version: 2.0
Status: Implementation Ready

---

# 1. Product overview

DropZone Inventory is a complete web-based inventory management system built with Laravel.

The product is intended for organizations, laboratories, teams, or administrative units that need to manage:
- inventory
- stock
- locations
- suppliers
- borrowing
- returns
- reports
- users
- permissions
- audit history

The visual identity follows `design.md`.

---

# 2. Required technology

Mandatory application framework:

```text
Laravel 13.x
```

Backend:
```text
PHP 8.3+
Laravel
Eloquent ORM
Laravel validation
Laravel policies/gates
Blade
```

Frontend:
```text
Blade
Tailwind CSS
Alpine.js
Vite
```

Database:
```text
MySQL
```

Tooling:
```text
Composer
npm
Git
```

Deployment:
```text
GitHub
Vercel
```

Vercel deployment uses the PHP community runtime `vercel-php`.

Vercel documents PHP as a community runtime, not an official first-party runtime. The current `vercel-php` project supports Composer and lists current PHP runtime versions. The implementation MUST verify runtime compatibility during deployment. citeturn378328search0turn629060search0

---

# 3. Deployment architecture

```text
Developer Linux
      ↓
    Git
      ↓
   GitHub
      ↓
 Vercel Git Integration
      ↓
 Vercel Build
      ↓
 Laravel PHP Function
      ↓
 MySQL
```

GitHub is the source repository.

Vercel is the application hosting/deployment platform.

MySQL is the persistent database.

The application must not depend on persistent local filesystem state because Vercel Functions have a read-only filesystem except for temporary `/tmp` storage. citeturn378328search0

---

# 4. Objectives

1. Centralize inventory data.
2. Maintain accurate stock.
3. Make every important stock change auditable.
4. Restrict operations by role.
5. Make items searchable and filterable.
6. Track borrowing and return.
7. Provide operational dashboard.
8. Provide reports and CSV exports.
9. Maintain a consistent DropZone visual system.
10. Make the app deployable from GitHub to Vercel.

---

# 5. Non-goals

Not part of MVP:
- ecommerce
- shopping cart
- checkout
- payment
- marketplace
- accounting
- payroll
- multi-tenant SaaS
- native mobile app
- AI forecasting
- asset depreciation
- advanced procurement
- persistent local file uploads on Vercel

---

# 6. Users

## ADMIN

Full administrative access.

Can:
- manage users
- manage inventory
- manage master data
- perform stock operations
- manage borrowing
- view audit logs
- view/export reports

## STAFF

Operational access.

Can:
- create/edit inventory
- manage stock
- manage borrowing
- return items
- manage categories
- manage locations
- manage suppliers
- view/export reports

Cannot:
- manage user roles
- administer users

## VIEWER

Read-only access.

Can:
- view dashboard
- view inventory
- view details
- view reports

Cannot mutate data.

---

# 7. Permission matrix

| Feature | ADMIN | STAFF | VIEWER |
|---|---:|---:|---:|
| Dashboard | Yes | Yes | Yes |
| Inventory read | Yes | Yes | Yes |
| Inventory create | Yes | Yes | No |
| Inventory update | Yes | Yes | No |
| Inventory delete | Yes | Limited | No |
| Stock IN | Yes | Yes | No |
| Stock OUT | Yes | Yes | No |
| Stock adjustment | Yes | Yes | No |
| Stock transfer | Yes | Yes | No |
| Borrowing | Yes | Yes | No |
| Return | Yes | Yes | No |
| Category management | Yes | Yes | Read |
| Location management | Yes | Yes | Read |
| Supplier management | Yes | Yes | Read |
| Reports | Yes | Yes | Yes |
| CSV export | Yes | Yes | Yes |
| User management | Yes | No | No |
| Audit log | Yes | Limited | No |

All permissions must be enforced server-side using Laravel authorization.

---

# 8. Authentication

Required:
- login
- logout
- session authentication
- password hashing
- inactive-user blocking
- validation errors

Login fields:
```text
email
password
```

Invalid credentials must return a generic message.

Do not reveal whether a specific email exists.

---

# 9. Dashboard

The dashboard must show live database data.

Required KPI:
- Total Items
- Total Stock
- Low Stock
- Out of Stock
- Currently Borrowed

Additional sections:
- recent transactions
- recent borrowings
- low-stock items
- out-of-stock items
- recently added items

No hardcoded production numbers.

---

# 10. Inventory module

## Inventory list

Columns:
```text
SKU
Nama Barang
Kategori
Lokasi
Stok
Minimum
Kondisi
Status
Updated
Actions
```

## Search

Search:
- SKU
- name

## Filters

- category
- location
- status
- condition
- stock status

## Sorting

- name
- SKU
- quantity
- created_at
- updated_at

## Pagination

Default:
```text
20
```

Options:
```text
10
20
50
100
```

Database pagination is required.

---

# 11. Item data

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

SKU must be unique.

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

---

# 12. Create item

Form:
```text
SKU
Name
Description
Category
Location
Supplier
Initial Quantity
Minimum Stock
Unit
Condition
Status
Image URL
```

Required fields:
```text
SKU
Name
Category
Location
Initial Quantity
Minimum Stock
Unit
Condition
Status
```

If initial quantity > 0:
- create item
- create initial `IN` transaction
- create audit event

---

# 13. Edit item

Allowed:
```text
name
description
category
location
supplier
minimum_stock
unit
condition
status
image_url
```

Quantity MUST NOT be changed directly through the standard item edit form.

Use stock transactions.

---

# 14. Item detail

Show:
- item
- SKU
- status
- current stock
- minimum stock
- condition
- category
- location
- supplier
- created
- updated

History:
- stock transactions
- borrowing history
- audit history

---

# 15. Stock status

```text
quantity > minimum_stock
    NORMAL

quantity > 0 AND quantity <= minimum_stock
    LOW

quantity = 0
    OUT_OF_STOCK
```

Visual:
```text
NORMAL       #00E676
LOW          #FFEA00
OUT_OF_STOCK #FF1744
```

Use explicit text labels as well as colors.

---

# 16. Stock transactions

Transaction types:
```text
IN
OUT
ADJUSTMENT
TRANSFER
RETURN
```

All stock-changing operations must:
- validate authorization
- validate item
- validate quantity
- preserve data integrity
- write transaction history
- write audit history

---

# 17. Stock IN

Use cases:
- initial stock
- procurement
- donation
- physical return

Input:
```text
Item
Quantity
Location
Note
```

Result:
```text
quantity += amount
```

---

# 18. Stock OUT

Use cases:
- usage
- damage
- loss
- disposal

Input:
```text
Item
Quantity
Reason
Note
```

Result:
```text
quantity -= amount
```

Reject when:
```text
result < 0
```

---

# 19. Stock adjustment

Input:
```text
Item
Current Stock
New Stock
Reason
Note
```

Calculation:
```text
difference = new_stock - current_stock
```

Record as:
```text
ADJUSTMENT
```

Never silently alter stock without recording the adjustment.

---

# 20. Stock transfer

Input:
```text
Item
Quantity
From Location
To Location
Note
```

Rules:
- source stock must be enough
- destination must be valid
- record transfer transaction
- update item location where the model uses a single current location
- write audit record

Use one database transaction.

---

# 21. Borrowing

Borrowing fields:
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

Borrowing item:
```text
id
borrowing_id
item_id
quantity
returned_quantity
condition_before
condition_after
```

---

# 22. Borrowing workflow

1. create borrowing
2. select borrower
3. select items
4. specify quantities
5. validate stock
6. confirm
7. decrease stock
8. create transaction
9. create audit
10. finish

All related operations must be atomic.

---

# 23. Return workflow

Support:
- full return
- partial return

Inputs:
```text
return quantity
condition after
note
```

On successful return:
- increase stock
- update returned quantity
- create RETURN transaction
- create audit event
- update status

Statuses:
```text
all returned -> RETURNED
some returned -> PARTIALLY_RETURNED
past due and not fully returned -> OVERDUE
```

---

# 24. Categories

Fields:
```text
name
code
description
```

Rules:
- name required
- code required
- code unique

Display:
```text
Name
Code
Item Count
Created
Actions
```

---

# 25. Locations

Fields:
```text
name
code
description
```

Display:
```text
Name
Code
Item Count
Actions
```

---

# 26. Suppliers

Fields:
```text
name
contact_name
phone
email
address
```

Display:
```text
Supplier
Contact
Phone
Email
Item Count
Actions
```

---

# 27. Users

ADMIN can:
- create
- edit
- change role
- deactivate
- reset password

Statuses:
```text
ACTIVE
INACTIVE
```

Do not allow an action that leaves the application without an administrator.

---

# 28. Audit log

Track:
```text
item create
item update
item delete
stock in
stock out
stock adjustment
stock transfer
borrowing create
borrow return
user create
role change
user deactivation
```

Fields:
```text
timestamp
user
action
entity
entity_id
metadata
```

Never store passwords or secrets.

---

# 29. Reports

Required reports:
- Inventory Report
- Low Stock Report
- Transaction Report
- Borrowing Report

Inventory columns:
```text
SKU
Name
Category
Location
Stock
Minimum
Condition
Status
```

Low stock:
```text
quantity <= minimum_stock
```

Transaction:
```text
Date
Type
Item
Quantity
Performed By
Note
```

Borrowing:
```text
Borrower
Item
Quantity
Borrowed At
Expected Return
Returned At
Status
```

---

# 30. CSV export

Required.

Exports must honor:
- search
- filters
- sorting

Suggested filenames:
```text
inventory-YYYY-MM-DD.csv
transactions-YYYY-MM-DD.csv
borrowings-YYYY-MM-DD.csv
```

---

# 31. UI requirements

Language:
```text
Bahasa Indonesia
```

Primary navigation:
```text
Dashboard
Inventory
Transactions
Borrowings
Categories
Locations
Suppliers
Reports
Users
Settings
```

Navigation must respect user role.

---

# 32. Design requirements

`design.md` is the single visual authority.

Colors:
```text
#FF3D00
#00E5FF
#FFEA00
#121212
#1E1E1E
#00E676
#FF1744
```

Fonts:
```text
Bebas Neue
Work Sans
Space Mono
```

Card radius:
```text
0px
```

Buttons/inputs:
```text
8px
```

Dialogs:
```text
12px
```

Chips:
```text
2px
```

No gradients.

No generic white admin template.

No ecommerce components.

---

# 33. UX states

Every data page:
- loading
- success
- empty
- error

Every mutation:
- pending state
- duplicate-submit prevention
- success feedback
- error feedback
- validation feedback

---

# 34. Responsive

Required widths:
```text
320
375
768
1024
1280
1440
```

Mobile:
- sidebar drawer
- one-column forms
- responsive inventory list
- reachable actions
- no overflow

Desktop:
- sidebar
- high-density inventory table
- dashboard grid
- multi-column forms where useful

---

# 35. Accessibility

Must support:
- semantic HTML
- keyboard use
- labels
- visible focus state
- readable contrast
- alt text
- explicit status labels

---

# 36. Security requirements

Protect against:
- SQL injection
- XSS
- CSRF
- IDOR
- broken access control
- credential leakage

Use Laravel's standard security primitives:
- CSRF middleware
- validation/Form Requests
- policies/gates
- Eloquent bindings
- password hashing
- secure sessions

---

# 37. Vercel-specific requirements

The application must be Vercel-compatible.

Because Vercel Functions have a read-only filesystem except for `/tmp`:
- do not depend on local persistent files
- do not depend on `storage/logs`
- do not depend on local uploads
- use database sessions/cache where appropriate
- use external object storage for future persistent file uploads
- keep runtime temporary files in `/tmp`

The current Vercel PHP community runtime supports Laravel examples and Composer, but it is a community runtime rather than an official Vercel runtime. citeturn378328search0turn629060search0

---

# 38. Required deployment files

Repository must contain:
```text
vercel.json
api/index.php
.vercelignore
composer.json
composer.lock
package.json
package-lock.json
vite.config.*
.env.example
```

---

# 39. Vercel entrypoint

`api/index.php` acts as the serverless entrypoint and boots Laravel.

It must load:
```text
vendor/autoload.php
bootstrap/app.php
```

The implementation must follow the Laravel version actually installed.

---

# 40. Vercel configuration

Use Vercel's `functions` configuration with the current compatible PHP community runtime.

Example starting point:

```json
{
  "$schema": "https://openapi.vercel.sh/vercel.json",
  "functions": {
    "api/*.php": {
      "runtime": "vercel-php@0.9.0"
    }
  },
  "routes": [
    {
      "src": "/build/(.*)",
      "dest": "/public/build/$1"
    },
    {
      "src": "/(favicon\\.ico|robots\\.txt)",
      "dest": "/public/$1"
    },
    {
      "src": "/(.*)",
      "dest": "/api/index.php"
    }
  ]
}
```

This is a deployment baseline, not a promise that every project needs identical settings. Verify the current runtime version and asset routing during implementation. Vercel documents community runtime configuration through `vercel.json`; the `vercel-php` project currently documents a similar `functions` plus catch-all route pattern. citeturn378328search1turn629060search0

---

# 41. Vercel environment variables

Required production configuration:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=
APP_URL=

LOG_CHANNEL=stderr

DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

Never commit real values.

---

# 42. Database

Use MySQL compatible with remote TLS connections.

Migrations are committed to Git.

Production migration:
```bash
php artisan migrate --force
```

Never:
```bash
php artisan migrate:fresh
php artisan migrate:refresh
php artisan db:wipe
```

on production.

---

# 43. Build

Required build sequence conceptually:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan optimize
```

The final implementation may express this through Vercel build settings or a Composer `vercel` script, but the effective build must perform the required steps.

The `vercel-php` runtime supports Composer and Composer `vercel` scripts. citeturn629060search0

---

# 44. GitHub deployment flow

The repository should be connected to Vercel.

Expected:
```text
push to GitHub
    ↓
Vercel detects commit
    ↓
build
    ↓
preview or production deployment
```

Production branch:
```text
main
```

Feature branches should create preview deployments when enabled.

---

# 45. Local setup

Required documentation:

```bash
git clone <repo>
cd <repo>

composer install
cp .env.example .env
php artisan key:generate

npm ci

php artisan migrate
php artisan db:seed

npm run dev
php artisan serve
```

If the Laravel dev script is configured differently, document the exact project command.

---

# 46. Testing

Required tests:

## Unit
- stock state
- stock validation
- permission checks
- borrowing state

## Feature
- login
- logout
- inventory CRUD
- category CRUD
- location CRUD
- supplier CRUD
- stock transactions
- borrowing
- return
- permissions
- audit

## Browser/E2E
- login
- create item
- edit item
- search
- filter
- stock IN
- stock OUT
- borrow
- partial return
- full return
- logout

---

# 47. Performance

Use:
- Eloquent pagination
- proper database indexes
- eager loading
- minimized client-side JavaScript
- optimized Vite assets

Avoid N+1 queries.

---

# 48. Definition of done

MVP is complete when:

```text
[ ] Laravel 13 application runs locally
[ ] MySQL migrations pass
[ ] authentication works
[ ] role-based authorization works
[ ] inventory CRUD works
[ ] categories work
[ ] locations work
[ ] suppliers work
[ ] stock IN works
[ ] stock OUT works
[ ] adjustment works
[ ] transfer works
[ ] negative stock is rejected
[ ] borrowing works
[ ] partial return works
[ ] full return works
[ ] overdue status works
[ ] dashboard works
[ ] search works
[ ] filtering works
[ ] sorting works
[ ] pagination works
[ ] reports work
[ ] CSV export works
[ ] user management works
[ ] audit works
[ ] responsive UI works
[ ] design.md is respected
[ ] tests pass
[ ] assets build
[ ] Vercel config exists
[ ] api/index.php exists
[ ] no secret is committed
[ ] GitHub-ready
[ ] Vercel-ready
```

---

# 49. Future features

Not required for MVP:
- QR/barcode scanning
- Excel import
- PDF report
- persistent image uploads
- email notification
- WhatsApp notification
- maintenance scheduling
- advanced asset lifecycle
- forecasting

Do not implement future features without a specific task.

---

# 50. Product success criteria

The system succeeds when:

```text
stock data is correct
stock history is auditable
permissions are enforced
inventory can be found quickly
borrowing is traceable
reports are usable
UI is visually consistent
GitHub is the source repository
Vercel can build and deploy the app
```
