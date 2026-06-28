#!/usr/bin/env python3

import json
import re
from pathlib import Path

base = Path(__file__).resolve().parent.parent
theme_dir = base.parent / "theme"
src_dir = theme_dir / "src"

icon_dirs = [
    "actions", "apps", "categories", "devices", "distributor-logos",
    "emblems", "fonts", "mimetypes", "places", "status", "stock",
]

unique = 0
for d in icon_dirs:
    p = src_dir / d
    if p.is_dir():
        unique += len(list(p.glob("*.svg")))

compound_rules = src_dir / "compound_icons_rules"
if compound_rules.exists():
    for line in compound_rules.read_text().splitlines():
        line = line.strip()
        if line and not line.startswith("#"):
            unique += 1

static_dir = theme_dir / "static"
if static_dir.is_dir():
    unique += len(list(static_dir.rglob("*.svg")))

copies_file = src_dir / "copies"
if copies_file.exists():
    for line in copies_file.read_text().splitlines():
        line = line.strip()
        if line and not line.startswith("#"):
            unique += 1

symlinks_file = src_dir / "symlinks"
symlink_count = 0
if symlinks_file.exists():
    for line in symlinks_file.read_text().splitlines():
        line = line.strip()
        if line and not line.startswith("#"):
            symlink_count += 1

variants_file = base / "variants.json"
variants = json.loads(variants_file.read_text())
variant_count = len(variants)

stats = {
    "unique_icons": unique,
    "with_symlinks": unique + symlink_count,
    "variants": variant_count,
}

output = base / "icons-stats.json"
output.write_text(json.dumps(stats, indent=2) + "\n")
print(
    f"stats: {unique} unique icons, {unique + symlink_count} with symlinks, "
    f"{variant_count} variants → {output}",
    file=__import__("sys").stderr,
)
