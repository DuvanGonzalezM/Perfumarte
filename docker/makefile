# Define 'local' como el entorno por defecto si no se especifica otro.
APP_ENV ?= local

COMPOSE_FILES := -f docker-compose.yml
ifeq ($(APP_ENV),production)
	COMPOSE_FILES += -f docker-compose.prod.yml
endif

COMPOSE_CMD = docker-compose $(COMPOSE_FILES)

.PHONY: up reload start restart stop down down_all status cli run logs app_key bootstrap compile dev serve broadcast migrate seed fresh test lint

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

# Arranque sobre una base de datos nueva: migraciones + roles/permisos + cuenta
# TI inicial. Pide la contraseña por teclado. Repetirlo es seguro: si la cuenta
# TI ya existe, no la toca. Opciones: make bootstrap opts="--link"
bootstrap: opts ?=
bootstrap:
	$(COMPOSE_CMD) exec app php artisan prais:bootstrap $(opts)

# Crea el esquema. Sin este paso la aplicación arranca sin ninguna tabla.
migrate:
	$(COMPOSE_CMD) exec app php artisan migrate --force

# Carga roles, permisos y el usuario técnico inicial. Sin este paso no hay
# ningún permiso en la base y ningún usuario puede acceder a nada.
seed:
	$(COMPOSE_CMD) exec app php artisan db:seed --force

# Reconstruye la base desde cero. Destructivo: solo para entornos de desarrollo.
fresh:
	$(COMPOSE_CMD) exec app php artisan migrate:fresh --seed

test:
	$(COMPOSE_CMD) exec app ./vendor/bin/pest

lint:
	$(COMPOSE_CMD) exec app ./vendor/bin/pint

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

cache_clear:
	$(COMPOSE_CMD) exec app php artisan cache:clear 
	$(COMPOSE_CMD) exec app php artisan config:clear
	$(COMPOSE_CMD) exec app php artisan route:cache
	$(COMPOSE_CMD) exec app php artisan view:cache
	$(COMPOSE_CMD) exec app php artisan optimize