# Define 'local' como el entorno por defecto si no se especifica otro.
APP_ENV ?= local

COMPOSE_FILES := -f docker-compose.yml
ifeq ($(APP_ENV),production)
	COMPOSE_FILES += -f docker-compose.prod.yml
endif

COMPOSE_CMD = docker-compose $(COMPOSE_FILES)

.PHONY: up reload start restart stop down down_all status cli run logs app_key compile dev serve broadcast

up reload: recreate ?=
up reload:
	$(COMPOSE_CMD) build --build-arg APP_ENV=$(APP_ENV) && $(COMPOSE_CMD) up -d --force-recreate --remove-orphans $(recreate)

start:
	$(COMPOSE_CMD) start

restart:
	make up recreate="--force-recreate --renew-anon-volumes"

stop: service ?= 
stop:
	$(COMPOSE_CMD) stop $(service)

down d:
	$(COMPOSE_CMD) down -v --remove-orphans

down_all:
	make down && docker image prune -a -f && docker volume prune -f && docker system prune -a -f --volumes

status ps s:
	$(COMPOSE_CMD) ps

cli exec: bash ?= bash
cli exec: path ?= /var/www
cli exec: service ?= app
cli exec:
	$(COMPOSE_CMD) exec -w $(path) $(service) $(bash) $(command)

run: bash ?= bash
run: ports ?=
run: service ?= app
run: 
	$(COMPOSE_CMD) run --rm $(ports) $(service) $(bash) $(command)

logs l: follow ?= -f
logs l: service ?= app
logs l:
	$(COMPOSE_CMD) logs $(follow) $(service)

app_key:
	$(COMPOSE_CMD) exec app php artisan key:generate

compile:
	$(COMPOSE_CMD) exec app npm run build

dev:
	$(COMPOSE_CMD) exec app npm run dev

serve: host ?= 0.0.0.0
serve: port ?= 8000
serve:
	$(COMPOSE_CMD) exec app php artisan serve --host=$(host) --port=$(port)

broadcast:
	$(COMPOSE_CMD) exec app php artisan reverb:start