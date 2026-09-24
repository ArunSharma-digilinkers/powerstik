# Powerstik brand — lime and ink

Source of truth: the client brochure (`docs/design/brand/brochure-1..4.jpg`, full PDF in
`resources/media-inbox/brochure.pdf`) and the supplied wordmark
(`docs/design/brand/powerstik-logo-master.png`, 1288 × 267, transparent).

This replaces the red scheme (`#E31E24`) that the home page design handoff used. Nothing of the
red brand survives; if you find a red that is not a validation error, it is a leftover.

## Colour

| Role | Value | Where |
|---|---|---|
| Brand lime | `#C7FF00` | the "stik" in the wordmark; every fill, block, chip and rule |
| Ink | `#221F20` | the "power" in the wordmark; body type, dark grounds, primary buttons |
| Ink deep | `#141213` | the second dark ground, for stacked dark sections |
| Paper | `#FFFFFF` | the brochure ground. Pure white, not the old cream |

The brochure prints the lime as `#D4FD00`; that is the CMYK conversion of the same colour, so
screen work uses the wordmark's `#C7FF00`.

### The one rule that matters

**Lime is a fill, not an ink.** On white it measures 1.13:1 — as type it is unreadable, and as a
6px dot it disappears. On ink it measures 15:1. So:

- **on white** — lime fills blocks, chips, buttons, rules and highlights; the type on it is ink
- **on ink** — lime *is* the type colour; this is where the brand shouts
- **on lime** — type is ink, the primary button is ink with white type
- **small type on white never gets lime.** Use `.chip` or `.label-mark`

`brand-800` (`#55700A`) is the only step on the ramp that passes as text on white (5.2:1). It is
there for admin hover states, not for the public site.

The brochure does put lime type on white twice ("Two **minds**", "One **spark!**") — both at
display size, as a second word in a headline. That is the only licence: ≥40px and extrabold.

### Not the brand colour

- **Validation errors and destructive actions are red** (`red-600`/`red-700`). They used to
  inherit the brand red by accident; now they are explicitly their own colour.
- **The focus ring is ink**, not lime — a lime ring on white fails WCAG 2.4.11. Dark sections
  carry the class `on-dark`, which flips the ring to lime.

## Type

Archivo (all type) and IBM Plex Mono (small caps, kickers, spec lines), self-hosted. The
brochure's own face is a licensed neo-grotesque in the Helvetica Now / Aktiv Grotesk family;
Archivo is the closest free stand-in and is already used across the site.

## Patterns these rules produced

| Class | What it is |
|---|---|
| `.kicker` | the lime label box: section kickers, `01 / WHAT WE DO` |
| `.chip` | the same box, one step smaller, for any other small mono label |
| `.label-mark` | the quiet alternative inside cards: ink mono type behind a lime tick |
| `.btn-brand` | primary action — lime fill, ink type, inverting to ink-on-lime |
| `.btn-ink` | ink fill, white type, inverting to ink-on-lime |
| `<x-site.cta-band>` | the full lime field, as the brochure's lime panels |

Hover on a light ground moves the lime, not the type: the industry card titles take a
marker-pen highlight (`group-hover:bg-brand-500`), links swap a lime underline for an ink one.

## Assets

Generated from the master by `docs/design/brand/README-assets.md`:

- `public/images/brand/logo.png` — 960 × 217, ink "power" + lime "stik", for light grounds
- `public/images/brand/logo-white.png` — the knockout: white "power" + lime "stik", for ink grounds
- `public/images/brand/mark.png` — the "p" in ink on a lime tile, 512px
- `public/favicon.ico`, `favicon-32.png`, `apple-touch-icon.png` — the same tile

The new wordmark carries **no tagline**. "better ideas" was part of the old artwork and is gone;
`<meta name="theme-color">` is the ink.

## Still to ask the client

- Vector artwork (SVG/AI/EPS) for the wordmark. Everything here is rebuilt from a 1288px PNG.
- Whether "better ideas" survives as a tagline anywhere off the logo.
- Whether the floating WhatsApp button should keep the ink treatment or go back to WhatsApp's
  own green (`--color-whatsapp` is still defined; it is one class in
  `components/site/whatsapp.blade.php`).

## Client facts taken from the brochure

Confirmed by Arun on 2026-09-24 and shipped as real content, not demo copy:

| Fact | Where it lives |
|---|---|
| Export map — Algeria, Russia, Afghanistan, UAE, Uganda, Nepal, Nigeria, Zimbabwe, Fiji (plus Bangladesh and the USA, confirmed separately) | `ContentSeeder`, `export_countries` setting |
| "Our Major Clientele": Livfast, Livguard, Eastman, Amaron, Uno Minda, Solance, Su-Kam, UTL | `ContentSeeder`; `show_logo` stays off until each gives permission |
| 2184, Sector-38, Phase-II, Rai Industrial Estate, Sonipat, Haryana 131029 | `contact.address` setting |
| +91 98992 69999 and +91 130 310 0105 | `contact.phone`, `contact.phone_alt` |
| "India's most trusted battery sticker" | the flagship eyebrow on the home page |
| "25 years of core experience in battery label design and printing" | the flagship paragraph on the home page |
| "5,00,000 labels per day" | `stats.labels_per_day`, shown in the machine park section |
| Amit and Sumit's roles and bios, and "Two minds / One spark!" | `ContentSeeder`; the heading on `/about#leadership` |

**Two things the brochure does not settle.** The pull-quotes under each founder are still the
design's invention and stay in `DemoContentSeeder`. And "25 years of core experience" sits beside
a 2002 founding date, which is 24 years — the brochure's own arithmetic, not ours.

The WhatsApp number is the brochure's mobile. Confirm it is actually on WhatsApp, or clear
`contact.whatsapp` in Settings to hide the button.

## Client artwork in `public/media/`

Three sets came from the client and are used as-is, beyond a crop and a mild level:

- **`home/export-map.webp`** — Design India's own export map, the dotted world with lime flag
  pills. The original is drawn for white, so it is recoloured for the ink ground of the Global
  section: the pills and their contents are masked and left alone, and everything else (the dot
  field, the leader lines, the pin halos) is remapped to a light grey whose opacity follows how
  dark it was. The script is in the commit that added it; the source is
  `media-inbox/map.png`.
- **`machines/*.webp`** — the makers' press cutouts, trimmed and capped at 1200px with their
  transparency intact, one per card in the machine park teaser.
- **`home/acid-test.webp`** — the client's own photograph of a sheet of the battery paper in the
  test tray, marked "Heat Test" in pencil and dated, with the acid stain climbing from the lower
  edge. It runs as a proof band under the flagship section, so "Acid / Heat / Grip" is evidenced
  rather than asserted. Processing was a tray-lip crop to 3:2 and a 1.14 contrast bump so the
  pencil and the stain read at all — nothing that changes what the test shows. Source is
  `media-inbox/acid-paper.jpeg`. The protocol beside it is the client's, given by Arun on
  2026-09-24: **sulphuric acid at 1.280 specific gravity — full battery electrolyte strength —
  60 °C, 24 hours.** It is set as fixed copy in `home.blade.php`; reuse the same three rows on
  the battery-labels and capabilities pages when they are built, rather than restating it in
  prose.

**Two things to settle with the client.**

1. The map carries nine pins — the brochure's nine markets. The site lists eleven, because
   Bangladesh and the USA were confirmed separately. Ask Design India for a map with those two
   added rather than editing their artwork here.
2. `media-inbox/canon.png` is a **Xerox Color 175** press, while the brochure credits Canon,
   Japan. The card is labelled "Digital press" with no brand until that is resolved.

The press pictures are manufacturer renders, not photographs of the Sonipat floor. The brochure
uses them the same way, so they are fine here — but real plant photography would be better, and
the About page's infrastructure slots need it regardless.
