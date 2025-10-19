.PHONY: build up down restart logs test clean install

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

restart: down up

logs:
	docker-compose logs -f

test:
	docker-compose exec php vendor/bin/phpunit

clean:
	docker-compose down -v
	rm -rf vendor

install: build up
	@echo "Waiting for services to start..."
	sleep 10
	docker-compose exec php composer install
	@echo "Application is running on http://localhost:8080"

shell:
	docker-compose exec php sh

mysql:
	docker-compose exec mysql mysql -uroot -proot tile_app
