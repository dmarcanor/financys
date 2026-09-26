up:
	docker compose up -d

down:
	docker compose down -v

setup:
	docker volume create financys_postgres_data
	docker volume create financys_redis_data
	docker volume create rabbitmq_data
	docker volume create rabbitmq_log
	docker volume create financys_mongo_data
	cp .env.example .env
	make setup-database
	make setup-mongo
	make up
	make install
	make generate-laravel-key
	make generate-jwt-key
	make migrate
	make seed

setup-database:
	@test -f .env || cp .env.example .env
	@echo "Configuring PostgreSQL connection in .env (press Enter to keep the default)"
	@printf "DB_CONNECTION [pgsql]: "; read -r conn; \
	conn=$${conn:-pgsql}; \
	printf "DB_HOST [127.0.0.1]: "; read -r host; \
	host=$${host:-127.0.0.1}; \
	printf "DB_PORT [5432]: "; read -r port; \
	port=$${port:-5432}; \
	printf "DB_DATABASE [app_db]: "; read -r db; \
	db=$${db:-app_db}; \
	printf "DB_USERNAME [pguser]: "; read -r user; \
	user=$${user:-pguser}; \
	printf "DB_PASSWORD [changeme]: "; \
	if [ -t 0 ]; then stty -echo; fi; \
	read -r pass; \
	if [ -t 0 ]; then stty echo; echo; fi; \
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

setup-mongo:
	@test -f .env || cp .env.example .env
	@echo "Configuring MongoDB connection in .env (press Enter to keep the default)"
	@printf "MONGO_HOST [mongo]: "; read -r host; \
	host=$${host:-mongo}; \
	printf "MONGO_PORT [27017]: "; read -r port; \
	port=$${port:-27017}; \
	printf "MONGO_DATABASE [financys]: "; read -r db; \
	db=$${db:-financys}; \
	printf "MONGO_USERNAME [dev]: "; read -r user; \
	user=$${user:-dev}; \
	printf "MONGO_PASSWORD [dev-pass]: "; \
	if [ -t 0 ]; then stty -echo; fi; \
	read -r pass; \
	if [ -t 0 ]; then stty echo; echo; fi; \
	pass=$${pass:-dev-pass}; \
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
	set_env MONGO_HOST "$$host"; \
	set_env MONGO_PORT "$$port"; \
	set_env MONGO_DATABASE "$$db"; \
	set_env MONGO_USERNAME "$$user"; \
	set_env MONGO_PASSWORD "$$pass"; \
	echo "MongoDB configuration written to .env"

install:
	docker compose run --rm php composer install

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