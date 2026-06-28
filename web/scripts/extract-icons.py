#!/usr/bin/env python3

import json
import re
import sys
from pathlib import Path

base = Path(__file__).resolve().parent.parent
files_list = base / "files.txt"
theme_dir = base.parent / "theme"
output = base / "icons.json"

icons = []

for line in files_list.read_text().splitlines():
    line = line.strip()
    if not line or line.startswith("#"):
        continue

    path = theme_dir / line
    if not path.exists():
        print(f"warning: {line} not found, skipping", file=sys.stderr)
        continue

    svg = path.read_text()
    match = re.search(r'\bd="([^"]+)"', svg)
    if match:
        icons.append({
            "name": path.stem,
            "category": path.parent.name,
            "d": match.group(1),
        })
    else:
        print(f"warning: no path d= found in {line}", file=sys.stderr)

output.write_text(json.dumps(icons, indent=4, ensure_ascii=False) + "\n")
print(f"extracted {len(icons)} icons to {output}", file=sys.stderr)
