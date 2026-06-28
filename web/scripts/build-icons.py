#!/usr/bin/env python3

import json
import re
import sys
from pathlib import Path

base = Path(__file__).resolve().parent.parent
icons = json.loads((base / "icons.json").read_text())
template_dir = base.parent / "theme" / "src"
output_dir = base / "icons"

templates = sorted(template_dir.glob("template_*.svg"))
if not templates:
    print(f"error: no templates found in {template_dir}", file=sys.stderr)
    sys.exit(1)

STRIP = [
    (re.compile(r'<\?xml[^?]*\?>\s*'), ''),
    (re.compile(r'\s*xmlns:dc="[^"]*"'), ''),
    (re.compile(r'\s*xmlns:cc="[^"]*"'), ''),
    (re.compile(r'\s*xmlns:rdf="[^"]*"'), ''),
    (re.compile(r'\s*xmlns:svg="[^"]*"'), ''),
    (re.compile(r'\s*xmlns:xlink="[^"]*"'), ''),
    (re.compile(r'\s*version="[^"]*"'), ''),
    (re.compile(r'\s*<desc>[^<]*</desc>'), ''),
    (re.compile(r'\s*<metadata>.*?</metadata>', re.DOTALL), ''),
    (re.compile(r'\s*id="(?:start|stop)"'), ''),
    (re.compile(r';stop-opacity:1'), ''),
    (re.compile(r';fill-opacity:1;stroke:none'), ''),
    (re.compile(r'\n\s*\n'), '\n'),
]

count = 0

for tpl_path in templates:
    variant = tpl_path.stem.removeprefix("template_")
    raw = tpl_path.read_text()

    optimized = raw.replace("xlink:href", "href")
    for pattern, replacement in STRIP:
        optimized = pattern.sub(replacement, optimized)

    variant_dir = output_dir / variant
    variant_dir.mkdir(parents=True, exist_ok=True)

    for icon in icons:
        title = icon["name"].replace("-", " ").title()
        svg = optimized.replace("{{TITLE}}", title).replace("{{PATH}}", icon["d"])
        (variant_dir / f"{icon['name']}.svg").write_text(svg)
        count += 1

print(
    f"built {count} SVG files ({len(templates)} variants × {len(icons)} icons) in {output_dir}",
    file=sys.stderr,
)
