#---
#--- Commandes de l'application
#--- ————————————————————————————————————————————————————————————————————————————————————————————————
#--- 
##
## Options disponibles :
##

# Chargement des variables d'environnement
include .env

# Arguments de la commande make
ARGS = $(filter-out $@,$(MAKECMDGOALS))

# Code de l'application
ifndef APP_CODE
$(error !!! APP_CODE is not set in .env !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!)
	APP_CODE = app
endif

# Nom de l'application = Code de l'application avec la première lettre en majuscule
APP_NAME = $(shell echo $(APP_CODE) | sed -E 's/^(.)(.*)/\U\1\L\2/')

# Binaire (local)
DOCKER = docker
COMPOSE = docker compose
WEBAPP_DOCKER = $(COMPOSE) exec webapp
# Binaire dans le container FrankenPHP
PHP = $(WEBAPP_DOCKER) php
COMPOSER = $(WEBAPP_DOCKER) composer
SYMFONY = $(WEBAPP_DOCKER) bin/console


.DEFAULT_GOAL = help
.PHONY: logs up down compose bash
.PHONY: composer install vendor update sf dotenv cc schema migrate
.PHONY: data-dump data-restore


help: Makefile ## Affiche cette aide
	@sed -n 's/^#---//p' $<
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m\x08\x08\x08/'



## ——— 🐳 Containers Docker ————————————————————————————————————————————————————————————————————————

up: ## Démarrage du service Docker FrankenPHP, MariaDB et autres services
	@$(COMPOSE) up --detach --remove-orphans --wait

down: ## Arrêt du service Docker FrankenPHP, MariaDB et autres services
	@$(COMPOSE) down --remove-orphans

compose: ## Exécute docker-compose avec ses arguments
	@$(COMPOSE) $(ARGS)

logs: ## Log des containers Docker avec le nom du container possible
	@$(COMPOSE) logs --tail=100 --follow --timestamps $(ARGS)

bash: ## Exécute un bash dans le container FrankenPHP
	@$(WEBAPP_DOCKER) bash



## ——— 🏗️  Projet ———————————————————————————————————————————————————————————————————————————————————

composer: ## Exécute une commande Composer
	@$(COMPOSER) $(ARGS)

check: ## Vérification du projet 🩺
	@echo
	@echo "\e[1;33mVérification du projet 🔍\e[0;0m"
	@echo "\e[1;33m=========================\e[0;0m"
	@echo
	@echo "  Nom du projet : \e[1;34m$(APP_NAME)\e[0;0m"
	@echo "  Version Symfony : \e[1;34m$(shell $(SYMFONY) --version | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')\e[0;0m"
	@echo "  Version PHP : \e[1;34m$(shell $(PHP) -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")\e[0;0m"
	@echo
	@$(SYMFONY) debug:dotenv

sf: ## Exécute une commande Symfony
	@$(SYMFONY) $(ARGS)

cc: ## Nettoyage des fichiers de cache
	@$(SYMFONY) cache:clear

schema: ## Vérification de la validité de la structure de la base de données 🩺
	@$(SYMFONY) doctrine:schema:validate -v

migrate: ## Mise à jour de la structure de la base de données
	@$(SYMFONY) doctrine:migrations:migrate



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
