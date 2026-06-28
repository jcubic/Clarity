# Clarity Icon Theme — Makefile
#
# Copyright (c) 2010-2026 Jakub T. Jankiewicz
# Licensed under CC-BY-SA 4.0 license

SHELL := /bin/bash

VERSION := $(shell grep '^VERSION=' bin/clarity | head -1 | sed 's/VERSION="\(.*\)"/\1/')
NAME := clarity-icons

PREFIX := $(HOME)/.local
BIN_DIR := $(PREFIX)/bin
CLARITY_HOME := $(HOME)/.clarity-icons
BASH_COMP_DIR := $(HOME)/.local/share/bash-completion/completions
ZSH_COMP_DIR := $(HOME)/.local/share/zsh/site-functions

SYS_PREFIX := /usr
SYS_BIN_DIR := $(SYS_PREFIX)/bin
SYS_SHARE_DIR := $(SYS_PREFIX)/share
SYS_CLARITY_HOME := $(SYS_SHARE_DIR)/clarity-icons
SYS_BASH_COMP_DIR := $(SYS_SHARE_DIR)/bash-completion/completions
SYS_ZSH_COMP_DIR := $(SYS_SHARE_DIR)/zsh/site-functions

.PHONY: install uninstall tar.gz deb rpm clean

install:
	@echo "Installing Clarity CLI v$(VERSION)..."
	install -d $(BIN_DIR)
	install -m 755 bin/clarity $(BIN_DIR)/clarity
	install -d $(BASH_COMP_DIR)
	install -m 644 bin/clarity-completion.bash $(BASH_COMP_DIR)/clarity
	install -d $(ZSH_COMP_DIR)
	install -m 644 bin/_clarity $(ZSH_COMP_DIR)/_clarity
	install -d $(CLARITY_HOME)
	rm -rf $(CLARITY_HOME)/src $(CLARITY_HOME)/static
	cp -a src $(CLARITY_HOME)/src
	test -d static && cp -a static $(CLARITY_HOME)/static || true
	cp index.theme $(CLARITY_HOME)/index.theme
	echo "$(VERSION)" > $(CLARITY_HOME)/.version
	@echo ""
	@echo "Installed. Run 'clarity use canus' to build the default theme."

uninstall:
	@echo "Removing Clarity CLI..."
	rm -f $(BIN_DIR)/clarity
	rm -f $(BASH_COMP_DIR)/clarity
	rm -f $(ZSH_COMP_DIR)/_clarity
	rm -rf $(CLARITY_HOME)
	@echo "Done. You may also want to remove ~/.local/share/icons/Clarity/"

tar.gz:
	$(eval DIST_DIR := $(NAME)-$(VERSION))
	rm -rf $(DIST_DIR)
	mkdir -p $(DIST_DIR)/bin $(DIST_DIR)/src $(DIST_DIR)/static
	cp bin/clarity $(DIST_DIR)/bin/
	cp bin/clarity-completion.bash $(DIST_DIR)/bin/
	cp bin/_clarity $(DIST_DIR)/bin/
	cp -a src/. $(DIST_DIR)/src/
	test -d static && cp -a static/. $(DIST_DIR)/static/ || true
	cp index.theme $(DIST_DIR)/
	cp Makefile $(DIST_DIR)/
	cp README.md $(DIST_DIR)/ 2>/dev/null || true
	cp CHANGELOG.md $(DIST_DIR)/ 2>/dev/null || true
	tar czf $(DIST_DIR).tar.gz $(DIST_DIR)
	rm -rf $(DIST_DIR)
	@echo "Created $(DIST_DIR).tar.gz"

deb:
	$(eval DEB_DIR := $(NAME)_$(VERSION)_all)
	rm -rf $(DEB_DIR)
	mkdir -p $(DEB_DIR)/DEBIAN
	mkdir -p $(DEB_DIR)/usr/bin
	mkdir -p $(DEB_DIR)/usr/share/clarity-icons/src
	mkdir -p $(DEB_DIR)/usr/share/bash-completion/completions
	mkdir -p $(DEB_DIR)/usr/share/zsh/site-functions
	install -m 755 bin/clarity $(DEB_DIR)/usr/bin/clarity
	cp -a src/. $(DEB_DIR)/usr/share/clarity-icons/src/
	test -d static && cp -a static $(DEB_DIR)/usr/share/clarity-icons/static || true
	cp index.theme $(DEB_DIR)/usr/share/clarity-icons/index.theme
	install -m 644 bin/clarity-completion.bash $(DEB_DIR)/usr/share/bash-completion/completions/clarity
	install -m 644 bin/_clarity $(DEB_DIR)/usr/share/zsh/site-functions/_clarity
	printf 'Package: $(NAME)\nVersion: $(VERSION)\nArchitecture: all\nMaintainer: Jakub T. Jankiewicz <jcubic@onet.pl>\nDepends: bash, coreutils, findutils, sed, grep, gawk, curl\nRecommends: librsvg2-bin\nSection: gnome\nPriority: optional\nHomepage: https://clarity.pl.eu.org\nDescription: Clarity vector icon theme for GTK\n Clean monoshape icons with CLI manager for switching variants,\n installing community themes, and building icons.\n' > $(DEB_DIR)/DEBIAN/control
	dpkg-deb --build $(DEB_DIR)
	rm -rf $(DEB_DIR)
	@echo "Created $(DEB_DIR).deb"

rpm:
	$(eval RPM_TOPDIR := $(CURDIR)/rpmbuild)
	rm -rf $(RPM_TOPDIR)
	mkdir -p $(RPM_TOPDIR)/{BUILD,RPMS,SOURCES,SPECS}
	cp clarity-icons.spec $(RPM_TOPDIR)/SPECS/
	rpmbuild -bb --define "_topdir $(RPM_TOPDIR)" \
		--define "_version $(VERSION)" \
		--define "_srcdir $(CURDIR)" \
		--buildroot "$(RPM_TOPDIR)/BUILDROOT" \
		$(RPM_TOPDIR)/SPECS/clarity-icons.spec
	cp $(RPM_TOPDIR)/RPMS/noarch/$(NAME)-$(VERSION)-*.noarch.rpm . 2>/dev/null || true
	rm -rf $(RPM_TOPDIR)
	@echo "Created $(NAME)-$(VERSION)-1.noarch.rpm"

clean:
	rm -rf $(NAME)-*.tar.gz $(NAME)_*.deb $(NAME)-*.rpm rpmbuild
	rm -rf scalable 16x16
