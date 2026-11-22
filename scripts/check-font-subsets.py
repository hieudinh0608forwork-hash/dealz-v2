#!/usr/bin/env python3
"""Ensure removing latin-ext does not remove a character used by theme source."""
import json
from pathlib import Path

ROOT = Path("wp-content/themes/dealz-theme")
LATIN = [
    (0x0000, 0x00FF), (0x0131, 0x0131), (0x0152, 0x0153), (0x02BB, 0x02BC),
    (0x02C6, 0x02C6), (0x02DA, 0x02DA), (0x02DC, 0x02DC), (0x0304, 0x0304),
    (0x0308, 0x0308), (0x0329, 0x0329), (0x2000, 0x206F), (0x20AC, 0x20AC),
    (0x2122, 0x2122), (0x2191, 0x2193), (0x2212, 0x2212), (0x2215, 0x2215),
    (0xFEFF, 0xFEFF), (0xFFFD, 0xFFFD),
]
VIETNAMESE = [
    (0x0102, 0x0103), (0x0110, 0x0111), (0x0128, 0x0129), (0x0168, 0x0169),
    (0x01A0, 0x01A1), (0x01AF, 0x01B0), (0x0300, 0x0301), (0x0303, 0x0304),
    (0x0308, 0x0309), (0x0323, 0x0323), (0x0329, 0x0329), (0x1EA0, 0x1EF9),
    (0x20AB, 0x20AB),
]
LATIN_EXT = [
    (0x0100, 0x02BA), (0x02BD, 0x02C5), (0x02C7, 0x02CC), (0x02CE, 0x02D7),
    (0x02DD, 0x02FF), (0x0304, 0x0304), (0x0308, 0x0308), (0x0329, 0x0329),
    (0x1D00, 0x1DBF), (0x1E00, 0x1E9F), (0x1EF2, 0x1EFF), (0x2020, 0x2020),
    (0x20A0, 0x20AB), (0x20AD, 0x20C4), (0x2113, 0x2113), (0x2C60, 0x2C7F),
    (0xA720, 0xA7FF),
]

def inside(codepoint, ranges):
    return any(start <= codepoint <= end for start, end in ranges)

required = {}
scanned = []
for path in ROOT.rglob("*"):
    if not path.is_file() or path.suffix.lower() not in {".php", ".js", ".css", ".html"}:
        continue
    if path.name == "fonts.css" or "assets/fonts" in path.as_posix():
        continue
    scanned.append(path.as_posix())
    for char in path.read_text(encoding="utf-8"):
        cp = ord(char)
        if inside(cp, LATIN_EXT) and not inside(cp, LATIN) and not inside(cp, VIETNAMESE):
            required.setdefault(cp, set()).add(path.as_posix())

result = {
    "source_files_scanned": len(scanned),
    "latin_ext_only_required_codepoints": [
        {"codepoint": f"U+{cp:04X}", "character": chr(cp), "files": sorted(files)}
        for cp, files in sorted(required.items())
    ],
}
print(json.dumps(result, ensure_ascii=False, indent=2))
if required:
    raise SystemExit("A source character requires latin-ext; retain the subset.")
