# 🏀 Basketball API - Laravel 12

API RESTful para gerenciamento de dados de basquete (NBA) com integração à API BallDontLie.

## 📖 Sobre o Projeto

Esta API foi desenvolvida para gerenciar informações de basquete incluindo:
- **Players** (Jogadores)
- **Teams** (Times)
- **Games** (Partidas)

A aplicação integra-se com a API pública [BallDontLie](https://www.balldontlie.io/) para importação de dados reais da NBA.

## 🚀 Tecnologias

- **PHP** 8.2+
- **Laravel** 12.x
- **MySQL** 8.0
- **Docker** & Docker Compose
- **Nginx**
- **Laravel Sanctum**
- **PHPUnit**
- **L5-Swagger**

## 📦 Requisitos

- Docker 20.10+
- Docker Compose 2.0+
- Git
- Make (opcional, recomendado)

## 🔧 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/leowebdesigner/gestaoesportiva.git
cd gestaoesportiva
```

### 2. Copie o ambiente

```bash
cp .env.example .env
```

### 3. Build e subida

```bash
make build
make up
```

### 4. Instalação do Laravel

```bash
make install
```

### 5. Ative as filas após o install

Rode o comando abaixo após o make install para que as sejam filas ativadas
```bash
make restart
```

## ▶️ Executando a Aplicação

- API: `http://localhost:8000`
- PHPMyAdmin: `http://localhost:8080`

Comandos úteis:

```bash
make up
make build
make down
make restart
make logs
make shell
make mysql
make composer-install
make install
make migrate
make migrate-fresh
make migrate-fresh-no-seed
make seed
make seed-users
make cache-clear
make test
make test-coverage
make import-teams
make import-players
make import-games
make import-all
make optimize
make swagger
make queue-work
make horizon
```

## 📥 Importação de Dados

```bash
make import-teams
make import-players
make import-games
make import-all
```

A API externa possui limite de 30 req/min. O client aplica controle de rate limit.

## 🧪 Testes

```bash
make test
make test-coverage
```

## 📚 Documentação da API

Swagger UI:

- `http://localhost:8000/api/documentation`

Gerar docs:

```bash
make swagger
```

## 📁 Estrutura do Projeto

```text
app/
├── Console/Commands
├── Contracts
├── Exceptions
├── External
├── Http
├── Jobs
├── Models
├── Policies
├── Providers
├── Repositories
├── Services
└── Traits
```

## 🔐 Autenticação

A API suporta dois métodos de autenticação:

### Bearer Token (Sanctum) - Usuários Internos
Para usuários do sistema/frontend (`is_external=false`):

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### X-Authorization - Usuários Externos
Para sistemas externos/APIs (`is_external=true`):

```bash
curl -X POST http://localhost:8000/api/v1/auth/x-login \
  -H "Content-Type: application/json" \
  -d '{"email":"external@api.com","password":"external123"}'
```

Uso do token:
```bash
curl http://localhost:8000/api/v1/players \
  -H "X-Authorization: {x_token}"
```

### Registro de Usuário Externo
```bash
curl -X POST http://localhost:8000/api/v1/auth/register-external \
  -H "Content-Type: application/json" \
  -d '{"name":"Client","email":"client@api.com","password":"pass123","password_confirmation":"pass123"}'
```

> ⚠️ **Importante**: Usuários internos NÃO podem usar X-Login e usuários externos NÃO podem usar Login normal.

## 🧵 Filas e Horizon

O worker de filas sobe automaticamente junto com o `make up` (container `queue`).
Se precisar reiniciar manualmente:

```bash
make restart-queue
```

A interface do Horizon fica em:

```
http://localhost/horizon
```

## 👥 Perfis de Acesso

### Interno - Administrador
- Email: `admin@example.com`
- Senha: `password`
- Autenticação: Bearer Token (Sanctum)
- Permissões: CRUD completo

### Interno - Usuário
- Email: `user@example.com`
- Senha: `password`
- Autenticação: Bearer Token (Sanctum)
- Permissões: criar, ler e atualizar (sem delete)

### Externo - API Client
- Email: `external@api.com`
- Senha: `external123`
- Autenticação: X-Authorization
- Permissões: mesmas do usuário interno
