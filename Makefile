#---
#--- Commandes du projet Symfony en cours
#--- ————————————————————————————————————————————————————————————————————————————————————————————————
#--- 
##
## Options disponibles :
##

include .env
ifneq (,$(wildcard ./.env.local))
    include .env.local
endif

# Test que le fichier ".env" est bien présent dans le dossier Docker ".devdocker"
ifneq ($(wildcard .devdocker),)
ifeq ($(wildcard .devdocker/.env),)
$(error "Le fichier .env n'existe pas dans le dossier .devdocker !!!")
endif
endif

# Arguments de la commande make
ARGS = $(filter-out $@,$(MAKECMDGOALS))

# Code de l'application
ifndef APP_CODE
$(warning !!! APP_CODE is not set in .env.local !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!)
	APP_CODE = $(shell basename $(CURDIR))
endif

# Nom de l'application = Code de l'application avec la première lettre en majuscule
APP_NAME = $(shell echo $(APP_CODE) | sed -E 's/^(.)(.*)/\U\1\L\2/')

# Système de gestion de base de données utilisé
ifndef APP_SGBD
$(warning !!! APP_SGBD is not set in .env.local !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!)
	APP_SGBD = mariadb
endif

# URL du dépôt Github
GITHUB_REMOTE := $(shell git config --get remote.origin.url)
ifeq ($(strip $(GITHUB_REMOTE)),)
$(warning !!! GITHUB_REMOTE is not set !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!)
endif

# Version
VERSION_PHP := $(shell php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
VERSION_SYMFONY := $(shell symfony console --version | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')
VERSION_APPLI := $(shell git describe --abbrev=0 2> /dev/null)
ifndef VERSION_APPLI
	VERSION_APPLI := dev
endif

# Binaire
SYMFONY = symfony
DOCKER = docker
COMPOSE = docker compose
PHPMD = ./vendor/bin/phpmd
PHPSTAN = ./vendor/bin/phpstan
PSALM = ./vendor/bin/psalm
PHPUNIT = ./vendor/bin/phpunit
PHP_CS_FIXER = ./vendor/bin/php-cs-fixer
RECTOR = ./vendor/bin/rector
PHP_METRICS = ./vendor/bin/phpmetrics

# Liste des conteneurs Docker de l'application
CONTAINERS := $(shell docker ps -q --filter "name=$(APP_CODE)-*")


.PHONY: test config build

help: Makefile ## Affiche cette aide
	@sed -n 's/^#---//p' $<
	@echo "  Nom du projet : \e[1;34m$(APP_NAME)\e[0;0m"
	@echo "  SGBD utilisé : \e[1;34m$(APP_SGBD)\e[0;0m"
	@echo "  Version PHP : \e[1;34m$(VERSION_PHP)\e[0;0m"
	@echo "  Version Symfony : \e[1;34m$(VERSION_SYMFONY)\e[0;0m"
	@echo "  Version du projet : \e[1;34m$(VERSION_APPLI)\e[0;0m"
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m\x08\x08\x08/'
	@if [ -f project.mk ]; then \
		grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' project.mk | awk 'BEGIN {FS = ":.*?## "}{printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m\x08\x08\x08/'; \
	fi



## —— 💻 Serveur web Symfony ———————————————————————————————————————————————————————————————————————

serve: ## Démarrage du serveur Symfony
	@$(SYMFONY) local:server:start --daemon --allow-all-ip

unserve: ## Arrêt du serveur Symfony
	@$(SYMFONY) local:server:stop

debug: ## Log de débogage du serveur Symfony
	@$(SYMFONY) server:log

upgrade: ## Mise à jour du serveur Symfony
	@$(SYMFONY) local:server:stop
	@wget https://get.symfony.com/cli/installer -O - | bash
	@ln -sf ~/.symfony5/bin/symfony ~/bin/symfony

php-set: ## Définition de la version de PHP à utiliser pour le système
	@$(SYMFONY) local:php:list
	@sudo update-alternatives --config php
	@$(SYMFONY) local:php:refresh



## ——— 🐳 Containers Docker ————————————————————————————————————————————————————————————————————————

up: ## Démarrage du service Docker MariaDB et autres services
	@if [ -d .devdocker ]; then cd .devdocker && $(COMPOSE) up --detach --remove-orphans; fi

down: ## Arrêt du service Docker MariaDB et autres services
	@if [ -d .devdocker ]; then cd .devdocker && $(COMPOSE) down; fi

build: ## Build des images Docker
	@if [ -d .devdocker ]; then cd .devdocker && $(COMPOSE) build --pull --no-cache; fi

compose: ## Exécute docker-compose avec ses arguments
	@if [ -d .devdocker ]; then cd .devdocker && $(COMPOSE) $(ARGS); fi

logs: ## Log des containers Docker avec le nom du container possible
	@if [ -d .devdocker ]; then cd .devdocker && $(COMPOSE) logs --tail=100 --follow --timestamps $(ARGS); fi

healthy: ## Vérification des conteneurs Docker 🩺
	@echo -n "Vérification du service docker : "
	@if [ -f .devdocker ]; then cd .devdocker && $(DOCKER) info > /dev/null 2>&1; fi
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



## ——— 🏗️  Projet ———————————————————————————————————————————————————————————————————————————————————

start: up serve debug ## Démarrage du service Docker MariaDB et Symfony

stop: down unserve ## Arrêt du service Docker MariaDB et Symfony

check: ## Vérification du projet 🩺
	@echo "  Nom du projet : \e[1;34m$(APP_NAME)\e[0;0m"
	@echo "  Version PHP système : \e[1;34m$(VERSION_PHP)\e[0;0m"
	@echo "  Version PHP appli : \e[1;34m$(shell cat .php-version)\e[0;0m"
	@echo "  Version Symfony : \e[1;34m$(VERSION_SYMFONY)\e[0;0m"
	@$(SYMFONY) console debug:container --env-vars
	$(MAKE) healthy
	$(MAKE) schema

dotenv: ## Affichage des variables d'environnement 🩺
	@$(SYMFONY) console debug:dotenv
	@$(SYMFONY) console debug:container --env-vars

audit: ## Vérification de la sécurité du projet 🩺
	@$(SYMFONY) check:security

update: ## Mise à jour des packages Symfony et du projet
	@$(SYMFONY) composer update

cc: ## Nettoyage des fichiers de cache
	@$(SYMFONY) console cache:clear

schema: ## Vérification de la validité de la structure de la base de données 🩺
	@$(SYMFONY) console doctrine:schema:validate -v

migrate: ## Mise à jour de la structure de la base de données
	@$(SYMFONY) console doctrine:migrations:migrate



## ——— 🗃️  Base de données MariaDB ——————————————————————————————————————————————————————————————————

data-dump: ## Exporter les données de la base de données "make data-dump file=/tmp/dump.sql"
	@$(eval file ?= /tmp/dump.sql)
	@cd .devdocker && $(COMPOSE) exec database bash -c 'mariadb-dump --user=$$MARIADB_USER --password=$$MARIADB_PASSWORD $$MARIADB_DATABASE' > $(file)
	@echo "[OK] Fichier de dump de la base de données exporté dans : $(file)"


data-restore: ## Restaurer les données de la base de données "make data-restore file=/tmp/dump.sql"
ifeq ($(strip $(file)),)
	$(error Le dump n'a pas été renseigné : "make data-restore file=/tmp/dump.sql")
endif
	@cd .devdocker && $(COMPOSE) exec -T database bash -c 'mariadb --user=$$MARIADB_USER --password=$$MARIADB_PASSWORD $$MARIADB_DATABASE' < $(file)
	@echo "[OK] Fichier de dump '$(file)' de la base de données importé dans la base de données"



## ——— 🧪 Tests unitaires ——————————————————————————————————————————————————————————————————————————

test: ## Tests unitaires avec PhpUnit
	@$(eval filter ?= '.')
	@$(PHPUNIT) --configuration phpunit.xml --filter=$(filter)


test-init: ## Initialisation des tests avec un nouveau jeu de données
ifeq ($(APP_SGBD),sqlite)
	@rm -rf datas/test.sqlite
	@$(SYMFONY) console doctrine:schema:drop --force --env=test
	@$(SYMFONY) console doctrine:schema:create --env=test
	@$(SYMFONY) console doctrine:fixtures:load --no-interaction --env=test
else
	@$(SYMFONY) console doctrine:cache:clear-metadata --env=test
	@$(SYMFONY) console doctrine:database:create --if-not-exists --env=test
	@$(SYMFONY) console doctrine:schema:drop --force --env=test
	@$(SYMFONY) console doctrine:schema:create --env=test
	@$(SYMFONY) console doctrine:schema:validate --env=test
	@$(SYMFONY) console doctrine:fixtures:load --no-interaction --env=test
endif



## ——— 💩 Coding standards —————————————————————————————————————————————————————————————————————————

phpmd: ## Analyse de la qualité du code
	@$(PHPMD) src ansi ruleset.xml
	@$(PHPMD) tests ansi ruleset.xml


codestyle: ## Analyse si le code suit le standard de Symfony
	@$(PHP_CS_FIXER) fix --dry-run --verbose --diff


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
	@XDEBUG_MODE=coverage $(PHPUNIT)--configuration phpunit.xml --coverage-html var/coverage


# Chargement des commandes personnalisées
ifneq (,$(wildcard ./project.mk))
    include project.mk
endif

##
##
