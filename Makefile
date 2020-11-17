install:
	composer install

validate:
	composer validate

lint:
	composer run-script phpcs -- --standard=PSR12 src bin tests

docker-install:
	@docker-compose run --rm composer install --ignore-platform-reqs

docker-validate:
	@docker-compose run --rm composer make validate

docker-composer:
	@docker-compose run --rm composer sh

docker-lint:
	@docker-compose run --rm composer make lint

docker-cli:
	@docker-compose run --rm -T cli bash
