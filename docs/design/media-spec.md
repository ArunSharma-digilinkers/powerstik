# Media spec

What shape and size every image on the site needs to be. Two kinds of media:
**admin uploads**, which editors put in through `/admin`, and **fixed media**,
which is committed to the repo and uploaded with `public/`.

## The pipeline (admin uploads only)

`App\Support\ImageStore` handles everything uploaded through the admin:

- Converted to **WebP at quality 82**. Send JPG or PNG; don't pre-convert.
- Scaled down to **2400px wide maximum**. Anything larger is resized, so a 6000px
  camera original is not worth uploading — it only costs upload time.
- Variants written at **480, 960 and 1600px** wide, and the public site serves
  them through `srcset`, so the browser picks the size the layout actually needs.
- **Nothing is cropped and nothing is padded.** The aspect ratio you upload is
  the aspect ratio the file keeps; the page then crops it with `object-cover` to
  fit the slot. Supply the right ratio or the crop will cut something you wanted.
- SVG is stored untouched, with no variants. Only client logos accept it.

So the rule for an editor is: **right ratio, at least the minimum width, and
never mind the file size** — the pipeline does the rest.

## Admin upload slots

"Min width" is the widest the slot is ever drawn, doubled for retina. Going over
it is free up to 2400. Going under it is the only way to get a soft image.

| Where | Field | Ratio | Min width | Recommended |
|---|---|---|---|---|
| Industries | Card photo | **4:3** | 880 | 1600 × 1200 |
| Industries | Page hero cutout | **~2:1, transparent PNG** | 1600 | 1980 × 994 |
| Our work | Cover image | **3:2** | 800 | 1800 × 1200 |
| Our work | Gallery | **4:3** | 800 | 1600 × 1200 |
| Insights | Cover image | **3:2** | 800 | 1800 × 1200 |
| Machines | Photo | **4:3** | 1280 | 1600 × 1200 |
| Leadership / team | Photo | **5:4** | 1080 | 1500 × 1200 |
| Story timeline | Photo | **4:3** | 1280 | 1600 × 1200 |
| Testimonials | Photo | **1:1** | 176 | 600 × 600 |
| Downloads | Cover thumbnail | **3:4** | 280 | 600 × 800 |
| Clients | Logo | any | — | see below |

Two notes on that table:

- **A project cover is used at three sizes**: the card on `/work`, the card on the
  home page, and the full-bleed hero on the case study itself, which is cropped to
  16:7. Compose so the subject survives losing the top and bottom — the hero crop
  is centred.
- **An industry's two images are not the same kind of picture.** The 4:3 card is a
  photograph, and it fills the grid cell on `/industries` under `object-cover`. The
  hero is a **cutout on transparency**, and it sits on the ink beside the headline at
  the top of the sector page — a photograph with a white ground will read there as a
  white rectangle floating on black. The hero column is hidden and the headline takes
  the full width while the field is empty, so a sector with only a card still looks
  finished.

### Client logos

Not photographs, so they follow their own rules:

- **SVG if you can get it, otherwise transparent PNG.** No white box behind it.
- **Trimmed tight** — no built-in margin. The site sets its own spacing, and a logo
  with padding baked in renders smaller than the ones beside it.
- Drawn **36–44px tall**, so a PNG wants to be **about 400px on its long edge**.
- Rendered greyscale at 75% opacity, colour on hover (`.logo-mark`), so a logo that
  only works in colour will read as grey most of the time.

## Fixed media (committed to the repo)

These are not uploaded through the admin. They are optimised by hand, committed,
and uploaded with the rest of `public/`. **Every one of them is optional**: the
page shows a tinted well instead until the file exists, so none of these block a
release. Export WebP at quality ~82.

### `public/media/home/`

| File | Ratio | Size | Notes |
|---|---|---|---|
| `hero.mp4` | 16:9 | 1920 × 1080 | The hero. Silent, 6.5s loop, 2.4 MB. Cut from `IMG_9026.MOV` |
| `hero.webp` | 16:9 | 1920 × 1080 | Poster for the video, and what shows without it. The same clip at 1.5s |
| `battery.webp` | 4:5 | 1200 × 1500 | Cutout on white. Trim tight — see below |
| `acid-test.webp` | 3:2 | 1400 × 933 | The test bench. Shared with `/battery-labels` |
| `export-map.webp` | 16:9 | 1400 × 787 | Redrawn map. Currently shows 9 of the 11 markets |
| `founders.webp` | 4:3 | 1600 × 1200 | **A studio portrait**, not the floor — a floor shot would still be better |

### `public/media/machines/`

Maker cutouts for the home teaser only, **never** for a machine record. Transparent
PNG converted to WebP, wide and short — **1200 × 420, about 2.9:1**. Drawn 92px tall.
`heidelberg`, `mark-andy`, `digital`, `corrugation`.

### `public/media/battery-labels/`

| File | Ratio | Size | Notes |
|---|---|---|---|
| `sheet.webp` | 16:10 | 1200 × 750 | Battery labels on the Heidelberg. Shipped at **1080 × 675** — the frame is only 1080 wide |
| `roll.webp` | 16:10 | 1200 × 750 | A finished reel on the Mark Andy, from the same clip as the hero |
| `packaging.webp` | 4:3 | 1200 × 900 | **Still missing** |

### `public/media/about/`

The list is `AboutController::MEDIA`. All 1600px on the long edge.

| File | Ratio |
|---|---|
| `design-india.webp` | 1:1 |
| `team-design`, `team-sales`, `team-production`, `team-quality` | 4:3 |
| `plant-hall.webp` | 4:3 |
| `plant-corrugation.webp`, `plant-labels.webp` | 4:3 |
| `sustainability.webp` | 4:5 (4:3 below `lg`) |
| `careers.webp` | 1:1 (4:3 below `lg`) |

## Seeder media

`database/seeders/media/` holds starter images that must reach production, attached
by `ContentSeeder` **only where the field is empty**. Same ratios as the admin slots
they fill: `industries/{slug}.jpg` (4:3), `industries/hero/{slug}.png` (~2:1 cutout,
PNG so the transparency survives), `team/{name-slug}.jpg` (5:4),
`clients/{name-slug}.png` (transparent).

There is no `machines/` folder here yet — a machine photographed locally has to be
re-uploaded in Admin → Machines on the server.

## Where the plant photography comes from

Everything on the floor was cut out of phone video and stills Arun supplied, with
AVFoundation (there is no ffmpeg or ImageMagick on this machine — `docs/` has no
toolchain to install, the Swift one-offs live in the session scratchpad). The sources
are in `resources/media-inbox/`, git-ignored.

| Slot | Source | Taken at |
|---|---|---|
| `home/hero.mp4` | `IMG_9026.MOV` (2160 × 3840, 23s) | 2.5–9.0s, 16:9 band from y 1312 |
| `home/hero.webp` | the same clip | 4.0s |
| `battery-labels/roll.webp` | the same clip | 12.0s |
| `battery-labels/sheet.webp` | `2345a5e0….MOV` (1080 × 1920) | 28s, band from y 450, light unsharp |
| `about/plant-labels.webp` | the same clip | 42s |
| `about/team-quality.webp` | `2AC0896D….MOV` (1080 × 1920) | 24s |
| `about/plant-hall.webp` | `IMG_7278.HEIC` (4032 × 3024) | left half only — see below |
| `about/careers.webp` | `IMG_5874.HEIC` (4032 × 3024) | — |
| `about/design-india.webp` | `e49bf162….JPG` (868 × 1280) | — |
| `home/founders.webp` | `FullSizeRender.HEIC` (1991 × 3370) | — |

Two things to know before cutting any more of these:

- **The first batch came through WhatsApp and was re-encoded to 576 × 1024**, which is
  under every size in the tables above. The replacements came off the phone directly.
  **Never accept plant media over WhatsApp.** Get the original by AirDrop or Drive.
- **Two of the three video sources carry a baked-in "Powerstik / packaging experts"
  wordmark** low in the frame, and it is not the wordmark this site uses. Every crop
  taken from them stops above it. Check for it in any new frame.
- **Every machine in the flexo hall carries a painted red swastika**, usually at eye
  level on the main housing — the marks from the machine puja. `IMG_7275`–`7284` is a
  sharp ten-shot set of that hall and nine of the ten show at least one. The site sells
  into eleven countries, so nothing goes out with one in frame. `plant-hall.webp` is the
  **left half** of `IMG_7278` for exactly this reason: the two marks on that press sit at
  x ≈ 2067 and x ≈ 3506 of 4032, so the crop stops at x = 2000. The same applies to
  `2345a5e0….MOV`, whose first five seconds are the puja itself. Before shipping any
  frame from the plant, scan it for saturated red (R > 120, R − G > 55, R − B > 50) and
  look at what the clusters are.
- **A video frame wants a light unsharp mask**, applied after the crop. 1080p handheld
  video is soft next to a photograph, and `sheet.webp` read as blurry on the page until
  the softest strip of the frame was cropped away and the rest sharpened. Measure the
  candidate bands (Laplacian variance at a common width) rather than eyeballing them —
  the sharpest band of a frame is rarely the one the subject is centred in.
- `about/design-india.webp` is the one file under its slot: 868px into a 566px well, so
  it is sharp at 1× and about three-quarters resolution on a retina screen. It is the
  largest version of that photograph that exists.

## Two traps

**Cutouts must be trimmed.** A product shot with a lot of white around it is drawn
inside a fixed-height well, so the built-in margin is subtracted from the space the
product gets and it renders small in a large empty box. `media/home/battery.webp` has
this problem today.

**A contain-well needs a definite row.** A `grid` well with a fixed height gives its
row `auto`, so a percentage `max-height` on the image has nothing to resolve against
and the image overflows at full size. Every such well carries `grid-rows-1` for this
reason — don't remove it. Where the image is photography rather than a cutout, use an
`aspect-[…] overflow-hidden` band with `object-cover` instead, as the cards do.

## Who and what is in these pictures

The site names a client only where `show_logo` is set, so what is legible in a
photograph matters as much as what it shows.

- **`home/hero.mp4`, `home/hero.webp` and `battery-labels/roll.webp`** run an **Amaron**
  web and reel. Amaron is a seeded client with `show_logo` on.
- **`battery-labels/sheet.webp` and `about/plant-labels.webp`** are battery warranty
  labels printed to generic type codes (N40 MF, NS40Z MF). No client brand is legible.
- **`about/plant-hall.webp`** runs a **Gold Star** web through the flexo line. Gold Star
  is a seeded client with `show_logo` on.
- **`about/careers.webp`** is about sixty identifiable staff at a cake-cutting outside
  the plant, April 2025. It is the client's own photograph of their own people, but it is
  the only image on the site that shows a crowd of faces — worth their nod.
- **`home/founders.webp` and `about/design-india.webp`** are Amit and Sumit Sharma.
  A third man appears beside them in the client's own 2026 social artwork
  (`media-inbox/8C1DD085….PNG`) — **he is their father.** He is not in `team_members`,
  the site does not name him, and that artwork is not used anywhere: it carries baked-in
  type, a tagline the site does not use and a green that is not the brand lime.
