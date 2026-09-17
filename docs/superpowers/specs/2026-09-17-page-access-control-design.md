# Page/Controller Access Control Hardening — Design Spec

Date: 2026-09-17
Status: Approved for planning

## 1. Problem

Authorization today is a single boolean: `auth` middleware (must be logged in).
The existing `roles -> role_access -> access.code` chain (see `$userAccess` in
[sidebar.blade.php:11-19](../../../resources/views/layouts/sidebar.blade.php#L11)
and [Login.php:57-63](../../../app/Http/Livewire/Auth/Login.php#L57)) is only
used to hide/show sidebar links and to pick a post-login redirect. It is never
enforced server-side. Any authenticated user can hit any of the ~130 routes in
`routes/web.php` directly by URL — including `/security-management` and
`/role-management` — regardless of their role's assigned access codes.

Goal: enforce the same access codes at the route level, so a user without a
code gets a 403 even if they type/bookmark the URL directly.

## 2. Confirmed decisions (from brainstorming)

- **Reuse existing `access.code` values** already used by the sidebar — no new
  granularity (no separate view/create/edit/delete permissions), no schema
  change to `roles`/`access`/`role_access`/`user_roles`.
- **Unauthorized behavior**: standard Laravel 403 page for normal navigation;
  Laravel's default exception handler already returns JSON automatically for
  `expectsJson()`/AJAX requests, so no extra branching is needed.
- **Super Admin bypass**: a new wildcard access code, `*`. A role that has `*`
  in its assigned access list bypasses every `access:` check. No new column —
  just one new row in `access` (`code = '*'`) assigned to the admin role(s)
  through the existing Role Management UI.
- **Enforcement layer**: route middleware (`->middleware('access:CODE')`) in
  `routes/web.php`, not per-controller/component checks. Centralizes the
  change to one file + one new middleware class.

## 3. Verified data model (no changes)

```
User --(user_roles: user_id, role_id)--> Role --(role_access: role_id, access_id)--> Access(code)
```

Confirmed live in `app/Models/User.php`, `Role.php` (`users()`, `access()`),
`Access.php` (`role()`). `app/Models/UserRoles.php` has dead methods
(`users()`/`roles()`) referencing an unused `useraccess_role` pivot — verified
via grep that nothing calls them; not part of the live flow and out of scope
for this change.

## 4. New components

### 4.1 `App\Services\AccessService`

Single source of truth for resolving a user's access codes, replacing the
duplicated `Cache::remember('user_access_' . id, ...)` snippet currently in
`sidebar.blade.php` and `Login.php`.

```php
namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;

class AccessService
{
    public function codesFor(?Authenticatable $user): array
    {
        if (! $user) {
            return [];
        }

        return Cache::remember(
            'user_access_' . $user->getAuthIdentifier(),
            600,
            fn () => $user->roles->flatMap->access->pluck('code')->unique()->toArray()
        );
    }

    public function hasAny(?Authenticatable $user, array $codes): bool
    {
        $userCodes = $this->codesFor($user);

        if (in_array('*', $userCodes, true)) {
            return true;
        }

        if (empty($codes)) {
            return true; // middleware applied with no codes = auth-only, no extra gate
        }

        return (bool) array_intersect($codes, $userCodes);
    }
}
```

`sidebar.blade.php` and `Login.php` are updated to call
`app(AccessService::class)->codesFor(auth()->user())` instead of duplicating
the cache logic.

### 4.2 `App\Http\Middleware\CheckAccess`

```php
namespace App\Http\Middleware;

use App\Services\AccessService;
use Closure;
use Illuminate\Http\Request;

class CheckAccess
{
    public function __construct(private AccessService $accessService)
    {
    }

    public function handle(Request $request, Closure $next, string ...$codes)
    {
        if (! $this->accessService->hasAny($request->user(), $codes)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
```

Registered as alias in `app/Http/Kernel.php` `$middlewareAliases`:
`'access' => \App\Http\Middleware\CheckAccess::class,`

Usage supports OR-of-multiple-codes for routes shared by two sections, e.g.
`->middleware('access:JAM-KERJA-INFURE,JAM-KERJA-SEITAI')`.

### 4.3 New `access` row (data change, not schema)

Insert one row via a migration (safe — migrating data into an existing,
unversioned table is fine regardless of how that table was originally
created):

```php
DB::table('access')->insertOrIgnore([
    'access_name' => 'Full Access (Super Admin)',
    'code' => '*',
    'description' => 'Bypasses all access: middleware checks',
    'status' => 1,
]);
```

After migration, an operator assigns this new "Full Access" entry to the
Super Admin role via the existing Role Management screen
(`role-management`) — no code change needed for that step.

## 5. Route → access code mapping

Applied directly in `routes/web.php`, grouped the same way the file is
already commented/sectioned. Codes match exactly what already gates the
matching sidebar section in `sidebar.blade.php`.

| Section (web.php) | Routes | `access:` code |
|---|---|---|
| Printer Settings | `/printer-settings` | *(none — auth only, not in sidebar today)* |
| Order LPK | `order-lpk`, `edit-order`, `add-order`, `lpk-entry`, `add-lpk`, `edit-lpk`, `cetak-lpk`, `order-report`, `report-lpk`, `cetak-order` | `ORDER` |
| Nippo Infure | `nippo-infure`, `edit-nippo`, `add-nippo`, `get-print-data/{id}`, `loss-infure`, `checklist-infure`, `label-gentan`, `report-checklist-infure`, `report-nippo-infure` (both defs), `report-gentan` (both defs) | `NIPPO-INFURE` |
| Nippo Seitai | `nippo-seitai`, `add-seitai`, `edit-seitai`, `loss-seitai`, `add-loss`, `mutasi-isi-palet`, `check-list-seitai`, `label-masuk-gudang`, `report-masuk-gudang`, `report-checklist-seitai`, `report-loss-seitai` | `NIPPO-SEITAI` |
| Jam Kerja (Infure) | `infure-jam-kerja` | `JAM-KERJA-INFURE` |
| Jam Kerja (Seitai) | `seitai-jam-kerja` | `JAM-KERJA-SEITAI` |
| Jam Kerja (shared) | `checklist-jam-kerja` | `JAM-KERJA-INFURE,JAM-KERJA-SEITAI` (OR) |
| Kenpin | `kenpin-infure`, `add-kenpin-infure`, `edit-kenpin-infure`, `kenpin-seitai-kenpin`(route `/kenpin-seitai`), `add-kenpin-seitai`, `edit-kenpin-seitai`, `mutasi-isi-palet-kenpin`, `print-label-gudang-kenpin`, `report-kenpin` | `KENPIN` |
| Warehouse (ops) | `penarikan-palet`, `pengembalian-palet`, `label-masuk-gudang-report` | `WAREHOUSE` |
| Report | `general-report`, `detail-report`, `production-loss-report` | `REPORT` |
| Master Tabel | `buyer`, `tipe-produk`, `jenis-produk`, `department`(`/departemen`), `working-shift`, `warehouse`(`/warehouse`, master page), `mesin`, `bagian-mesin`, `detail-bagian-mesin`, `jadwal-mesin`, `karyawan`, `menu-katanuki`, `product`(`/master-produk`), `add-master-product`, `edit-master-product`, `menu-loss-infure`, `menu-loss-katagori`, `menu-loss-kenpin`, `menu-loss-klasifikisasi`, `menu-loss-seitai`, `kemasan-box`, `kemasan-gasio`, `kemasan-inner`, `kemasan-layer`, `master-jam-mati-mesin-infure`, `master-jam-mati-mesin-seitai`, `masalah-kenpin-infure`, `masalah-kenpin-seitai` | `MASTER` |
| Administration | `security-management`, `add-user`, `edit-user`, `role-management` | `ADM` |
| Inventory (hidden menu, `d-none` in sidebar today) | `pemasukan-barang`, `pengeluaran-barang`, `posisi-wip`, `bahan-baku`, `barang-jadi`, `mesin-peralatan`, `barang-reject` | `ADMIN` |
| Dashboard Infure (page + all AJAX sub-routes under `DashboardInfureController`/`DashboardInfureControllerOld`) | `dashboard-infure*`, `dashboard-infure-old*` (11 sub-routes) | `DASHBOARD-INFURE` |
| Dashboard Seitai | `dashboard-seitai` | `DASHBOARD-SEITAI` |
| Ungated utility | `/`, `/test`, `/docs/{file}` (already has its own `auth` middleware), `{any}` catch-all | *(none — auth only, unchanged)* |

Not in sidebar and not clearly tied to one module → left **auth-only** (no
`access:` middleware), per the confirmed decision to not add restrictions
beyond what's already expressed in the sidebar today: `/printer-settings`,
`/test`, `/`.

Note: `/report-nippo-infure` and `/report-gentan` are each defined twice in
`routes/web.php` (lines 326-329 & 348-351, and 331-346 & 368-383) with the
same URI/name — only the later definition is ever actually routed to (later
registration wins on duplicate name/URI). Both definitions get the
`NIPPO-INFURE` middleware for consistency, but this duplication is
pre-existing dead code, not something this change needs to clean up.

## 6. Out of scope (flagged, not fixed here)

`Route::get('{any}', [HomeController::class, 'index'])` at the end of the
`auth` group renders any Blade view name matched from the URL — a
pre-existing broad surface (not part of the access-code gap this task
fixes). Will be flagged as a separate follow-up after this change ships, not
modified here.

## 7. Testing

New `tests/Feature/AccessControlTest.php`:
- User with no access codes → GET a representative gated route (e.g.
  `/security-management`) → 403.
- User with the matching code → 200.
- User whose role has `*` → 200 on any gated route.
- Guest (not logged in) → still redirected to login (existing `auth`
  behavior, unchanged).
- Sidebar/Login post-login redirect still works after switching to
  `AccessService` (regression check, not new behavior).

## 8. Rollout note

Because every currently-authenticated session's sidebar already reflects
correct access codes (the cosmetic gate has existed and been correctly
populated), no user should lose access to a page they can already see in
their sidebar. The risk is the inverse: a role that's missing a code for a
page it should have (e.g. codes assigned inconsistently in the past). Ask
the user to spot-check a couple of real accounts per role after deploy.
