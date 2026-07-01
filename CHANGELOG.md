# Changelog

All notable changes to the Clarity Icon Theme are documented in this file.

## 1.0.0-beta.3 (TBA)

### Icons
* fix up-arrow icon in thunar
* new icons (Thunar):  open-menu, view-grid, image-red-eye, view-compact, view-dual, view-list
* fix rendering symbolic icons in Thunar's toolbar
* add missing flat templates to the `clarity` commands

## 1.0.0-beta.2 (2026-07-01)

### CLI
* fix `clarity update` when installed system-wide (deb/rpm) — skip CLI overwrite, warn to use package manager
* add update notification on regular commands (checks GitHub releases once per 24h, source/tarball installs only)
* show actual file paths in `clarity --help` FILES section (binary, completions, sources)
* display paths with `~/` instead of `/home/<user>/`

### Website
* add download template endpoint — reverses conversion so community themes can be re-uploaded
* add edit metadata icon next to theme description

## 1.0.0-beta (2026-06-29)

### CLI — `clarity` theme manager
* new `clarity` CLI replacing the old `configure`/`make`/`change-theme` workflow
* `clarity use <variant>` — switch between built-in color variants
* `clarity install @user/theme` — download community themes from the gallery
* `clarity remove @user/theme` — uninstall community themes
* `clarity create <file.svg>` — create custom local themes from SVG templates
* `clarity update` — update CLI, base theme, and installed community themes
* `clarity uninstall` — clean removal of all Clarity files
* `clarity list` — show available variants with version info for community themes
* parallel icon building with progress indicator (uses all CPU cores)
* 16x16 PNG generation via `rsvg-convert`
* bash and zsh shell completions
* community theme version tracking via `.meta` files
* symlink guard to protect development installs during `clarity update`

### Website — clarity.pl.eu.org
* new PHP website (Slim 4 + Twig 3) deployed on Wasmer Edge
* landing page with hero, install section, variant browser, gallery, and theme creation guide
* variant browser with tabbed icon previews and oklch color system
* community theme gallery with cover icon previews
* theme detail pages with icon preview, install instructions, and terminal widget
* 3-step upload wizard with server-side SVG validation (8 checks)
* magic link email authentication (Resend API) with JWT session cookies
* theme editing — re-upload SVG, edit metadata (description, dark/light mode)
* theme deletion by owner
* likes, view counts, and download counts
* icon rendering API with ETag caching (`/api/icon/{user}/{theme}/{icon}`)
* theme download API (`/api/theme/{user}/{name}`)
* lightweight version endpoint (`/api/theme/{user}/{name}/version`)
* curl-pipe installer (`curl -sL https://clarity.pl.eu.org/install | bash`)
* HTML minification in production
* cache-busting with `?v=<crc32>` on static assets
* real icon statistics from build-time `icons-stats.json`
* Docker Compose dev environment with MySQL
* database migrations via Python script in CI

### Build system
* `make install` / `make uninstall` for source installs to `~/.local/`
* `make deb` — build `.deb` package with `dpkg-deb`
* `make rpm` — build `.rpm` package with `rpmbuild`
* `make tar.gz` — build distributable tarball
* Dockerfile for local package build testing
* GitHub Actions CI/CD — lint (php-cs-fixer, phpstan), validate variants, deploy to Wasmer Edge
* GitHub Actions release workflow — build and attach packages on version tags
* variant validation (`validate-variants.py`) ensuring `variants.json` matches template SVGs

### Icons
* fix microphone icon alignment

## 0.9.0 (2026-05-30)
* Add Helium browser icon
* Add Diablo icon

## 0.8.0 (2025-08-27)
* Missing icons and aliases for xfce new naming scheme
* liferea and obs symlinks
* Peek Icon

## 0.7.0 (2025-03-02)
* Add Android and scrcpy
* Add file for: ICC Profile, Snap package file, Windows/Wine ini file
* Add symlinks: Windows/Wine shortcut, X.509 Certificate, empty file, generic file without a dedicated icon

## 0.6.0 (2024-03-08)
* Add Brave, OBS, and Android Studio icons

## 0.5.2 (2022-11-01)
* Add new symlink for switch user and new icon to system lock in XFce

## 0.5.1 (2018-12-09)
* Fix big icons in Evolution on Debian based distros

## 0.5.0 (2018-12-07)
* News icon
* xfce4-pulseaudio-plugin
* application-x-wine-extension-aspx
* winefile
* software-update-available
* software-update-urgent
* ibus-engine
* blueberry compound icons
* gnome-disks
* Fix compound icons (move bottom icons on the left)
* Headset Icon
* Pen Drive + Card Icon
* Headphones Icon
* VPN Network Icon
* E-Book Reader Icon
* audio-speakers symlink
* drive-multidisk Icon
* Anaconda Icon
* telegram icon
* SELinux issue icon
* VLC-Xmas
* dnf-dragora symlink
* M$ application-x-ms-application
* blivet icon
* Parole icon
* Symlinks yumex-dnf, fedora-logo-icon, text-x-component, gnome-color-manager, preferences-system-time

## 0.4.7 (2018-02-04)
* Blueberry icons

## 0.4.2
* JSON-File icon, System Reboot, Gstreamer Editing Services project file icon

## 0.4.1
* Fix large icons

## 0.4.0
* Nikon NEF, Wordpress, Freemind, pithos Preferences, XFce panel menu
* Application Engineering Ufraw, new embeds
* Themable System Upgrade Gnome Panel, notification area, window list, window menu
* Gnumeric wireshark, pdfedit jdownloader, glade mimetype
* Change monodevelop icon
* Symlink to cups, pdfmod

## 0.3.2
* Add changeable wireless loading and missing symlinks
* Fix license in README

## 0.3.1
* Add icons for Libre Office, Flash Player, changeable Wireless
* Fix build scripts
* Add 16x16 pixel icons

## 0.2.10
* Add 4 icons, symlinks gzip version of 9 mimetypes
* New theme dark canus
* Remove shadows
* Fix bugs

## 0.2.9
* Add 8 new mimetypes, Dropbox icon and status icons

## 0.2.8
* Deb fixes

## 0.2.7
* Add 25 new icons for mimetypes, and 4 new applications icons and some fixes

## 0.2.6
* Add status indicators for vpn connection, laptop battery, ups, keyboard, mouse and 97 new icons

## 0.2.5
* Add 76 new icons and missing symlinks

## 0.2.4.1 - 0.2.4.3
* Some fixes and deb packages related

## 0.2.4
* Add symlink virtualbox-bin and thunderbird-icon
* Add icons for Eclipse, qjackctl, kvirc, Calibre and KeePassX

## 0.2.3
* Add icon for caffeine
* Add rules for make to change distributor logos
* Add icons for Tuxtremsplit, Kmajhongg, Kshisen, Yahtzeesharp and Meld
* Add missing symlink to applications-utilities

## 0.2.2
* Add icons for ailurus, avant-window-navigator, dockbarx, easytag, filezilla, geany, k3b, palimpset, shutter, skype, nautilus, qt, keyboard shortcuts, monitor preferences and lottanzb
* Add new themes lux_caeruleus, lux_violaceus
* Add change-theme script with GUI to change color of icons
* Add icons for xfburn

## 0.2.1
* Add symlink to gz and chm files, other subtitles files and add icon for changelog
* Add icons for subtitles, Corel Draw, EPS, WMF
* Change AI, folder windows, folder Internet, folder html, folder user
* Fix missing icons that stay the same across different color schemes
* Update configure script to check if building rules are valid
* Fix Fedora issue (Fedora doesn't display svg files that have specific comments between xml prolog and svg root tag)
