up:
	docker compose up -d

down:
	docker compose down -v

setup:
	docker volume create financys_postgres_data
	docker volume create financys_redis_data
	make up
	cp .env.example .env
	make setup-database
	make install
	make generate-laravel-key
	make generate-jwt-key
	make migrate
	make seed

setup-database:
	@test -f .env || cp .env.example .env
	@echo "Configuring PostgreSQL connection in .env (press Enter to keep the default)"
	@read -p "DB_CONNECTION [pgsql]: " conn; \
	conn=$${conn:-pgsql}; \
	read -p "DB_HOST [127.0.0.1]: " host; \
	host=$${host:-127.0.0.1}; \
	read -p "DB_PORT [5432]: " port; \
	port=$${port:-5432}; \
	read -p "DB_DATABASE [app_db]: " db; \
	db=$${db:-app_db}; \
	read -p "DB_USERNAME [pguser]: " user; \
	user=$${user:-pguser}; \
	read -s -p "DB_PASSWORD [changeme]: " pass; \
	echo; \
	pass=$${pass:-changeme}; \
	set_env() { \
		key=$$1; value=$$2; \
		if grep -q "^$$key=" .env; then \
			sed "s|^$$key=.*|$$key=$$value|" .env > .env.tmp && mv .env.tmp .env; \
		elif grep -q "^# $$key=" .env; then \
			sed "s|^# $$key=.*|$$key=$$value|" .env > .env.tmp && mv .env.tmp .env; \
		else \
			echo "$$key=$$value" >> .env; \
		fi; \
	}; \
	set_env DB_CONNECTION "$$conn"; \
	set_env DB_HOST "$$host"; \
	set_env DB_PORT "$$port"; \
	set_env DB_DATABASE "$$db"; \
	set_env DB_USERNAME "$$user"; \
	set_env DB_PASSWORD "$$pass"; \
	echo "Database configuration written to .env"

install:
	docker run --rm -w /app -v ${PWD}:/app composer composer install

generate-laravel-key:
	docker compose exec php php artisan key:generate

generate-jwt-key:
	docker compose exec php php artisan jwt:secret

migrate:
	docker compose exec php php artisan migrate

rollback:
	docker compose exec php php artisan migrate:rollback

seed:
	docker compose exec php php artisan db:seed

test:
	docker compose exec php sh -c 'DB_DATABASE=financys_test php artisan migrate:fresh --force'
	docker compose exec php ./vendor/bin/pest --parallel

