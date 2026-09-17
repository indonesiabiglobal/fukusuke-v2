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
