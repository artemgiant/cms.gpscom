.PHONY: help install up down restart build shell root-shell mysql logs clean fresh test

# Кольори для виводу
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)

# Допомога
help: ## Показати всі доступні команди
	@echo ''
	@echo '${GREEN}Використання:${RESET}'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<команда>${RESET}'
	@echo ''
	@echo '${GREEN}Команди:${RESET}'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${YELLOW}%-20s${GREEN}%s${RESET}\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo ''

# Установка проекту
install: ## Установка залежностей та початкове налаштування
	@echo "${GREEN}Установка проекту...${RESET}"
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
	@if [ ! -f .env ]; then cp .env.example .env; fi
	chmod +x ./vendor/bin/sail
	./vendor/bin/sail up -d
	./vendor/bin/sail artisan key:generate
	@echo "${GREEN}✓ Проект встановлено!${RESET}"

# Запуск та зупинка
up: ## Запустити Docker контейнери
	@echo "${GREEN}Запуск контейнерів...${RESET}"
	./vendor/bin/sail up -d
	@echo "${GREEN}✓ Контейнери запущено!${RESET}"
	@echo "${YELLOW}Сайт: http://localhost:8000${RESET}"
	@echo "${YELLOW}phpMyAdmin: http://localhost:8080${RESET}"

down: ## Зупинити Docker контейнери
	@echo "${YELLOW}Зупинка контейнерів...${RESET}"
	./vendor/bin/sail down
	@echo "${GREEN}✓ Контейнери зупинено!${RESET}"

restart: ## Перезапустити контейнери
	@echo "${YELLOW}Перезапуск контейнерів...${RESET}"
	./vendor/bin/sail restart
	@echo "${GREEN}✓ Контейнери перезапущено!${RESET}"

build: ## Перебудувати Docker образи
	@echo "${GREEN}Перебудова образів...${RESET}"
	./vendor/bin/sail build --no-cache
	@echo "${GREEN}✓ Образи перебудовано!${RESET}"

# Робота з контейнерами
shell: ## Зайти в контейнер Laravel (bash)
	./vendor/bin/sail shell

root-shell: ## Зайти в контейнер Laravel як root
	./vendor/bin/sail root-shell

mysql: ## Зайти в MySQL консоль
	./vendor/bin/sail mysql

mysql-root: ## Зайти в MySQL як root
	docker exec -it $$(docker ps --filter name=mysql --format "{{.Names}}") mysql -u root -ppassword

# База даних
migrate: ## Запустити міграції
	@echo "${GREEN}Запуск міграцій...${RESET}"
	./vendor/bin/sail artisan migrate
	@echo "${GREEN}✓ Міграції виконано!${RESET}"

migrate-fresh: ## Скинути БД та запустити міграції заново
	@echo "${YELLOW}⚠ Увага: Всі дані будуть видалено!${RESET}"
	@read -p "Продовжити? (y/n): " confirm && [ $$confirm = y ] || exit 1
	./vendor/bin/sail artisan migrate:fresh
	@echo "${GREEN}✓ База даних оновлена!${RESET}"

migrate-seed: ## Запустити міграції та seeders
	@echo "${GREEN}Запуск міграцій та seeders...${RESET}"
	./vendor/bin/sail artisan migrate --seed
	@echo "${GREEN}✓ Готово!${RESET}"

seed: ## Запустити seeders
	./vendor/bin/sail artisan db:seed

rollback: ## Відкотити останню міграцію
	./vendor/bin/sail artisan migrate:rollback


# Composer та NPM
composer-install: ## Встановити composer залежності
	./vendor/bin/sail composer install

composer-update: ## Оновити composer залежності
	./vendor/bin/sail composer update

npm-install: ## Встановити npm залежності
	./vendor/bin/sail npm install

npm-dev: ## Запустити npm dev (Vite)
	./vendor/bin/sail npm run dev

npm-build: ## Зібрати assets для продакшну
	./vendor/bin/sail npm run build

npm-watch: ## Запустити npm watch
	./vendor/bin/sail npm run watch

# Очищення
clean: ## Очистити кеш та логи
	@echo "${GREEN}Очищення кешу...${RESET}"
	./vendor/bin/sail artisan cache:clear
	./vendor/bin/sail artisan config:clear
	./vendor/bin/sail artisan route:clear
	./vendor/bin/sail artisan view:clear
	@echo "${GREEN}✓ Кеш очищено!${RESET}"

optimize: ## Оптимізувати Laravel (кешування)
	@echo "${GREEN}Оптимізація...${RESET}"
	./vendor/bin/sail artisan config:cache
	./vendor/bin/sail artisan route:cache
	./vendor/bin/sail artisan view:cache
	@echo "${GREEN}✓ Оптимізовано!${RESET}"

fresh: ## Повне оновлення проекту
	@echo "${GREEN}Повне оновлення проекту...${RESET}"
	./vendor/bin/sail down -v
	./vendor/bin/sail up -d
	./vendor/bin/sail composer install
	./vendor/bin/sail artisan key:generate
	./vendor/bin/sail artisan migrate:fresh --seed
	./vendor/bin/sail npm install
	./vendor/bin/sail npm run build
	@echo "${GREEN}✓ Проект оновлено!${RESET}"

# Artisan команди
tinker: ## Запустити tinker
	./vendor/bin/sail artisan tinker

queue: ## Запустити queue worker
	./vendor/bin/sail artisan queue:work

# Тестування
test: ## Запустити тести
	./vendor/bin/sail artisan test

test-coverage: ## Запустити тести з покриттям
	./vendor/bin/sail artisan test --coverage

pint: ## Виправити код style (Laravel Pint)
	./vendor/bin/sail pint

# Логи та моніторинг
logs: ## Показати логи всіх контейнерів
	./vendor/bin/sail logs

logs-app: ## Показати логи Laravel
	./vendor/bin/sail logs laravel.test

logs-mysql: ## Показати логи MySQL
	./vendor/bin/sail logs mysql

logs-follow: ## Слідкувати за логами в реальному часі
	./vendor/bin/sail logs -f

# Права доступу
permissions: ## Виправити права доступу
	@echo "${GREEN}Виправлення прав доступу...${RESET}"
	sudo chown -R $$USER:$$USER .
	chmod +x ./vendor/bin/sail
	@echo "${GREEN}✓ Права виправлено!${RESET}"

# Інше
ps: ## Показати запущені контейнери
	docker ps --filter name=$$(basename $$(pwd))

stats: ## Показати статистику контейнерів
	docker stats --no-stream

prune: ## Видалити невикористані Docker ресурси
	@echo "${YELLOW}⚠ Видалення невикористаних ресурсів...${RESET}"
	docker system prune -f
	@echo "${GREEN}✓ Готово!${RESET}"


# За замовчуванням показати допомогу
.DEFAULT_GOAL := help
