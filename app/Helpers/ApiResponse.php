<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ApiResponse — standar envelope untuk seluruh Mobile API.
 *
 * Struktur sukses         : { success, message, data }
 * Struktur paginated      : { success, message, data[], meta{...} }
 * Struktur error          : { success, message, data: null }
 */
class ApiResponse
{
    // ──────────────────────────────────────────────────────────
    // Success responses
    // ──────────────────────────────────────────────────────────

    /** 200 — sukses dengan payload opsional */
    public static function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * 200 — paginated list.
     *
     * Meta yang dikembalikan:
     *   current_page, per_page, total, last_page, from, to
     */
    public static function paginated(LengthAwarePaginator $paginator, string $message = 'OK'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
        ]);
    }

    /** 201 — resource baru berhasil dibuat */
    public static function created(mixed $data = null, string $message = 'Data berhasil disimpan.'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /** 200 — resource berhasil dihapus */
    public static function deleted(string $message = 'Data berhasil dihapus.'): JsonResponse
    {
        return self::success(null, $message);
    }

    // ──────────────────────────────────────────────────────────
    // Error responses
    // ──────────────────────────────────────────────────────────

    /** 404 — data tidak ditemukan */
    public static function notFound(string $message = 'Data tidak ditemukan.'): JsonResponse
    {
        return self::error($message, 404);
    }

    /** 4xx/5xx — error dengan pesan kustom */
    public static function error(string $message = 'Terjadi kesalahan.', int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
        ], $status);
    }
}
