#!/usr/bin/env python3

import json
import sys
from pathlib import Path

base = Path(__file__).resolve().parent.parent
template_dir = base.parent / "theme" / "src"

variants_file = base / "variants.json"
if not variants_file.exists():
    print("error: variants.json not found", file=sys.stderr)
    sys.exit(1)

variants = json.loads(variants_file.read_text())
variant_ids = {v["id"] for v in variants}

templates = sorted(template_dir.glob("template_*.svg"))
template_ids = {p.stem.removeprefix("template_") for p in templates}

missing_in_json = template_ids - variant_ids
missing_templates = variant_ids - template_ids

errors = []

if missing_in_json:
    for name in sorted(missing_in_json):
        errors.append(f"template_{name}.svg exists but '{name}' is missing from variants.json")

if missing_templates:
    for name in sorted(missing_templates):
        errors.append(f"'{name}' is in variants.json but template_{name}.svg does not exist")

for v in variants:
    parent = v.get("parent")
    if parent and parent not in variant_ids:
        errors.append(f"'{v['id']}' references parent '{parent}' which is not in variants.json")
    if parent and "toggle" not in v:
        errors.append(f"'{v['id']}' has a parent but is missing the 'toggle' field")

if errors:
    for e in errors:
        print(f"error: {e}", file=sys.stderr)
    sys.exit(1)

print(f"variants.json OK — {len(variant_ids)} variants, {len(template_ids)} templates", file=sys.stderr)
