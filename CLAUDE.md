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

- Red `#E31E24`, ink `#121212`, paper `#F4F2EE`, tagline "better ideas". These values come from the home page design and supersede the logo samples (`#EC2028`/`#231F20`). All tokens are in `resources/css/tokens.css`.
- Fonts: Archivo (everything) and IBM Plex Mono (small-caps labels, kickers, spec lines), self-hosted via @fontsource.
- Square corners and no shadows. The only circles are the WhatsApp button, the dots and the avatars.
- Assets are in `public/images/brand/`: `logo.png` and `logo-white.png`, the knockout version for dark grounds. Ask the client for SVGs.
- Raw media inbox: `resources/media-inbox/` (git-ignored). Optimise media before it goes into the app.
- Design specs: `docs/design/` (the home handoff README is the reference for tokens and patterns).

## Decisions

- WhatsApp: click-to-chat (`wa.me`) only.
- The site shows 8 export countries (the value lives in Settings).
- CRM integration is undecided. Until then, leads are stored in the DB and emailed.
- Git: commit messages have **no trailer** (no Co-Authored-By or attribution lines). Default branch `main`.

## Public site architecture

- Layout: `<x-layouts.site>` holds the CMYK bar, utility bar, sticky header, footer and WhatsApp button (`components/site/*`).
- Nav, footer links and conversion URLs are in `config/site.php`. Pages that aren't built yet 404.
- `App\Support\Site` provides settings, the `wa.me` link, the `tel:` link and export countries.
- Section header pattern (numbered kicker, H2, intro over a 2px rule): `<x-site.section-header>`.
- Home: `HomeController` plus `home.blade.php`. The fixed marketing copy (tiles, process steps, machine teaser) lives in the view. Industries, countries, clients, featured projects, testimonials, leadership, counters (Settings → Proof strip) and the founders' note (Settings → Home page) come from the DB.
- Fixed home media: drop files at `public/media/home/{hero.mp4, hero.webp, battery.webp, founders.webp}`. Tinted wells show until they exist.
- Motion lives in `resources/js/site.js`: counters and arcs fire on scroll, and everything respects `prefers-reduced-motion`. Any element with `data-track="…"` sends a GA4 click event.
- Starter images that must reach production (e.g. industry cards) live in `database/seeders/media/` and are attached by `ContentSeeder` only where the field is empty. On the server, run it with the **Seed** action in Admin → System. `public/uploads` itself is git-ignored.
- About (`/about`, `/about-us` 301s to it): `AboutController` plus `about.blade.php`, spec in `docs/design/about-handoff.md`. It is one scrolling page with anchors (`#story`, `#leadership`, `#quality`, `#careers`…), not the sub-pages in the brief's sitemap. The story timeline is `timeline_events` (an Alpine tablist with arrow keys and `?year=`), leaders are `team_members` with `is_leadership` (plus `quote`), and roles are open `job_openings`. Teams, QC stages, the test list and plant facts are fixed copy in the view. Certifications, sustainability and the 360° walkthrough link come from Settings → About page, and each block stays hidden while its setting is empty, so no unverified claim ships. Photography goes in `public/media/about/*.webp` (the list is in `AboutController::MEDIA`). Section numbers count only the sections shown.
- Inner pages: dark hero with `<x-site.breadcrumb>`. The red CTA band is `<x-site.cta-band>` with `<x-site.cta-link>` rows.
- **`DemoContentSeeder`** loads the designs' illustrative case studies, testimonials, founders' quote, the story milestones after 2002, leadership bios and quotes, open roles and sustainability claims for local work only. It refuses to run in production. Never present that copy as real.

## Admin panel architecture

- Every content type is a subclass of `App\Http\Controllers\Admin\ResourceController`. The subclass declares `fields()` (built with `App\Support\Admin\Field`) and `columns()`, and the base class handles list, form, validation, uploads and relation syncing.
- **Adding a screen:** write the migration, the model, and a controller extending `ResourceController`, then add one line to `config/admin.php`. That entry registers the routes and the sidebar link.
- Images go through `App\Support\ImageStore`: WebP plus 480/960/1600px variants on the `public_uploads` disk. Use `ImageStore::srcset()` on the public site.
- Rich text uses the Trix editor and is sanitised by `stevebauman/purify` (allowed tags are in `config/purify.php`).
- Leads have their own screens (`LeadController`) and are never generic CRUD. Private attachments are stored on the `local` disk.
- Tests: `php artisan test` (SQLite in memory).
