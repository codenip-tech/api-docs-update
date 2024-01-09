#!/bin/bash

UID = $(shell id -u)
DOCKER_BE = codenip-api-docs-update
BUILD_DIR = /tmp/build

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

start: ## Start the containers
	docker network create symfony-network || true
	cp -n docker-compose.yml.dist docker-compose.yml || true
	U_ID=${UID} docker-compose up -d

stop: ## Stop the containers
	U_ID=${UID} docker-compose stop

restart: ## Restart the containers
	$(MAKE) stop && $(MAKE) start

build: ## Rebuilds all the containers
	docker network create symfony-network || true
	cp -n docker-compose.yml.dist docker-compose.yml || true
	U_ID=${UID} docker-compose build

prepare: ## Runs backend commands
	$(MAKE) composer-install

run: ## starts the Symfony development server in detached mode
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} symfony serve -d

logs: ## Show Symfony logs in real time
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} symfony server:log

# Backend commands
composer-install: ## Installs composer dependencies
	U_ID=${UID} docker exec --user ${UID} ${DOCKER_BE} composer install --no-interaction
# End backend commands

ssh: ## bash into the be container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bash

code-style: ## Run PHP-CS-FIXER
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} composer f:s

build-prod: ## Creates a binary file for PROD
	mkdir ${BUILD_DIR}
	cp static-build.Dockerfile ${BUILD_DIR}/static-build.Dockerfile
	git archive HEAD | tar -x -C ${BUILD_DIR}
	(cd ${BUILD_DIR} && \
		echo APP_ENV=${{ secrets.APP_ENV }} > .env.local && \
		echo APP_SECRET=${{ secrets.APP_SECRET }} >> .env.local && \
		echo DATABASE_URL=${{ secrets.DATABASE_URL }} >> .env.local && \
		rm -Rf tests/ && \
		rm -Rf tools/ && \
		composer install --ignore-platform-reqs --no-dev -a && \
		composer dump-env prod && \
		docker build -t static-app -f static-build.Dockerfile . && \
		docker cp $$(docker create --name static-app-tmp static-app):/go/src/app/dist/frankenphp-linux-x86_64 app && \
		docker rm static-app-tmp)

