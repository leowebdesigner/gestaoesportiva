.PHONY: up down build restart logs shell mysql install migrate migrate-fresh seed cache-clear test test-coverage \
	import-teams import-players import-games import-all optimize swagger

# Comandos Docker
up:            ## Sobe os containers
	docker-compose up -d

down:          ## Para os containers
	docker-compose down

build:         ## Build dos containers
	docker-compose build

restart:       ## Reinicia containers
	docker-compose down && docker-compose up -d

logs:          ## Visualiza logs
	docker-compose logs -f

shell:         ## Acessa shell do container app
	docker-compose exec app bash

mysql:         ## Acessa MySQL CLI
	docker-compose exec mysql mysql -u basketball -psecret

# Comandos Laravel
install:       ## Composer install + key generate + migrations + seeders
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate --seed
	docker-compose exec app php artisan storage:link

composer-install: ## Executa composer install no container app
	docker-compose exec app composer install

migrate:       ## Executa migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migrations com seeders
	docker-compose exec app php artisan migrate:fresh --seed

seed:          ## Executa seeders
	docker-compose exec app php artisan db:seed

cache-clear:   ## Limpa todos os caches
	docker-compose exec app php artisan optimize:clear

test:          ## Executa testes PHPUnit
	docker-compose exec app php artisan test

test-coverage: ## Testes com coverage report
	docker-compose exec app php artisan test --coverage

# Comandos de Importação
import-teams:  ## Importa times da API externa
	docker-compose exec app php artisan import:teams

import-players: ## Importa jogadores da API externa
	docker-compose exec app php artisan import:players

import-games:  ## Importa jogos da API externa
	docker-compose exec app php artisan import:games

import-all:    ## Importa todos os dados
	docker-compose exec app php artisan import:all

# Comandos de Manutenção
optimize:      ## Otimiza a aplicação
	docker-compose exec app php artisan optimize

swagger:       ## Gera documentação Swagger
	docker-compose exec app php artisan l5-swagger:generate
