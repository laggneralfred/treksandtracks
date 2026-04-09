# Public Content Map

This is the restored public-facing experience for the archived Treks and Tracks site.

## Core Pages

| Title | WP ID | URL/path | Type | Media | Key media IDs / paths | Status | Notes |
|---|---:|---|---|---|---|---|---|
| Video Library | 2 | `/blogs/?page_id=2` | page | YouTube embed via Viper Video Quicktags | `QjMWBLFx8TQ` | Restored | Still uses legacy YouTube shortcode behavior |
| Picture Library | 31 | `/blogs/?page_id=31` | page | `flagallery` | gallery `gid=1`, gallery path `wp-content/flagallery/patience` | Restored | Flash-era gallery preserved with fallback output |
| Our Vessel "Patience" | 219 | `/blogs/?page_id=219` | page | Local uploads | `wp-content/uploads/2011/03/*` | Restored | Inline images now resolve locally |
| The Crew | 329 | `/blogs/?page_id=329` | page | Local uploads | `wp-content/uploads/2010/12/IMG_1669compressed.jpg`, `wp-content/uploads/2011/03/the-crew.jpg`, `wp-content/uploads/2011/03/16Dtrekstracks2.jpg` | Restored | Relative upload links were repaired |
| Baja Clip | 426 | `/blogs/?page_id=426` | page | Vimeo link | `21028628` | Restored | Historical link kept as-is |

## Baja Trip Posts

| Title | WP ID | URL/path | Type | Media | Key media IDs / paths | Status | Notes |
|---|---:|---|---|---|---|---|---|
| Baja Coast Sailing - Preview | 238 | `/blogs/?p=238` | post | Vimeo iframe | `21028628` | Restored | Embedded player renders |
| Full video of the Baja sailing leg is up! | 546 | `/blogs/?p=546` | post | Vimeo iframe | `21419422` | Restored | Embedded player renders |
| Farewell beautiful Puerto Vallarta ...until next time! | 552 | `/blogs/?p=552` | post | Local uploads | `wp-content/uploads/2011/04/PV-crew.jpg`, `PV-crew-2.jpg`, `PV-crew-3.jpg` | Restored | Local image references repaired |
| 7 Days at Sea | 576 | `/blogs/?p=576` | post | Local uploads | `wp-content/uploads/2011/04/IMG_3086c.jpg`, `IMG_3106c.jpg`, `IMG_3110c.jpg` | Restored | Local image references repaired |
| Welcome to mainland Mexico surfing | 590 | `/blogs/?p=590` | post | Local uploads | `wp-content/uploads/2011/04/IMG_3245jake.jpg` | Restored | Local image references repaired |
| Caleta de Campos and Nexpa | 594 | `/blogs/?p=594` | post | Vimeo + local uploads | `22139559`, `wp-content/uploads/2011/04/surf-3.jpg` | Restored | Mixed remote/local media |
| Daily life at sea | 775 | `/blogs/?p=775` | post | Vimeo link | `24079671` | Restored | Remote video dependency remains |

## Patagonia / Later Expedition Posts

| Title | WP ID | URL/path | Type | Media | Key media IDs / paths | Status | Notes |
|---|---:|---|---|---|---|---|---|
| Treks and Tracks new Expedition- Patagonia horse supported rock climbing | 956 | `/blogs/?p=956` | post | Vimeo iframe | `29846147` | Restored | Large whitespace in post content is historical |
| Patagonia Expedition Movie Part 1 of 5 | 1175 | `/blogs/?p=1175` | post | Vimeo shortcode | `41335151` | Restored | Shortcode renders |
| Patagonia Expedition Movie - part 2/5 | 1180 | `/blogs/?p=1180` | post | Vimeo shortcode | `41859427` | Restored | Shortcode renders |
| Patagonia Expedition Movie - part 3/5 | 1183 | `/blogs/?p=1183` | post | Vimeo shortcode | `42228869` | Restored | Shortcode renders |
| Patagonia Expedition part 4/5 | 1196 | `/blogs/?p=1196` | post | Vimeo shortcode | `43251676` | Restored | Shortcode renders |
| Horse supported mountaineering expedition- 5 part movie series | 1237 | `/blogs/?p=1237` | post | Vimeo shortcode | `41335151`, `41859427`, `42228869`, `43251676` | Restored | Multi-part movie series |
| A day of rock climbing with Treks and Tracks | 1244 | `/blogs/?p=1244` | post | Vimeo shortcode | `42439927` | Restored | Video-focused post |

## Preservation Notes

- The archive is WordPress-first; Laravel is only the wrapper shell.
- The gallery experience is authentic but Flash-era.
- The Vimeo and YouTube media are mostly remote embeds, so they depend on external availability.
- Local uploads and flagallery assets are present and are the primary preserved visual archive.

