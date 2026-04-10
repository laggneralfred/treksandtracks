# Treks and Tracks Restoration Decision Memo

## Goal
Decide what to restore, what to mine for useful content, and what to ignore across the Treks and Tracks sources.

## Decision Summary

### 1. Canonical WordPress archive
- **What it is:** The restored public site at `public/blogs` in the current repository.
- **Decision:** **Restore and preserve**
- **Why:** This is the only complete WordPress public archive found. It has the full theme, plugins, uploads, gallery assets, and the working historical site structure.
- **Risks:** Low. Keep it as the source of truth and avoid mutating it unless a clearly broken public asset is found.

### 2. Main media archive
- **What it is:** `public/blogs/wp-content/uploads` and `public/blogs/wp-content/flagallery` in the restored site.
- **Decision:** **Restore and preserve**
- **Why:** This is the authentic photo and gallery archive for the historic public site. It is the main reason the restored site still feels original.
- **Risks:** Medium only if files are renamed, moved, or replaced. Preserve paths and old references.

### 3. Main DB source
- **What it is:** `treksand_IC_20100824_144554.sql`
- **Decision:** **Restore and preserve**
- **Why:** This is the WordPress database dump that matches the restored public archive. It contains the post/page content and attachment references needed for the site to render.
- **Risks:** Low. It is already being used as the working WordPress DB source.

### 4. Later mixed bundle
- **What it is:** `trecksandtracks_php7`
- **Decision:** **Mine, preserve separately, do not replace the canonical archive**
- **Why:** It is a later mixed backup bundle with some newer business/course-era material, but it is not a fuller WordPress public archive than the restored site.
- **Risks:** Medium. It is a mixed bundle, so content from different eras is interleaved. Pull only clearly useful public-facing material.

### 5. Partial WordPress shell in `trecksandtracks_php7/blogs`
- **What it is:** A small WordPress core shell without a complete `wp-content` tree.
- **Decision:** **Preserve separately, mostly ignore**
- **Why:** It is incomplete and does not add a usable WordPress archive by itself. It does not contain the media or theme assets that matter for public restoration.
- **Risks:** Low. Keep it only as forensic evidence or for configuration clues.

## What Should Be Mined

- Later public page text from the mixed bundle if it represents a post-2016 course or business-era site.
- Select documents in `attachment_files` from the mixed bundle if they are public handouts or brochures.
- Selected images in the mixed bundle if they clearly belong to later public offerings.
- Any WordPress-related SQL snippets only if they reveal unique later-era public content.

## What Should Be Ignored

- Partial WordPress shells that lack `wp-content`, uploads, or a matching database.
- Mixed business/admin dumps unless they clearly contain public page or media content.
- Duplicate WordPress SQL dumps already represented in the restored archive.
- Generic code scaffolds, admin-only files, and unrelated backend history.

## Practical Recommendation

1. Keep the restored WordPress archive as the canonical public site.
2. Mine the later mixed bundle only for clearly new public-facing content, documents, or images.
3. Do not merge the later mixed bundle wholesale.
4. Do not replace the restored site with the partial WordPress shell.

## Short Version

- **Restore:** canonical WordPress archive, main media archive, main DB source
- **Mine:** later mixed bundle for unique public text/docs/images
- **Ignore:** partial WordPress shell as a standalone site

