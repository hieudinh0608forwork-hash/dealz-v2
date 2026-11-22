# Third-party assets

## Bricolage Grotesque
- Source: official Google Fonts CSS API, fetched once in GitHub Actions run 44956887983.
- Source URL: https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Be+Vietnam+Pro:wght@400;600;700&display=swap
- Weights: 700 and 800; WOFF2 subsets: latin and vietnamese (latin-ext removed after source-code coverage scan).
- License: SIL Open Font License 1.1; see wp-content/themes/dealz-theme/assets/fonts/OFL-Bricolage-Grotesque.txt.

## Be Vietnam Pro
- Source: official Google Fonts CSS API, fetched once in GitHub Actions run 44956887983.
- Weights: 400, 600 and 700; WOFF2 subsets: latin and vietnamese (latin-ext removed after source-code coverage scan).
- License: SIL Open Font License 1.1; see wp-content/themes/dealz-theme/assets/fonts/OFL-Be-Vietnam-Pro.txt.
- FontTools inspection: downloaded WOFF2 subset files do not expose the OpenType tnum feature. Keep the requested font family; do not silently substitute another font. This is a known typography limitation pending owner decision.

## Size and runtime behavior
- Total WOFF2 payload after latin-ext removal: 201,432 bytes (196.71 KiB), below the 300 KiB cap.
- Generated fonts.css references only local files. The deployed theme must not request Google Fonts or any other external font origin.
- font-proof.json records source, exact files, byte sizes and tnum inspection result.


## Subset audit (22/11/2026)
- Removed the five latin-ext WOFF2 files after scanning all 12 theme PHP/JS/CSS/HTML source files. No source character required only the latin-ext subset; Latin and Vietnamese subsets remain. Characters U+2301, U+2726, U+2190, U+2197, U+2713 and U+FF0B were already outside all three Google Fonts subset ranges and continue to use fallback fonts.
- Remaining WOFF2 payload: 201,432 bytes (196.71 KiB), below the 300 KiB cap. Only two font files are preloaded by the theme.


## Be Vietnam Pro digit metrics (22/11/2026)
- FontTools `hmtx` for weight 400 Latin, unitsPerEm 1000: 0=676, 1=385, 2=651, 3=660, 4=710, 5=660, 6=680, 7=594, 8=634, 9=680.
- The ten digits are not tabular. Keep Be Vietnam Pro and use min-width/right-aligned time and money columns; do not rely on `tnum`.
- CI evidence: [PHP syntax run 44989847815](https://github.com/hieudinh0608forwork-hash/dealz-v2/actions/runs/44989847815).
