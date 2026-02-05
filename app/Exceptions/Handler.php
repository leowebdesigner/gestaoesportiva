<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($e);
        }

        return parent::render($request, $e);
    }

    public function renderApi($request, Throwable $e): JsonResponse
    {
        return $this->handleApiException($e);
    }

    private function handleApiException(Throwable $e): JsonResponse
    {
        $response = match (true) {
            $e instanceof LaravelValidationException => [
                'success' => false,
                'message' => __('messages.errors.invalid_data'),
                'errors' => $e->errors(),
                'code' => 422,
            ],
            $e instanceof ModelNotFoundException => [
                'success' => false,
                'message' => __('messages.errors.resource_not_found'),
                'errors' => null,
                'code' => 404,
            ],
            $e instanceof AuthenticationException => [
                'success' => false,
                'message' => __('messages.errors.not_authenticated'),
                'errors' => null,
                'code' => 401,
            ],
            $e instanceof AuthorizationException => [
                'success' => false,
                'message' => $e->getMessage() ?: __('messages.errors.unauthorized_action'),
                'errors' => null,
                'code' => $e->hasStatus() ? (int) $e->status() : 403,
            ],
            $e instanceof AccessDeniedHttpException => [
                'success' => false,
                'message' => $e->getMessage() ?: __('messages.errors.unauthorized_action'),
                'errors' => null,
                'code' => 403,
            ],
            $e instanceof ThrottleRequestsException => [
                'success' => false,
                'message' => __('messages.errors.too_many_requests'),
                'errors' => null,
                'code' => 429,
            ],
            $e instanceof BusinessException => [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'code' => $e->httpCode,
            ],
            $e instanceof ValidationException => [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors,
                'code' => $e->httpCode,
            ],
            $e instanceof UnauthorizedException => [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'code' => $e->httpCode,
            ],
            $e instanceof NotFoundException => [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'code' => $e->httpCode,
            ],
            $e instanceof ExternalApiException => [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'code' => $e->httpCode,
            ],
            default => [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : __('messages.errors.internal_server_error'),
                'errors' => null,
                'code' => 500,
            ],
        };

        return response()->json([
            'success' => $response['success'],
            'message' => $response['message'],
            'errors' => $response['errors'],
            'meta' => [
                'timestamp' => now()->toDateTimeString(),
                'version' => config('app.api_version', '1.0'),
            ],
        ], $response['code']);
    }
}
