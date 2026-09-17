# Page/Controller Access Control Hardening Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Enforce the existing `access.code` values (currently only used to hide sidebar menu items) at the route level, so an authenticated user without the right code gets a 403 instead of being able to open any of the ~130 routes directly by URL.

**Architecture:** One new `AccessService` (resolves a user's access codes, replacing 2 duplicated cache snippets) + one new `CheckAccess` route middleware (`access:CODE[,CODE...]`) applied directly on each route in `routes/web.php`. No schema change — a new wildcard `*` access code (seeded via migration) lets an admin role bypass every check. A DB-independent route-audit test (`RouteAccessMappingTest`) is the safety net that verifies every authenticated route is either gated with the documented code or explicitly allowlisted as intentionally ungated.

**Tech Stack:** Laravel (this repo's existing version), PHPUnit, Mockery (already a Laravel test dependency).

**Spec:** [docs/superpowers/specs/2026-09-17-page-access-control-design.md](../specs/2026-09-17-page-access-control-design.md)

## Global Constraints

- Reuse existing `access.code` values only — no new granularity, no schema/migration changes to `roles`/`access`/`role_access`/`user_roles`.
- Unauthorized access → plain `abort(403)`. Do not add custom JSON branching — Laravel's default exception handler already returns JSON for `expectsJson()` requests.
- Wildcard bypass code is the literal string `*`.
- **This sandbox cannot reach the project's dev Postgres DB** (`192.168.100.100:2121`, confirmed by a hung `php artisan db:table access` call during planning). Any test that touches real `User`/`Role`/`Access` Eloquent relations (i.e. calls `AccessService::codesFor()` un-mocked) **cannot be run here** — write it, but run it on a host with DB access (e.g. the deploy/dev server). Tests that only boot the framework (Feature tests hitting a route/middleware with a **mocked** `AccessService`, or pure route introspection) do NOT touch DB and run fine here — this was verified during planning (`php artisan test tests/Feature/ExampleTest.php` completed in ~5s against a guest `GET /` request, no hang).
- Per the user's global CLAUDE.md rules: every `git commit` other than pure docs/rename must go through the `code-reviewer` subagent (BLOCKER/SHOULD-FIX/NICE-TO-HAVE table) before committing, and every commit needs explicit user confirmation first.
- `routes/web.php` already has all target routes inside `Route::group(['middleware' => 'auth'], function () { ... })` (lines 133-441) — every edit in this plan appends `->middleware('access:CODE')` to an existing route definition; it never removes or reorders routes.

---

## File Structure

- Create `app/Services/AccessService.php` — resolves and caches a user's access codes; pure matching logic.
- Create `app/Http/Middleware/CheckAccess.php` — route middleware, thin wrapper around `AccessService::hasAny()`.
- Modify `app/Http/Kernel.php` — register the `access` middleware alias.
- Create `database/migrations/2026_09_17_000000_seed_wildcard_access_code.php` — seeds the `*` bypass code.
- Modify `resources/views/layouts/sidebar.blade.php` and `app/Http/Livewire/Auth/Login.php` — replace duplicated cache snippets with `AccessService`.
- Modify `routes/web.php` — append `->middleware('access:CODE')` to every route named in the spec's mapping table.
- Modify `routes/api.php` — add `access:DASHBOARD-SEITAI` to the Dashboard Seitai AJAX group (found during Task 5, see spec addendum).
- Create `tests/Unit/AccessServiceMatchesTest.php` — pure logic test (DB-independent).
- Create `tests/Feature/CheckAccessMiddlewareTest.php` — middleware test with mocked `AccessService` (DB-independent).
- Create `tests/Feature/RouteAccessMappingTest.php` — the full route-audit safety net (DB-independent).
- Create `tests/Feature/AccessControlTest.php` — real HTTP + real DB end-to-end test (**cannot run in this sandbox**, run externally).

---

### Task 1: AccessService

**Files:**
- Create: `app/Services/AccessService.php`
- Test: `tests/Unit/AccessServiceMatchesTest.php`

**Interfaces:**
- Produces: `App\Services\AccessService::matches(array $userCodes, array $requiredCodes): bool`, `::codesFor(?Authenticatable $user): array`, `::hasAny(?Authenticatable $user, array $codes): bool`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit;

use App\Services\AccessService;
use PHPUnit\Framework\TestCase;

class AccessServiceMatchesTest extends TestCase
{
    private AccessService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AccessService();
    }

    public function test_matches_true_when_user_has_required_code(): void
    {
        $this->assertTrue($this->service->matches(['ORDER', 'MASTER'], ['ORDER']));
    }

    public function test_matches_false_when_user_lacks_required_code(): void
    {
        $this->assertFalse($this->service->matches(['MASTER'], ['ORDER']));
    }

    public function test_matches_true_for_any_of_multiple_required_codes(): void
    {
        $this->assertTrue($this->service->matches(['JAM-KERJA-SEITAI'], ['JAM-KERJA-INFURE', 'JAM-KERJA-SEITAI']));
    }

    public function test_matches_true_when_user_has_wildcard(): void
    {
        $this->assertTrue($this->service->matches(['*'], ['ADM']));
    }

    public function test_matches_true_when_no_codes_required(): void
    {
        $this->assertTrue($this->service->matches([], []));
    }

    public function test_matches_false_when_user_has_no_codes(): void
    {
        $this->assertFalse($this->service->matches([], ['ORDER']));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Unit/AccessServiceMatchesTest.php`
Expected: FAIL — `Class "App\Services\AccessService" not found`

- [ ] **Step 3: Write the implementation**

```php
<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;

class AccessService
{
    /**
     * Resolve the cached list of access codes for a user (User -> Role -> Access.code).
     * Same cache key/TTL as the pre-existing sidebar/login snippets it replaces.
     */
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
        return $this->matches($this->codesFor($user), $codes);
    }

    /**
     * '*' bypasses everything; no required codes means "no extra gate beyond auth".
     */
    public function matches(array $userCodes, array $requiredCodes): bool
    {
        if (in_array('*', $userCodes, true)) {
            return true;
        }

        if ($requiredCodes === []) {
            return true;
        }

        return (bool) array_intersect($requiredCodes, $userCodes);
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Unit/AccessServiceMatchesTest.php`
Expected: PASS (6 tests)

- [ ] **Step 5: Commit**

```bash
git add app/Services/AccessService.php tests/Unit/AccessServiceMatchesTest.php
git commit -m "feat(access): add AccessService with wildcard-aware matching"
```

---

### Task 2: CheckAccess middleware

**Files:**
- Create: `app/Http/Middleware/CheckAccess.php`
- Modify: `app/Http/Kernel.php:57-68` (add alias to `$middlewareAliases`)
- Test: `tests/Feature/CheckAccessMiddlewareTest.php`

**Interfaces:**
- Consumes: `App\Services\AccessService::hasAny(?Authenticatable $user, array $codes): bool` (Task 1)
- Produces: `App\Http\Middleware\CheckAccess::handle(Request $request, Closure $next, string ...$codes)`, registered as route middleware alias `access`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAccess;
use App\Services\AccessService;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CheckAccessMiddlewareTest extends TestCase
{
    public function test_allows_request_when_access_service_grants_it(): void
    {
        $accessService = Mockery::mock(AccessService::class);
        $accessService->shouldReceive('hasAny')->once()->with(null, ['ORDER'])->andReturn(true);

        $middleware = new CheckAccess($accessService);
        $request = Request::create('/order-lpk', 'GET');

        $response = $middleware->handle($request, fn ($req) => new \Illuminate\Http\Response('ok'), 'ORDER');

        $this->assertSame('ok', $response->getContent());
    }

    public function test_aborts_with_403_when_access_service_denies_it(): void
    {
        $accessService = Mockery::mock(AccessService::class);
        $accessService->shouldReceive('hasAny')->once()->with(null, ['ADM'])->andReturn(false);

        $middleware = new CheckAccess($accessService);
        $request = Request::create('/security-management', 'GET');

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        $middleware->handle($request, fn ($req) => new \Illuminate\Http\Response('ok'), 'ADM');
    }

    public function test_passes_multiple_codes_through_to_access_service(): void
    {
        $accessService = Mockery::mock(AccessService::class);
        $accessService->shouldReceive('hasAny')
            ->once()
            ->with(null, ['JAM-KERJA-INFURE', 'JAM-KERJA-SEITAI'])
            ->andReturn(true);

        $middleware = new CheckAccess($accessService);
        $request = Request::create('/checklist-jam-kerja', 'GET');

        $response = $middleware->handle(
            $request,
            fn ($req) => new \Illuminate\Http\Response('ok'),
            'JAM-KERJA-INFURE',
            'JAM-KERJA-SEITAI'
        );

        $this->assertSame('ok', $response->getContent());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/CheckAccessMiddlewareTest.php`
Expected: FAIL — `Class "App\Http\Middleware\CheckAccess" not found`

- [ ] **Step 3: Write the implementation**

```php
<?php

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

Modify `app/Http/Kernel.php` — add one line inside `$middlewareAliases` (after the existing `'can'` entry, alphabetically before `'guest'`):

```php
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'access' => \App\Http\Middleware\CheckAccess::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/CheckAccessMiddlewareTest.php`
Expected: PASS (3 tests)

- [ ] **Step 5: Commit**

```bash
git add app/Http/Middleware/CheckAccess.php app/Http/Kernel.php tests/Feature/CheckAccessMiddlewareTest.php
git commit -m "feat(access): add CheckAccess route middleware (access:CODE alias)"
```

---

### Task 3: Wildcard access code migration

**Files:**
- Create: `database/migrations/2026_09_17_000000_seed_wildcard_access_code.php`

**Interfaces:**
- Produces: one row in `access` with `code = '*'`, consumed by `AccessService::matches()` (Task 1) at runtime.

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('access')->insertOrIgnore([
            'access_name' => 'Full Access (Super Admin)',
            'code' => '*',
            'description' => 'Bypasses every access: middleware check on every gated route.',
            'status' => 1,
        ]);
    }

    public function down(): void
    {
        DB::table('access')->where('code', '*')->delete();
    }
};
```

- [ ] **Step 2: Verify column names before running**

This sandbox cannot reach the dev DB to confirm `access` table columns directly. Before running this migration on a host that CAN reach the DB, run:
`php artisan db:table access`
and confirm the column list includes `access_name`, `code`, `description`, `status` (matching `App\Models\Access::$fillable` plus the `code` column already read by `sidebar.blade.php`/`Login.php`). If a column name differs, fix the migration's `insertOrIgnore` array before running.

- [ ] **Step 3: Run the migration (on a host with DB access, not in this sandbox)**

Run: `php artisan migrate --path=database/migrations/2026_09_17_000000_seed_wildcard_access_code.php`
Expected: 1 migration applied, no errors.

- [ ] **Step 4: Assign the new code to the Super Admin role**

Manual step in the running app: open `role-management`, edit the Super Admin (or equivalent top) role, tick the new "Full Access (Super Admin)" access entry, save.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2026_09_17_000000_seed_wildcard_access_code.php
git commit -m "feat(access): seed wildcard '*' access code for admin bypass"
```

---

### Task 4: Consolidate sidebar + login onto AccessService

**Files:**
- Modify: `resources/views/layouts/sidebar.blade.php:11-19`
- Modify: `app/Http/Livewire/Auth/Login.php:1-9,57-62`

**Interfaces:**
- Consumes: `App\Services\AccessService::codesFor(?Authenticatable $user): array` (Task 1)

Behavior-preserving refactor: same cache key (`user_access_{id}`), same TTL (600s), same source query. No new test — covered by not changing the resulting `$userAccess` array.

- [ ] **Step 1: Update the sidebar**

Modify `resources/views/layouts/sidebar.blade.php`, replacing lines 11-19:

```php
    @php
        $userAccess = [];
        if (auth()->check()) {
            $userAccess = \Illuminate\Support\Facades\Cache::remember(
                'user_access_' . auth()->id(),
                600, // 10 menit — cukup lama, role jarang berubah
                fn() => auth()->user()->roles->flatMap->access->pluck('code')->unique()->toArray()
            );
        }
    @endphp
```

with:

```php
    @php
        $userAccess = app(\App\Services\AccessService::class)->codesFor(auth()->user());
    @endphp
```

- [ ] **Step 2: Update Login.php**

Modify `app/Http/Livewire/Auth/Login.php` — add one import after the existing `use Illuminate\Support\Facades\Cache;` on line 7 (leave that line as-is; `Cache` is still used by the untouched `mount()` method):

```php
use App\Services\AccessService;
```

Replace lines 57-62:

```php
            // Cache access saat login agar sidebar tidak query ulang saat halaman pertama dibuka
            $userAccess = Cache::remember(
                'user_access_' . auth()->id(),
                600,
                fn() => auth()->user()->roles->flatMap->access->pluck('code')->unique()->toArray()
            );
```

with:

```php
            // Cache access saat login agar sidebar tidak query ulang saat halaman pertama dibuka
            $userAccess = app(AccessService::class)->codesFor(auth()->user());
```

Note: `Login.php`'s `mount()` method (lines 20-35) has its own separate, pre-existing `$userRoles` computation (`Cache::remember('user_roles_...', ..., fn() => auth()->user()->roles->pluck('code')...)`) that reads `code` off the `Role` model, which has no `code` column — this looks like a pre-existing, unrelated bug. Leave it untouched; it's out of scope for this change (flag separately after this plan ships).

- [ ] **Step 3: Manual smoke check (needs DB — cannot run in this sandbox)**

On a host with DB access: log in as a real user, confirm the sidebar shows the same menu items as before this change, and confirm post-login redirect still lands on the expected page.

- [ ] **Step 4: Commit**

```bash
git add resources/views/layouts/sidebar.blade.php app/Http/Livewire/Auth/Login.php
git commit -m "refactor(access): consolidate sidebar/login onto AccessService"
```

(Pre-commit note: this touches two files with real logic — run the `code-reviewer` subagent per project CLAUDE.md before this commit, since it's not a docs-only or pure-rename change.)

---

### Task 5: Route-audit safety net (RED)

**Files:**
- Create: `tests/Feature/RouteAccessMappingTest.php`

**Interfaces:**
- Consumes: nothing from prior tasks (pure route introspection against `routes/web.php` as it exists on disk).
- Produces: the single test every subsequent web.php batch task must move closer to green.

This test walks every registered route that carries the `auth` middleware. If its name is in `EXPECTED`, it must carry exactly the `access:` codes listed. If its name (or, for unnamed routes, its URI) is in the ungated allowlists, it must NOT carry any `access:` middleware. Anything else fails loudly, naming the offending route — this is what stops a future route from silently shipping unprotected.

- [ ] **Step 1: Write the test (fails everywhere until routes/web.php is edited)**

```php
<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteAccessMappingTest extends TestCase
{
    /** route name => required access code(s) (OR'd together, matching the ->middleware('access:A,B') syntax) */
    private const EXPECTED = [
        'order-lpk' => ['ORDER'],
        'edit-order' => ['ORDER'],
        'add-order' => ['ORDER'],
        'lpk-entry' => ['ORDER'],
        'add-lpk' => ['ORDER'],
        'edit-lpk' => ['ORDER'],
        'cetak-lpk' => ['ORDER'],
        'order-report' => ['ORDER'],
        'report-lpk' => ['ORDER'],
        'cetak-order' => ['ORDER'],

        'nippo-infure' => ['NIPPO-INFURE'],
        'edit-nippo' => ['NIPPO-INFURE'],
        'add-nippo' => ['NIPPO-INFURE'],
        'get-print-data' => ['NIPPO-INFURE'],
        'loss-infure' => ['NIPPO-INFURE'],
        'checklist-infure' => ['NIPPO-INFURE'],
        'label-gentan' => ['NIPPO-INFURE'],
        'nippo-infure-print' => ['NIPPO-INFURE'],
        'report-nippo-infure' => ['NIPPO-INFURE'],
        'report-gentan' => ['NIPPO-INFURE'],

        'nippo-seitai' => ['NIPPO-SEITAI'],
        'add-seitai' => ['NIPPO-SEITAI'],
        'edit-seitai' => ['NIPPO-SEITAI'],
        'loss-seitai' => ['NIPPO-SEITAI'],
        'add-loss' => ['NIPPO-SEITAI'],
        'mutasi-isi-palet' => ['NIPPO-SEITAI'],
        'check-list-seitai' => ['NIPPO-SEITAI'],
        'label-masuk-gudang' => ['NIPPO-SEITAI'],
        'report-masuk-gudang' => ['NIPPO-SEITAI'],
        'report-checklist-seitai' => ['NIPPO-SEITAI'],
        'report-loss-seitai' => ['NIPPO-SEITAI'],

        'infure-jam-kerja' => ['JAM-KERJA-INFURE'],
        'seitai-jam-kerja' => ['JAM-KERJA-SEITAI'],
        'checklist-jam-kerja' => ['JAM-KERJA-INFURE', 'JAM-KERJA-SEITAI'],

        'kenpin-infure' => ['KENPIN'],
        'add-kenpin-infure' => ['KENPIN'],
        'edit-kenpin-infure' => ['KENPIN'],
        'kenpin-seitai-kenpin' => ['KENPIN'],
        'add-kenpin-seitai' => ['KENPIN'],
        'edit-kenpin-seitai' => ['KENPIN'],
        'mutasi-isi-palet-kenpin' => ['KENPIN'],
        'print-label-gudang-kenpin' => ['KENPIN'],
        'report-kenpin' => ['KENPIN'],

        'penarikan-palet' => ['WAREHOUSE'],
        'pengembalian-palet' => ['WAREHOUSE'],
        'label-masuk-gudang-report' => ['WAREHOUSE'],

        'general-report' => ['REPORT'],
        'detail-report' => ['REPORT'],
        'production-loss-report' => ['REPORT'],

        'buyer' => ['MASTER'],
        'tipe-produk' => ['MASTER'],
        'jenis-produk' => ['MASTER'],
        'department' => ['MASTER'],
        'working-shift' => ['MASTER'],
        'warehouse' => ['MASTER'],
        'mesin' => ['MASTER'],
        'bagian-mesin' => ['MASTER'],
        'detail-bagian-mesin' => ['MASTER'],
        'jadwal-mesin' => ['MASTER'],
        'karyawan' => ['MASTER'],
        'menu-katanuki' => ['MASTER'],
        'product' => ['MASTER'],
        'add-master-product' => ['MASTER'],
        'edit-master-product' => ['MASTER'],
        'menu-loss-infure' => ['MASTER'],
        'menu-loss-katagori' => ['MASTER'],
        'menu-loss-kenpin' => ['MASTER'],
        'menu-loss-klasifikisasi' => ['MASTER'],
        'menu-loss-seitai' => ['MASTER'],
        'kemasan-box' => ['MASTER'],
        'kemasan-gasio' => ['MASTER'],
        'kemasan-inner' => ['MASTER'],
        'kemasan-layer' => ['MASTER'],
        'master-jam-mati-mesin-infure' => ['MASTER'],
        'master-jam-mati-mesin-seitai' => ['MASTER'],
        'masalah-kenpin-infure' => ['MASTER'],
        'masalah-kenpin-seitai' => ['MASTER'],

        'security-management' => ['ADM'],
        'add-user' => ['ADM'],
        'edit-user' => ['ADM'],
        'role-management' => ['ADM'],

        'pemasukan-barang' => ['ADMIN'],
        'pengeluaran-barang' => ['ADMIN'],
        'posisi-wip' => ['ADMIN'],
        'bahan-baku' => ['ADMIN'],
        'barang-jadi' => ['ADMIN'],
        'mesin-peralatan' => ['ADMIN'],
        'barang-reject' => ['ADMIN'],

        'dashboard-infure-old' => ['DASHBOARD-INFURE'],
        'dashboard-infure-kadou-jikan-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-hasil-produksi-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-loss-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-loss-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-counter-trouble-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-produksi-loss-per-mesin' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-loss-per-mesin' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-loss-per-kasus' => ['DASHBOARD-INFURE'],
        'dashboard-infure-kadou-jikan-frekuensi-trouble' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-mesin-masalah-loss-daily' => ['DASHBOARD-INFURE'],
        'dashboard-infure-ranking-problem-machine-daily' => ['DASHBOARD-INFURE'],
        'dashboard-infure-total-produksi-per-bulan' => ['DASHBOARD-INFURE'],
        'dashboard-infure-peringatan-katagae' => ['DASHBOARD-INFURE'],
        'dashboard-infure-loss-per-bulan' => ['DASHBOARD-INFURE'],
        'dashboard-infure-produksi-per-bulan' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-mesin-masalah-loss-monthly' => ['DASHBOARD-INFURE'],
        'dashboard-infure-ranking-problem-machine-monthly' => ['DASHBOARD-INFURE'],
        'dashboard-seitai' => ['DASHBOARD-SEITAI'],

        // routes/api.php — Route::middleware(['web','auth'])->group(...) wrapping
        // DashboardSeitaiController's AJAX endpoints (the Seitai counterpart to the
        // Dashboard Infure AJAX sub-routes above). Found during Task 5 (this file was
        // never inspected during spec-writing) — see spec addendum in section 5.
        'api.dashboard-seitai-produksi-loss-per-mesin' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-top-loss-per-mesin' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-top-loss-per-kasus' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-kadou-jikan-frekuensi-trouble' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-top-mesin-masalah-loss-daily' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-ranking-problem-machine-daily' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-total-produksi-per-bulan' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-peringatan-katagae' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-loss-per-bulan' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-produksi-per-bulan' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-top-mesin-masalah-loss-monthly' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-top-loss-per-kasus-monthly' => ['DASHBOARD-SEITAI'],
        'api.dashboard-seitai-ranking-problem-machine-monthly' => ['DASHBOARD-SEITAI'],
    ];

    /** Named routes intentionally left auth-only (no access: middleware), per the spec. */
    private const UNGATED_NAMED_ALLOWLIST = [
        'printer.settings',
    ];

    /**
     * Originally-unnamed routes, matched by URI. Checked BEFORE the name-based
     * checks below and regardless of $route->getName() — Laravel assigns a random
     * 'generated::...' name to originally-unnamed routes the first time the app
     * boots/matches a route (Illuminate\Routing\AbstractRouteCollection::
     * generateRouteName()), so $name is not reliably null by the time this test runs.
     */
    private const UNGATED_URI_ALLOWLIST = [
        'docs/{file}',
        '/',
        'test',
        '{any}',
    ];

    public function test_every_auth_route_is_either_mapped_or_allowlisted(): void
    {
        $unexpected = [];
        $mismatched = [];

        foreach (Route::getRoutes() as $route) {
            $middlewareNames = $route->gatherMiddleware();

            if (! in_array('auth', $middlewareNames, true)) {
                continue;
            }

            $accessCodes = $this->extractAccessCodes($middlewareNames);
            $name = $route->getName();

            if (in_array($route->uri(), self::UNGATED_URI_ALLOWLIST, true)) {
                if ($accessCodes !== []) {
                    $mismatched[] = sprintf('%s: expected no access: middleware, got [%s]', $route->uri(), implode(',', $accessCodes));
                }
                continue;
            }

            if ($name !== null && array_key_exists($name, self::EXPECTED)) {
                if ($accessCodes !== self::EXPECTED[$name]) {
                    $mismatched[] = sprintf(
                        '%s: expected [%s], got [%s]',
                        $name,
                        implode(',', self::EXPECTED[$name]),
                        implode(',', $accessCodes)
                    );
                }
                continue;
            }

            if ($name !== null && in_array($name, self::UNGATED_NAMED_ALLOWLIST, true)) {
                if ($accessCodes !== []) {
                    $mismatched[] = sprintf('%s: expected no access: middleware, got [%s]', $name, implode(',', $accessCodes));
                }
                continue;
            }

            $unexpected[] = $name ?? $route->uri();
        }

        $this->assertSame([], $unexpected, 'Routes with no mapping and no allowlist entry: ' . implode(', ', $unexpected));
        $this->assertSame([], $mismatched, 'Routes whose access: middleware does not match EXPECTED: ' . implode('; ', $mismatched));
    }

    private function extractAccessCodes(array $middlewareNames): array
    {
        foreach ($middlewareNames as $middleware) {
            if (str_starts_with($middleware, 'access:')) {
                return explode(',', substr($middleware, strlen('access:')));
            }
        }

        return [];
    }
}
```

- [ ] **Step 2: Run test to verify it fails (RED — this is expected)**

Run: `php artisan test tests/Feature/RouteAccessMappingTest.php`
Expected: FAIL — most route names land in `$unexpected` because `routes/web.php` has no `access:` middleware yet.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/RouteAccessMappingTest.php
git commit -m "test(access): add route-audit safety net (RED before web.php is gated)"
```

(Committing a known-failing test is intentional here — it's the TDD red state for the batch tasks that follow. Note this in the PR/commit body if code-reviewer flags it.)

---

### Task 6a: Gate Order LPK + Nippo Infure + Nippo Seitai routes

**Files:**
- Modify: `routes/web.php:135-219, 314-319, 326-351, 358-383` (see exact lines below)

Append `->middleware('access:CODE')` to each listed route. Multi-line closure routes are shown by their final chain line only (the body above is unchanged).

- [ ] **Step 1: Order LPK block (web.php:138-147)**

```php
    Route::get('/order-lpk', OrderLpkController::class)->name('order-lpk')->middleware('access:ORDER');
    Route::get('/edit-order', EditOrderController::class)->name('edit-order')->middleware('access:ORDER');
    Route::get('/add-order', AddOrderController::class)->name('add-order')->middleware('access:ORDER');

    Route::get('/lpk-entry', LpkEntryController::class)->name('lpk-entry')->middleware('access:ORDER');
    Route::get('/add-lpk', AddLpkController::class)->name('add-lpk')->middleware('access:ORDER');
    Route::get('/edit-lpk', EditLpkController::class)->name('edit-lpk')->middleware('access:ORDER');

    Route::get('/cetak-lpk', CetakLpkController::class)->name('cetak-lpk')->middleware('access:ORDER');
    Route::get('/order-report', OrderReportController::class)->name('order-report')->middleware('access:ORDER');
```

- [ ] **Step 2: Nippo Infure block (web.php:150-152, 197, 199-202, 207)**

```php
    Route::get('/nippo-infure', NippoInfureController::class)->name('nippo-infure')->middleware('access:NIPPO-INFURE');
    Route::get('/edit-nippo/', EditNippoController::class)->name('edit-nippo')->middleware('access:NIPPO-INFURE');
    Route::get('/add-nippo', AddNippoController::class)->name('add-nippo')->middleware('access:NIPPO-INFURE');
```

The `get-print-data/{produk_asemblyid}` closure (web.php:154-197) keeps its body — only its final line changes:

```php
    })->name('get-print-data')->middleware('access:NIPPO-INFURE');
```

```php
    Route::get('/loss-infure', LossInfureController::class)->name('loss-infure')->middleware('access:NIPPO-INFURE');

    Route::get('/checklist-infure', CheckListInfureController::class)->name('checklist-infure')->middleware('access:NIPPO-INFURE');
    Route::get('/label-gentan', LabelGentanController::class)->name('label-gentan')->middleware('access:NIPPO-INFURE');
```

The `report-checklist-infure` closure (web.php:204-207) — only its final line changes:

```php
    })->name('nippo-infure-print')->middleware('access:NIPPO-INFURE');
```

- [ ] **Step 3: Nippo Seitai block (web.php:210-212, 214-219)**

```php
    Route::get('/nippo-seitai', NippoSeitaiController::class)->name('nippo-seitai')->middleware('access:NIPPO-SEITAI');
    Route::get('/add-seitai', AddSeitaiController::class)->name('add-seitai')->middleware('access:NIPPO-SEITAI');
    Route::get('/edit-seitai', EditSeitaiController::class)->name('edit-seitai')->middleware('access:NIPPO-SEITAI');

    Route::get('/loss-seitai', LossSeitaiController::class)->name('loss-seitai')->middleware('access:NIPPO-SEITAI');
    Route::get('/add-loss', AddSeitaiController::class)->name('add-loss')->middleware('access:NIPPO-SEITAI');

    Route::get('/mutasi-isi-palet', MutasiIsiPaletController::class)->name('mutasi-isi-palet')->middleware('access:NIPPO-SEITAI');
    Route::get('/check-list-seitai', CheckListSeitaiController::class)->name('check-list-seitai')->middleware('access:NIPPO-SEITAI');
    Route::get('/label-masuk-gudang', LabelMasukGudangController::class)->name('label-masuk-gudang')->middleware('access:NIPPO-SEITAI');
```

- [ ] **Step 4: Scattered utility routes — final lines only**

`report-lpk` (web.php:314-319) → `ORDER`. Final line:
```php
    })->name('report-lpk')->middleware('access:ORDER');
```

`report-masuk-gudang` (web.php:321-324) → `NIPPO-SEITAI`:
```php
    })->name('report-masuk-gudang')->middleware('access:NIPPO-SEITAI');
```

`report-nippo-infure`, first definition (web.php:326-329) → `NIPPO-INFURE`:
```php
    })->name('report-nippo-infure')->middleware('access:NIPPO-INFURE');
```

`report-gentan`, first definition (web.php:331-346) → `NIPPO-INFURE`:
```php
    })->name('report-gentan')->middleware('access:NIPPO-INFURE');
```

`report-nippo-infure`, second definition (web.php:348-351, this is the one actually routed to since it's registered later) → `NIPPO-INFURE`:
```php
    })->name('report-nippo-infure')->middleware('access:NIPPO-INFURE');
```

`cetak-order` (web.php:353-356) → `ORDER`:
```php
    })->name('cetak-order')->middleware('access:ORDER');
```

`report-checklist-seitai` (web.php:358-361) → `NIPPO-SEITAI`:
```php
    })->name('report-checklist-seitai')->middleware('access:NIPPO-SEITAI');
```

`report-loss-seitai` (web.php:363-366) → `NIPPO-SEITAI`:
```php
    })->name('report-loss-seitai')->middleware('access:NIPPO-SEITAI');
```

`report-gentan`, second definition (web.php:368-383, the one actually routed to) → `NIPPO-INFURE`:
```php
    })->name('report-gentan')->middleware('access:NIPPO-INFURE');
```

- [ ] **Step 5: Run the audit test — confirm these route names no longer appear in the failure list**

Run: `php artisan test tests/Feature/RouteAccessMappingTest.php`
Expected: still FAIL overall (later batches not done yet), but the failure message must no longer mention any of: `order-lpk, edit-order, add-order, lpk-entry, add-lpk, edit-lpk, cetak-lpk, order-report, report-lpk, nippo-infure, edit-nippo, add-nippo, get-print-data, loss-infure, checklist-infure, label-gentan, nippo-infure-print, report-nippo-infure, report-gentan, cetak-order, nippo-seitai, add-seitai, edit-seitai, loss-seitai, add-loss, mutasi-isi-palet, check-list-seitai, label-masuk-gudang, report-masuk-gudang, report-checklist-seitai, report-loss-seitai`.

- [ ] **Step 6: Commit**

```bash
git add routes/web.php
git commit -m "feat(access): gate Order LPK, Nippo Infure and Nippo Seitai routes"
```

---

### Task 6b: Gate Jam Kerja + Kenpin + Warehouse + Report routes

**Files:**
- Modify: `routes/web.php:222-246`

- [ ] **Step 1: Jam Kerja block (web.php:222-224)**

```php
    Route::get('/infure-jam-kerja', InfureJamKerjaController::class)->name('infure-jam-kerja')->middleware('access:JAM-KERJA-INFURE');
    Route::get('/seitai-jam-kerja', SeitaiJamKerjaController::class)->name('seitai-jam-kerja')->middleware('access:JAM-KERJA-SEITAI');
    Route::get('/checklist-jam-kerja', CheckListJamKerjaController::class)->name('checklist-jam-kerja')->middleware('access:JAM-KERJA-INFURE,JAM-KERJA-SEITAI');
```

- [ ] **Step 2: Kenpin block (web.php:227-237)**

```php
    Route::get('/kenpin-infure', KenpinInfureController::class)->name('kenpin-infure')->middleware('access:KENPIN');
    Route::get('/add-kenpin-infure', AddKenpinInfureController::class)->name('add-kenpin-infure')->middleware('access:KENPIN');
    Route::get('/edit-kenpin-infure', EditKenpinInfureController::class)->name('edit-kenpin-infure')->middleware('access:KENPIN');

    Route::get('/kenpin-seitai', KenpinSeitaiController::class)->name('kenpin-seitai-kenpin')->middleware('access:KENPIN');
    Route::get('/add-kenpin-seitai', AddKenpinSeitaiController::class)->name('add-kenpin-seitai')->middleware('access:KENPIN');
    Route::get('/edit-kenpin-seitai', EditKenpinSeitaiController::class)->name('edit-kenpin-seitai')->middleware('access:KENPIN');

    Route::get('/mutasi-isi-palet-kenpin', MutasiIsiPaletKenpinController::class)->name('mutasi-isi-palet-kenpin')->middleware('access:KENPIN');
    Route::get('/print-label-gudang-kenpin', PrintLabelGudangKenpinController::class)->name('print-label-gudang-kenpin')->middleware('access:KENPIN');
    Route::get('/report-kenpin', ReportKenpinController::class)->name('report-kenpin')->middleware('access:KENPIN');
```

- [ ] **Step 3: Warehouse + Report block (web.php:240-246)**

```php
    Route::get('/penarikan-palet', PenarikanPaletController::class)->name('penarikan-palet')->middleware('access:WAREHOUSE');
    Route::get('/pengembalian-palet', PengembalianPaletController::class)->name('pengembalian-palet')->middleware('access:WAREHOUSE');

    Route::get('/general-report', GeneralReportController::class)->name('general-report')->middleware('access:REPORT');
    Route::get('/detail-report', DetailReportController::class)->name('detail-report')->middleware('access:REPORT');
    Route::get('/production-loss-report', ProductionLossReportController::class)->name('production-loss-report')->middleware('access:REPORT');
    Route::get('/label-masuk-gudang-report', LabelMasukGudangReportController::class)->name('label-masuk-gudang-report')->middleware('access:WAREHOUSE');
```

- [ ] **Step 4: Run the audit test — confirm these route names no longer appear in the failure list**

Run: `php artisan test tests/Feature/RouteAccessMappingTest.php`
Expected: failure list no longer mentions: `infure-jam-kerja, seitai-jam-kerja, checklist-jam-kerja, kenpin-infure, add-kenpin-infure, edit-kenpin-infure, kenpin-seitai-kenpin, add-kenpin-seitai, edit-kenpin-seitai, mutasi-isi-palet-kenpin, print-label-gudang-kenpin, report-kenpin, penarikan-palet, pengembalian-palet, general-report, detail-report, production-loss-report, label-masuk-gudang-report`.

- [ ] **Step 5: Commit**

```bash
git add routes/web.php
git commit -m "feat(access): gate Jam Kerja, Kenpin, Warehouse and Report routes"
```

---

### Task 6c: Gate Master Tabel routes

**Files:**
- Modify: `routes/web.php:249-298`

- [ ] **Step 1: Apply `access:MASTER` to every Master Tabel route**

```php
    // Buyer
    Route::get('/buyer', BuyerController::class)->name('buyer')->middleware('access:MASTER');

    // Master Tabel Produk
    Route::get('/tipe-produk', TipeProduk::class)->name('tipe-produk')->middleware('access:MASTER');
    Route::get('/jenis-produk', JenisProduk::class)->name('jenis-produk')->middleware('access:MASTER');

    // master table department
    Route::get('/departemen', Department::class)->name('department')->middleware('access:MASTER');

    // master table working shift
    Route::get('/working-shift', WorkingShift::class)->name('working-shift')->middleware('access:MASTER');

    // master table warehouse
    Route::get('/warehouse', Warehouse::class)->name('warehouse')->middleware('access:MASTER');

    // master table mesin
    Route::get('/mesin', Machine::class)->name('mesin')->middleware('access:MASTER');
    Route::get('/bagian-mesin', MachinePartController::class)->name('bagian-mesin')->middleware('access:MASTER');
    Route::get('/detail-bagian-mesin', MachinePartDetailController::class)->name('detail-bagian-mesin')->middleware('access:MASTER');

    Route::get('/jadwal-mesin', JadwalMachineController::class)->name('jadwal-mesin')->middleware('access:MASTER');

    // master table karyawan
    Route::get('/karyawan', Employee::class)->name('karyawan')->middleware('access:MASTER');

    // master table katanuki
    Route::get('/menu-katanuki', KatanukiController::class)->name('menu-katanuki')->middleware('access:MASTER');

    // master table produk
    Route::get('/master-produk', MasterProduk::class)->name('product')->middleware('access:MASTER');
    Route::get('/add-master-produk', AddMasterProduk::class)->name('add-master-product')->middleware('access:MASTER');
    Route::get('/edit-master-produk', EditProduk::class)->name('edit-master-product')->middleware('access:MASTER');

    Route::get('/menu-loss-infure', MenuLossInfureController::class)->name('menu-loss-infure')->middleware('access:MASTER');
    Route::get('/menu-loss-kategori', MenuLossKatagoriController::class)->name('menu-loss-katagori')->middleware('access:MASTER');
    Route::get('/menu-loss-kenpin', MenuLossKenpinController::class)->name('menu-loss-kenpin')->middleware('access:MASTER');
    Route::get('/menu-loss-klasifikasi', MenuLossKlasifikisasiController::class)->name('menu-loss-klasifikisasi')->middleware('access:MASTER');
    Route::get('/menu-loss-seitai', MenuLossSeitaiController::class)->name('menu-loss-seitai')->middleware('access:MASTER');

    Route::get('/kemasan-box', BoxController::class)->name('kemasan-box')->middleware('access:MASTER');
    Route::get('/kemasan-gasio', GaisoController::class)->name('kemasan-gasio')->middleware('access:MASTER');
    Route::get('/kemasan-inner', InnerController::class)->name('kemasan-inner')->middleware('access:MASTER');
    Route::get('/kemasan-layer', LayerController::class)->name('kemasan-layer')->middleware('access:MASTER');

    Route::get('/jam-mati-mesin-infure', JamMatiMesinInfureController::class)->name('master-jam-mati-mesin-infure')->middleware('access:MASTER');
    Route::get('/jam-mati-mesin-seitai', JamMatiMesinSeitaiController::class)->name('master-jam-mati-mesin-seitai')->middleware('access:MASTER');

    // Masalah Kenpin
    Route::get('/masalah-kenpin-infure', MasalahKenpinInfureController::class)->name('masalah-kenpin-infure')->middleware('access:MASTER');
    Route::get('/masalah-kenpin-seitai', MasalahKenpinSeitaiController::class)->name('masalah-kenpin-seitai')->middleware('access:MASTER');
```

- [ ] **Step 2: Run the audit test — confirm no Master Tabel route names remain in the failure list**

Run: `php artisan test tests/Feature/RouteAccessMappingTest.php`
Expected: failure list no longer mentions any of the 28 route names listed under `MASTER` in `EXPECTED`.

- [ ] **Step 3: Commit**

```bash
git add routes/web.php
git commit -m "feat(access): gate Master Tabel routes"
```

---

### Task 6d: Gate Administration + Inventory + Dashboard routes

**Files:**
- Modify: `routes/web.php:301-312, 400-429`
- Modify: `routes/api.php:33-52`

This is the highest-risk batch (Administration = user/role management). Recommend the code-reviewer subagent pays extra attention here.

- [ ] **Step 1: Administration block (web.php:301-304)**

```php
    Route::get('/security-management', SecurityManagementController::class)->name('security-management')->middleware('access:ADM');
    Route::get('/add-user', AddUserController::class)->name('add-user')->middleware('access:ADM');
    Route::get('/edit-user', EditUserController::class)->name('edit-user')->middleware('access:ADM');
    Route::get('/role-management', \App\Http\Livewire\Administration\RoleManagementController::class)->name('role-management')->middleware('access:ADM');
```

- [ ] **Step 2: Inventory block (web.php:306-312)**

```php
    Route::get('/pemasukan-barang', PemasukanBarangController::class)->name('pemasukan-barang')->middleware('access:ADMIN');
    Route::get('/pengeluaran-barang', PengeluaranBarangController::class)->name('pengeluaran-barang')->middleware('access:ADMIN');
    Route::get('/posisi-wip', PosisiWipController::class)->name('posisi-wip')->middleware('access:ADMIN');
    Route::get('/bahan-baku', BahanBakuController::class)->name('bahan-baku')->middleware('access:ADMIN');
    Route::get('/barang-jadi', BarangJadiController::class)->name('barang-jadi')->middleware('access:ADMIN');
    Route::get('/mesin-peralatan', MesinPeralatanController::class)->name('mesin-peralatan')->middleware('access:ADMIN');
    Route::get('/barang-reject', BarangRejectController::class)->name('barang-reject')->middleware('access:ADMIN');
```

- [ ] **Step 3: Dashboard controller groups (web.php:400-429) — apply `->middleware()` on the `Route::controller()` group itself**

```php
    Route::controller(DashboardInfureControllerOld::class)->middleware('access:DASHBOARD-INFURE')->group(function () {
        Route::get('/dashboard-infure-old', 'index')->name('dashboard-infure-old');
        Route::get('/dashboard-infure-old/kadou-jikan', 'getkadouJikanInfure')->name('dashboard-infure-kadou-jikan-infure');
        Route::get('/dashboard-infure-old/hasil-produksi', 'getHasilProduksiInfure')->name('dashboard-infure-hasil-produksi-infure');
        Route::get('/dashboard-infure-old/loss/infuregetLossInfure')->name('dashboard-infure-loss-infure');
        Route::get('/dashboard-infure-old/top-loss', 'getTopLossInfure')->name('dashboard-infure-top-loss-infure');
        Route::get('/dashboard-infure-old/counter-trouble', 'getCounterTroubleInfure')->name('dashboard-infure-counter-trouble-infure');
    });
    Route::controller(DashboardInfureController::class)->middleware('access:DASHBOARD-INFURE')->group(function () {
        Route::get('/dashboard-infure', 'index')->name('dashboard-infure');
        Route::get('/dashboard-infure/produksi-loss-per-mesin', 'getProduksiLossInfure')->name('dashboard-infure-produksi-loss-per-mesin');
        Route::get('/dashboard-infure/top-loss-per-mesin', 'getTopLossByMachineInfure')->name('dashboard-infure-top-loss-per-mesin');
        Route::get('/dashboard-infure/top-loss-per-kasus', 'getTopLossByKasusInfure')->name('dashboard-infure-top-loss-per-kasus');
        Route::get('/dashboard-infure/kadou-jikan-frekuensi-trouble', 'getKadouJikanFrekuensiTrouble')->name('dashboard-infure-kadou-jikan-frekuensi-trouble');
        Route::get('/dashboard-infure/top-mesin-masalah-loss-daily', 'getTopMesinMasalahLossDaily')->name('dashboard-infure-top-mesin-masalah-loss-daily');
        Route::get('/dashboard-infure/ranking-problem-machine-daily', 'getRankingProblemMachineDaily')->name('dashboard-infure-ranking-problem-machine-daily');

        // monthly
        Route::get('/dashboard-infure/total-produksi-per-bulan', 'getTotalProductionMonthly')->name('dashboard-infure-total-produksi-per-bulan');
        Route::get('/dashboard-infure/peringatan-katagae', 'getPeringatanKatagae')->name('dashboard-infure-peringatan-katagae');
        Route::get('/dashboard-infure/loss-per-bulan', 'getLossMonthly')->name('dashboard-infure-loss-per-bulan');
        Route::get('/dashboard-infure/produksi-per-bulan', 'getProductionMonthly')->name('dashboard-infure-produksi-per-bulan');
        Route::get('/dashboard-infure/top-mesin-masalah-loss-monthly', 'getTopMesinMasalahLossMonthly')->name('dashboard-infure-top-mesin-masalah-loss-monthly');
        Route::get('/dashboard-infure/ranking-problem-machine-monthly', 'getRankingProblemMachineMonthly')->name('dashboard-infure-ranking-problem-machine-monthly');
    });

    // Seitai - hanya route untuk view/page
    Route::controller(DashboardSeitaiController::class)->middleware('access:DASHBOARD-SEITAI')->group(function () {
        Route::get('/dashboard-seitai', 'index')->name('dashboard-seitai');
    });
```

- [ ] **Step 4: Gate the Dashboard Seitai AJAX API in `routes/api.php`**

Found during Task 5 (this file was never inspected during spec-writing — see spec's
section 5 addendum): `routes/api.php:33-52` has its own `Route::middleware(['web','auth'])`
group wrapping `DashboardSeitaiController`'s AJAX data endpoints — the Seitai counterpart
to the Dashboard Infure AJAX sub-routes gated in Step 3. Apply `access:DASHBOARD-SEITAI`
the same way:

```php
Route::middleware(['web', 'auth', 'access:DASHBOARD-SEITAI'])->group(function () {
    // Seitai Dashboard API
    Route::controller(DashboardSeitaiController::class)->prefix('dashboard-seitai')->group(function () {
        // Daily endpoints
        Route::get('/produksi-loss-per-mesin', 'getProduksiLossSeitai')->name('api.dashboard-seitai-produksi-loss-per-mesin');
        Route::get('/top-loss-per-mesin', 'getTopLossByMachineSeitai')->name('api.dashboard-seitai-top-loss-per-mesin');
        Route::get('/top-loss-per-kasus', 'getTopLossByKasusSeitai')->name('api.dashboard-seitai-top-loss-per-kasus');
        Route::get('/kadou-jikan-frekuensi-trouble', 'getKadouJikanFrekuensiTrouble')->name('api.dashboard-seitai-kadou-jikan-frekuensi-trouble');
        Route::get('/top-mesin-masalah-loss-daily', 'getTopMesinMasalahLossDaily')->name('api.dashboard-seitai-top-mesin-masalah-loss-daily');
        Route::get('/ranking-problem-machine-daily', 'getRankingProblemMachineDaily')->name('api.dashboard-seitai-ranking-problem-machine-daily');

        // Monthly endpoints
        Route::get('/total-produksi-per-bulan', 'getTotalProductionMonthly')->name('api.dashboard-seitai-total-produksi-per-bulan');
        Route::get('/peringatan-katagae', 'getPeringatanKatagae')->name('api.dashboard-seitai-peringatan-katagae');
        Route::get('/loss-per-bulan', 'getLossMonthly')->name('api.dashboard-seitai-loss-per-bulan');
        Route::get('/produksi-per-bulan', 'getProductionMonthly')->name('api.dashboard-seitai-produksi-per-bulan');
        Route::get('/top-mesin-masalah-loss-monthly', 'getTopMesinMasalahLossMonthly')->name('api.dashboard-seitai-top-mesin-masalah-loss-monthly');
        Route::get('/top-loss-per-kasus-monthly', 'getTopLossByCaseMonthly')->name('api.dashboard-seitai-top-loss-per-kasus-monthly');
        Route::get('/ranking-problem-machine-monthly', 'getRankingProblemMachineMonthly')->name('api.dashboard-seitai-ranking-problem-machine-monthly');
    });
});
```

Only the `Route::middleware([...])` array on the outer group changes (add `'access:DASHBOARD-SEITAI'`
as a third element) — the routes inside are otherwise untouched.

- [ ] **Step 5: Run the full audit test — expect GREEN**

Run: `php artisan test tests/Feature/RouteAccessMappingTest.php`
Expected: PASS. Every route in `EXPECTED` and both allowlists is accounted for; no unmapped route remains.

- [ ] **Step 6: Commit**

```bash
git add routes/web.php routes/api.php
git commit -m "feat(access): gate Administration, Inventory and Dashboard routes"
```

(Pre-commit note: run the `code-reviewer` subagent before this commit — it changes `/security-management` and `/role-management` access, the most security-sensitive part of this change.)

---

### Task 7: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Run the entire test suite that's runnable in this sandbox**

Run: `php artisan test tests/Unit/AccessServiceMatchesTest.php tests/Feature/CheckAccessMiddlewareTest.php tests/Feature/RouteAccessMappingTest.php`
Expected: all PASS.

- [ ] **Step 2: Sanity-check route:list**

Run: `php artisan route:list --name=security-management`
Expected: output shows `access:ADM` in the route's middleware column (alongside `auth`).

Run: `php artisan route:list --name=printer.settings`
Expected: output shows `auth` only, no `access:` middleware.

- [ ] **Step 3: No commit needed — this task is verification only.**

---

### Task 8: End-to-end AccessControlTest (write here, run on a host with DB access)

**Files:**
- Create: `tests/Feature/AccessControlTest.php`

**This test cannot run in this sandbox** (needs the real dev Postgres DB). Write it now, commit it, and have the user run it on a host that can reach the DB before relying on it as a regression gate in CI.

- [ ] **Step 1: Write the test**

```php
<?php

namespace Tests\Feature;

use App\Models\Access;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function makeUserWithAccessCode(string $code): User
    {
        $user = User::factory()->create(['status' => 1]);
        $role = Role::create(['role_name' => 'Test Role ' . uniqid(), 'description' => 'test', 'status' => 1, 'can_delete' => 1]);
        $access = new Access();
        $access->access_name = 'Test Access ' . uniqid();
        $access->code = $code;
        $access->description = 'test';
        $access->status = 1;
        $access->save();
        $role->access()->attach($access->id);
        $user->roles()->attach($role->id);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/security-management');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_without_code_gets_403(): void
    {
        $user = $this->makeUserWithAccessCode('SOME-OTHER-CODE');

        $response = $this->actingAs($user)->get('/security-management');

        $response->assertStatus(403);
    }

    public function test_authenticated_user_with_matching_code_gets_200(): void
    {
        $user = $this->makeUserWithAccessCode('ADM');

        $response = $this->actingAs($user)->get('/security-management');

        $response->assertStatus(200);
    }

    public function test_wildcard_code_bypasses_every_check(): void
    {
        $user = $this->makeUserWithAccessCode('*');

        $response = $this->actingAs($user)->get('/security-management');

        $response->assertStatus(200);
    }
}
```

- [ ] **Step 2: Run on a host with DB access**

Run: `php artisan test tests/Feature/AccessControlTest.php`
Expected: all 4 tests PASS. `DatabaseTransactions` rolls back every insert — no residue left in the shared dev database.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/AccessControlTest.php
git commit -m "test(access): add end-to-end AccessControlTest (run on host with DB access)"
```

---

## Self-Review Notes

- **Spec coverage:** AccessService/CheckAccess (spec §4.1-4.2) → Tasks 1-2. Wildcard row (§4.3) → Task 3. Full route mapping table (§5) → Tasks 6a-6d, verified by Task 5/7's audit test. Out-of-scope catch-all flag (§6) → flagged post-plan, not a task (see below). Testing (§7) → Tasks 1,2,5,8. Rollout note (§8) → Task 4 Step 3 + Task 3 Step 4.
- **Type/name consistency checked:** `AccessService::matches()/codesFor()/hasAny()` signatures identical across Tasks 1, 2, 5, 8. `CheckAccess` constructor/alias identical across Tasks 2 and 6a-6d usage (`access:CODE`).
- **No placeholders:** every step above has literal, copy-pasteable code or an exact `php artisan` command.
- **Amended during execution (Task 5, controller ruling):** `routes/api.php` was never
  inspected while writing this plan. Task 5's RED-state run surfaced 13 real
  `api.dashboard-seitai-*` routes with `auth` middleware and no access code — added to
  Task 5's `EXPECTED` map and to Task 6d as a new Step 4 (`access:DASHBOARD-SEITAI`).
  Also fixed a bug in Task 5's own test logic: the `UNGATED_URI_ALLOWLIST` check assumed
  `$route->getName() === null` for unnamed routes, which Laravel does not guarantee once
  the app has booted (it assigns a random `generated::...` name lazily) — the check is
  now unconditional on URI, checked first. See the spec's §5 addendum and the SDD ledger
  for full detail.

## After this plan ships

Flag the pre-existing `Route::get('{any}', [HomeController::class, 'index'])` catch-all (renders any Blade view name matched from the URL) as a separate follow-up investigation — out of scope for this change per spec §6.
