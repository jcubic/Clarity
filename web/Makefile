.PHONY: vendor serve build clean

PORT ?= 8081

ICON_SOURCES := $(wildcard ../theme/src/*/*.svg)
TEMPLATES := $(wildcard ../theme/src/template_*.svg)

build: vendor icons

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

clean:
	rm -f icons.json icons/.build-stamp
	rm -rf icons
