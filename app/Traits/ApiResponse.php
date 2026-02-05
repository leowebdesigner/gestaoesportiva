<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    use ApiMeta;

    protected function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => self::apiMeta(),
        ], $code);
    }

    protected function created($data = null, string $message = 'Created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    protected function error(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => self::apiMeta(),
        ], $code);
    }

    protected function paginated($paginator, string $message = 'Success'): JsonResponse
    {
        if ($paginator instanceof \Illuminate\Http\Resources\Json\AnonymousResourceCollection) {
            $resource = $paginator;
            $paginator = $resource->resource;

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $resource->collection,
                'meta' => array_merge(self::apiMeta(), [
                    'pagination' => [
                        'total' => $paginator->total(),
                        'per_page' => $paginator->perPage(),
                        'current_page' => $paginator->currentPage(),
                        'last_page' => $paginator->lastPage(),
                    ],
                ]),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => array_merge(self::apiMeta(), [
                'pagination' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ],
            ]),
        ]);
    }
}
