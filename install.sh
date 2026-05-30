#!/bin/bash
# Clarity Icon Theme - Install/Update/Uninstall Script
# https://github.com/jcubic/Clarity
#
# Usage:
#   Install/Update: curl -sL https://raw.githubusercontent.com/jcubic/Clarity/master/install.sh | bash
#   Uninstall:      curl -sL https://raw.githubusercontent.com/jcubic/Clarity/master/install.sh | bash -s -- --uninstall
#
# Copyright (c) 2010-2026 Jakub T. Jankiewicz
# Released under CC-BY-SA 4.0 license

{

set -euo pipefail

REPO="jcubic/Clarity"
THEME_NAME="Clarity"
INSTALL_DIR="${XDG_DATA_HOME:-$HOME/.local/share}/icons/${THEME_NAME}"
VERSION_FILE="$INSTALL_DIR/.clarity-version"
GITHUB_API="https://api.github.com/repos/$REPO/releases/latest"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BOLD='\033[1m'
RESET='\033[0m'

info()  { echo -e "${GREEN}==>${RESET} ${BOLD}$1${RESET}"; }
warn()  { echo -e "${YELLOW}==> WARNING:${RESET} ${BOLD}$1${RESET}"; }
error() { echo -e "${RED}==> ERROR:${RESET} ${BOLD}$1${RESET}" >&2; }

show_help() {
    cat <<'HELP'
Clarity Icon Theme Installer

Usage:
  bash install.sh [OPTIONS]
  curl -sL https://raw.githubusercontent.com/jcubic/Clarity/master/install.sh | bash -s -- [OPTIONS]

Options:
  --help, -h       Show this help message
  --uninstall      Remove the Clarity icon theme
  --check          Check if an update is available (don't install)
  --force          Force reinstall even if already up to date
  --dir PATH       Install to a custom directory (default: ~/.local/share/icons/Clarity)

HELP
}

get_installed_version() {
    if [ -f "$VERSION_FILE" ]; then
        cat "$VERSION_FILE"
    else
        echo ""
    fi
}

fetch_release_info() {
    local response
    response=$(curl -sL -w "\n%{http_code}" "$GITHUB_API") || {
        error "Failed to connect to GitHub API"
        exit 1
    }

    local http_code
    http_code=$(echo "$response" | tail -1)
    local body
    body=$(echo "$response" | sed '$d')

    if [ "$http_code" != "200" ]; then
        error "GitHub API returned HTTP $http_code. Check https://github.com/$REPO/releases"
        exit 1
    fi

    echo "$body"
}

parse_version() {
    echo "$1" | grep -o '"tag_name": *"[^"]*"' | head -1 | sed 's/.*"\([^"]*\)"$/\1/'
}

parse_asset_url() {
    echo "$1" | grep -o '"browser_download_url": *"[^"]*\.tar\.gz"' | head -1 | sed 's/.*"\(https:[^"]*\)"$/\1/'
}

do_check() {
    info "Checking for updates..."

    local installed
    installed=$(get_installed_version)

    local release_info
    release_info=$(fetch_release_info)
    local latest
    latest=$(parse_version "$release_info")

    if [ -z "$latest" ]; then
        error "Could not determine latest version"
        exit 1
    fi

    if [ -z "$installed" ]; then
        info "Clarity is not installed. Latest version: $latest"
    elif [ "$installed" = "$latest" ]; then
        info "Clarity $installed is up to date"
    else
        info "Update available: $installed -> $latest"
        echo "  Run the install command again to update."
    fi
}

do_uninstall() {
    if [ ! -d "$INSTALL_DIR" ]; then
        warn "Clarity is not installed at $INSTALL_DIR"
        exit 0
    fi

    info "Uninstalling Clarity from $INSTALL_DIR..."
    rm -rf "$INSTALL_DIR"
    info "Clarity icon theme has been removed"
    echo ""
    echo "  You may need to change your icon theme in your desktop settings."
}

do_install() {
    local force="$1"

    for cmd in curl tar; do
        if ! command -v "$cmd" > /dev/null 2>&1; then
            error "'$cmd' is required but not found"
            exit 1
        fi
    done

    info "Fetching latest release info..."
    local release_info
    release_info=$(fetch_release_info)

    local latest
    latest=$(parse_version "$release_info")
    if [ -z "$latest" ]; then
        error "Could not determine latest version"
        exit 1
    fi

    local installed
    installed=$(get_installed_version)
    if [ "$force" = false ] && [ "$installed" = "$latest" ]; then
        info "Clarity $installed is already up to date (use --force to reinstall)"
        return 0
    fi

    local asset_url
    asset_url=$(parse_asset_url "$release_info")
    if [ -z "$asset_url" ]; then
        error "No tarball found in release $latest"
        error "Please report this at https://github.com/$REPO/issues"
        exit 1
    fi

    if [ -n "$installed" ]; then
        info "Updating Clarity: $installed -> $latest"
    else
        info "Installing Clarity $latest"
    fi

    local tmpdir
    tmpdir=$(mktemp -d)
    trap 'rm -rf "$tmpdir"' EXIT

    info "Downloading Clarity $latest..."
    curl -sL "$asset_url" -o "$tmpdir/clarity.tar.gz" || {
        error "Download failed"
        exit 1
    }

    info "Extracting..."
    mkdir -p "$INSTALL_DIR"

    if [ -d "$INSTALL_DIR" ] && [ -n "$installed" ]; then
        rm -rf "$INSTALL_DIR"
        mkdir -p "$INSTALL_DIR"
    fi

    tar xzf "$tmpdir/clarity.tar.gz" -C "$tmpdir"

    local extracted_dir
    extracted_dir=$(find "$tmpdir" -maxdepth 1 -mindepth 1 -type d ! -name ".*" | head -1)

    if [ -z "$extracted_dir" ]; then
        error "Unexpected tarball structure"
        exit 1
    fi

    cp -a "$extracted_dir"/. "$INSTALL_DIR"/

    echo "$latest" > "$VERSION_FILE"

    if command -v gtk-update-icon-cache > /dev/null 2>&1; then
        info "Updating icon cache..."
        gtk-update-icon-cache -f "$INSTALL_DIR" 2>/dev/null || true
    fi

    info "Clarity $latest installed to $INSTALL_DIR"
    echo ""
    echo "  To apply, select 'Clarity' in your desktop's appearance settings."
    echo "  To change the color theme, run: cd $INSTALL_DIR && make <theme>"
    echo "  Available themes: canus (default), violaceus, caeruleus, viridis, luteus,"
    echo "                    dark_canus, albus, lux_violaceus, lux_caeruleus"
    echo ""
    echo "  To update in the future, re-run this command."
    echo "  To uninstall: curl -sL https://raw.githubusercontent.com/jcubic/Clarity/master/install.sh | bash -s -- --uninstall"
}

main() {
    local action="install"
    local force=false

    for arg in "$@"; do
        case "$arg" in
            --uninstall)   action="uninstall" ;;
            --check)       action="check" ;;
            --force)       force=true ;;
            --help|-h)     action="help" ;;
            --dir)         ;; # handled below
            *)
                if [ "${prev_arg:-}" = "--dir" ]; then
                    INSTALL_DIR="$arg"
                    VERSION_FILE="$INSTALL_DIR/.clarity-version"
                fi
                ;;
        esac
        prev_arg="$arg"
    done

    case "$action" in
        help)      show_help ;;
        uninstall) do_uninstall ;;
        check)     do_check ;;
        install)   do_install "$force" ;;
    esac
}

main "$@"

}
