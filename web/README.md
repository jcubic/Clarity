# Clarity Website

The website for the [Clarity GTK+ icon theme](https://clarity.pl.eu.org), built with PHP (Slim 4 + Twig 3) and deployed on Wasmer Edge.

## Features

- Landing page with icon previews, variant showcase, and install instructions
- Community theme gallery with uploads, likes, and download counts
- Upload wizard with SVG validation (works without JavaScript)
- Magic link authentication (no passwords)
- Icon rendering API — themes stored as SVG templates, icons rendered on the fly
- CLI integration — `clarity install @user/theme` downloads from the gallery

## Local Development

### Docker (recommended)

```bash
docker compose up
```

This starts the PHP dev server on [localhost:8081](http://localhost:8081) and a MySQL database. The `.env.local` file is already configured for Docker — no setup needed.

First run only — install dependencies and build icons:

```bash
docker compose exec web composer install
docker compose exec web make build
docker compose exec web python3 scripts/migrate.py
```

### Without Docker

Requires PHP 8.3+, MySQL, and Python 3.

```bash
make serve    # builds icons + starts PHP dev server on :8081
```

Edit `.env.local` to point at your local MySQL instance.

### Notes

When `RESEND_API_KEY` is not set, the upload flow shows the magic link URL directly in the browser instead of sending email.

### Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `DB_HOST` | Yes | MySQL host |
| `DB_NAME` | Yes | Database name |
| `DB_USERNAME` | Yes | Database user |
| `DB_PASSWORD` | Yes | Database password |
| `DB_PORT` | No | Database port (default: 3306) |
| `JWT_SECRET` | Yes | Secret for signing auth tokens |
| `RESEND_API_KEY` | No | Resend API key for magic link emails |
| `APP_DEBUG` | No | Set to `1` for debug mode |

## Project Structure

```
web/
  index.php                    # Slim app entry point — all routes
  Makefile                     # build, ci, serve, check, stats, validate-variants
  variants.json                # Hand-written variant metadata (source of truth)
  icons.json                   # Generated — icon path data extracted from SVGs
  icons-stats.json             # Generated — unique icons, symlinks, variant counts
  cover-icons.json             # Icon names used for gallery card previews
  css/style.css                # All CSS (dark theme, oklch colors)
  js/main.js                   # Progressive enhancement JS
  src/
    Database.php               # PDO wrapper, all DB queries
    Mailer.php                 # Resend API wrapper for magic link emails
    SvgConverter.php           # Converts uploaded SVG (placeholder circle → {{PATH}})
    SvgValidator.php           # Server-side SVG validation checks
  templates/
    base.html.twig             # Document shell
    pages/                     # Full page templates (home, upload, theme, 404)
    sections/                  # Landing page sections
    components/                # header, footer, svg-symbols
  scripts/
    build-icons.py             # Generates per-variant SVG icons from templates
    build-stats.py             # Counts icons/symlinks/variants → icons-stats.json
    extract-icons.py           # Extracts icon path data → icons.json
    validate-variants.py       # Validates variants.json matches template_*.svg files
    migrate.py                 # Database migrations
```

## Build & Code Quality

```bash
make build    # composer install + build icons + validate variants + build stats
make check    # php-cs-fixer (dry-run) + phpstan level 6 + validate-variants
make format   # auto-fix formatting
make lint     # phpstan only
```

## API Endpoints

| Endpoint | Description |
|----------|-------------|
| `GET /api/icon/{user}/{theme}/{icon}` | Render an icon through a theme template |
| `GET /api/theme/{user}/{name}` | Download a theme's SVG template |
| `GET /api/theme/{user}/{name}/version` | Get a theme's current version |
| `POST /api/validate` | Validate an uploaded SVG file |
| `GET /api/check-name` | Check if a theme name is taken |
| `POST /api/like/{user}/{name}` | Like a theme |

## License

Copyright (c) 2026 [Jakub T. Jankiewicz](https://jakub.jankiewicz.org/)

Licensed under [CC-BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0/).
