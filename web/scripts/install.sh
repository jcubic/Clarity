#!/usr/bin/env bash
# Clarity CLI installer — v{{VERSION}}
# https://clarity.pl.eu.org
#
# Usage: curl -sL https://clarity.pl.eu.org/install | bash
#
# Copyright (c) 2010-2026 Jakub T. Jankiewicz
# Released under CC-BY-SA 4.0 license

{

set -euo pipefail

CLARITY_VERSION="{{VERSION}}"
REPO="jcubic/Clarity"
API_BASE="{{API_BASE}}"
CLI_URL="https://raw.githubusercontent.com/${REPO}/wasmer/theme/bin/clarity"
ARCHIVE_URL="https://github.com/${REPO}/archive/refs/heads/wasmer.tar.gz"
CLARITY_HOME="${HOME}/.clarity-icons"
BIN_DIR="${HOME}/.local/bin"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
DIM='\033[2m'
RESET='\033[0m'

info()  { echo -e "${GREEN}==>${RESET} ${BOLD}$1${RESET}"; }
warn()  { echo -e "${YELLOW}==> WARNING:${RESET} ${BOLD}$1${RESET}"; }
error() { echo -e "${RED}==> ERROR:${RESET} ${BOLD}$1${RESET}" >&2; }

echo ""
echo -e "${BOLD}  Clarity Icon Theme — CLI Installer${RESET}"
echo -e "  ${DIM}v${CLARITY_VERSION}${RESET}"
echo ""

# Check required tools
missing=0
for cmd in curl tar sed grep find bash; do
    if ! command -v "$cmd" > /dev/null 2>&1; then
        error "'$cmd' is required but not found"
        missing=1
    fi
done
[[ "$missing" -eq 1 ]] && exit 1

# Check optional dependencies
if ! command -v rsvg-convert > /dev/null 2>&1; then
    warn "rsvg-convert not found — 16x16 PNG icons will not be generated"
    echo -e "  ${DIM}Install: sudo dnf install librsvg2-tools  (Fedora)${RESET}"
    echo -e "  ${DIM}         sudo apt install librsvg2-bin    (Debian/Ubuntu)${RESET}"
    echo ""
fi

# Download CLI script
info "Downloading clarity CLI..."
mkdir -p "$BIN_DIR"
curl -sL "$CLI_URL" -o "$BIN_DIR/clarity" || {
    error "Failed to download CLI from $CLI_URL"
    exit 1
}
chmod +x "$BIN_DIR/clarity"

# Download base theme
info "Downloading base icon theme..."
tmpdir=$(mktemp -d)
trap 'rm -rf "$tmpdir"' EXIT

curl -sL "$ARCHIVE_URL" -o "$tmpdir/clarity.tar.gz" || {
    error "Failed to download theme archive"
    exit 1
}

info "Extracting source files..."
tar xzf "$tmpdir/clarity.tar.gz" -C "$tmpdir"

extracted=$(find "$tmpdir" -maxdepth 1 -mindepth 1 -type d ! -name ".*" | head -1)
if [[ -z "$extracted" || ! -d "$extracted/theme/src" ]]; then
    error "Unexpected archive structure"
    exit 1
fi

mkdir -p "$CLARITY_HOME"
cp -a "$extracted/theme/src" "$CLARITY_HOME/src"
[[ -d "$extracted/theme/static" ]] && cp -a "$extracted/theme/static" "$CLARITY_HOME/static"
cp "$extracted/theme/index.theme" "$CLARITY_HOME/index.theme"

echo "$CLARITY_VERSION" > "$CLARITY_HOME/.version"

info "Clarity CLI v${CLARITY_VERSION} installed successfully!"

# Check PATH
if ! echo "$PATH" | tr ':' '\n' | grep -qx "$BIN_DIR"; then
    echo ""
    warn "$BIN_DIR is not in your \$PATH"
    echo -e "  ${DIM}Add this to your shell profile (~/.bashrc or ~/.zshrc):${RESET}"
    echo ""
    echo -e "    ${CYAN}export PATH=\"\$HOME/.local/bin:\$PATH\"${RESET}"
    echo ""
    echo -e "  ${DIM}Then restart your shell or run: source ~/.bashrc${RESET}"
fi

echo ""
echo -e "  ${BOLD}Quick start:${RESET}"
echo -e "    ${CYAN}clarity use caeruleus${RESET}          ${DIM}# switch to blue variant${RESET}"
echo -e "    ${CYAN}clarity list${RESET}                   ${DIM}# show available variants${RESET}"
echo -e "    ${CYAN}clarity --help${RESET}                 ${DIM}# show all commands${RESET}"
echo ""

}
