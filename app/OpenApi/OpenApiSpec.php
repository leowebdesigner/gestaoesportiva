<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'Basketball API - Laravel 12', description: 'API RESTful para gerenciamento de dados de basquete.')]
#[OA\Server(url: 'http://localhost:8000', description: 'Local server')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token'
)]
#[OA\Tag(name: 'Auth', description: 'Autenticação e sessão')]
#[OA\Tag(name: 'Players', description: 'Gerenciamento de jogadores')]
#[OA\Tag(name: 'Teams', description: 'Gerenciamento de times')]
#[OA\Tag(name: 'Games', description: 'Gerenciamento de jogos')]
#[OA\PathItem(
    path: '/api/v1/auth/login',
    post: new OA\Post(
        tags: ['Auth'],
        summary: 'Login user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'admin@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 401, description: 'Invalid credentials')
        ]
    )
)]
class OpenApiSpec
{
}
