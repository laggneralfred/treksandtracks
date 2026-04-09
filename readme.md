# Treks and Tracks Preservation Notes

This repository is an archived legacy web application with two layers:

- a thin Laravel shell used for the landing pages
- a preserved WordPress 4.5.3 archive under `public/blogs/`, which is the authentic public site

## What this codebase really is

- Laravel version: `5.8.32`
- PHP target in `composer.json`: `^7.1.3`
- Historic public site: WordPress 4.5.3 with the `magazine-basic` theme
- Media-heavy experience: WordPress pages, posts, and the `flash-album-gallery` / `vipers-video-quicktags` plugins

## Where the real site lives

The visitor-facing archive is in:

- `public/blogs/`

Important public pages:

- `Picture Library` (`page_id=31`)
- `Video Library` (`page_id=2`)
- `Our Vessel "Patience"` (`page_id=219`)
- `The Crew` (`page_id=329`)
- `Baja Clip` (`page_id=426`)

## Runtime

Use a legacy-compatible PHP runtime. A local Docker image is provided for this purpose.

Build:

```bash
docker build -t treksandtracks-legacy .
```

Run:

```bash
docker run --rm -p 8080:80 -e APP_URL=http://localhost:8080 -v "$PWD":/var/www/html treksandtracks-legacy
```

## WordPress database

The most important dump is:

- `treksand_IC_20100824_144554.sql`

That dump restores the public WordPress archive content and gallery metadata.

`tt_prod_v7.sql` is a separate older business database and is preserved for historical reference, but it is not the core public site.

## Legacy compatibility choices

- `public/blogs/wp-config.php` uses environment overrides for local DB settings.
- `public/blogs/wp-config.php` also sets `WP_HOME` and `WP_SITEURL` from the active host so the archive can render locally without rewriting the database again.
- Legacy WordPress warnings are suppressed so the archived pages do not show modern runtime noise.

## Known limitations

- Flash-era gallery and video behavior is preserved where the assets still exist, but modern browsers will not execute Flash players.
- Some video content is still remote-hosted Vimeo or YouTube embeds.
- This repo intentionally preserves dated structure and plugin code instead of modernizing it.

## Preservation rule

Do not upgrade Laravel or WordPress unless a compatibility fix is absolutely required to keep the archive viewable.
