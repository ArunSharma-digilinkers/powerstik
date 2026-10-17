# Rebuilding the brand assets

Everything in `public/images/brand/` and the three favicons is generated from
`powerstick-logo-master.png` in this folder (2729 × 522, already trimmed to its
opaque bounding box — don't trim it again).

Two things make this more than a resize:

1. **The master is supplied in `#DBE836`**, which is not the brand lime. Every
   generated file is recoloured to `#C7FF00` first. See `brand.md`.
2. **The knockout is selective.** "Power" sits on the lime block and must stay ink;
   only the type to the right of the block — "stick" and the ® — turns white. A
   whole-image ink→white swap would wipe "Power" off its own block.

The generator is `scratchpad/bin/brandkit.php` from the session that built these
(PHP + GD, because this machine has no ImageMagick and no Pillow). It finds the
block's right edge by probing a row 6% down from the top, where the block is lime
across its full width and nothing else is, then:

| Output | How |
|---|---|
| `images/brand/logo.png` | recoloured master at 960px wide |
| `images/brand/logo-white.png` | ink → white only where `x > blockRight`, at 960px |
| `images/brand/mark.png` | first ink glyph ("P") centred on a 512px lime tile, 20% padding |
| `apple-touch-icon.png` (180), `favicon-32.png` (32) | the same tile |
| `favicon.ico` | 16/32/48 PNG payloads in an ICO container, written by hand |

`favicon.ico` is built directly: a six-byte header, one sixteen-byte directory
entry per size, then the PNG payloads. PNG-in-ICO is legal and every browser in
use reads it; GD cannot write ICO itself.

Replace this whole step the moment the client sends vector artwork.
