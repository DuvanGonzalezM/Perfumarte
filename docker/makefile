up reload: recreate ?=
up reload: ## [*] Up/Reload one or all service containers
	docker-compose build --build-arg APP_ENV=${APP_ENV} && docker-compose up -d --force-recreate --remove-orphans $(recreate)

start: ## [*] Start one or all service containers
	docker-compose start

restart: ## [*] Restart one or all service containers
	make up recreate="--force-recreate --renew-anon-volumes"

stop: service ?= 
stop: ## [*] Stop or or all service containers
	docker-compose stop $(service)

down d: ## [*] Down one or all service container[s], network[s] and volume[s]
	docker-compose down -v --remove-orphans

down_all: ## [*] Down all service container[s], network[s] and volume[s]
	make down && docker image prune -a -f && docker volume prune -f && docker system prune -a -f --volumes

status ps s: ## [*] Show status of services
	docker-compose ps

cli exec: bash ?= bash
cli exec: path ?= /var/www
cli exec: service ?= app
cli exec: ## [*] Execute commands in service containers, use "command"  argument to send the command. By Default enter the shell.
	docker-compose exec -w $(path) $(service) $(bash) $(command)

run: bash ?= bash
run: ports ?=
run: service ?= app
run: ## [*] Run commands in a new service container
	docker-compose run --rm $(ports) $(service) $(bash) $(command)

logs l: follow ?= -f
logs l: service ?= app
logs l: ## [*] Show logs. Usage: make logs
	docker-compose logs $(follow) $(service)

app_key: ## [*] Generate a new application key
	docker-compose exec app php artisan key:generate

compile: ## [*] Build npm packages
	docker-compose exec app npm run build

dev: ## [*] Build npm packages in development mode
	docker-compose exec app npm run dev

serve: host ?= 0.0.0.0
serve: port ?= 8000
serve: ## [*] Run Laravel server
	docker-compose exec app php artisan serve --host=$(host) --port=$(port)

broadcast: host ?= 0.0.0.0
broadcast: port ?= 8088
broadcast: ## [*] Run Laravel server
	docker-compose exec app php artisan reverb:start