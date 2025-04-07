#---
#--- Commandes du projet Symfony en cours
#--- ————————————————————————————————————————————————————————————————————————————————————————————————
#--- 
##
## Options disponibles :
##

# Chargement des variables d'environnement
include .env
ifeq (,$(wildcard ./.env.local))
$(error "Le fichier .env.local n'existe pas dans le dossier courant !!!")
endif
include .env.local

# Arguments de la commande make
ARGS = $(filter-out $@,$(MAKECMDGOALS))

# Code de l'application
ifndef APP_CODE
$(error !!! APP_CODE is not set in .env.local !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!)
	APP_CODE = app
endif

# Nom de l'application = Code de l'application avec la première lettre en majuscule
APP_NAME = $(shell echo $(APP_CODE) | sed -E 's/^(.)(.*)/\U\1\L\2/')

# Système de gestion de base de données utilisé
ifndef DATABASE_TYPE
$(warning !!! DATABASE_TYPE is not set in .env.local !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!)
	DATABASE_TYPE = mariadb
endif

# URL du dépôt Github
GITHUB_REMOTE := $(shell git config --get remote.origin.url)
ifeq ($(strip $(GITHUB_REMOTE)),)
$(warning !!! GITHUB_REMOTE is not set !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!)
endif

# Binaire (local)
DOCKER = docker
COMPOSE = docker compose
WEBAPP_DOCKER = $(COMPOSE) exec webapp
# Binaire dans le container FrankenPHP
PHP = $(WEBAPP_DOCKER) php
COMPOSER = $(WEBAPP_DOCKER) composer
SYMFONY = $(WEBAPP_DOCKER) bin/console
PHPMD = $(WEBAPP_DOCKER) vendor/bin/phpmd
PHPSTAN = $(WEBAPP_DOCKER) vendor/bin/phpstan
PSALM = $(WEBAPP_DOCKER) vendor/bin/psalm
PHPUNIT = $(WEBAPP_DOCKER) vendor/bin/phpunit
PHP_CS_FIXER = $(WEBAPP_DOCKER) vendor/bin/php-cs-fixer
RECTOR = $(WEBAPP_DOCKER) vendor/bin/rector
PHP_METRICS = $(WEBAPP_DOCKER) vendor/bin/phpmetrics

# Liste des conteneurs Docker de l'application
CONTAINERS = $(shell docker ps -q --filter "name=$(APP_CODE)-*")

# Version
VERSION_PHP = $(shell $(PHP) -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
ifeq ($(strip $(VERSION_PHP)),)
	VERSION_PHP = $(shell cat .php-version)
endif
VERSION_SYMFONY = $(shell $(SYMFONY) --version | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')
VERSION_APPLI = $(shell git describe --abbrev=0 2> /dev/null)
ifeq ($(strip $(VERSION_APPLI)),)
	VERSION_APPLI = dev
endif


.DEFAULT_GOAL = help
.PHONY: test config build logs serve unserve debug upgrade php-set up down compose bash healthy deploy push
.PHONY: composer install vendor update sf dotenv cc schema migrate audit
.PHONY: data-dump data-restore
.PHONY: test test-init phpmd codestyle phpstan psalm rector
.PHONY: codestyle-fix rector-fix
.PHONY: php-metrics coverage

help: Makefile ## Affiche cette aide
	@sed -n 's/^#---//p' $<
	@echo "  Nom du projet : \e[1;34m$(APP_NAME)\e[0;0m"
	@echo "  SGBD utilisé : \e[1;34m$(DATABASE_TYPE)\e[0;0m"
	@echo "  Version PHP : \e[1;34m$(VERSION_PHP)\e[0;0m"
	@echo "  Version Symfony : \e[1;34m$(VERSION_SYMFONY)\e[0;0m"
	@echo "  Version du projet : \e[1;34m$(VERSION_APPLI)\e[0;0m"
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m\x08\x08\x08/'
	@if [ -f project.mk ]; then \
		grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' project.mk | awk 'BEGIN {FS = ":.*?## "}{printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m\x08\x08\x08/'; \
	fi



## —— 💻 Serveur web local Symfony —————————————————————————————————————————————————————————————————

serve: ## Démarrage du serveur Symfony
	@symfony local:server:start --daemon --allow-all-ip

unserve: ## Arrêt du serveur Symfony
	@symfony local:server:stop

debug: ## Log de débogage du serveur Symfony
	@symfony server:log

upgrade: ## Mise à jour du serveur Symfony
	@symfony local:server:stop
	@wget https://get.symfony.com/cli/installer -O - | bash
	@ln -sf ~/.symfony5/bin/symfony ~/bin/symfony

php-set: ## Définition de la version de PHP à utiliser pour le système
	@symfony local:php:list
	@sudo update-alternatives --config php
	@symfony local:php:refresh



## ——— 🐳 Containers Docker ————————————————————————————————————————————————————————————————————————

up: ## Démarrage du service Docker FrankenPHP, MariaDB et autres services
	@SERVER_NAME=:80 $(COMPOSE) --env-file .env.local up --detach --remove-orphans --wait

down: ## Arrêt du service Docker FrankenPHP, MariaDB et autres services
	@$(COMPOSE) --env-file .env.local down --remove-orphans

build: ## Build des images Docker FrankenPHP
	@echo "\e[1;33mConstruction des images en version PHP : \e[1;34m$(VERSION_PHP)\e[1;33m"; echo "===============================================\e[0;0m\n"
	@$(COMPOSE) --env-file .env.local build --pull --no-cache --build-arg VERSION_PHP=${VERSION_PHP}

compose: ## Exécute docker-compose avec ses arguments
	@$(COMPOSE) --env-file .env.local $(ARGS)

logs: ## Log des containers Docker avec le nom du container possible
	@$(COMPOSE) logs --tail=100 --follow --timestamps $(ARGS)

bash: ## Exécute un bash dans le container FrankenPHP
	@$(WEBAPP_DOCKER) bash

healthy: ## Vérification des conteneurs Docker 🩺
	@echo -n "Vérification du service docker : "
	@$(DOCKER) info > /dev/null 2>&1
	@echo "[\e[1;32mOK\e[0;0m]"
	@for item in $(CONTAINERS); do \
		echo -n "Vérification du container \e[1;34m`$(DOCKER) inspect --format "{{.Name}}" $$item | sed 's/^\/\?//'`\e[0;0m : "; \
		if test '"healthy"' = `$(DOCKER) inspect --format "{{json .State.Health.Status }}" $$item`; then \
			echo "[\e[1;32mHEALTHY\e[0;0m]"; \
		elif test '"unhealthy"' = `$(DOCKER) inspect --format "{{json .State.Health.Status }}" $$item`; then \
			echo "[\e[1;31mUNHEALTHY\e[0;0m]"; \
		else \
			echo "[\e[1;33m`$(DOCKER) inspect --format "{{json .State.Health.Status }}" $$item`\e[0;0m]"; \
		fi; \
	done

deploy: ## Déploiement du projet
	@if test -z "${APP_IMAGE}"; then \
		echo "!!! APP_IMAGE is not set in .env.local !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"; exit 1; \
	fi
	@$(MAKE) down
	@git checkout master
	@echo "\e[1;33mGénération de l'image \e[1;34m${APP_IMAGE}:${VERSION_APPLI}\e[1;33m"; echo "===============================================\e[0;0m\n"
	@$(DOCKER) build --pull --tag ${APP_IMAGE} --target frankenphp_prod \
		--build-arg VERSION_PHP=${VERSION_PHP} --build-arg CREATED_AT="$(shell date -u +"%Y-%m-%dT%H:%M:%SZ")" \
		--build-arg APP_CODE=${APP_NAME} --build-arg VERSION_APPLI=${VERSION_APPLI} .
	@$(DOCKER) build --pull --tag ${APP_IMAGE}:${VERSION_APPLI} --target frankenphp_prod \
		--build-arg VERSION_PHP=${VERSION_PHP} --build-arg CREATED_AT="$(shell date -u +"%Y-%m-%dT%H:%M:%SZ")" \
		--build-arg APP_CODE=${APP_NAME} --build-arg VERSION_APPLI=${VERSION_APPLI} .
	@git checkout develop
	@SERVER_NAME=:80 VERSION_PHP=${VERSION_PHP} $(COMPOSE) --file compose.yaml --file compose.prod.yaml --env-file .env.local \
		up --build --remove-orphans

push: ## Pousser le projet sur le registry Docker Hub ou autre
	@if test -z "${APP_IMAGE}"; then \
		echo "!!! APP_IMAGE is not set in .env.local !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"; exit 1; \
	fi
	@git checkout master
	@echo "\e[1;33mDéploiement de l'image \e[1;34m${APP_IMAGE}:${VERSION_APPLI}\e[1;33m sur le registry Docker Hub"; echo "=============================================================================\e[0;0m\n"
	@$(DOCKER) push ${APP_IMAGE}
	@$(DOCKER) push ${APP_IMAGE}:${VERSION_APPLI}
	@git checkout develop


## —— 🧙 Composer ——————————————————————————————————————————————————————————————————————————————————

composer: ## Exécute une commande Composer
	@$(COMPOSER) $(ARGS)

install: ## Installation des packages Symfony et du projet
	@$(COMPOSER) install --prefer-dist

vendor: ## Installation des packages Symfony et du projet en production
	@$(COMPOSER) install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction

update: ## Mise à jour des packages Symfony et du projet
	@$(COMPOSER) update

audit: ## Audit des packages 🩺
	@echo "\n\e[1;33mAudit de sécurité de l'application\n=============================================================================\e[0;0m\n"
	@$(COMPOSER) audit --no-dev



## ——— 🏗️  Projet ———————————————————————————————————————————————————————————————————————————————————

start: up logs ## Démarrage du service Docker MariaDB et Symfony

stop: down ## Arrêt du service Docker MariaDB et Symfony

check: ## Vérification du projet 🩺
	@echo "  Nom du projet : \e[1;34m$(APP_NAME)\e[0;0m"
	@echo "  Version PHP appli : \e[1;34m$(VERSION_PHP)\e[0;0m"
	@echo "  Version PHP système : \e[1;34m$(shell $(PHP) -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")\e[0;0m"
	@echo "  Version Symfony : \e[1;34m$(VERSION_SYMFONY)\e[0;0m"
	@$(SYMFONY) debug:container --env-vars
	$(MAKE) healthy
	$(MAKE) schema

sf: ## Exécute une commande Symfony
	@$(SYMFONY) $(ARGS)

dotenv: ## Affichage des variables d'environnement 🩺
	@$(SYMFONY) debug:dotenv
	@$(SYMFONY) debug:container --env-vars

cc: ## Nettoyage des fichiers de cache
	@$(SYMFONY) cache:clear

schema: ## Vérification de la validité de la structure de la base de données 🩺
	@$(SYMFONY) doctrine:schema:validate -v

db-migrate: ## Mise à jour de la structure de la base de données
	@$(SYMFONY) doctrine:migrations:migrate

release-major: ## Création d'une release majeure du projet
	@$(eval version ?= $(shell echo $(VERSION_APPLI) | awk -F. '{print ($$1+1)"."0".0"}'))
	$(MAKE) audit
	$(MAKE) quality
	$(MAKE) test
	@echo "\n\n\e[1;33mCréation de la release majeure \e[1;34m${version}\e[1;33m\n=============================================================================\e[0;0m\n"
	@echo "VERSION=$(version)\nMETHOD=release" > make.release.tmp
	@git flow release start $(version)

release-minor: ## Création d'une release mineure du projet
	@$(eval version ?= $(shell echo $(VERSION_APPLI) | awk -F. '{print $$1"."($$2+1)".0"}'))
	$(MAKE) audit
	$(MAKE) quality
	$(MAKE) test
	@echo "\n\n\e[1;33mCréation de la release mineure \e[1;34m${version}\e[1;33m\n=============================================================================\e[0;0m\n"
	@echo "VERSION=$(version)\nMETHOD=release" > make.release.tmp
	@git flow release start $(version)

release-hotfix: ## Création d'une hotfix mineure du projet	
	@$(eval version ?= $(shell echo $(VERSION_APPLI) | awk -F. '{print $$1"."$$2"."($$3+1)}'))
	$(MAKE) audit
	$(MAKE) quality
	$(MAKE) test
	@echo "\n\n\e[1;33mCréation de la hotfix \e[1;34m${version}\e[1;33m\n=============================================================================\e[0;0m\n"
	@echo "VERSION=$(version)\nMETHOD=hotfix" > make.release.tmp
	@git flow hotfix start $(version)

release-finish: ## Finalisation d'une release du projet
	@if [ ! -f make.release.tmp ]; then \
		echo "\e[1;37mVous devez d'abord créer une release avec \e[1;34m'make release-major'\e[1;37m, \e[1;34m'make release-minor'\e[1;37m ou \e[1;34m'make release-hotfix'\e[0;0m"; exit 1; \
	fi;
	@set -a; \
	. ./make.release.tmp; \
	echo "\e[1;33mFinalisation de la release \e[1;34m$${METHOD}:$${VERSION}\e[1;33m\n=============================================================================\e[0;0m\n"; \
	read -p "Êtes vous sur de vouloir finaliser la release $${METHOD}:$${VERSION} ? [y/N] " confirm; \
	if [ "$${confirm}" != "y" ]; then \
		echo "Annulation de la release \e[1;34m$${METHOD}:$${VERSION}\e[0;0m"; \
		exit 1; \
	fi; \
	git flow $${METHOD} finish $${VERSION}; \
	set +a;
	@rm make.release.tmp
	@git push
	@git push --tags
#	@git stash pop

## ——— 🗃️  Base de données MariaDB ——————————————————————————————————————————————————————————————————

data-dump: ## Exporter les données de la base de données "make data-dump file=/tmp/dump.sql"
	@$(eval file ?= /tmp/dump.sql)
	@$(COMPOSE) exec database bash -c 'mariadb-dump --user=$$MARIADB_USER --password=$$MARIADB_PASSWORD $$MARIADB_DATABASE' > $(file)
	@echo "[OK] Fichier de dump de la base de données exporté dans : $(file)"


data-restore: ## Restaurer les données de la base de données "make data-restore file=/tmp/dump.sql"
ifeq ($(strip $(file)),)
	$(error Le dump n'a pas été renseigné : "make data-restore file=/tmp/dump.sql")
endif
	@$(COMPOSE) exec -T database bash -c 'mariadb --user=$$MARIADB_USER --password=$$MARIADB_PASSWORD $$MARIADB_DATABASE' < $(file)
	@echo "[OK] Fichier de dump '$(file)' de la base de données importé dans la base de données"



## ——— 🧪 Tests unitaires ——————————————————————————————————————————————————————————————————————————

test: ## Tests unitaires avec PhpUnit
	@$(eval filter ?= '.')
	@if [ $(filter) = '.' ]; then \
		if [ $(DATABASE_TYPE) = sqlite ]; then rm -rf datas/test.sqlite; fi; \
		$(SYMFONY) doctrine:schema:drop --force --env=test; \
		$(SYMFONY) doctrine:schema:drop --force --env=test; \
		$(SYMFONY) doctrine:schema:create --env=test; \
		$(SYMFONY) doctrine:fixtures:load --no-interaction --env=test; \
		$(SYMFONY) cache:clear --env=test; \
	fi
	@$(PHPUNIT) --configuration phpunit.xml --filter=$(filter)


test-init: ## Initialisation des tests avec un nouveau jeu de données
ifeq ($(DATABASE_TYPE),sqlite)
	@rm -rf datas/test.sqlite
	@$(SYMFONY) doctrine:schema:drop --force --env=test
	@$(SYMFONY) doctrine:schema:create --env=test
	@$(SYMFONY) doctrine:fixtures:load --no-interaction --env=test
else
	@$(SYMFONY) doctrine:cache:clear-metadata --env=test
	@$(SYMFONY) doctrine:database:create --if-not-exists --env=test
	@$(SYMFONY) doctrine:schema:drop --force --env=test
	@$(SYMFONY) doctrine:schema:create --env=test
#	@$(SYMFONY) doctrine:schema:validate --env=test
	@$(SYMFONY) doctrine:fixtures:load --no-interaction --env=test
endif



## ——— 💩 Coding standards —————————————————————————————————————————————————————————————————————————

quality: ## Analyse complète de la qualité du code
	@echo "\n\e[1;33mContrôle de la propreté du code de l'application"; echo "=============================================================================\e[0;0m\n"
	@$(SYMFONY) lint:yaml .
	@$(SYMFONY) lint:twig templates/
	@$(SYMFONY) lint:container
	@$(MAKE) codestyle
	@echo "\n\e[1;33mContrôle de la qualité du code de l'application"; echo "=============================================================================\e[0;0m\n"
	@$(MAKE) phpmd
	@$(MAKE) phpstan
	@$(MAKE) psalm
	@$(MAKE) rector

codestyle: ## Analyse si le code suit le standard de Symfony
	@$(PHP_CS_FIXER) fix --dry-run --verbose --diff

phpmd: ## Analyse du code avec PHP Mess Detector
	@$(PHPMD) src ansi ruleset.xml
	@$(PHPMD) tests ansi ruleset.xml

phpstan: ## Analyse statique du code PHP avec PHPStan
	@$(PHPSTAN) analyse src tests --configuration=phpstan.neon

psalm: ## Analyse statique du code PHP avec Psalm
	@$(PSALM) --config=psalm.xml
#--alter --issues=InvalidReturnType,MissingParamType,PossiblyUnusedMethod --dry-run

rector: ## Analyse de la qualité du code en suivant les recommandations
	@$(RECTOR) process --dry-run --config rector.php



## ——— 🖍️  Fixer les erreurs de standard de dev de Symfony ———————————————————————————————————————————

codestyle-fix: ## Corrige les erreurs de standard de dev de Symfony
	@$(PHP_CS_FIXER) fix

rector-fix: ## Corrige de la qualité du code en suivant les recommandations
	@$(RECTOR) process --config rector.php



## ——— 📈 Métriques de qualité du code ———————————————————————————————————————————————————————————————

php-metrics: ## Calcule les métriques de qualité du code
	@$(PHP_METRICS) --config=metrics.yml

coverage: ## Tests unitaires avec PhpUnit en générant des rapports de couverture
	@XDEBUG_MODE=coverage $(PHPUNIT) --configuration phpunit.xml --coverage-html var/coverage



# Chargement des commandes personnalisées
ifneq (,$(wildcard ./project.mk))
    include project.mk
endif

##
##
