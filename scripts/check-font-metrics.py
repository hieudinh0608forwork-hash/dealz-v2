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
proof_path = FONT_PATH.parent / "font-proof.json"
proof = json.loads(proof_path.read_text(encoding="utf-8"))
if proof.get("be_vietnam_pro_400_digit_advance_widths") != widths:
    raise SystemExit("Committed font-proof.json digit widths do not match the WOFF2 metrics.")
if proof.get("be_vietnam_pro_400_units_per_em") != result["units_per_em"]:
    raise SystemExit("Committed font-proof.json units-per-em do not match the WOFF2 font.")
if proof.get("be_vietnam_pro_400_tabular_by_advance_width") != result["tabular_by_advance_width"]:
    raise SystemExit("Committed font-proof.json tabular status does not match the WOFF2 metrics.")
print(json.dumps(result, ensure_ascii=False, indent=2))
