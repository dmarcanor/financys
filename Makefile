up:
	docker compose up -d

down:
	docker compose down -v

install:
	docker run --rm -w /app -v ${PWD}:/app composer composer install

generate-laravel-key:
	docker compose exec php php artisan key:generate

generate-jwt-secret:
	docker compose exec php php artisan jwt:secret

migrate:
	docker compose exec php php artisan migrate

rollback:
	docker compose exec php php artisan migrate:rollback

seed:
	docker compose exec php php artisan db:seed

test:
	docker compose exec php ./vendor/bin/pest --parallel

