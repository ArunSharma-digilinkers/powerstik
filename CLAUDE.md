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

- Red `#EC2028`, ink `#231F20`, tagline "better ideas".
- Assets are in `public/images/brand/`. Raw media inbox: `resources/media-inbox/` (git-ignored). Optimise media before it goes into the app.
- Fonts: Montserrat (display) and Inter (text), self-hosted via @fontsource.

## Decisions

- WhatsApp: click-to-chat (`wa.me`) only.
- The site shows 8 export countries (the value lives in Settings).
- CRM integration is undecided. Until then, leads are stored in the DB and emailed.
