<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
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

    private function handleApiException(Throwable $e): JsonResponse
    {
        $response = match (true) {
            $e instanceof LaravelValidationException => [
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors(),
                'code' => 422,
            ],
            $e instanceof ModelNotFoundException => [
                'success' => false,
                'message' => 'Recurso não encontrado.',
                'errors' => null,
                'code' => 404,
            ],
            $e instanceof AuthenticationException => [
                'success' => false,
                'message' => 'Não autenticado.',
                'errors' => null,
                'code' => 401,
            ],
            $e instanceof AuthorizationException => [
                'success' => false,
                'message' => $e->getMessage() ?: 'Ação não autorizada.',
                'errors' => null,
                'code' => $e->status ?? 403,
            ],
            $e instanceof ThrottleRequestsException => [
                'success' => false,
                'message' => 'Muitas requisições. Tente novamente mais tarde.',
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
                'message' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor.',
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
