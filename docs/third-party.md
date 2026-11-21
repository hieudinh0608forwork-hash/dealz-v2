# Third-party assets

## Bricolage Grotesque
- Source: official Google Fonts CSS API, fetched once in GitHub Actions run 44956887983.
- Source URL: https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Be+Vietnam+Pro:wght@400;600;700&display=swap
- Weights: 700 and 800; WOFF2 subsets: latin, latin-ext, vietnamese.
- License: SIL Open Font License 1.1; see wp-content/themes/dealz-theme/assets/fonts/OFL-Bricolage-Grotesque.txt.

## Be Vietnam Pro
- Source: official Google Fonts CSS API, fetched once in GitHub Actions run 44956887983.
- Weights: 400, 600 and 700; WOFF2 subsets: latin, latin-ext, vietnamese.
- License: SIL Open Font License 1.1; see wp-content/themes/dealz-theme/assets/fonts/OFL-Be-Vietnam-Pro.txt.
- FontTools inspection: downloaded WOFF2 subset files do not expose the OpenType tnum feature. Keep the requested font family; do not silently substitute another font. This is a known typography limitation pending owner decision.

## Size and runtime behavior
- Total WOFF2 payload: 279,284 bytes (272.74 KiB), above the approximate 220 KiB target.
- Generated fonts.css references only local files. The deployed theme must not request Google Fonts or any other external font origin.
- font-proof.json records source, exact files, byte sizes and tnum inspection result.
