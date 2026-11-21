# Local font assets

The design rulebook requires Bricolage Grotesque and Be Vietnam Pro, served only from local WOFF2 files.

Place appropriately licensed files in this directory and define @font-face in the theme stylesheet. Recommended weights:
- Bricolage Grotesque: 700 and 800.
- Be Vietnam Pro: 400, 600 and 700.

Use font-display: swap. Do not load Google Fonts or any other remote font endpoint. Do not commit font files unless their license permits redistribution.

Until the approved WOFF2 files are supplied, the theme uses the system sans-serif fallback. This is a known visual handoff gap.
