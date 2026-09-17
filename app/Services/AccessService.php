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

    /** Ordered fallback: first code the user has wins. Keep in sync with routes/web.php's access: middleware. */
    private const LANDING_ROUTES = [
        'NIPPO-INFURE' => '/nippo-infure',
        'NIPPO-SEITAI' => '/nippo-seitai',
        'ORDER' => '/order-lpk',
        'KENPIN' => '/kenpin-infure',
        'JAM-KERJA-INFURE' => '/infure-jam-kerja',
        'JAM-KERJA-SEITAI' => '/seitai-jam-kerja',
        'WAREHOUSE' => '/penarikan-palet',
        'REPORT' => '/general-report',
        'MASTER' => '/buyer',
        'ADM' => '/security-management',
        'ADMIN' => '/pemasukan-barang',
        'DASHBOARD-INFURE' => '/dashboard-infure',
        'DASHBOARD-SEITAI' => '/dashboard-seitai',
    ];

    public function landingRouteFor(?Authenticatable $user): string
    {
        return $this->resolveLanding($this->codesFor($user));
    }

    /**
     * Pure resolver: first code (in LANDING_ROUTES declaration order) the user
     * has wins. Falls back to '/printer-settings' (the only always-reachable,
     * ungated page) if the user has none of the mapped codes.
     */
    public function resolveLanding(array $codes): string
    {
        if (in_array('*', $codes, true)) {
            return '/nippo-infure';
        }

        foreach (self::LANDING_ROUTES as $code => $route) {
            if (in_array($code, $codes, true)) {
                return $route;
            }
        }

        return '/printer-settings';
    }
}
