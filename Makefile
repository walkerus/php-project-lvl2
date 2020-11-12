install:
	composer install

validate:
	composer validate

lint:
	composer run-script phpcs -- --standard=PSR12 src bin

docker-install:
	@docker run --rm -v ${PWD}:/app composer:2.0 install --ignore-platform-reqs

docker-validate:
	@docker run --rm -v ${PWD}:/app composer:2.0 make validate


docker-composer:
	@docker run --rm --interactive --tty -v ${PWD}:/app composer:2.0 sh

docker-lint:
	@docker run --rm -v ${PWD}:/app composer:2.0 make lint

docker-cli:
	@docker run --rm --interactive --tty -v ${PWD}:/app -w /app php:7.4-cli bash
