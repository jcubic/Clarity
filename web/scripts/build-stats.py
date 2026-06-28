#!/usr/bin/env python3

import json
from pathlib import Path

base = Path(__file__).resolve().parent.parent
theme_dir = base.parent / "theme"
src_dir = theme_dir / "src"

icon_dirs = [
    "actions", "apps", "categories", "devices", "distributor-logos",
    "emblems", "mimetypes", "places", "status", "stock",
]

names = set()
for d in icon_dirs:
    p = src_dir / d
    if p.is_dir():
        for svg in p.glob("*.svg"):
            names.add(svg.stem)

compound_rules = src_dir / "compound_icons_rules"
if compound_rules.exists():
    for line in compound_rules.read_text().splitlines():
        line = line.strip()
        if line and not line.startswith("#"):
            dest = line.split("=")[0].strip()
            names.add(Path(dest).stem)

static_dir = theme_dir / "static"
if static_dir.is_dir():
    for svg in static_dir.rglob("*.svg"):
        names.add(svg.stem)

copies_file = src_dir / "copies"
if copies_file.exists():
    for line in copies_file.read_text().splitlines():
        line = line.strip()
        if line and not line.startswith("#"):
            parts = line.split()
            if len(parts) >= 2:
                names.add(Path(parts[1]).stem)

alias_count = 0
symlinks_file = src_dir / "symlinks"
if symlinks_file.exists():
    for line in symlinks_file.read_text().splitlines():
        line = line.strip()
        if line and not line.startswith("#"):
            alias_count += 1

variants_file = base / "variants.json"
variants = json.loads(variants_file.read_text())
variant_count = len(variants)

unique = len(names)
stats = {
    "unique_icons": unique,
    "with_symlinks": unique + alias_count,
    "variants": variant_count,
}

output = base / "icons-stats.json"
output.write_text(json.dumps(stats, indent=2) + "\n")
print(
    f"stats: {unique} unique icons, {unique + alias_count} with aliases, "
    f"{variant_count} variants → {output}",
    file=__import__("sys").stderr,
)
