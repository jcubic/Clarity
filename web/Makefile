.PHONY: vendor serve build clean ci migrate lint format check validate-variants

PORT ?= 8081

ICON_SOURCES := $(wildcard ../theme/src/*/*.svg)
TEMPLATES := $(wildcard ../theme/src/template_*.svg)

build: vendor icons validate-variants

ci: icons
	sed -i 's%web/icons%#web/icons%g' ../.gitignore
	cat .wasmerignore >> ../.gitignore
	cp favicon/favicon.ico .

migrate:
	pip install pymysql && python3 scripts/migrate.py

icons: icons/.build-stamp

icons.json: files.txt scripts/extract-icons.py $(ICON_SOURCES)
	python3 scripts/extract-icons.py

icons/.build-stamp: icons.json scripts/build-icons.py $(TEMPLATES)
	python3 scripts/build-icons.py
	@touch $@

vendor: vendor/autoload.php

vendor/autoload.php: composer.json composer.lock
	composer install --no-interaction
	@touch $@

serve: build
	php -S localhost:$(PORT) index.php

lint: vendor
	vendor/bin/phpstan analyse

format: vendor
	vendor/bin/php-cs-fixer fix

check: vendor validate-variants
	vendor/bin/php-cs-fixer fix --dry-run --diff
	vendor/bin/phpstan analyse

validate-variants:
	python3 scripts/validate-variants.py

clean:
	rm -f icons.json icons/.build-stamp .php-cs-fixer.cache
	rm -rf icons
