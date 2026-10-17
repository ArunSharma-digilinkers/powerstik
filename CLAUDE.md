# Powerstik website

Corporate website for Powerstik (a brand of Design India). Built with Laravel 12 and MySQL.
Full plan: `docs/PLAN.md`. Source brief: `Powerstik_Website_Architecture.docx`.

- Repo: https://github.com/ArunSharma-digilinkers/powerstik.git
- Local: `http://powerstik.test`, DB `powerstik` (root/root)

## Project rules (from Arun)

- **No PHP enums.** Use string/int class constants on the model instead, e.g. `User::ROLE_ADMIN` plus a `User::ROLES` label map.
- **No Filament, no Livewire.** The admin panel is our own Blade views at `/admin`, styled with Tailwind + Alpine.
- **Laravel 12.** Do not upgrade the major version.
- Designs and media arrive page by page. Build each page only from the design supplied for it.

## Hosting: Linux shared hosting with cPanel, **no SSH**

Nothing can be run by hand on the server, so every release has to work as it is uploaded.

- `vendor/` and `public/build/` are built locally or in CI and uploaded by FTP. The server never runs composer or npm.
- Migrations and cache rebuilds go through the token-protected `POST /_ops/deploy` (`OPS_TOKEN` in `.env`) or through Admin → System.
- Queue: `database`, processed by `schedule:run` from a single cPanel cron. There are no daemons.
- Cache and sessions: `file`. There is no Redis.
- Uploads go to the `public_uploads` disk (`public/uploads`). Don't rely on `storage:link`, because the host may block symlinks.

## Brand

- **Lime `#C7FF00` and ink `#221F20` on white.** Straight from the client brochure and the supplied wordmark; this replaced the red `#E31E24` scheme the home design shipped with. **Read `docs/design/brand.md` before touching colour.** All tokens are in `resources/css/tokens.css`.
- **Lime is a fill, not an ink.** It is unreadable as type on white (1.13:1) and brilliant on ink (15:1). On light grounds it fills blocks, chips, rules and buttons with ink type on top; on dark grounds it is the type colour. Small type on white never gets lime — use `.kicker`, `.chip` or `.label-mark`.
- Validation errors and destructive actions are **red** (`red-600`), not the brand colour. The focus ring is **ink**; sections on a dark ground carry `on-dark`, which flips it to lime.
- Fonts: Archivo (everything) and IBM Plex Mono (small-caps labels, kickers, spec lines), self-hosted via @fontsource.
- Square corners and no shadows. The only circles are the WhatsApp button, the dots and the avatars.
- Assets are in `public/images/brand/`: `logo.png`, `logo-white.png` (knockout for dark grounds) and `mark.png`, plus the three favicons. All are generated from `docs/design/brand/powerstik-logo-master.png` — see `docs/design/brand/README-assets.md`. Ask the client for SVGs. The wordmark has no tagline; "better ideas" belonged to the old artwork.
- Raw media inbox: `resources/media-inbox/` (git-ignored). Optimise media before it goes into the app.
- Design specs: `docs/design/` — `brand.md` for colour and patterns, `media-spec.md` for the ratio and minimum width of every image slot, then the per-page handoffs. **Every admin image field carries its ratio as help text**; change the two together.
- **Client facts come from the brochure** (`docs/design/brand/brochure-*.jpg`): the export map, the "Our Major Clientele" row, the Sonipat address and both phone numbers, the founders' roles and bios, "India's most trusted battery sticker" and "5,00,000 labels per day". These are the client's own words and ship in `ContentSeeder` / `config/settings.php`. Anything *not* in the brochure or the brief is still ours and belongs in `DemoContentSeeder`.

## Decisions

- WhatsApp: click-to-chat (`wa.me`) only.
- The site shows 11 export countries: the nine on the brochure's map plus Bangladesh and the USA, which Arun confirmed. The counter is a setting; the rows are seeded by `ContentSeeder`.
- CRM integration is undecided. Until then, leads are stored in the DB and emailed.
- Git: commit messages have **no trailer** (no Co-Authored-By or attribution lines). Default branch `main`.

## Public site architecture

- Layout: `<x-layouts.site>` holds the CMYK bar, utility bar, sticky header, footer and WhatsApp button (`components/site/*`).
- Nav, footer links and conversion URLs are in `config/site.php`. Pages that aren't built yet 404.
- `App\Support\Site` provides settings, the `wa.me` link, the `tel:` link and export countries.
- Section header pattern (numbered kicker, H2, intro over a 2px rule): `<x-site.section-header>`.
- Home: `HomeController` plus `home.blade.php`. The fixed marketing copy (tiles, process steps, machine teaser) lives in the view. Industries, countries, clients, featured projects, testimonials, leadership, counters (Settings → Proof strip) and the founders' note (Settings → Home page) come from the DB.
- Fixed home media, committed to the repo and uploaded with the rest of `public/`: `public/media/home/{hero.mp4, hero.webp, battery.webp, acid-test.webp, founders.webp, export-map.webp}` and `public/media/machines/{heidelberg, mark-andy, digital, corrugation}.webp`. Tinted wells show until a file exists, and the Global section falls back to the generated arc diagram when `export-map.webp` is missing.
- **All the plant photography is cut from phone video and stills in `resources/media-inbox/`** (git-ignored) with AVFoundation — there is no ffmpeg or ImageMagick on this machine. `media-spec.md` records the source clip and timestamp of every file, which brands are legible in each, and the two traps: the first batch came through WhatsApp and arrived re-encoded to 576 × 1024 (never accept plant media that way), and two of the source clips carry a baked-in wordmark that is not the site's, so every crop stops above it.
- Motion lives in `resources/js/site.js`: counters and arcs fire on scroll, and everything respects `prefers-reduced-motion`. Any element with `data-track="…"` sends a GA4 click event.
- Starter images that must reach production live in `database/seeders/media/` and are attached by `ContentSeeder` only where the field is empty: `industries/{slug}.jpg` → the 4:3 card, `industries/hero/{slug}.png` → the ~2:1 transparent cutout that sits on the sector page's dark hero, `team/{name-slug}.jpg` → a portrait, `clients/{name-slug}.png` → a client logo (transparent PNG), `machines/{name-slug}.jpg` → a 4:3 machine photograph. On the server, run it with the **Seed** action in Admin → System. `public/uploads` itself is git-ignored.
- About (`/about`, `/about-us` 301s to it): `AboutController` plus `about.blade.php`, spec in `docs/design/about-handoff.md`. It is one scrolling page with anchors (`#story`, `#leadership`, `#quality`, `#careers`…), not the sub-pages in the brief's sitemap. The story timeline is `timeline_events` (an Alpine tablist with arrow keys and `?year=`), leaders are `team_members` with `is_leadership` (plus `quote`), and roles are open `job_openings`. Teams, QC stages, the test list and plant facts are fixed copy in the view. Certifications, sustainability and the 360° walkthrough link come from Settings → About page, and each block stays hidden while its setting is empty, so no unverified claim ships. Photography goes in `public/media/about/*.webp` (the list is in `AboutController::MEDIA`); four of the ten are filled — `design-india`, `team-quality`, `plant-labels` and `careers`. Section numbers count only the sections shown.
- Inner pages: dark hero with `<x-site.breadcrumb>`. The red CTA band is `<x-site.cta-band>` with `<x-site.cta-link>` rows.
- **Battery labels (`/battery-labels`)**: `BatteryLabelsController` plus `battery-labels.blade.php`. One scrolling page with anchors (`#paper`, `#types`, `#formats`, `#packaging`, `#clients`, `#work`, `#sample`), like About rather than the brief's four sub-pages. Section numbers count only the sections shown. The four label types, the sheet/roll copy and the roll-spec vocabulary are fixed copy in the view; clients, export countries and the battery case studies (projects tagged with the Battery industry) come from the DB. The acid-and-heat protocol rows are the same three the home page carries — **change them in both places or neither**. The sample form at `#sample` is the same `enquiry/steps/{sample,contact}` partials as `/request-a-sample`, so the fields never drift apart; `config('site.sample_url')` points here.
- **The paper has no brand name and no data sheet on the page**, because both are still open with the client (PLAN.md §7.3). Nothing claims a GSM, a temperature rating or a certification. Photography goes in `public/media/battery-labels/{sheet,roll,packaging}.webp`; the roll shot and the test-bench photograph are shared with the home page from `public/media/home/`.
- **Industries (`/industries`, `/industries/{slug}`)**: `IndustryController` plus `industries/{index,show}.blade.php`. One shared template over every published industry, so the page count grows from the admin without a deploy; an unpublished sector 404s. Each sector's `excerpt`, `challenges`, `what_we_supply` and `compliance_notes` are edited in Admin → Industries, and **every one of those blocks stays hidden while its field is empty** — section numbers count only what is shown, so a half-filled sector never ships a heading with nothing under it. The `note` ("Acid & heat resistant") is the one fact held for every sector and drives the constraint band.
- The detail page's supply list is `product_types`, minus any product named after a *different* sector — that is what keeps "Battery labels" off the pharma page and on the battery one. Its CTA carries `?industry={name}` into the RFQ, where the industry select reads it.
- Editor rich text renders through `.prose-site` (in `app.css`); it is already purified on save, so the views print it with `{!! !!}`.
- **In production the sector bodies are empty until marketing writes them** — `DemoContentSeeder` holds the illustrative per-sector copy for local work only, and deliberately leaves `what_we_supply` blank on every sector so the product-type fallback stays under review.
- **Capabilities (`/capabilities`, `/capabilities/{slug}`, `/capabilities/machine-park`)**: `CapabilityController`. These are real sub-pages, not anchors like About, because the home page's "What we do" grid and the footer have linked those five URLs since launch. The narrative copy is fixed in Blade (PLAN.md §4) — there is **no `capabilities` table**. `CapabilityController::DISCIPLINES` holds the shared spine (hero, fact strip, deliverables) for each of the four; the part that makes a page its own is a partial in `capabilities/blocks/{slug}.blade.php`. Settings (`moq`, `dispatch_days`) are substituted into the fact strip at request time, not baked into the constant.
- **Machine park is the only DB-driven page here**, grouped by `Machine::CATEGORIES` with empty categories skipped, and specs shown only when an editor has filled them. It shows a machine's own admin-uploaded photo and **never maps the maker cutouts in `public/media/machines/`** — one of them is a different press from the one the brochure credits, and a single Speedmaster picture cannot stand for two. For the same reason the digital press is seeded with **no make**, matching the unbranded home card. See `docs/design/brand.md`.
- A machine photo sits in an `aspect-[4/3] overflow-hidden` band, the same idiom as the industry cards, and is cropped with `object-cover`. **Don't give it a fixed-height grid well**: the implicit row is `auto`, so `max-h-full` has nothing definite to resolve against and the photograph spills over the name and the spec list. Arun's SM-74 and CD-102 shots were the first images the well ever held and they exposed it.
- **Admin uploads live in `public/uploads`, which is git-ignored**, so a machine photographed and uploaded locally has no picture in production. The route that does reach the server is `database/seeders/media/machines/{name-slug}.jpg`, attached by `ContentSeeder::attachMachinePhotos()` where the field is empty. The SM-74 and CD-102 predate it and are still admin-only.
- **Global (`/global`)**: `GlobalController` plus `global.blade.php`. Markets, their notes and their blurbs come from `export_countries`; nothing on the page hard-codes the count, including the headline. The client's map artwork is shared with home, and the page **says in a caption that the artwork predates two of the markets** — it carries the brochure's nine pins while the site lists eleven (`docs/design/brand.md`; a redrawn map has been asked for). Named export clients need both `show_logo` and an `export_country_id`, so that section is hidden until a client gives permission. The international form at `#enquiry` reuses `enquiry/steps/{market,contact}`, and `config('site.international_url')` points here.
- The arc fallback for the export map is `App\Support\ExportMap::arcs()`, shared by home and global — it draws only when `public/media/home/export-map.webp` is missing.
- **Our work (`/work`, `/work/{slug}`)**: `WorkController`. Filtering is plain query strings (`?industry=&product=&tech=`) resolved against the three pivot tables and rendered on the server, so it needs no JavaScript and every filtered view is its own shareable URL. **Facet counts are calculated within the other active filters**, and a facet at zero is dropped — except the one currently selected, which stays so the filter can be undone. The slug column comes from the model's own table (`WorkController::slugColumn()`), never from the relation name: `productTypes` lives in `product_types`, and building the qualifier out of the relation name made `?product=` a 500 that the tests missed because the other two facets are spelled the same both ways. Unpublished sectors are filtered out of the eager loads too, because a project card falls back to naming its sectors.
- A case study prints Problem → What we did → Result **only when `is_case_study` is on and the block has been written**; a client is named only where `show_logo` is set. Each tag on a case study links back into the filtered listing.
- Pagination is `<x-site.pagination>` — Laravel's bundled views are rounded and shadowed, which the brand is not.
- **Resources**: `ResourcesController` (`/resources` hub, `/resources/faqs`, `/resources/material-guide`), `DownloadController` (`/resources/downloads`) and `InsightController` (`/insights`, `/insights/{slug}`). The hub only offers a section that has something behind it. FAQs are `<details>`/`<summary>` — no Alpine, so they open without JavaScript and the browser's own in-page search finds the answers — and the page emits FAQPage JSON-LD built from the same answers it prints. The material guide is `glossary_terms` with an A–Z jump bar over only the letters that have a term. Insights hides drafts and future-dated posts (`Post::scopePublished`) and emits Article JSON-LD.
- **A `requires_email` download is released through a short-lived signed URL** (15 minutes), and files a `download` lead which asks for name and email only — no phone. This is a marketing gate, not access control: files live on the public `public_uploads` disk, and their names carry a random suffix, so the page never exposes a path but the disk is still public.
- **`/resources/downloads` is empty in production** and is linked from home twice and from the footer, so its empty state is a working page — it explains, offers to email the profile, and points at the material guide and FAQs. Fill it in Admin → Downloads.
- `/client/login` is a **placeholder page**, not a 404: PLAN.md §3 puts the client zone in phase 2 but the utility bar and footer link it at launch.

## Client logos

The 14 logos in `database/seeders/media/clients/` were cut from the single sheet the
client supplied (`media-inbox/clientele.png`, a 2-column transparent grid) — see the
commit that added them. "Unique Energos" is the one name on the site with no logo on
that sheet; the views fall back to setting the name in type, so nothing breaks.

**`show_logo` ships `true`**, confirmed by Arun on 2026-09-25: Design India already
prints these logos in its own brochure, so the site is the same public act. Turn one off
in Admin → Clients if a client objects. `attachClientLogos()` never touches the flag —
it only fills an empty `logo`.

Logos render through **`.logo-mark`** (in `app.css`): greyscale at 75%, full colour on
hover. Fourteen competing brand colours would fight the lime, and hovering is there for
anyone who wants to look. There is no hover on touch, so phone visitors see the quiet
version throughout — a known trade-off of the chosen treatment.
- **Conversion pages (the lead engine).** `EnquiryController` serves all of them. `/get-a-quote`, `/request-a-sample` and `/international-enquiry` are one Alpine wizard (`enquiry/form.blade.php` plus a partial per step in `enquiry/steps/`), switched by `EnquiryController::INTENTS`; `/contact` carries the message form and the `#callback` panel on ink. The sample and international forms have pages of their own **only until `/battery-labels#sample` and `/global#enquiry` exist** — `config/site.php` points at them meanwhile.
- Every form posts to `POST /enquiry/{type}` and goes through `App\Support\LeadIntake`, which holds the rules per lead type, splits the submission between the `leads` columns and `payload`, stores artwork on the private `local` disk and queues `LeadReceived` (to Settings → Lead notification emails) and `LeadAcknowledged` (to the enquirer). A rejected submission bounces back with its errors in a **bag named after the lead type**, so the two forms on `/contact` never show each other's messages. `/thank-you/{type}` shows the reference number and fires the GA4 `generate_lead` event.
- Form parts: `<x-site.field>` (label, control, help, error — `conditional` takes an Alpine expression for a field that is only sometimes required, like the roll specs), `<x-site.form-errors>` and `<x-site.honeypot>`. Classes are `.site-input`, `.site-label`, `.site-choice` and `.site-error`. The wizard works without JavaScript: `x-show` leaves every step open and `x-cloak` hides the step nav, so it degrades to one long form.
- Spam control is the honeypot plus `throttle:8,1`. **A filled honeypot files the lead as `spam` rather than dropping it**, because the admin inbox hides spam by default and a real person is never silently lost. Turnstile (PLAN.md §2) is not built.
- `CaptureUtm` middleware parks `utm_*` and `gclid` in the session on any GET; `LeadIntake` writes them onto the lead.
- **No admin alert goes out until Settings → Contact has `lead_recipients` or `email` filled in.** The lead is still stored and the enquirer still gets their receipt.
- **`DemoContentSeeder`** loads the designs' illustrative case studies, testimonials, founders' quote, leadership bios and quotes, open roles and sustainability claims for local work only. It refuses to run in production. Never present that copy as real.

## Admin panel architecture

- Every content type is a subclass of `App\Http\Controllers\Admin\ResourceController`. The subclass declares `fields()` (built with `App\Support\Admin\Field`) and `columns()`, and the base class handles list, form, validation, uploads and relation syncing.
- **Adding a screen:** write the migration, the model, and a controller extending `ResourceController`, then add one line to `config/admin.php`. That entry registers the routes and the sidebar link.
- Images go through `App\Support\ImageStore`: WebP plus 480/960/1600px variants on the `public_uploads` disk. Use `ImageStore::srcset()` on the public site.
- Rich text uses the Trix editor and is sanitised by `stevebauman/purify` (allowed tags are in `config/purify.php`).
- Leads have their own screens (`LeadController`) and are never generic CRUD. Private attachments are stored on the `local` disk.
- Tests: `php artisan test` (SQLite in memory).
