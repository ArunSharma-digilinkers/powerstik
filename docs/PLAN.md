# Powerstik website: build plan (Laravel + MySQL on cPanel shared hosting)

Source brief: `Powerstik_Website_Architecture.docx` (Sept 2026 draft).
The brief suggests WordPress or Webflow. We're building it in **Laravel 12 with a custom Blade admin panel** instead (no Filament). That still gives the team a place to add case studies and blog posts, as §9 of the brief asks.

**Decisions so far (2026-09-19):** Laravel 12 · custom Blade admin, no Filament · Linux shared hosting with cPanel, **no SSH** · WhatsApp = click-to-chat (`wa.me`) only · show **8 export countries** · CRM integration decided later (leads stored in the DB + emailed until then).

---

## 1. Hosting constraints and what they mean for the build

A cPanel shared host has no Node.js, no Supervisor, no Redis and no root access, and it often limits symlinks and `exec`. Every design choice below works within those limits.

| Constraint | Decision |
|---|---|
| No Node on server | Build the Vite assets locally or in CI, and ship `public/build/` with each release. The server never runs `npm`. |
| No Supervisor (queue daemons) | `QUEUE_CONNECTION=database`. One cPanel cron runs `schedule:run` every minute. The scheduler runs `queue:work --stop-when-empty --max-time=50`, which handles mail and image conversions (and a CRM webhook later, once a CRM is chosen). |
| No Redis | `CACHE_STORE=file` (or `database`), `SESSION_DRIVER=file`. Full-page response cache on marketing pages keeps the site fast anyway. |
| Document root | The Laravel app sits outside `public_html`. `public_html` either points at `app/public` (addon-domain docroot setting) or is a symlink to it. The fallback if symlinks are blocked is a thin `index.php` shim. |
| `storage:link` may fail | Keep uploads on a `public_uploads` disk rooted in `public/uploads`, so nothing depends on a symlink. |
| PHP version | Laravel 12 needs PHP ≥ 8.2. Select 8.3 or 8.4 in cPanel's *MultiPHP Manager* if offered. |
| PHP extensions | Needs `pdo_mysql, mbstring, openssl, fileinfo, gd or imagick, intl, zip, exif`. `intl` is sometimes off by default. Enable it in *Select PHP Version → Extensions*. |
| Email | SMTP through a cPanel mailbox (e.g. `noreply@powerstik.net`). Add SPF/DKIM in cPanel *Email Deliverability*. |
| Bandwidth and CPU | Self-host a compressed hero video (≤ 3–4 MB, 720p H.264 + WebM, poster image, no autoplay on slow connections). Longer films go on YouTube, embedded with a lite facade. Put Cloudflare (free) in front for CDN and caching. |

### Deployment

There is **no SSH**, so nothing can run `composer`, `npm` or `artisan` by hand on the server. Every release is therefore fully prebuilt:

1. `composer install --no-dev -o` and `npm run build` run on your machine (or in GitHub Actions), so `vendor/` and `public/build/` ship with the release.
2. Upload by **FTP**, either from GitHub Actions (FTP deploy action, changed files only) or as a zip extracted in cPanel File Manager.
3. **Post-deploy steps without a shell:** the app has a token-protected `POST /_ops/deploy` endpoint (the token is in `.env`, the endpoint is disabled when the token is empty). It runs `migrate --force`, `optimize:clear`, `optimize`, `view:cache` and `queue:restart`, then returns the output. The admin panel also gets a *System* page that runs the same actions behind the admin login.
4. **Cron** (cPanel → Cron Jobs, the one thing cPanel always allows): `* * * * * /usr/local/bin/php /home/<user>/powerstik/artisan schedule:run >> /dev/null 2>&1`. The PHP binary path is shown in cPanel.
5. First install: create the DB and user in cPanel *MySQL Databases*, upload `.env` through File Manager, then call the deploy endpoint to migrate and seed the first admin user.

---

## 2. Stack

| Layer | Choice | Why |
|---|---|---|
| Framework | Laravel 12, PHP 8.2+ | Chosen by you; fits the hosting limits |
| DB | MySQL 8 / MariaDB 10.6+ (whatever cPanel provides) | |
| Admin / CMS | **Custom Blade admin** at `/admin` | Own auth (session login, roles: admin / editor / sales), CRUD screens for every content type in §4, leads inbox, settings, redirects, media uploads. Built from the same Tailwind components as the site; no admin framework dependency. |
| Frontend | Blade components + **Tailwind CSS 4** + **Alpine.js** | Server-rendered, fast on mobile (many visitors arrive from WhatsApp links), good for SEO |
| Interactive forms | **Alpine.js** + normal form POST | Multi-step smart RFQ with conditional roll-spec fields and artwork upload. Server-side validation per step, so it still works without JS. No Livewire needed. |
| Motion | **GSAP + ScrollTrigger** (self-hosted) | Die-line folding, CMYK layer build-up, counters, map arcs (creative direction, §8 of the brief) |
| Images | `intervention/image` + small in-house `Media` model | WebP and responsive `srcset` sizes generated on upload in the admin |
| SEO | `spatie/laravel-sitemap`, custom `<x-seo>` component, JSON-LD | Organization, LocalBusiness, Product (battery paper), FAQPage, BreadcrumbList, Article |
| Redirects | `redirects` table + middleware, managed in admin | 301s from old powerstik.net URLs |
| Spam | Honeypot + Cloudflare Turnstile | No reCAPTCHA cookie banner burden |
| i18n (phase 2) | `spatie/laravel-translatable` JSON columns + `/{locale}` route prefix | Schema is multilingual-ready from day one; only English ships at launch |
| Analytics | GA4 + GTM, `generate_lead` events per conversion type | Quote conversion tracking (§9 of the brief) |

---

## 3. Sitemap → routes

| Section | URL | Notes |
|---|---|---|
| Home | `/` | 13-section scrolling narrative (§5 of the brief) |
| About | `/about`, `/about/{our-story, leadership, design-india, our-people, quality, infrastructure, sustainability}` | |
| Careers | `/careers`, `/careers/{job}` | Openings + application with CV upload |
| Capabilities | `/capabilities`, `/capabilities/{design-branding, label-printing, corrugated-packaging, printing-technologies}` | |
| Machine park | `/capabilities/machine-park` | Spec card per machine (DB-driven) |
| Battery labels | `/battery-labels`, `/battery-labels/{proprietary-paper, label-types, battery-packaging, clients}` | Flagship. "Request a sample" form |
| Industries | `/industries`, `/industries/{slug}` | 9 sectors, one shared template, DB-driven. Main SEO asset |
| Our work | `/work`, `/work/{slug}` | Filter by industry / product type / technology; case studies use Problem → What we did → Result |
| Global | `/global` | Export map, why partner with us, international enquiry (country + port) |
| Resources | `/resources/downloads`, `/resources/material-guide`, `/resources/faqs`, `/insights`, `/insights/{slug}` | Downloads can optionally ask for an email address first |
| Quote | `/get-a-quote` | Smart RFQ wizard |
| Contact | `/contact` | Map, department contacts, callback request |
| Client zone | `/client/*` | **Phase 2.** Only a placeholder login link at launch |
| System | `/sitemap.xml`, `/robots.txt`, `/admin` (custom Blade admin) | |

---

## 4. Data model (MySQL)

Content that marketing will edit lives in the DB. The fixed narrative copy on About and Capabilities stays in Blade, with editable blocks only where the team is likely to change it.

**Content**
- `industries`: name, slug, hero, challenges, what_we_supply, compliance_notes, seo fields, sort
- `capabilities`: slug, title, intro, body blocks (JSON), seo
- `machines`: name, make, model, category (offset/digital/flexo/corrugation), specs (JSON), photo, sort
- `projects` (portfolio + case studies): title, slug, client_id, is_case_study, problem, solution, result, gallery. Pivot tables link it to industries, `product_types` and `technologies`
- `clients`: name, logo, is_export, country_id, **show_logo (permission flag)**, sort
- `testimonials`: client, person, role, quote, video_url
- `timeline_events`: year, title, body, image (Our story)
- `team_members`: name, role, department, bio, photo, is_leadership
- `export_countries`: name, ISO code, lat/lng, blurb, clients (the map is drawn from this table)
- `downloads`: title, file, category, requires_email
- `faqs`, `glossary_terms`, `posts` + `post_categories`, `job_openings`
- `battery_paper`: a single record for the proprietary paper (brand name, properties, test results JSON, datasheet)
- `settings`: key/value store for phone, WhatsApp number, addresses, social links, counters (since, clients, countries, people, dispatch days, MOQ)
- `redirects`: from, to, code, hits

**Leads** (every submission gets one row in `leads`, with a `type` enum and a `payload` JSON)
- types: `quote`, `international`, `sample`, `callback`, `contact`, `download`, `job_application`
- quote-specific columns: product_type, size, quantity, form (sheet/roll), material, finish, required_by, roll specs (core size, winding direction, labels/roll, OD); artwork uploads go to private storage
- status workflow in admin: new → contacted → quoted → won/lost, with an assigned coordinator
- `LeadSubmitted` event → queued listeners: admin email and customer acknowledgement email. The thank-you page shows a pre-filled `wa.me` click-to-chat link. A CRM driver slot exists but is **off until you choose a CRM**.

**Phase 2 (tables designed now, built later):** `client_accounts`, `orders/jobs`, `invoices`, `artwork_proofs`.

---

## 5. Design system (to be built from your designs)

Built once, before any page, so every page you send can be assembled from the same parts:

- **Tokens:** brand colours (derived from the Powerstik logo), a restrained neutral scale, a "CMYK" accent set used only for print motifs, type scale (bold display + readable text face), spacing, radii
- **Print-world motifs as components:** `<x-reg-mark>`, crop-mark frame, colour-bar divider, corrugated-flute section divider (SVG)
- **Layout components:** header (utility bar + nav + persistent *Get a quote*), mega-menu, footer, floating WhatsApp button, breadcrumb, section header, CTA band
- **Content components:** counter strip, logo wall, service tile, industry card, case-study card, machine spec card, timeline, testimonial (text/video), FAQ accordion, download card, filter bar, form fields and wizard steps
- **Motion:** one shared GSAP module with reduced-motion fallbacks (`prefers-reduced-motion` disables all of it)

---

## 6. Build phases

| # | Milestone | Contents | Depends on |
|---|---|---|---|
| 0 | **Setup** | Laravel 12 + Tailwind/Vite scaffold, git repo, `.env` for local/prod, FTP deploy + `/_ops/deploy` endpoint, staging subdomain on the cPanel account | cPanel/FTP details |
| 1 | **Design system + global shell** | Tokens, header, footer, WhatsApp button, SEO component, base layout, 404/500 pages | Brand/logo, header/footer design |
| 2 | **Content models + admin** | Admin auth and layout, then migrations, models and CRUD screens for everything in §4, seeders with content from the brief | none (runs in parallel with 1) |
| 3 | **Home page** | All 13 sections, counters, logo wall, map arcs, hero video | Home design + media |
| 4 | **Lead engine** | Smart RFQ wizard, callback, contact, sample, international, download gate. Emails, admin inbox, GA4 events | Quote page design |
| 5 | **Inner pages** | About (7), Capabilities (5), Battery labels (4), Industries (template × 9), Work (listing + case study), Global, Resources, Careers | Designs arrive page by page |
| 6 | **SEO & launch prep** | Sitemap, JSON-LD, meta, OG images, 301 map from the current site, Lighthouse pass (target ≥ 90 mobile), accessibility pass, Cloudflare | Old URL list |
| 7 | **Launch** | Production deploy, DNS, GA4 check, Search Console, redirect verification | Management sign-off |
| P2 | Client zone, artwork approval, French/Russian, blog cadence | | |
| P3 | ERP job tracking, product configurator | | |

**How the page-by-page designs and media will be handled:** once you send a page design, I'll build it against the shared components, pulling content from the DB where §4 defines a model. Media goes in `resources/media-inbox/`. From there I optimise it (WebP/AVIF, responsive sizes, video transcode) before it enters the app. Nothing is committed un-optimised.

---

## 7. Open items

From the brief (§11), plus technical ones:

**Business**
1. Permission to show client logos and name export clients (the `show_logo` flag handles this per client)
2. Certifications held (ISO etc.)
3. Brand name and shareable test data for the battery label paper
4. Whether other Design India businesses need space on the site
5. Date of the plant photo/video shoot. Until then we use placeholders shaped like the final media.
6. Confirm quote-only (no public pricing). Assumed yes.
7. ~~8 vs 9 countries~~ → **8 export countries** (decided). The counter is a setting, so it can change without a deploy.

**Technical**
8. Hosting: **cPanel, no SSH** (decided). Still needed: PHP versions offered, FTP credentials, main or addon domain, whether a staging subdomain is allowed
9. CRM target for leads: **later** (leads kept in the admin inbox + emailed meanwhile)
10. WhatsApp: **click-to-chat only** (decided). Need the business WhatsApp number.
11. Old powerstik.net URL list for 301s (I can crawl the live site to build it)
12. Sender mailbox for form emails, and which inbox(es) receive each lead type
