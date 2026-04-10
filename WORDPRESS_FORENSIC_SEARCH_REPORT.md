# WordPress Forensic Search Report

Scope: Treks and Tracks-related WordPress material across the local OneDrive tree.  
Goal: identify WordPress archives, uploads, themes, plugins, SQL dumps, and backup bundles that might contain public content, photos, videos, or database history.

## Summary

The canonical restored archive at:

- `/mnt/c/Users/alfre/OneDrive/Documents/code/treksandtracks.com/public/blogs`

is still the only **complete** WordPress public archive I could verify.

The later mixed bundle at:

- `/mnt/c/Users/alfre/OneDrive/websites/digital_ocean_projects/trecksandtracks_php7`

contains only a **partial WordPress shell** under `blogs/` plus SQL dumps. It does **not** include the `wp-content` media/theme/plugin tree needed to stand alone as a richer public WordPress site.

## What was found

### Canonical restored WordPress archive

Path:

- `/mnt/c/Users/alfre/OneDrive/Documents/code/treksandtracks.com/public/blogs`

Evidence:

- complete `wp-admin`, `wp-includes`, and `wp-content` tree
- `wp-content/uploads` is present and intact
- `wp-content/flagallery` is present and intact
- `wp-content/themes` includes the live theme set
- `wp-content/plugins` includes the legacy plugin set used by the public site
- SQL-backed public pages/posts already restored

Size / scale:

- about `408M`
- about `5,441` files

This remains the primary source of truth for the public archive.

### Candidate mixed bundle WordPress shell

Path:

- `/mnt/c/Users/alfre/OneDrive/websites/digital_ocean_projects/trecksandtracks_php7/blogs`

Evidence:

- `blogs/index.php`
- `blogs/wp-admin/*`
- `blogs/readme.html`
- `blogs/license.txt`
- `blogs/.htaccess`
- no `blogs/wp-content`
- no `blogs/wp-includes`
- no `wp-config.php`

Size / scale:

- about `3.8M`
- about `199` files

This is a WordPress core shell only. It is not a usable archival copy of the public site by itself.

### Candidate SQL dumps

#### `treksand_IC_20100824_144554.sql`

Path:

- `/mnt/c/Users/alfre/OneDrive/websites/digital_ocean_projects/trecksandtracks_php7/treksand_IC_20100824_144554.sql`

Evidence:

- `CREATE TABLE \`wp_commentmeta\``
- `wp_*` tables present
- WordPress content and comments are present
- size about `49 MB`

This is the same WordPress-era dump already present in the canonical restored repo, so it does not appear to add unique public content.

#### `treksand_IC_20100824_152827.sql`

Path:

- `/mnt/c/Users/alfre/OneDrive/websites/digital_ocean_projects/trecksandtracks_php7/treksand_IC_20100824_152827.sql`

Evidence:

- phpMyAdmin dump
- database name `treksand_IC_20100824_152827`
- table structures shown are `phpbb_*`, not WordPress
- size about `360 KB`

This is not a WordPress archive.

#### `treksandtracks_tretra7.sql`

Path:

- `/mnt/c/Users/alfre/OneDrive/websites/digital_ocean_projects/trecksandtracks_php7/treksandtracks_tretra7.sql`

Evidence:

- mixed business/admin-style dump
- size about `7.6 MB`
- no WordPress table markers were identified in the spot checks

This is not a WordPress public archive.

## Completeness verdict

### Candidate WordPress portion

The candidate WordPress portion is:

- partial WordPress shell only
- missing `wp-content`
- missing media tree
- missing theme/plugin archive
- missing `wp-config.php`

It is therefore **not** a fuller or better WordPress archive than the restored site.

### Compared with the canonical restored archive

The canonical restored archive is clearly richer:

- has the actual public media tree
- has the gallery assets
- has the complete legacy plugin set
- has the theme set
- has the live DB-backed content

The candidate does not appear to contain unique WordPress media or unique WordPress-era pages worth merging into the restored archive.

## WordPress-specific items worth preserving

These are worth keeping as forensic references, but not as replacements:

- `/mnt/c/Users/alfre/OneDrive/websites/digital_ocean_projects/trecksandtracks_php7/blogs`
  - partial WordPress shell
  - useful only as evidence that another WP install once existed in the mixed bundle
- `/mnt/c/Users/alfre/OneDrive/websites/digital_ocean_projects/trecksandtracks_php7/treksand_IC_20100824_144554.sql`
  - WP dump, but duplicated by the canonical archive
- `/mnt/c/Users/alfre/OneDrive/Documents/code/treksandtracks.com/public/blogs/Search-Replace-DB-master`
  - useful archive maintenance tool, not source content
- `/mnt/c/Users/alfre/OneDrive/Documents/code/treksandtracks.com/public/blogs/wp-content/uploads`
  - primary public photo archive
- `/mnt/c/Users/alfre/OneDrive/Documents/code/treksandtracks.com/public/blogs/wp-content/flagallery`
  - gallery-specific image archive

## Final verdict

**Is the candidate WordPress portion worth recovering?**

No, not as a standalone WordPress archive.

**Does it contain unique WordPress content not already in the restored archive?**

No unique WordPress public content was confirmed.

**Is it worth merging any WP files or DB content?**

Not as a primary action. The candidate dump is duplicated by the restored archive, and the candidate `blogs/` tree is incomplete.

**Should it replace the restored site?**

No. The restored WordPress archive remains the canonical public site.

