# Handoff: Powerstik.net — About us

**Companion document.** Read `README.md` (the home page handoff) first — it carries the full design tokens, typography scale, colour table, shared chrome (CMYK bar, utility bar, sticky header, final CTA band, footer, floating WhatsApp button) and the global production requirements. This document covers only what is **specific to the About us page**.

All shared chrome on this page is **identical to the home page** with one change: the nav's "About" item is marked active with `border-bottom: 2px solid #E31E24; padding-bottom: 3px` and no hover colour change. Build the chrome once as a layout component with an `activeNav` prop.

## Overview

`/about-us` — the credibility page. It carries the brand's core differentiator: Powerstik is a **design studio that grew its own factory**, not a printer that later hired designers. The brief (§6.1) specifies eight subsections; this design delivers all of them as one scrolling page rather than eight separate URLs, because the story only lands when read in sequence.

Audiences served: new domestic prospects looking for proof of stature, international battery manufacturers assessing credibility, and job seekers (careers sits at the bottom).

> **Decision to confirm with the client:** the brief's sitemap lists Our story / Leadership / Design India / Our people / Quality / Infrastructure / Sustainability / Careers as **sub-pages**. This design consolidates them into one scrolling page with anchor links. If the client wants separate URLs for SEO, each section here becomes a page and the section header + its content block port directly — but Careers and Quality are the only two with enough standalone content to justify it.

## Fidelity

**High-fidelity.** Same token set as the home page; no new colours, fonts or spacing values are introduced.

**Desktop only** — pinned `width: 1440px`. See the home page README's "Responsive behaviour" section; page-specific notes at the end of this document.

---

## Page structure

Container is the same throughout: `max-width: 1360px`, `44px` horizontal padding, centred on a `1440px` canvas.

### 1. Page hero (dark)

- Background `#121212`, text `#FFFFFF`, padding `88px 44px 84px`.
- **Breadcrumb**, `40px` bottom margin: IBM Plex Mono `11.5px`, `letter-spacing: .12em`, uppercase, `#8F8B84` — `Home` (link, hover `#FFFFFF`) / `About us` (current, `#FFFFFF`). Establish this breadcrumb pattern for every inner page.
- Two columns `1.15fr .85fr`, `80px` gap, `align-items: end`.
  - Left: red-rule kicker (`46px × 2px #E31E24` + Plex Mono `12px`, `.14em`, uppercase, `#E31E24`) reading `Since 2002 · Haryana, India`, `26px` bottom margin. Then H1 at **`78px`**, weight `800`, `letter-spacing: -.04em`, `line-height: .96`: **"It started at a / design table."** (hard break).
    - Note the inner-page H1 is `78px` against the home hero's `88px` — deliberate hierarchy. Use `78px` for all inner-page H1s.
  - Right: standfirst `19px`/`1.55`, `#C9C5BE`: *"Powerstik is the print and packaging arm that grew out of a design studio — not a press that later hired designers. Twenty-four years, one plant, ~200 people, and the same two brothers still signing off on jobs."*

### 2. Proof strip (4 cells)

Same construction as the home page's six-cell strip but **static — no count-up animation** (the numbers are context here, not a headline claim). Background `#121212`, `border-top: 1px solid #2A2A2A`, `grid-template-columns: repeat(4, minmax(0,1fr))`, cells padded `40px 24px 42px` with `border-right: 1px solid #2A2A2A` (omitted on the last).

Value `44px`/`800`, `letter-spacing: -.04em`, `line-height: 1`; label Plex Mono `11.5px`, `.1em`, uppercase, `#8F8B84`, `10px` top margin.

| Value | Label |
| --- | --- |
| 2002 | Founded as a design setup |
| 200+ | People across design, plant & QC |
| 1,500+ | Domestic clients served |
| 8 | Export countries |

### 3. Our story — interactive timeline ⭐

The page's one piece of real interactivity, and the brief's explicit ask ("interactive timeline from 2002 through each machine added to first export"). **The editorial idea: every machine Powerstik bought answered a job they had already said yes to.** Preserve that framing — it is what makes the timeline a story rather than a list.

- Padding `108px 44px 0`. Section header uses the shared pattern (numbered kicker `01 / Our story`, H2 `58px`/`800`/`-.035em`/`1`, right-hand intro `17px`/`1.55`/`#55524D`, `2px solid #121212` bottom rule at `26px` padding).
- H2: **"Every machine has a reason."** Intro: *"We never bought equipment speculatively. Each addition answered a job we had already said yes to. Pick a year."*

**Year tab bar** — `display: flex`, `44px` top margin, `border-bottom: 1px solid #DBD7D0`. Seven `<button>` elements, each `flex: 1`:

- Archivo `21px`, weight `700`, `letter-spacing: -.02em`, padding `18px 16px`, **`text-align: left`**, `border: none`, `cursor: pointer`, `appearance: none`.
- `border-right: 1px solid #DBD7D0` on all but the last.
- **Inactive**: `background: transparent`, `color: #A8A49C`.
- **Active**: `background: #E31E24`, `color: #FFFFFF`.
- `transition: background .15s, color .15s`.
- Left-aligned labels inside full-width buttons is intentional — flush-left is the system's rule.

**Detail panel** below — `grid-template-columns: 1fr 1fr`, `64px` gap, `align-items: center`, `56px` top padding:

- Left: kicker (Plex Mono `11.5px`, `.12em`, uppercase, `#E31E24`), H3 `42px`/`800`/`-.035em`/`1.04` at `18px` top margin, body `18px`/`1.6`/`#55524D`, `max-width: 52ch`, `text-wrap: pretty`, then a meta line — `32px` top margin, `22px` padding-top, `border-top: 1px solid #DBD7D0`, Plex Mono `11.5px`, `.08em`, uppercase, `#6F6C66`.
- Right: `aspect-ratio: 4/3` image on `#E4E0D9` (slot `ps-ab-story`).

**Content — the seven milestones.** `active` index defaults to `0` (2002).

| Year | Kicker | Title | Body | Meta |
| --- | --- | --- | --- | --- |
| 2002 | The beginning | A design table and borrowed press time | Amit and Sumit Sharma set up Design India as a small design consultancy in Haryana — logos, labels and packaging artwork for local manufacturers. Every job was printed by somebody else, and every job came back slightly wrong. | Design India founded · Haryana |
| 2006 | First press | We stopped sending work out | The first offset press arrived because a battery client needed a run we could not trust a vendor with. Powerstik became the name on the invoice for anything printed in-house. | Powerstik® brand established |
| 2010 | Heidelberg SM-74 | Five colours, and a real carton business | The Heidelberg SM-74 turned occasional print into a production line. Mono cartons for pharma and cosmetics followed within the year. | Heidelberg SM-74 installed |
| 2014 | Corrugation | From mono carton to 9-ply | A full corrugation line meant we could ship the box as well as the label on it. QSR and appliance clients arrived because one supplier could now do both. | Corrugation line commissioned |
| 2017 | The paper | Battery label paper, developed in-house | Existing label stock kept lifting under electrolyte splash and bonnet heat. We developed our own acid- and heat-resistant paper for lead-acid production lines — still our flagship product. | Proprietary substrate developed |
| 2019 | Roll labels | Mark Andy E5 and automatic applicators | Flexo roll labels let us supply clients running automatic applicators — defined core size and winding direction, reel after reel. | Mark Andy E5 flexo installed |
| 2022 | Going out | First export consignment | A battery manufacturer in Nepal placed the first export order. Seven more countries followed. Export documentation is now handled by the same coordinators who handle domestic jobs. | Exports begin · now 8 countries |

> ⚠️ **Placeholder years and events.** The brief confirms only **2002 (founding)**, the machine list, and that exports now reach eight countries. The **intermediate dates (2006, 2010, 2014, 2017, 2019, 2022) and the narrative attached to each are reconstructed, not verified.** Get the real install years and the real first-export year and country from Amit before launch — this section's whole credibility rests on the dates being true.

**Production notes**
- Each milestone should have **its own archive image**; the prototype reuses a single slot (`ps-ab-story`) because there is no photography yet. Add an `image` field to the milestone model and swap on selection with a short cross-fade (~200ms).
- Accessibility: implement as a proper tablist — `role="tablist"` on the bar, `role="tab"` + `aria-selected` on each button, `role="tabpanel"` + `aria-labelledby` on the detail panel, arrow-key navigation between years.
- Deep-linkable: `?year=2017` should preselect a milestone so the section can be shared.
- Consider making the year bar sticky on scroll within the section on desktop.

### 4. Leadership

- Background `#E9E5DE`, `108px` top margin, borders `1px solid #DBD7D0` top and bottom, padding `96px 44px`.
- Section header: `02 / Leadership`, H2 **"Two brothers, two halves."**, intro *"Between them they cover the whole job — one runs the floor, one runs the relationships. Both still answer the phone."*
- Two cards, `grid-template-columns: 1fr 1fr`, `gap: 1px` on a `#CFCAC2` grid background (the gap is the rule), `40px` top margin. Card background `#F4F2EE`, no padding on the card itself.
- Card internals: `aspect-ratio: 5/4` portrait on `#DBD7D0`, then a `34px 32px 38px` text block — role (Plex Mono `11px`, `.1em`, uppercase, `#E31E24`), name **H3 `34px`/`800`/`-.03em`/`1`** at `14px` top margin, bio `16.5px`/`1.6`/`#55524D` with `text-wrap: pretty`, then a pull-quote at `26px` top margin above `20px` padding-top and `border-top: 1px solid #DBD7D0`, set `16px`/`1.5`, weight `500`, **italic**, `#121212`.
  - Italic is used **nowhere else in the system** — it is reserved for these two quotes. Keep it that way.

**Amit Sharma** — Operations & production — slot `ps-ab-amit` (portrait, on the shop floor)

> Amit runs the plant — press scheduling, material buying, quality and dispatch. If your job is on a machine right now, he knows which one and when it comes off. He learned the floor before he ran it, which is why make-ready arguments with pressmen tend to be short.
>
> *"A delivery date is a promise, not an estimate."*

**Sumit Sharma** — Marketing & clients — slot `ps-ab-sumit` (portrait, in the design room)

> Sumit handles clients, new business and the design side of the house. He still reviews artwork personally before it goes to plate, and most of the 1,500 client relationships started as a conversation with him. He is the reason the studio never became a back office.
>
> *"Design is the reason they call us. Delivery is the reason they call again."*

> ⚠️ **Placeholder.** The brief gives only each brother's remit (Amit: operations & backend; Sumit: marketing & clients) and asks for "personal, creative bios". The bios and both quotes above are **written in their likely voice and are not their words.** They must be approved or rewritten by Amit and Sumit before launch.

### 5. Design India (dark)

- Background `#0C0C0C` (the deep ink otherwise reserved for the battery spotlight — used here because this is the page's other "brand truth" moment), text `#FFFFFF`, padding `96px 44px`.
- Two columns `1.05fr .95fr`, `80px` gap, centred.
- Left: kicker `03 / Design India`, H2 `56px`/`800`/`-.04em`/`1` — **"Powerstik is a brand of Design India."** Body `18px`/`1.6`/`#C9C5BE`, `max-width: 54ch`: *"Design India is the parent company — the design consultancy founded in 2002. Powerstik is the manufacturing brand it built to produce its own work: labels, cartons and corrugated packaging. One ownership, one quality standard, one accounts team."*
- Then two cells, `1fr 1fr`, `gap: 1px` on `#262626`, cells `#0C0C0C` padded `28px 24px`, `44px` top margin. Name `22px`/`700`/`-.02em`; body `15px`/`1.5`/`#8F8B84`.
  - **Design India** — Branding, packaging design, artwork and 3D mockups.
  - **Powerstik®** — Offset, digital and flexo print; labels and corrugation. (The `®` is set at `13px` with `vertical-align: super`.)
- Right: `aspect-ratio: 1/1` image on `#161616` (slot `ps-ab-di`) — studio and plant side by side.

> ⚠️ **Open item (brief §11).** Whether **other Design India businesses need space on the site** is unconfirmed. If they do, this section is where they belong — the two-cell grid extends to three or four cells cleanly.

### 6. Our people

- Padding `108px 44px 0`. Kicker `04 / Our people`, H2 **"~200 people, four teams."**, intro *"Nobody is a middleman. The person who designs your label works twenty metres from the press that prints it."*
- `repeat(4, minmax(0,1fr))`, `gap: 1px` on `#DBD7D0`, `40px` top margin. Cells `#F4F2EE`, no padding.
- Cell internals: `aspect-ratio: 4/3` image on `#E4E0D9`, then `28px 26px 32px` text — team number (Plex Mono `11px`, `.1em`, uppercase, `#A8A49C`), H3 `23px`/`700`/`-.025em`/`1.1` at `12px` top margin, body `15px`/`1.55`/`#55524D`.

| # | Team | Body | Slot |
| --- | --- | --- | --- |
| Team 01 | Design studio | Packaging and label design, dielines, 3D mockups and artwork prep. Where every job starts. | `ps-ab-team1` |
| Team 02 | Sales & coordination | One named coordinator per client, from enquiry through dispatch and reorder. | `ps-ab-team2` |
| Team 03 | Production | Pressmen, flexo operators and the corrugation crew running three shifts. | `ps-ab-team3` |
| Team 04 | Quality & dispatch | Incoming inspection, in-process checks, PDI, packing and export documentation. | `ps-ab-team4` |

### 7. Quality

- Background `#E9E5DE`, `108px` top margin, borders top and bottom, padding `96px 44px`.
- Kicker `05 / Quality`, H2 **"Checked three times before it ships."**, intro *"Incoming material, in-process control and pre-dispatch inspection. Reorders are matched against the retained sample, not a memory."*
- Two columns `1.1fr .9fr`, `64px` gap, `48px` top margin, `align-items: start`.

**Left — three stage cards**: `repeat(3, minmax(0,1fr))`, `gap: 1px` on `#CFCAC2`, cells `#F4F2EE` padded `30px 26px 34px`. Stage label (Plex Mono `11px`, `.1em`, uppercase, `#E31E24`), H3 `22px`/`700`/`-.025em`/`1.15`, body `15px`/`1.55`/`#55524D`.

| Stage | Name | Body |
| --- | --- | --- |
| Stage 01 | Incoming material | Board, paper and ink checked against spec on arrival — GSM, caliper, shade and adhesive batch recorded. |
| Stage 02 | In-process control | Colour read against the approved proof at set intervals; registration, die accuracy and lamination checked on the run. |
| Stage 03 | Pre-dispatch (PDI) | Sample pulled from the packed lot, matched to the retained sample, counted and signed before the truck loads. |

**Right — test list**: a `Test list` heading (Plex Mono `11px`, `.12em`, uppercase, `#6F6C66`, `18px` bottom margin) over rows separated by `1px solid #CFCAC2` (`border-top` on the container, `border-bottom` on each row). Each row is a flex with `align-items: baseline`, `space-between`, `20px` gap, `13px 0` padding — test name `16px`/`500` left, stage Plex Mono `11px`, `.08em`, uppercase, `#6F6C66`, right-aligned.

| Test | Stage |
| --- | --- |
| GSM & caliper | Incoming |
| Shade match to retained sample | Incoming + PDI |
| Colour density / delta-E | In-process |
| Registration & die accuracy | In-process |
| Adhesion / peel | In-process |
| Acid & heat resistance | Battery stock |
| Bursting strength & ply bond | Corrugated |
| Box compression | Corrugated |
| Count & packing check | PDI |

**Certifications box** — `28px` top margin, `22px 24px` padding, **`1px dashed #A8A49C`**. The dashed border is a deliberate "unresolved content" marker, the only dashed border in the system: *"Certification marks to be confirmed by management — ISO and any sector-specific approvals will be listed here."*

> ⚠️ **Open item (brief §11).** Which certifications Powerstik holds is unconfirmed. Once known, replace the dashed box with certification marks (solid border, logo row). **Delete the dashed treatment — do not ship it.**
>
> ⚠️ The nine tests are a **plausible reconstruction** from the brief's mention of a "test list", not the client's actual QC schedule. Get the real list from Amit.

### 8. Infrastructure (dark)

- Background `#121212`, text `#FFFFFF`, padding `96px 44px`.
- Header on the dark variant (`1px solid #2E2E2E` rule): kicker `06 / Infrastructure`, H2 **"Walk the floor."** Right side holds a link — **See the machine park** → home page `#machines`, Plex Mono `12px`, `.1em`, uppercase, `#FFFFFF`, `border-bottom: 2px solid #E31E24`, `padding-bottom: 4px`.
- **Photo grid**, `40px` top margin: `grid-template-columns: 2fr 1fr`, two auto rows, `gap: 16px`.
  - **Item 1** — the hero: `grid-row: span 2`, `height: 100%`, `min-width: 0`, background `#161616`, slot `ps-ab-plant1`.
    - ⚠️ **Implementation note.** This item originally carried `aspect-ratio: 16/9`; combined with `grid-row: span 2` it fed its own height back into the grid item's automatic minimum width via `min-width: auto`, inflating the `2fr` track and overflowing the canvas by 276px. It is now sized by its two-row span with `height: 100%` and `min-width: 0`. **If you reintroduce an aspect ratio on a row-spanning grid item, set `min-width: 0` on it.**
  - **Items 2 & 3**: `aspect-ratio: 4/3`, `min-width: 0`, background `#161616` — slots `ps-ab-plant2` (corrugation line), `ps-ab-plant3` (label finishing and rewinding).
  - **Walkthrough badge**, absolutely positioned inside item 1 at `left: 22px; bottom: 22px`: flex row, `12px` gap, background `rgba(12,12,12,.82)`, `1px solid #3A3A3A`, padding `12px 16px`, `white-space: nowrap`, `pointer-events: none`. A `9px` red dot then `360° walkthrough · press hall` in Plex Mono `11px`, `.1em`, uppercase, `#FFFFFF`.
    - In production this should be a **real play/enter affordance** for the 360° tour the brief asks for, not a passive label — make it a button that launches the walkthrough (Matterport, Pannellum, or a simple video lightbox).
- **Plant facts row** below, `16px` top margin: `repeat(4, minmax(0,1fr))`, `gap: 1px` on `#2A2A2A`, cells `#121212` padded `26px 22px`. Value `26px`/`800`/`-.03em`; label Plex Mono `11px`, `.08em`, uppercase, `#8F8B84`.

| Value | Label |
| --- | --- |
| 3 shifts | Production running |
| Offset · digital · flexo | Print processes in-house |
| To 9-ply | Corrugation capability |
| 2–5 days | Dispatch after approval |

### 9. Sustainability

- Padding `108px 44px 0`. **No section rule here** — this section leads with the image instead, breaking the rhythm deliberately before Careers picks it back up.
- Two columns `.85fr 1.15fr`, `72px` gap, centred.
- Left: `aspect-ratio: 4/5` image on `#E4E0D9` (slot `ps-ab-sustain`) — baled waste board / recycled substrate stock.
- Right: kicker `07 / Sustainability`, H2 `52px`/`800`/`-.035em`/`1.02` — **"Paper is a recyclable material. We treat it like one."** Body `17.5px`/`1.6`/`#55524D`, `max-width: 54ch`: *"Corrugated waste is baled and returned to the mill, make-ready sheets are reused for sampling, and we specify recycled board wherever the job allows it."*
- Four cells, `1fr 1fr`, `gap: 1px` on `#DBD7D0`, cells `#F4F2EE` padded `24px 22px`, `40px` top margin. Name `18px`/`700`/`-.02em`; body `14.5px`/`1.5`/`#55524D`.

| Name | Body |
| --- | --- |
| Waste board baled | Corrugated trim returned to the mill rather than landfilled. |
| Make-ready reused | Set-up sheets become client samples and internal proofs. |
| Recycled board specified | Offered as default wherever the job permits it. |
| Right-first-time | Fewer reprints is the largest single saving we make. |

- Below, a disclaimer line: Plex Mono `11px`, `.08em`, uppercase, `#A8A49C` — *"Claims to be confirmed with management before launch"*.

> ⚠️ **Placeholder — highest risk on this page.** The brief marks sustainability as **"(if applicable)"**. Every claim above is **invented as a plausible placeholder.** Environmental claims carry legal and reputational exposure. **Either verify each one with management or delete the entire section** before launch. Remove the disclaimer line once resolved; do not ship it.

### 10. Careers

- Background `#E9E5DE`, `108px` top margin, borders top and bottom, padding `96px 44px`.
- Kicker `08 / Careers`, H2 **"Come make things that ship."**, intro *"Designers here see their artwork on a pallet within the week. That is rare, and it is the reason people stay."*
- Two columns `1.25fr .75fr`, `64px` gap, `44px` top margin, `align-items: start`.
- **Left — role list**: `border-top: 1px solid #CFCAC2`, then each role a full-row link — flex, `space-between`, `24px` gap, padding `26px 8px`, `border-bottom: 1px solid #CFCAC2`, hover background `#F4F2EE`. Title H3 `25px`/`700`/`-.025em`; meta Plex Mono `11px`, `.08em`, uppercase, `#6F6C66`, `7px` top margin; trailing `→` in Plex Mono `13px`, `#E31E24`.

| Role | Meta |
| --- | --- |
| Packaging designer | Design studio · Haryana · Full time |
| Offset machine operator | Production · Haryana · Full time |
| Client coordinator | Sales & coordination · Haryana · Full time |
| Quality inspector | Quality & dispatch · Haryana · Full time |

- Disclaimer below at `22px` top margin, Plex Mono `11px`, `.08em`, uppercase, `#A8A49C`: *"Open roles are placeholders — to be supplied by HR"*.
- **Right**: `aspect-ratio: 1/1` image on `#DBD7D0` (slot `ps-ab-careers`, team candid), then a CTA at `16px` top margin — **Send us your portfolio** with trailing `→`, background `#121212`, `#FFFFFF`, `16px`/`700`, padding `20px 24px`, flex `space-between`; hover → `#E31E24`.

> ⚠️ **Placeholder.** The four roles are illustrative — HR must supply real openings. Needs an **empty state** for when nothing is open ("No open roles right now — send your portfolio anyway"). The portfolio CTA needs a real destination: a careers inbox or an upload form.

### 11. Final CTA band

Identical construction to the home page's red band (`#E31E24`, padding `104px 44px`, `1.1fr .9fr` columns, `72px` gap, H2 `72px`/`800`/`-.04em`/`.96`, three stacked action rows with trailing `→`) — **only the copy differs**, tuned to someone who has just read the whole story:

- H2: **"Now you know us. / Send us a job."**
- Body: *"Size, quantity, sheet or roll, and your artwork. A named coordinator replies within one working day."*
- Actions: **Start the smart RFQ** (white fill), **Download company profile** (outlined), **WhatsApp us** (outlined).
  - Note the middle action differs from the home page's — a visitor this deep is more likely to want the profile PDF than a callback.

### 12. Footer

Identical to the home page, except the **Company** column's links point to this page's anchors (`#story`, `#leadership`, `#quality`, `#careers`) rather than to `/about-us`. In production this is automatic — same footer component, real routes.

---

## State Management

- **`active`** (integer, default `0`) — the selected milestone index in the story timeline. The only interactive state on the page.
  - Production additions: read an initial value from a `?year=` query param; sync it back on change for shareable links.
- No other state. Everything else is static content.

## Content models

| Collection | Fields | Count | CMS-editable? |
| --- | --- | --- | --- |
| `milestones` | year, kicker, title, body, meta, **image** (add) | 7 | Yes — the client will add to this |
| `leaders` | role, name, bio, quote, image | 2 | Yes |
| `teams` | count, name, body, image | 4 | Rarely |
| `qcStages` | stage, name, body | 3 | No |
| `tests` | name, when | 9 | Rarely |
| `plantFacts` | value, label | 4 | Rarely |
| `sustain` | name, body | 4 | No (or delete) |
| `roles` | title, meta, + description & apply link (add) | 4 | **Yes — needs frequent editing by HR** |

`milestones` and `roles` are the two that genuinely need CMS editing. `roles` should also gain a `description` field and an `applyUrl`/inbox target — the prototype's list rows have nowhere to go.

## Interactions & Behaviour

**In the prototype**
- Story timeline year tabs — click to swap the detail panel; `.15s` background/colour transition on the tab.
- Hover states on every link, card and role row (documented above).
- Anchor-link navigation from footer and nav.

**Required in production**
- Timeline as a proper **tablist** with `role`/`aria-selected`/`aria-controls`, arrow-key nav, and a focus-visible ring (`outline: 2px solid #E31E24; outline-offset: 2px`).
- Per-milestone image with a ~200ms cross-fade on change.
- `?year=` deep linking.
- The 360° walkthrough badge wired to a real viewer.
- Scroll-reveal on section entry if used elsewhere on the site — keep it subtle and honour `prefers-reduced-motion: reduce`.
- Real routes for every link; the careers CTA needs a destination.
- An unused `@keyframes psRise` (fade + 10px rise) sits in the prototype's stylesheet — either use it for scroll reveals or strip it.

## Responsive behaviour

Not designed. Page-specific guidance on top of the home page README's notes:

- **Story timeline** is the hard one. Seven equal tabs at `21px` will not fit below ~1000px. Options, in order of preference: (1) horizontally scrollable tab strip with snap points, keeping the active tab in view; (2) an accordion — each year a collapsible row with its detail inline; (3) a vertical timeline with a rule down the left and all seven milestones expanded. Don't shrink the year type below `16px`.
- **Leadership**: 2 → 1 column; portrait above text, keep the `5/4` ratio.
- **Our people**: 4 → 2 → 1 column.
- **Quality**: stage cards 3 → 1; test list drops below them at full width. The name/stage flex rows already wrap acceptably.
- **Infrastructure photo grid**: collapse to a single column, hero first — the `2fr 1fr` span arrangement won't survive narrow widths.
- **Sustainability / Careers**: stack; in Careers put the role list first and the photo + CTA after it.
- **Type**: inner-page H1 `78px` → floor `36px`; section H2 `58px` → `30px`; timeline H3 `42px` → `26px`; leader name `34px` → `26px`. Use `clamp()`.

## Assets needed

13 image positions, all empty. **Real factory and team photography — no stock** (brief §8).

| Slot id | What belongs there | Aspect |
| --- | --- | --- |
| `ps-ab-story` | Archive photo per milestone — early studio, each press installation, first export consignment (**7 images needed, one per year**) | 4:3 |
| `ps-ab-amit` | Portrait — Amit Sharma on the shop floor | 5:4 |
| `ps-ab-sumit` | Portrait — Sumit Sharma in the design room | 5:4 |
| `ps-ab-di` | Studio and plant side by side — designers at screens, press behind | 1:1 |
| `ps-ab-team1` | Designers at screens | 4:3 |
| `ps-ab-team2` | Coordination desk | 4:3 |
| `ps-ab-team3` | Pressmen at the SM-74 | 4:3 |
| `ps-ab-team4` | QC bench with retained samples | 4:3 |
| `ps-ab-plant1` | 360° plant walkthrough — main press hall (**video or 360° asset**) | fills 2-row span |
| `ps-ab-plant2` | Corrugation line | 4:3 |
| `ps-ab-plant3` | Label finishing and rewinding | 4:3 |
| `ps-ab-sustain` | Baled waste board / recycled substrate stock | 4:5 |
| `ps-ab-careers` | Team candid — design room or shop floor | 1:1 |

**Archive photography is a real risk.** Photos from 2002–2014 may not exist at usable quality. If they don't, the timeline still works with the plant-today photography, or with scans of early artwork, invoices and machine delivery paperwork — which would arguably be more characterful. Ask before the shoot so it can be planned.

## Open items specific to this page

1. **Real milestone dates and events** for 2006 / 2010 / 2014 / 2017 / 2019 / 2022 — currently reconstructed. Highest priority on this page.
2. **Founder bios and quotes** — written, not spoken. Need Amit's and Sumit's approval.
3. **Certifications held** — dashed placeholder box must be replaced or removed.
4. **Real QC test list** — the nine tests are a reconstruction.
5. **Sustainability**: verify every claim or delete the section.
6. **Open roles** from HR, plus an application destination.
7. **Whether other Design India businesses need space** — the Design India section is where they'd go.
8. **Archive photography** — does it exist? Plan around the answer.
9. **One page or eight?** — confirm the consolidation decision noted under Overview.

## Files

| File | What it is |
| --- | --- |
| `Powerstik About.dc.html` | The About page prototype. **Visual reference only** — do not port its runtime. |
| `README-about-us.md` | This document. |
| `README.md` | The home page handoff — read first; carries all shared tokens and chrome. |

Keep `image-slot.js` and `assets/logo.png` alongside it to open the file locally.
