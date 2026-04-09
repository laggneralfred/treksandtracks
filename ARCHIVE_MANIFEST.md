# Archive Manifest

This manifest lists the preservation-critical files and directories for the restored Treks and Tracks archive.

## Primary database source

- `treksand_IC_20100824_144554.sql`

## WordPress archive

- `public/blogs/wp-config.php`
- `public/blogs/wp-content/uploads/`
- `public/blogs/wp-content/flagallery/`
- `public/blogs/wp-content/themes/magazine-basic/`
- `public/blogs/wp-content/plugins/flash-album-gallery/`
- `public/blogs/wp-content/plugins/flagallery-skins/`
- `public/blogs/wp-content/plugins/vipers-video-quicktags/`
- `public/blogs/wp-content/plugins/wp-youtube-lyte/`
- `public/blogs/wp-content/plugins/revver-wordpress-video-plugin/`
- `public/blogs/wp-content/plugins/all-in-one-seo-pack/`

## Legacy theme assets that matter

- `public/blogs/wp-content/themes/magazine-basic/index.php`
- `public/blogs/wp-content/themes/magazine-basic/front-page.php`
- `public/blogs/wp-content/themes/magazine-basic/functions.php`
- `public/blogs/wp-content/themes/magazine-basic/header.php`
- `public/blogs/wp-content/themes/magazine-basic/footer.php`
- `public/blogs/wp-content/themes/magazine-basic/content-page.php`
- `public/blogs/wp-content/themes/magazine-basic/content-gallery.php`
- `public/blogs/wp-content/themes/magazine-basic/content-video.php`
- `public/blogs/wp-content/themes/magazine-basic/library/css/`
- `public/blogs/wp-content/themes/magazine-basic/library/js/`
- `public/blogs/wp-content/themes/magazine-basic/library/images/`

## WordPress content directories

- `public/blogs/wp-content/uploads/2010/08/`
- `public/blogs/wp-content/uploads/2010/09/`
- `public/blogs/wp-content/uploads/2010/11/`
- `public/blogs/wp-content/uploads/2010/12/`
- `public/blogs/wp-content/uploads/2011/01/`
- `public/blogs/wp-content/uploads/2011/02/`
- `public/blogs/wp-content/uploads/2011/03/`
- `public/blogs/wp-content/uploads/2011/04/`
- `public/blogs/wp-content/uploads/2012/02/`
- `public/blogs/wp-content/uploads/2012/03/`
- `public/blogs/wp-content/uploads/2012/07/`
- `public/blogs/wp-content/uploads/2013/01/`
- `public/blogs/wp-content/uploads/2013/03/`

## Laravel shell files that still matter

- `composer.json`
- `composer.lock`
- `Dockerfile`
- `.dockerignore`
- `routes/web.php`
- `resources/views/treksandtracks.blade.php`
- `resources/views/poem.blade.php`
- `resources/views/welcome.blade.php`
- `public/cover.css`
- `public/asset/img/background_old.jpg`
- `public/images/check.png`

## Preservation docs created during restoration

- `readme.md`
- `PUBLIC_CONTENT_MAP.md`
- `PUBLIC_CONTENT_INDEX.csv`
- `RESTORATION_HANDOFF_SUMMARY.md`
- `ARCHIVE_MANIFEST.md`

## Historical database artifact

- `tt_prod_v7.sql`

## Notes

- The WordPress archive under `public/blogs/` is the authoritative public experience.
- The Laravel shell is a wrapper, not the historical content source.
