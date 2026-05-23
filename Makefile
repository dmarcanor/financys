up:
	docker compose up -d

down:
	docker compose down -v

install:
	docker run --rm -w /app -v ${PWD}:/app composer composer install

migrate:
	docker compose exec php php artisan migrate

seed:
	docker compose exec php php artisan db:seed

test:
	docker compose exec php ./vendor/bin/pest

