# Basketball API - Laravel 12

API RESTful para gerenciamento de dados de basquete (NBA) com integração à API BallDontLie.

## Requisitos

- Docker (20.10+)
- Docker Compose (2.0+)
- Make (opcional, recomendado)

## Subindo a aplicação

Com Make:

```bash
make build
make up
```

Sem Make:

```bash
docker-compose build
docker-compose up -d
```

Acesso:
- `http://localhost:8000` (Laravel)
- `http://localhost:8080` (phpMyAdmin)

## Comandos úteis

```bash
make logs
make shell
make down
make migrate-fresh
```
