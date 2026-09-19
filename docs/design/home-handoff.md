# Handoff: Powerstik.net Home Page

## Overview

A complete redesign of the home page for **powerstik.net** — Powerstik is a design-studio-turned-print-and-packaging-manufacturer (a brand of Design India), based in Haryana, India. It supplies labels, mono cartons and corrugated packaging to ~1,500 domestic clients across nine industries, exports to eight countries, and its flagship product is a proprietary acid- and heat-resistant **battery label paper**.

The home page is a **single scrolling narrative** — thirteen sections, as specified in the client's architecture brief (included at `reference/Powerstik_Website_Architecture.docx`, section 5). Its job is conversion: **"Get a quote" is the primary action**, persistent in the header, repeated in the hero, and given a full-bleed red CTA band before the footer. A floating WhatsApp button is fixed on screen because Indian B2B buyers arrive via and prefer WhatsApp.

Positioning line driving the whole page: **"Designed like a studio. Delivered like a factory."**

## About the Design Files

The files in this bundle are **design references created in HTML** — a prototype showing intended look, copy and behaviour. **They are not production code to copy directly.**

`Powerstik Home.dc.html` is authored in a streaming component format with a proprietary runtime (`<x-dc>`, `<sc-for>`, `{{ }}` holes, a `Component extends DCLogic` class). **Do not try to port that runtime.** Read it as a visual and content spec only — open it in a browser to see the rendered design.

The task is to **recreate this design in the target codebase's environment** using its established patterns and libraries. The client brief recommends **WordPress or Webflow** as the CMS so their team can add case studies and blog posts themselves; if you are building a bespoke front end instead, Next.js or Astro with a headless CMS both suit this content. Either way:

- All styling in the prototype is **inline** (a constraint of the authoring runtime). In production, move it to whatever the codebase uses — Tailwind, CSS modules, SCSS. Do not preserve inline styles.
- All repeated content (counters, capability tiles, process steps, industries, machines, countries, case studies, testimonials) lives as **arrays in the logic class's `renderVals()` method**. These are the content models — they should become CMS collections / JSON, not hard-coded markup. See "Content models" below for the full data.
- `image-slot.js` is a prototyping-only drag-and-drop image placeholder. **Do not ship it.** Replace every `<image-slot>` with a real `<img>`/`<picture>` (or CMS image field). See "Assets" for the list of 18 slots and what belongs in each.

## Fidelity

**High-fidelity.** Colours, typography, spacing, copy and interaction states are final and intentional — recreate them precisely. Exact values are in "Design Tokens" below.

Two deliberate caveats:

1. **Desktop only.** The page is pinned to a fixed `width: 1440px` canvas. Mobile and tablet layouts have **not** been designed. This matters: the brief calls for mobile-first performance because most traffic arrives from WhatsApp links. Responsive behaviour needs a design pass — recommendations under "Responsive behaviour".
2. **Placeholder content.** Phone number, plant street address, testimonial attributions and all imagery are placeholders. Flagged inline below.

---

## Page structure

Top to bottom. All sections are full-bleed; content sits in a centred container of `max-width: 1360px` with `44px` horizontal padding.

### 0. CMYK bar (decorative, 4px tall)

Five flex children, full width, no gap:

| Flex | Colour |
| --- | --- |
| 1 | `#00AEEF` (cyan) |
| 1 | `#EC008C` (magenta) |
| 1 | `#FFF200` (yellow) |
| 1 | `#121212` (key) |
| 6 | `#E31E24` (brand red) |

This is the page's one print-world motif — a press colour bar. The 1:1:1:1:6 ratio means the brand red dominates and the CMYK reference reads as a detail rather than a rainbow. Keep it.

### 1. Utility bar

- Background `#121212`, height `38px`, text `#A8A49C` in IBM Plex Mono `11.5px`, `letter-spacing: .06em`, uppercase.
- Left: `A brand of Design India · Branding / Design / Print / Packaging`
- Right, in a `26px` gap flex row: `Client login` (link), `Download company profile` (link), phone number in `#FFFFFF` — **placeholder `+91 98100 00000`, replace with the real number**.
- Link hover: colour → `#FFFFFF`.
- The brief lists a **language switcher here in phase 2** (French for Algeria, Russian; possibly Arabic, Bengali). Leave room on the right.

### 2. Header (sticky)

- `position: sticky; top: 0; z-index: 40`. Background `rgba(244,242,238,.94)` with `backdrop-filter: blur(8px)`. Bottom border `1px solid #DBD7D0`. Height `84px`.
- Three flex children, `space-between`, `40px` gap: logo, nav, CTA.
- **Logo**: `assets/logo.png` at `height: 40px`, width auto. Links to `#top`.
- **Nav**: `30px` gap, `14.5px`, weight `600`, `letter-spacing: -.01em`, colour `#121212`, hover `#E31E24`. Items: About, Capabilities, **Battery labels**, Industries, Our work, Global, Resources.
  - "Battery labels" carries a `6px` red dot (`#E31E24`, `border-radius: 50%`) before its label, marking it as the flagship. Keep this — it is the page's one navigational emphasis.
- **CTA**: "Get a quote", background `#E31E24`, text `#FFFFFF`, `14px`/weight `700`, padding `13px 22px`, square corners, `white-space: nowrap`. Hover: background → `#121212`.
- Per the brief this header (logo + nav + persistent quote button) is **global to every page**, not home-only. Build it as a shared layout component.

### 3. Hero

- `height: 760px`, `position: relative`, background `#121212`, `overflow: hidden`.
- Full-bleed image/video absolutely positioned at `inset: 0`, `object-fit: cover`. **Intended as a video loop** per the brief: SM-74 press running, a label roll unwinding, a 9-ply box being die-cut. Currently an image slot (`ps-hero`).
- Scrim over it: `linear-gradient(90deg, rgba(10,10,10,.92) 0%, rgba(10,10,10,.78) 46%, rgba(10,10,10,.18) 100%)`, `pointer-events: none`. Left-weighted so type stays legible while the right side of the footage shows through.
- Content block bottom-left, `padding: 0 44px 88px`:
  - **Kicker**: a `46px × 2px` red rule (`#E31E24`) then `Design studio, est. 2002 · Print & packaging plant, Haryana` — IBM Plex Mono `12px`, `letter-spacing: .14em`, uppercase, `#FFFFFF`, `14px` gap, `26px` bottom margin.
  - **H1**: `88px`, weight `800`, `line-height: .94`, `letter-spacing: -.035em`, `max-width: 20ch`, `text-wrap: balance`. Two lines: `Designed like a studio.` in `#FFFFFF`, then `Delivered like a factory.` in `#E31E24`. Hard `<br>` between — the two-sentence structure is the point.
  - **Body**: `19px`, `line-height: 1.5`, `#D6D2CB`, `max-width: 56ch`, `28px` top margin: *"We began as a small design setup and built the plant to match — Heidelberg offset, flexo roll labels and a full corrugation line, all fed by our own designers. Labels and packaging from 50 units to any volume, dispatched in 2–5 days."*
  - **Two CTAs**, `14px` gap, `40px` top margin: **Get a quote** (solid `#E31E24`, `#FFFFFF`, `16px`/`700`, padding `18px 32px`; hover → white bg, `#121212` text) and **Explore capabilities** (`1px solid rgba(255,255,255,.4)`, `#FFFFFF`, `16px`/`600`, padding `17px 30px`; hover → border `#FFFFFF`, bg `rgba(255,255,255,.08)`).
- Note the scrim and text block are `pointer-events: none` with `pointer-events: auto` restored on the button row — in production that hack is unnecessary; it only exists so the prototype's image placeholder stays droppable.

### 4. Proof strip — animated counters

- Background `#121212`, top border `1px solid #2A2A2A`, text `#FFFFFF`.
- `grid-template-columns: repeat(6, minmax(0, 1fr))`. Each cell: `padding: 44px 24px 46px`, `border-right: 1px solid #2A2A2A`.
- Per cell: value at `46px`, weight `800`, `letter-spacing: -.04em`, `line-height: 1`; label below at `10px` top margin, IBM Plex Mono `11.5px`, `letter-spacing: .1em`, uppercase, `#8F8B84`.
- **Counter animation**: on mount, a single `requestAnimationFrame` loop over **1500ms** with cubic ease-out (`1 - (1-p)³`) drives one shared progress value `t` from 0→1; every counter interpolates from it. Numbers format with `toLocaleString('en-IN')` (Indian grouping: `1,500`).
  - "Since" counts `1900 → 2002`; `1,500+`, `8`, `200+` count `0 → n`; `From 50` counts `1 → 50`; `2–5` is static.
  - In production, **gate this on an IntersectionObserver** so it fires when the strip scrolls into view, and respect `prefers-reduced-motion: reduce` by rendering final values immediately. The prototype fires on mount because the strip is near the top.

| Value | Label |
| --- | --- |
| 2002 | Designing since |
| 1,500+ | Domestic clients |
| 8 | Export countries |
| 200+ | People on the floor |
| 2–5 | Day dispatch |
| From 50 | Unit minimum |

> ⚠️ **Open item.** The brief's proof strip says "9 countries" but names only eight export markets (Nepal, Bangladesh, Afghanistan, Uganda, USA, Fiji, Russia, Algeria). The design uses **8** throughout. Confirm with the client and keep the counter, the Global section heading and the footer country list in sync.

### 5. Client logo wall

- Background `#F4F2EE`, bottom border `1px solid #DBD7D0`.
- Label above: `Trusted by 1,500+ manufacturers`, IBM Plex Mono `11.5px`, `letter-spacing: .12em`, uppercase, `#6F6C66`, padding `34px 44px 12px`.
- **Infinite marquee**: the row's children are duplicated (two identical sets) inside a `width: max-content` flex row animated with `@keyframes psMarquee { to { transform: translateX(-50%) } }` at `46s linear infinite`. Translating exactly `-50%` of a doubled row is what makes the loop seamless — keep that relationship if you change the content length.
- Edge fade: `mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent)` on the viewport.
- Each entry is **set as text, not a logo file**: `30px`, weight `700`, `letter-spacing: -.035em`, colour `#9A958D`, `white-space: nowrap`, `padding: 0 34px`.

> ⚠️ **Open item.** Permission to display client logos is unconfirmed in the brief, so names are typeset as wordmarks. Once permission lands, swap to real SVG logos — keep them single-colour `#9A958D` and normalise optical heights so the wall reads evenly. Entries: Livguard, Eastman, Amaron, Tata Green, Unique Energos, Exide MSME partners, Aquafresh RO, Pizza chains, Surgical OEMs. The last three are generic stand-ins pending named permissions.
>
> Also: **pause the marquee on `prefers-reduced-motion: reduce`** and on hover.

### 6. What we do — four tiles

- Container padded `108px 44px 0`.
- **Section header pattern** (reused in sections 6, 8, 9, 12 — build it once as a component):
  - Flex row, `align-items: flex-end`, `space-between`, `60px` gap, `border-bottom: 2px solid #121212`, `padding-bottom: 26px`.
  - Left: numbered kicker — IBM Plex Mono `11.5px`, `letter-spacing: .14em`, uppercase, `#E31E24`, `18px` bottom margin (`01 / What we do`) — then an H2 at `58px`, weight `800`, `letter-spacing: -.035em`, `line-height: 1`.
  - Right: intro paragraph, `17px`, `line-height: 1.55`, `#55524D`, `max-width` ~`40–42ch`.
  - The 2px rule under the header is the system's strongest structural device. Don't soften it to a hairline.
- H2: **"Four disciplines, one roof."** Intro: *"Artwork, plates, print and box never change hands between vendors. That is why a brief on Monday ships as a pallet on Friday."*
- **Tile grid**: `repeat(4, minmax(0,1fr))`, `gap: 1px`, grid background `#DBD7D0` — the gap *is* the rule. Each tile: background `#F4F2EE`, padding `40px 32px 34px`, `min-height: 320px`, column flex, whole tile links to `#quote`. Hover: background → `#FFFFFF`.
- Tile internals: number (Plex Mono `11.5px`, `#A8A49C`), H3 (`27px`/`700`, `letter-spacing: -.025em`, `line-height: 1.1`, `20px` top margin), body (`15.5px`/`1.55`, `#55524D`), then a spec line pushed to the bottom with `margin-top: auto; padding-top: 26px` — Plex Mono `11.5px`, `letter-spacing: .1em`, uppercase, `#E31E24`. The `margin-top: auto` is what keeps the four red spec lines on a shared baseline regardless of body length.

### 7. Battery labels — flagship spotlight (dark)

The page's centrepiece. Background `#0C0C0C` (a half-step darker than the other dark sections, deliberately), text `#FFFFFF`, `108px` top margin, padding `100px 44px`.

- Two columns, `grid-template-columns: 1.05fr .95fr`, `80px` gap, centred.
- **Left column**:
  - Outlined badge: `1px solid #E31E24`, text `#E31E24`, Plex Mono `11px`, `letter-spacing: .14em`, uppercase, padding `8px 14px`, `30px` bottom margin — `Flagship · Battery labels`.
  - H2 `62px`, weight `800`, `letter-spacing: -.04em`, `line-height: .98`: **"Paper that survives / the acid and the heat."** (hard break).
  - Body `18px`/`1.55`, `#C9C5BE`, `max-width: 54ch`: *"Our proprietary battery label paper was developed in-house for Indian and export lead-acid production lines: electrolyte splash, bonnet heat and high-speed applicators. Main, warning, terminal and warranty labels in sheet or roll, plus the corrugated box around them."*
  - **Three property cells**: `repeat(3, minmax(0,1fr))`, `gap: 1px`, grid bg `#262626`, cells `#0C0C0C` padded `24px 20px`, `44px` top margin. Each: word at `30px`/`800`, `letter-spacing: -.03em`, then Plex Mono `11px` caption `#8F8B84`.
    - **Acid** → No lift, no bleed
    - **Heat** → Bonnet-temperature stable
    - **Grip** → Applicator-ready adhesion
  - Two CTAs, `14px` gap, `40px` top margin: **Request a sample** (solid `#E31E24`, `15.5px`/`700`, padding `16px 28px`, hover → white) and **Download data sheet** (`1px solid #3A3A3A`, hover border `#FFFFFF`). Both are named conversions in the brief.
- **Right column**: `aspect-ratio: 4/5` image (`ps-battery`), background `#161616`.
- **Acid-test badge**, overlapping the image's bottom-left corner at `left: -22px; bottom: -22px`: `132px × 132px`, `1px solid #E31E24`, background `rgba(12,12,12,.86)`, `pointer-events: none`. Contains centred Plex Mono `10.5px` uppercase text `Acid / test / passed` (`line-height: 1.6`) over an **animated ring**: a `54px` absolutely-positioned circle, `1px solid #E31E24`, `border-radius: 50%`, running `@keyframes psDrop { 0% { transform: scale(.2); opacity: 0 } 30% { opacity: .9 } 100% { transform: scale(2.6); opacity: 0 } }` at `3.4s ease-out infinite`.
  - This is the brief's "acid-test animation", reduced to one restrained pulse — a droplet spreading and being repelled. Resist elaborating it. Disable under `prefers-reduced-motion`.

> ⚠️ **Open item.** The paper's **brand name** and **shareable test data** are unconfirmed (brief §11). The design says "our proprietary battery label paper" generically — once named, that name should carry this section's headline and the badge, and real test figures should replace the three qualitative cells.

### 8. How we work — process timeline

- Padding `108px 44px 0`. Section header: kicker `02 / How we work`, H2 **"Brief to pallet in 2–5 days."**, intro *"One coordinator owns your job end to end. Bulk orders included — the clock starts at artwork approval."*
- Six columns, `repeat(6, minmax(0,1fr))`, `48px` top margin, each with `padding-right: 22px`.
- Per step, a timeline marker: flex row, `10px` gap, `22px` bottom margin — an `11px` red dot (`#E31E24`, `border-radius: 50%`, `flex: none`) followed by a `1px` line of `#CFCAC2` filling the remaining width. Then Plex Mono step number (`11px`, `letter-spacing: .1em`, `#A8A49C`), H3 (`22px`/`700`, `letter-spacing: -.02em`), body (`14.5px`/`1.5`, `#55524D`).

| Step | Title | Body |
| --- | --- | --- |
| Step 01 | Brief | Spec, quantity, deadline. One coordinator assigned. |
| Step 02 | Design | Artwork or dieline from our studio, mockup included. |
| Step 03 | Approval | Digital proof signed off. The 2–5 day clock starts here. |
| Step 04 | Print | Heidelberg, Canon or Mark Andy, whichever suits the run. |
| Step 05 | QC / PDI | Incoming, in-process and pre-dispatch inspection. |
| Step 06 | Dispatch | Packed, documented and out — bulk orders included. |

### 9. Industries grid

- Padding `108px 44px 0`. Kicker `03 / Industries`, H2 **"Nine sectors, nine rulebooks."**, intro *"Pharma legibility, food-safe inks, QSR grease resistance — we know the constraint before you name it."*
- `repeat(3, minmax(0,1fr))`, `gap: 24px`, `40px` top margin. Nine cards, each a link (currently `#quote`; **in production each should link to its own industry page** — the brief calls these the strongest SEO asset, one page per sector).
- Card: `aspect-ratio: 4/3` image on `#E4E0D9`, then a caption row — `border-top: 1px solid #121212`, `14px` top margin, `16px 2px 0` padding, flex with `align-items: baseline`, `space-between`, `16px` gap. Left: H3 `22px`/`700`, `letter-spacing: -.02em`. Right: Plex Mono `11px`, `letter-spacing: .08em`, uppercase, `#6F6C66`, right-aligned.
- The baseline-aligned name-and-constraint pairing is the card's whole idea: the sector, and the technical reason Powerstik is credible in it.

| Sector | Constraint note |
| --- | --- |
| Battery | Acid & heat resistant |
| RO & water | Wet-surface adhesion |
| Pharma | Legibility compliance |
| Cosmetics | Foil & spot UV |
| Toys | Child-safe inks |
| Food | Food-safe inks |
| Pizza & QSR | Grease resistance |
| Surgical | Sterile-pack ready |
| Electrical | Warning & rating labels |

### 10. Machine park teaser

- Background `#E9E5DE` (the mid tone that separates this and the founders' section from the `#F4F2EE` ground), borders `1px solid #DBD7D0` top and bottom. Padding `96px 44px`.
- Two columns `.82fr 1.18fr`, `72px` gap, centred.
- Left: kicker `04 / Machine park`, H2 `52px`/`800`, `letter-spacing: -.035em`, `line-height: 1.02` — **"We bought the machines so you wouldn't have to chase them."** Body `17px`/`1.55`, `#55524D`: *"Offset, digital, flexo and corrugation on one floor, ~200 people running them. No job is subcontracted out of sight."* Then a text link **"Take the plant tour"** — `border-bottom: 2px solid #E31E24`, `padding-bottom: 4px`, `15.5px`/`700`, `#121212`, `30px` top margin. Points at the About/Infrastructure page (the brief wants a photo tour or 360° walkthrough there).
- Right: `repeat(2, minmax(0,1fr))`, `gap: 1px`, grid bg `#CFCAC2`; cells `#F4F2EE` padded `30px 28px`. Each: kind (Plex Mono `11px`, `letter-spacing: .1em`, uppercase, `#E31E24`), name (`24px`/`700`, `letter-spacing: -.025em`, `line-height: 1.15`, `14px` top margin), spec (`14.5px`/`1.5`, `#55524D`).

| Kind | Machine | Spec |
| --- | --- | --- |
| Offset | Heidelberg SM-74 | Five-colour sheetfed, the workhorse for cartons and sheet labels. |
| Offset | Heidelberg CD-102 | Large-format sheetfed for long runs and heavy stock. |
| Flexo | Mark Andy E5 | Roll label press — applicator-ready reels, defined winding. |
| Digital + corrugation | Canon & full flute line | Short runs and samples; mono carton to 9-ply in-house. |

### 11. Global reach

- Background `#121212`, text `#FFFFFF`, padding `100px 44px`.
- Header uses the same pattern but with a **`1px solid #2E2E2E`** rule instead of the 2px black one (dark ground). Kicker `05 / Global reach`, H2 **"Eight countries, one standard."**, intro `#C9C5BE`: *"Export documentation, Incoterms, port-ready packing and repeat-order colour consistency, handled by the same coordinator."*
- Two columns `1.15fr .85fr`, `64px` gap, `56px` top margin.
- **Left — arc diagram (inline SVG, `viewBox="0 0 620 420"`, `overflow: visible`, width 100%).** This is deliberately **schematic, not cartographic** — a hub with arcs fanning out, not a world map. Recreating it faithfully matters, so here is the exact construction:
  - Hub at `(96, 300)`. Rendered as a `7px` white filled circle, plus a `16px` unfilled circle stroked `#4A4A4A`, with the label `Haryana, India` at `(hub.x - 4, hub.y + 40)` in Archivo `17px`/`700`, `letter-spacing: -.02em`, `#FFFFFF`.
  - For each country at index `i` of 8, with `r = 400`:
    - `a = -π·0.46 + (i / 7) · (π·0.42)`
    - `x = 96 + cos(a) · r · 1.22`
    - `y = 300 + sin(a) · r · 0.62`
    - control point `cx = (96 + x) / 2`, `cy = min(300, y) − 78`
    - path `d = "M96 300 Q{cx} {cy} {x} {y}"`
  - Arc stroke: `strokeWidth: 1.1`, no fill; alternating colour — **odd** indices `#4A4A4A`, **even** indices the accent `#E31E24`. Each is drawn on with `stroke-dasharray: 700; stroke-dashoffset: 700` and `@keyframes psDash { to { stroke-dashoffset: 0 } }` over `2.2s ease-out forwards`, staggered by `0.25 + i · 0.14` seconds — so the routes trace outward in sequence.
  - Endpoint: `3.6px` circle filled `#E31E24`, plus a label at `(x + 10, y + 4)` in IBM Plex Mono `11.5px`, `letter-spacing: .06em`, `#C9C5BE`.
  - **Known issue to fix in production:** the first four labels (Nepal, Bangladesh, Afghanistan, Uganda) crowd together and sit close to the arcs. Either widen the angular spread (increase the `π·0.42` sweep), push the near labels further out, or drop labels from the SVG and rely on the country cards beside it. Your call — the diagram's job is to convey reach, not to be read as a list.
  - Animate in on scroll with an IntersectionObserver; skip the draw-on under `prefers-reduced-motion`.
- **Right — country cards**: `repeat(2, minmax(0,1fr))`, `gap: 1px`, grid bg `#2A2A2A`, cells `#121212` padded `20px 18px`. Name `19px`/`700`, `letter-spacing: -.02em`; note Plex Mono `10.5px`, `letter-spacing: .08em`, uppercase, `#8F8B84`, `6px` top margin.

| Country | Note |
| --- | --- |
| Nepal | Battery labels |
| Bangladesh | Labels & cartons |
| Afghanistan | Battery labels |
| Uganda | Battery labels |
| USA | Speciality print |
| Fiji | Labels |
| Russia | Battery labels |
| Algeria | Battery labels |

- Below the cards, `34px` top margin: **International enquiry** button — background `#FFFFFF`, text `#121212`, `15.5px`/`700`, padding `16px 28px`; hover → `#E31E24` bg, white text. Links to the international enquiry form (which per the brief carries **country and port fields**).

> ⚠️ **Open item.** Naming export clients needs permission (brief §11). The country notes describe product categories only, which is safe — keep it that way unless permissions come through.

### 12. Featured work — three case studies

- Padding `108px 44px 0`. Kicker `06 / Featured work`, H2 **"Problem, press, result."** In place of the usual intro paragraph, the right side holds a link: **See full portfolio** — Plex Mono `12px`, `letter-spacing: .1em`, uppercase, `#121212`, `border-bottom: 2px solid #E31E24`, `padding-bottom: 4px`, `white-space: nowrap`.
- `repeat(3, minmax(0,1fr))`, `gap: 28px`, `40px` top margin. Each card is a link on background `#FFFFFF` (hover → `#E9E5DE`) — note these cards are **white**, not the `#F4F2EE` ground, so they lift slightly without a shadow.
- Card: `aspect-ratio: 3/2` image on `#E4E0D9`; then padding `26px 26px 30px` containing tag (Plex Mono `11px`, `letter-spacing: .1em`, uppercase, `#E31E24`), H3 (`25px`/`700`, `letter-spacing: -.025em`, `line-height: 1.15`, `14px` top margin), body (`15px`/`1.55`, `#55524D`), and a result line at `20px` top margin above `padding-top: 16px` and `border-top: 1px solid #DBD7D0`, set `14.5px`/`700`.
- Structure follows the brief's **Problem → What we did → Result** format; the result line is the payoff and should always be a number.

| Tag | Title | Body | Result |
| --- | --- | --- | --- |
| Battery · Case study | A label that stopped peeling in transit | Electrolyte splash was lifting a client's existing labels before the batteries reached the dealer. | Zero field rejections across 1.2M units |
| QSR · Corrugated | 9-ply that survives a delivery fleet | Grease-resistant print and a flute spec rebuilt for stacking in bike boxes. | Damage claims down 60% |
| Pharma · Design + print | Legible at 6pt, compliant in three states | Carton redesign that fit mandated copy without losing the brand block. | Approved first submission |

> ⚠️ **Placeholder.** These three case studies are **illustrative, written to show the format** — they are not verified client outcomes. Replace with real case studies and real figures before launch. The brief calls for 4–6 in-depth ones on the Our Work page.

### 13. Founders' note

- Background `#E9E5DE`, `border-top: 1px solid #DBD7D0`, `108px` top margin, padding `96px 44px`.
- Two columns `.9fr 1.1fr`, `72px` gap, centred. Left: `aspect-ratio: 4/3` image on `#DBD7D0` (`ps-founders`) — the brief specifies a **candid** photo of Amit & Sumit, not a posed portrait.
- Right: kicker `07 / A note from the founders`, then the quote at **`28px`, weight `500`, `line-height: 1.38`, `letter-spacing: -.02em`, `text-wrap: pretty`** — large enough to read as a statement, light enough not to compete with the section H2s:

  > *"In 2002 we were two brothers with a design table and borrowed time on someone else's press. We kept saying yes to work we couldn't yet produce — then bought the machine that could. Twenty-four years later the design table is still where every job starts."*

- Attribution row below: `36px` top margin, `26px` padding-top, `border-top: 1px solid #CFCAC2`, `48px` gap. Two entries — name `17px`/`700`, role in Plex Mono `11px`, `letter-spacing: .08em`, uppercase, `#6F6C66`, `5px` top margin:
  - **Amit Sharma** — Operations & production
  - **Sumit Sharma** — Marketing & clients

> ⚠️ **Placeholder.** The quote is written in the founders' likely voice but is **not a real quote** — it must be approved or rewritten by Amit and Sumit before launch.

### 14. Testimonials

- Continues the `#E9E5DE` ground (same visual block as the founders' note — they read as one "people" band). `border-bottom: 1px solid #DBD7D0`, padding `0 44px 96px`.
- `repeat(3, minmax(0,1fr))`, `gap: 1px`, grid bg `#CFCAC2`; cells `#F4F2EE` padded `38px 32px 34px`, column flex.
- Per card: an opening `"` glyph at `34px`/`800`, `line-height: 1`, `#E31E24`; quote `17.5px`/`1.5`, `letter-spacing: -.01em`, `#121212`, `14px` top margin; then an attribution row pushed down with `margin-top: auto; padding-top: 28px` — a `44px` circular avatar (`#DBD7D0`, `overflow: hidden`) and, beside it at `14px` gap, name (`14.5px`/`700`) and role (Plex Mono `10.5px`, `letter-spacing: .08em`, uppercase, `#6F6C66`).

| Quote | Name | Role |
| --- | --- | --- |
| They sent a mockup the same evening and the pallet in four days. We stopped shopping around. | Procurement head | Battery manufacturer, Gurugram |
| The colour on our reorder matched the batch from two years ago. For us that is the whole job. | Brand manager | Personal care, Delhi NCR |
| Documentation was right the first time, which almost never happens with a new supplier. | Import manager | Distributor, Kampala |

> ⚠️ **Placeholder.** Quotes and attributions are illustrative — anonymised by role to show the pattern. Replace with real, attributed quotes. The brief prefers **short video clips**; if those arrive, the card needs a video thumbnail treatment (design pass required).

### 15. Final CTA band

- **Full-bleed `#E31E24`**, text `#FFFFFF`, padding `104px 44px`. This is the page's one moment where the brand red runs as a field — the poster close. Don't dilute it.
- Two columns `1.1fr .9fr`, `72px` gap, centred.
- Left: H2 `72px`, weight `800`, `letter-spacing: -.04em`, `line-height: .96` — **"Have a job? / Get a quote in 24 hours."** (hard break; the brief's exact line). Body `19px`/`1.5`, `rgba(255,255,255,.92)`, `max-width: 50ch`: *"Send size, quantity, sheet or roll and your artwork. A coordinator — a person, named, reachable — replies within one working day."*
- Right: three stacked action rows, `12px` gap. Each is a flex row with `space-between` holding a label and a Plex Mono `13px` `→`:
  1. **Start the smart RFQ** — background `#FFFFFF`, text `#121212`, `17px`/`700`, padding `22px 28px`; hover → `#121212` bg, white text.
  2. **WhatsApp us** — `1px solid rgba(255,255,255,.5)`, `#FFFFFF`, `17px`/`600`, padding `21px 28px`; hover → bg `rgba(255,255,255,.12)`, border `#FFFFFF`.
  3. **Request a callback** — same as 2.
- These are three of the brief's five named conversions (the other two — Request a sample, Download company profile — appear in the battery section and utility bar).

### 16. Footer

- Background `#121212`, text `#8F8B84`, base size `14.5px`.
- Upper: padding `76px 44px 0`, `grid-template-columns: 1.4fr 1fr 1fr 1fr`, `56px` gap.
  - **Column 1**: logo at `height: 34px` with `filter: brightness(0) invert(1)` to knock it out white — **replace this with a proper white logo asset**; the filter is a prototype shortcut that flattens the red. Then *"Powerstik is a brand of Design India. Labels, cartons and corrugated packaging, designed and printed in-house."* (`line-height: 1.6`, `max-width: 34ch`, `#A8A49C`), then the address in `#6F6C66`.
    - ⚠️ Address is a placeholder: `Plot · Industrial Area / Haryana, India`. The brief wants the **plant address with an embedded map** — add the map here.
  - **Columns 2–4**: each has a heading — Plex Mono `11px`, `letter-spacing: .12em`, uppercase, `#FFFFFF`, `18px` bottom margin — over a column-flex link list, `11px` gap, links `#A8A49C` → hover `#FFFFFF`.
    - **Capabilities**: Design & branding, Label printing, Corrugated packaging, Machine park, Battery labels
    - **Company**: Our story, Leadership, Quality & PDI, Global, Careers
    - **Resources**: Company profile (PDF), Paper data sheets, Material guide, FAQs, Client zone login
  - ⚠️ The brief also wants **certifications** and **social links** in the footer. Certifications held (ISO etc.) are an unconfirmed open item — leave a slot in column 1 or add a fifth column once known.
- Lower bar: `56px 44px 0` wrapper, then `border-top: 1px solid #262626`, padding `24px 0 36px`, flex `space-between`, Plex Mono `11px`, `letter-spacing: .08em`, uppercase, `#6F6C66`. Left `© 2026 Design India · Powerstik®`; right the export country list, dot-separated.

### 17. Floating WhatsApp button

- `position: fixed; right: 26px; bottom: 26px; z-index: 60`. `60px × 60px`, `border-radius: 50%` (the one intentional circle on the page — it is a platform convention, not a brand shape), background `#25D366` (WhatsApp brand green), hover `#1DAE52`, `box-shadow: 0 8px 26px rgba(0,0,0,.28)`. Contains a `30px` inline WhatsApp glyph in `currentColor` (`#FFFFFF`), centred.
- Per the brief this is **global to every page**. Wire it to `https://wa.me/<number>` with a prefilled message; on desktop it should open WhatsApp Web.

---

## Interactions & Behaviour

**Implemented in the prototype**

| Behaviour | Detail |
| --- | --- |
| Sticky header | Translucent `rgba(244,242,238,.94)` + `backdrop-filter: blur(8px)`, `z-index: 40`. |
| Counter count-up | Shared `requestAnimationFrame` clock, 1500ms, ease-out cubic `1-(1-p)³`, on mount. |
| Logo marquee | CSS `transform: translateX(-50%)` on a doubled row, `46s linear infinite`. |
| Acid-drop pulse | `psDrop` scale `.2 → 2.6` with opacity fade, `3.4s ease-out infinite`. |
| Arc draw-on | `stroke-dashoffset` 700 → 0, `2.2s ease-out forwards`, staggered `0.25 + i·0.14s`. |
| Hover states | Documented per component above. Every link and card has one. |
| In-page nav | All nav links are `#anchor` jumps. |

**Required in production, not in the prototype**

- **Scroll-triggered animation.** Gate the counters and arc draw-on behind an `IntersectionObserver` (fire once, ~25% visibility). Currently both run on mount.
- **`prefers-reduced-motion: reduce`.** Must disable the marquee, the acid pulse and the arc draw-on, and render counters at their final values immediately.
- **Focus-visible states.** The prototype has none. Every link and button needs a visible keyboard focus ring — suggest `outline: 2px solid #E31E24; outline-offset: 2px` (use `#FFFFFF` on the red CTA band, where a red ring would vanish).
- **Real routing.** Most links point at `#quote` or `#anchor` placeholders. Map them to real routes per the brief's sitemap (§3) — notably each industry card → its own sector page.
- **Smart RFQ form.** The brief's key conversion: a step-by-step form — product type, size, quantity, sheet/roll, material, finish, artwork upload, required date — where **choosing "roll" reveals roll-spec fields** (core size, winding direction). Submissions route to CRM (Zoho/HubSpot) and trigger a WhatsApp acknowledgement. The form itself is **not designed** — it needs its own design pass.
- **Scroll motifs.** The brief suggests dielines folding into boxes and labels building up colour layer by layer. The client chose "motifs as small accents only" for this page, so these were deliberately left out. Don't add them without asking.
- **Hero video.** Needs a poster frame, `muted`/`loop`/`playsinline`, lazy loading, and a still-image fallback on slow connections — most traffic is mobile via WhatsApp.
- **Analytics.** Google Analytics with **quote-conversion tracking** on every CTA (header, hero, sample request, RFQ, WhatsApp, callback).
- **SEO.** Target terms: "battery label printing India", "acid resistant battery labels", "corrugated box manufacturer Haryana". Schema markup and **301 redirects from the old powerstik.net URLs** are required.

## Responsive behaviour

**Not designed — needs a pass.** The prototype is a fixed `1440px` canvas. Guidance for the breakpoints:

- **Header**: seven nav items plus a persistent CTA will not fit below ~1100px. Collapse nav to a hamburger but **keep "Get a quote" visible at all widths** — it is the primary action on every page.
- **Proof strip**: 6 columns → 3×2 → 2×3. Scale the `46px` numerals down with `clamp()`; they are the first thing to overflow.
- **Battery spotlight, machine park, founders, global, final CTA**: all two-column — stack to one, image first except in the founders' section.
- **Industries**: 3 → 2 → 1 column.
- **Case studies**: 3 → 1 column, or a horizontal snap-scroller on mobile.
- **Process timeline**: 6 columns → a vertical list with the dot-and-line marker rotated, or a horizontal scroller.
- **Arc diagram**: the labels won't survive narrow widths. Below ~900px, drop the SVG and show the country cards alone in a single column.
- **Type scale**: the `88px` H1 and `72px` CTA headline need `clamp()`. Suggested floors — H1 `40px`, section H2 `32px`, CTA `36px`.

## State Management

Minimal — this is a marketing page.

- `t` — a single `0→1` animation progress float for the counters (prototype holds it in component state; in production prefer a CSS-driven or hook-based count-up, and one observer for the whole strip).
- `hasAnimated` per animated section — so scroll-triggered animations fire once, not on every re-entry.
- Mobile nav open/closed.
- The RFQ form (separate page) needs real form state: step index, field values, conditional roll-spec visibility, file upload progress, submit/success/error.

No data fetching on this page beyond CMS-rendered content.

## Content models

These arrays live in `renderVals()` in the prototype and are reproduced in full in the section tables above. As CMS collections:

| Collection | Fields | Count |
| --- | --- | --- |
| Counters | value, label | 6 |
| Client logos | name (→ logo asset) | 9 |
| Capability tiles | num, title, body, spec | 4 |
| Process steps | num, title, body | 6 |
| Industries | name, note, image, → page link | 9 |
| Machines | kind, name, spec | 4 |
| Export countries | name, note | 8 |
| Case studies | tag, title, body, result, image | 3 featured |
| Testimonials | text, name, role, avatar | 3 |

The brief wants the team to add case studies and blog posts themselves — case studies, industries and testimonials are the collections that must be CMS-editable.

## Design Tokens

### Colour

| Token | Hex | Use |
| --- | --- | --- |
| Brand red | `#E31E24` | Primary CTA, kickers, accents, final CTA field |
| Ink | `#121212` | Body text, dark sections, utility bar, footer |
| Deep ink | `#0C0C0C` | Battery spotlight ground only |
| Paper | `#F4F2EE` | Page ground, light cards |
| Paper mid | `#E9E5DE` | Machine park, founders, testimonials band |
| White | `#FFFFFF` | Case-study cards, dark-ground text, inverse CTA |
| Rule light | `#DBD7D0` | 1px borders on light ground |
| Rule mid | `#CFCAC2` | Grid gaps on `#E9E5DE` |
| Rule dark | `#2A2A2A` / `#262626` / `#2E2E2E` | Borders and grid gaps on dark grounds |
| Image placeholder | `#E4E0D9` | Empty image wells |
| Body grey | `#55524D` | Body copy on light |
| Mono grey | `#6F6C66` | Small caps labels on light |
| Muted grey | `#8F8B84` | Labels on dark, footer body |
| Light grey | `#A8A49C` | Utility bar, footer links, tile numbers |
| Dark-ground body | `#C9C5BE` / `#D6D2CB` | Body copy on dark |
| Logo wall grey | `#9A958D` | Client wordmarks |
| WhatsApp green | `#25D366` (hover `#1DAE52`) | Floating button only |
| CMYK bar | `#00AEEF` `#EC008C` `#FFF200` | Top bar only — never as UI colour |

### Typography

Two families, both from Google Fonts:

- **Archivo** — weights 400, 500, 600, 700, 800, 900. All headings, body copy, UI labels. Tight negative tracking at display sizes is essential to the look.
- **IBM Plex Mono** — weights 400, 500, 600. Every small-caps label, kicker, spec line and technical note. This is the print/engineering register and it does a lot of the work; don't substitute a sans.

```
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
```

| Role | Family | Size | Weight | Tracking | Leading |
| --- | --- | --- | --- | --- | --- |
| Hero H1 | Archivo | 88px | 800 | −.035em | .94 |
| Final CTA H2 | Archivo | 72px | 800 | −.04em | .96 |
| Battery H2 | Archivo | 62px | 800 | −.04em | .98 |
| Section H2 | Archivo | 58px | 800 | −.035em | 1 |
| Machine park H2 | Archivo | 52px | 800 | −.035em | 1.02 |
| Counter value | Archivo | 46px | 800 | −.04em | 1 |
| Property word (battery) | Archivo | 30px | 800 | −.03em | — |
| Logo wordmark | Archivo | 30px | 700 | −.035em | — |
| Founders' quote | Archivo | 28px | 500 | −.02em | 1.38 |
| Tile H3 | Archivo | 27px | 700 | −.025em | 1.1 |
| Case-study H3 | Archivo | 25px | 700 | −.025em | 1.15 |
| Machine name | Archivo | 24px | 700 | −.025em | 1.15 |
| Step / industry H3 | Archivo | 22px | 700 | −.02em | — |
| Country name | Archivo | 19px | 700 | −.02em | — |
| Hero / CTA body | Archivo | 19px | 400 | — | 1.5 |
| Battery body | Archivo | 18px | 400 | — | 1.55 |
| Testimonial quote | Archivo | 17.5px | 400 | −.01em | 1.5 |
| Section intro / body | Archivo | 17px | 400 | — | 1.55 |
| Button (large) | Archivo | 16–17px | 600–700 | — | — |
| Tile body | Archivo | 15.5px | 400 | — | 1.55 |
| Case-study body | Archivo | 15px | 400 | — | 1.55 |
| Nav link | Archivo | 14.5px | 600 | −.01em | — |
| Footer body / step body | Archivo | 14.5px | 400 | — | 1.5–1.6 |
| Header CTA | Archivo | 14px | 700 | −.01em | — |
| Hero kicker | Plex Mono | 12px | 400 | .14em | uppercase |
| Section kicker | Plex Mono | 11.5px | 400 | .14em | uppercase |
| Utility bar / spec line | Plex Mono | 11.5px | 400 | .06–.1em | uppercase |
| Small label | Plex Mono | 11px | 400 | .08–.12em | uppercase |
| Micro label | Plex Mono | 10.5px | 400 | .08em | uppercase |

### Spacing

Not a strict scale — the page uses a small set of repeated values:

- Section vertical rhythm: **`108px`** between light sections, **`96–104px`** padding inside tinted/dark bands.
- Container: `max-width: 1360px`, `44px` horizontal padding, centred. Page canvas `1440px`.
- Grid gaps: **`1px`** where the gap is a rule (tiles, machine cards, country cards, testimonials, battery properties), **`24px`** (industries) or **`28px`** (case studies) where cards float apart.
- Section header rule: `padding-bottom: 26px`, `border-bottom: 2px solid #121212` (light) / `1px solid #2E2E2E` (dark).
- Card padding: `40px 32px 34px` (tiles), `30px 28px` (machines), `26px` (case studies), `38px 32px 34px` (testimonials), `20px 18px` (countries).
- Kicker → heading: `18px`. Heading → body: `24–28px`. Body → CTA row: `40px`.

### Radius, shadows, borders

- **Border radius: `0` everywhere.** No rounded corners on any card, button, image or input. The only exceptions are the WhatsApp button, the timeline dots, the nav's flagship dot and the testimonial avatars — all true circles at `50%`.
- **Shadows: none**, except `0 8px 26px rgba(0,0,0,.28)` on the floating WhatsApp button. Separation is done with rules and ground-tone changes, not elevation.
- **Borders**: `1px` for ordinary rules; `2px solid #121212` for section-header rules; `2px solid #E31E24` as the underline on text links.

## Assets

### Provided in this bundle

- **`assets/logo.png`** — the Powerstik wordmark (red "power", black "stik", `®`, tagline "better ideas"). Used at `40px` height in the header and `34px` in the footer.
  - ⚠️ **Request an SVG** from the client, plus a **white/knockout variant** for the footer. The prototype fakes the footer version with a CSS filter, which flattens the red.
  - The `®` must be preserved — Powerstik is a registered mark.
- **`reference/Powerstik_Website_Architecture.docx`** — the client's full architecture and content plan. Read it for the other pages: About, Capabilities, Battery labels, Industries, Our work, Global, Resources, RFQ/Contact, and the phased Client Zone roadmap.

### Needed — imagery

**No real photography exists yet.** The brief is explicit: **real factory photography and video, never stock.** A plant photo/video shoot is an open item (§11) and should be scheduled early — it is the critical path for launch.

The prototype marks 18 image positions with `<image-slot>` elements. Replace each with a real `<img>`/`<picture>`:

| Slot id | What belongs there | Aspect |
| --- | --- | --- |
| `ps-hero` | **Video loop**: SM-74 running, label roll unwinding, 9-ply box die-cut | full-bleed, 1440×760 |
| `ps-battery` | Battery label on a cell, acid-drop test close-up | 4:5 |
| `ps-ind-battery` | Battery labels on the line | 4:3 |
| `ps-ind-ro` | RO unit labelling | 4:3 |
| `ps-ind-pharma` | Pharma carton | 4:3 |
| `ps-ind-cosmetics` | Cosmetics packaging | 4:3 |
| `ps-ind-toys` | Toy box print | 4:3 |
| `ps-ind-food` | Food packaging | 4:3 |
| `ps-ind-qsr` | Pizza box | 4:3 |
| `ps-ind-surgical` | Surgical pack | 4:3 |
| `ps-ind-electrical` | Electrical rating label | 4:3 |
| `ps-case-1` | Battery label case study | 3:2 |
| `ps-case-2` | QSR corrugated case study | 3:2 |
| `ps-case-3` | Pharma carton case study | 3:2 |
| `ps-founders` | Candid photo, Amit & Sumit on the shop floor | 4:3 |
| `ps-q1`, `ps-q2`, `ps-q3` | Testimonial avatars | 1:1 circle |

### Needed — other

- Client logo SVGs (pending permission): Livguard, Eastman, Amaron, Tata Green, Unique Energos + named export clients.
- Certification marks (ISO etc.) for the footer — which certifications Powerstik holds is unconfirmed.
- Company profile PDF, catalogue, and battery-paper data sheet for the download CTAs.
- WhatsApp icon is inline SVG in the prototype. The brief specifies **Lucide** as the icon set for anything else.

## Open items to confirm with the client

Carried from the brief's §11, plus what the design surfaced:

1. **Export country count** — 8 named vs. "9 countries" in the brief's proof strip. The design uses 8.
2. **Permission to show client logos** and to name export clients.
3. **Certifications held** (ISO etc.) for the footer.
4. **Brand name and shareable test data** for the proprietary battery paper — currently described generically.
5. **Real phone number and plant street address** (+ map embed).
6. **Founders' quote** — written in their voice, needs their approval.
7. **Case studies and testimonials** — the three of each shown are illustrative, not real.
8. **Photo/video shoot** date at the plant. Critical path.
9. Whether other Design India businesses need space on the site.
10. Confirm **quote-based enquiries only**, no public pricing (the design assumes this).

## Files in this bundle

| File | What it is |
| --- | --- |
| `README.md` | This document. Self-sufficient — implement from it. |
| `Powerstik Home.dc.html` | The design prototype. **Visual reference only** — do not port its runtime. Open in a browser to view. |
| `image-slot.js` | Prototyping-only image placeholder component. **Do not ship.** |
| `assets/logo.png` | Powerstik wordmark. |
| `reference/Powerstik_Website_Architecture.docx` | The client's architecture & content plan — the source of truth for the rest of the site. |

Note: `Powerstik Home.dc.html` references `image-slot.js` and `assets/logo.png` by relative path, so keep the folder structure intact when opening it locally.
