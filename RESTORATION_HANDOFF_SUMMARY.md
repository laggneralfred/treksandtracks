# Treks and Tracks Restoration Handoff

This repository is a preserved legacy website, not a modern app rewrite.

## What this project is

- A thin Laravel 5.8 shell provides the current landing pages.
- The authentic public archive is a WordPress 4.5.3 site under `public/blogs/`.
- The public-facing look and media history live primarily in WordPress pages, posts, uploads, and legacy plugins.
- The most important database source is `treksand_IC_20100824_144554.sql`.

## What was restored

- The archived WordPress site now renders locally.
- The Picture Library works through the original `flagallery` setup.
- The Video Library and the main Vimeo/YouTube pages render.
- Local uploads are present and resolve correctly.
- Legacy content URLs were repaired so the historic pages display with their original media.

## What was not missing

- The main WordPress uploads archive is intact.
- Gallery source files for the preserved `Patience` gallery are intact.
- The legacy page/post content needed for the public site is intact.
- The current limitations are historical, not data-loss problems.

## What still depends on third parties

- Vimeo embeds and links.
- YouTube embeds.
- Any legacy Flash-era gallery behavior that the browser can no longer execute natively.

## How to run locally

Build the legacy container:

```bash
docker build -t treksandtracks-legacy .
```

Run it:

```bash
docker run --rm -p 8080:80 -e APP_URL=http://localhost:8080 -v "$PWD":/var/www/html treksandtracks-legacy
```

If you need to restore the WordPress archive database separately, import `treksand_IC_20100824_144554.sql` into a local MariaDB instance and point `public/blogs/wp-config.php` at it through the existing `WP_DB_*` environment variables.

## Where the key content lives

- `public/blogs/` is the real public archive.
- `public/blogs/wp-content/uploads/` contains the preserved image archive.
- `public/blogs/wp-content/flagallery/` contains the gallery source images.
- `public/blogs/wp-content/themes/magazine-basic/` contains the historical theme.
- `public/blogs/wp-content/plugins/flash-album-gallery/` contains the gallery plugin.
- `public/blogs/wp-content/plugins/vipers-video-quicktags/` contains the legacy video shortcodes.

## What not to change casually

- Do not replace the WordPress theme.
- Do not upgrade Laravel or WordPress unless a compatibility fix is required to keep the archive viewable.
- Do not remove legacy plugin files just because they are old.
- Do not rename or flatten the `public/blogs/` structure.
- Do not rewrite the gallery or video content unless a file is proven missing.

## Known limitations

- Flash-era gallery behavior is preserved, but modern browsers cannot fully reproduce the original Flash experience.
- Some video content depends on Vimeo or YouTube availability.
- The restored site intentionally keeps its dated structure and output for archival fidelity.
