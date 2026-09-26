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
| Industries | Page hero photo | **16:7** | 2400 | 2400 × 1050 |
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
- **An industry needs both photos.** The 4:3 card is the grid on `/industries`; the
  16:7 hero is the band at the top of the sector page, and the band is hidden
  entirely when it is empty, so a sector with only a card still looks finished.

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
| `hero.mp4` | 16:9 | 1920 × 1080 | The hero. Silent, short loop, well under 5 MB |
| `hero.webp` | 16:9 | 1920 × 1080 | Poster for the video, and what shows without it |
| `battery.webp` | 4:5 | 1200 × 1500 | Cutout on white. Trim tight — see below |
| `acid-test.webp` | 3:2 | 1400 × 933 | The test bench. Shared with `/battery-labels` |
| `export-map.webp` | 16:9 | 1400 × 787 | Redrawn map. Currently shows 9 of the 11 markets |
| `founders.webp` | 4:3 | 1600 × 1200 | The founders on the floor |

### `public/media/machines/`

Maker cutouts for the home teaser only, **never** for a machine record. Transparent
PNG converted to WebP, wide and short — **1200 × 420, about 2.9:1**. Drawn 92px tall.
`heidelberg`, `mark-andy`, `digital`, `corrugation`.

### `public/media/battery-labels/`

| File | Ratio | Size |
|---|---|---|
| `sheet.webp` | 16:10 | 1200 × 750 |
| `roll.webp` | 16:10 | 1200 × 750 |
| `packaging.webp` | 4:3 | 1200 × 900 |

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
they fill: `industries/{slug}.jpg` (4:3), `industries/hero/{slug}.jpg` (16:7),
`team/{name-slug}.jpg` (5:4), `clients/{name-slug}.png` (transparent).

There is no `machines/` folder here yet — a machine photographed locally has to be
re-uploaded in Admin → Machines on the server.

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
