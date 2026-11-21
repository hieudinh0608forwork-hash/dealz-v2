#!/usr/bin/env python3
"""Fetch locally hosted WOFF2 subsets from the official Google Fonts CSS endpoint.

This is a development-only CI helper. The deployed WordPress theme references only
files committed under assets/fonts and never requests Google Fonts at runtime.
"""
import json
import os
import re
import urllib.request

CSS_URL = (
    "https://fonts.googleapis.com/css2?"
    "family=Bricolage+Grotesque:wght@700;800"
    "&family=Be+Vietnam+Pro:wght@400;600;700&display=swap"
)
UA = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120 Safari/537.36"
req = urllib.request.Request(CSS_URL, headers={"User-Agent": UA})
with urllib.request.urlopen(req, timeout=60) as response:
    css = response.read().decode("utf-8")

out_dir = os.path.join("assets", "fonts")
os.makedirs(out_dir, exist_ok=True)
faces = []
proof = []
for match in re.finditer(r"@font-face\s*\{([^}]+)\}", css, re.S):
    block = match.group(1)
    prefix = css[max(0, match.start() - 80):match.start()]
    subset_match = re.search(r"/\*\s*(vietnamese|latin-ext|latin)\s*\*/\s*$", prefix)
    if not subset_match:
        continue
    subset = subset_match.group(1)
    family_match = re.search(r"font-family:\s*'([^']+)'", block)
    weight_match = re.search(r"font-weight:\s*([^;]+)", block)
    src_match = re.search(r"src:\s*url\((https://[^)]+\.woff2)\)", block)
    unicode_match = re.search(r"unicode-range:\s*([^;]+);", block)
    if not (family_match and weight_match and src_match and unicode_match):
        continue
    family = family_match.group(1)
    if family not in ("Bricolage Grotesque", "Be Vietnam Pro"):
        continue
    weight = weight_match.group(1).strip()
    family_slug = "bricolage-grotesque" if family.startswith("Bricolage") else "be-vietnam-pro"
    weight_slug = weight.replace(" ", "").replace("..", "-")
    filename = f"{family_slug}-{weight_slug}-{subset}.woff2"
    path = os.path.join(out_dir, filename)
    font_req = urllib.request.Request(src_match.group(1), headers={"User-Agent": UA})
    with urllib.request.urlopen(font_req, timeout=60) as font_response:
        data = font_response.read()
    with open(path, "wb") as output:
        output.write(data)
    faces.append(
        "@font-face {\n"
        f"  font-family: '{family}';\n"
        "  font-style: normal;\n"
        f"  font-weight: {weight};\n"
        "  font-display: swap;\n"
        f"  src: url('./{filename}') format('woff2');\n"
        f"  unicode-range: {unicode_match.group(1)};\n"
        "}\n"
    )
    proof.append({"family": family, "weight": weight, "subset": subset, "file": filename, "bytes": len(data)})

if not proof:
    raise SystemExit("Google Fonts CSS did not contain the expected WOFF2 subsets.")
with open(os.path.join(out_dir, "fonts.css"), "w", encoding="utf-8") as output:
    output.write("/* Generated from the official Google Fonts CSS API; self-hosted at runtime. */\n")
    output.write("\n".join(faces))
total = sum(item["bytes"] for item in proof)
result = {"source": CSS_URL, "files": proof, "total_bytes": total, "total_kib": round(total / 1024, 2)}
with open(os.path.join(out_dir, "font-proof.json"), "w", encoding="utf-8") as output:
    json.dump(result, output, ensure_ascii=False, indent=2)
print(json.dumps(result, ensure_ascii=False, indent=2))
