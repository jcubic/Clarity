.PHONY: vendor serve build clean

PORT ?= 8081

ICON_SOURCES := $(wildcard theme/src/*/*.svg)
TEMPLATES := $(wildcard theme/src/template_*.svg)

build: vendor icons

icons: web/icons/.build-stamp

web/icons.json: web/files.txt web/scripts/extract-icons.py $(ICON_SOURCES)
	python3 web/scripts/extract-icons.py

web/icons/.build-stamp: web/icons.json web/scripts/build-icons.py $(TEMPLATES)
	python3 web/scripts/build-icons.py
	@touch $@

vendor: web/vendor/autoload.php

web/vendor/autoload.php: web/composer.json web/composer.lock
	cd web && composer install --no-interaction
	@touch $@

serve: build
	cd web && php -S localhost:$(PORT) index.php

clean:
	rm -f web/icons.json web/icons/.build-stamp
	rm -rf web/icons
