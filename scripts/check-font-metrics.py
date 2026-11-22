#!/usr/bin/env python3
"""Measure Be Vietnam Pro digit advance widths from the committed WOFF2 file."""
import json
from pathlib import Path
from fontTools.ttLib import TTFont

FONT_PATH = Path("wp-content/themes/dealz-theme/assets/fonts/be-vietnam-pro-400-latin.woff2")
font = TTFont(FONT_PATH)
cmap = {}
for table in font["cmap"].tables:
    if table.isUnicode():
        cmap.update(table.cmap)

widths = {}
for digit in "0123456789":
    glyph_name = cmap.get(ord(digit))
    if glyph_name is None:
        raise SystemExit(f"Missing digit glyph in Be Vietnam Pro WOFF2: {digit}")
    widths[digit] = int(font["hmtx"].metrics[glyph_name][0])

result = {
    "font": str(FONT_PATH),
    "units_per_em": int(font["head"].unitsPerEm),
    "advance_widths": widths,
    "unique_advance_widths": sorted(set(widths.values())),
    "tabular_by_advance_width": len(set(widths.values())) == 1,
}
print(json.dumps(result, ensure_ascii=False, indent=2))
