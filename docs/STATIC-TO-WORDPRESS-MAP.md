# Static → WordPress migration map

Phase 4: Ravo Film only imported into local WordPress; all other business content remains reference data. The Phase 3 mapping overrides below supersede earlier Pro-oriented field suggestions. Baseline: `static-approved-v1` / `19e1be22a7952f655003abab5cc1344ac35fbb9b`. Target choices and media eligibility are in [migration plan](WORDPRESS-MIGRATION-PLAN.md); detailed field properties are in [CMS model](CMS-DATA-MODEL.md).

## Pages, components and source files

| Current source | Future equivalent | Preservation / migration instruction |
|---|---|---|
| index.html; build_site.home() | front-page.php and Home fields | Hero → Clients → What we do only. Preserve approved collage; no new selected-work section. |
| work.html; build_site.work()/portfolio_entry() | archive-project.php / template-parts/project-card.php | Project query, identical 4:3 grid/classes/data attributes, real-project counts and populated filters. No duplicate Work Page route. |
| work/short-form-brand-content.html | /work/short-form-brand-content/ via single-project.php | Existing descriptive capability identity; client/date unknown. |
| work/tid-group.html | /work/tid-group/ via single-project.php | Existing website screenshots, summary and native digital treatment. |
| work/stories-in-the-moment.html | /work/stories-in-the-moment/ via single-project.php | Existing portrait treatment; no fabricated campaign/client. |
| work/ravo-film.html | /work/ravo-film/ via single-project.php | Verified website creation; contain listing crop; no technology/date/results invention. |
| work/ai-assisted-content.html | /work/ai-assisted-content/ via single-project.php | Keep AI-assisted identification; not physical-shoot evidence. |
| build_site.project_detail() | single-project.php / project-media.php | Summary, service links, genuine media, public documentation note, Next loop and context actions. |
| services.html; services_page() | page-services.php / service-panel.php | Ordered visible service terms, all eight initial terms, existing anchored fallback and one enhanced active panel. |
| about.html; about() | page-about.php | Fixed composition populated from controlled Page fields. |
| contact.html; contact() | page-contact.php | Conversational form and fixed field definitions; endpoint remains inactive until backend phase. |
| 404.html; not_found() | 404.php | Existing copy/actions, real HTTP 404; no homepage catch-all. |
| build_site.header()/nav()/brand() | header.php / route helper | Skip link/menu/logo/current-page state; navigation fixed to Work/Services/About/Contact. Resolve home_url/permalinks, not .html strings. |
| build_site.footer() | footer.php / Site Settings | One canonical contact source, approved light logo and current Privacy disclosure. No added CTA section. |
| build_site.arrow_icon()/link() | helpers.php | Same inline long/compact SVG geometry, semantics, classes and event hooks. |
| build_site.image()/media()/image_dimensions | project-media.php / media helpers / WP attachment metadata | Intrinsic sizes, lazy/eager behavior, contextual alt/caption, native ratio and approval guard. No private media URL fallback. |
| assets/css/global.css | theme assets/css/global.css | Exact brand/fonts/tokens, reduced motion and accessibility rules. |
| assets/css/components.css | theme assets/css/components.css | Header/footer/CTA shared rules unchanged. |
| assets/css/pages/{home,work,services,about,contact,project,404}.css | same relative theme paths | Conditional enqueue; no new visual system. |
| assets/js/global.js, motion.js | same relative theme paths | Module loading, menu/token/reduced-motion behavior; public .page-* class mapping. |
| assets/js/home.js, data.js | home enhancement + minimal PHP-rendered label configuration | Replace only the runtime site.json request; avoid shipping whole site/contact configuration. Retire unused data loader only after parity QA. |
| assets/js/work.js | same enhancement with absolute permalink data | Preserve query/history/from propagation; counts derive from eligible projects. |
| assets/js/services.js | same enhancement with PHP-rendered panels | Anchor/button conversion, keyboard behavior, hashes, ARIA and mobile selection visibility. |
| assets/js/project.js | same enhancement with generated service slug whitelist | Back-to-Work context and immutable taxonomy identity. |
| assets/js/contact.js | same initial safe enhancement; future same-origin handler integration | Keep URL validation/removal/error associations; backend adaptation later, not automatic activation. |
| assets/images/showmakers-logo.webp and showmakers-logo-light.png | theme-owned brand images | No recoloring, logo redesign or staff brand controls. |
| assets/images/hero/showmakers-hero-collage.{png,webp} | theme seed artwork; optional approved Home attachment | Original PNG retained; WebP delivery 2048×768 with current CSS right crop/mask. Import only if field model chosen. |
| assets/images/work/* approved / clients/* | Media Library approved attachments | Path-to-ID import map; do not require staff to edit paths. Reuse approved file across placements. |
| assets/images/platforms/* | theme-owned approved SVG icons | Fixed trusted icon keys. Keep supplied provenance/license notices; no unrestricted SVG upload permission. |
| assets/images/placeholders/* | local test/theme fallback assets | Never import development fixture as a real published Project. Fallback must be clearly labelled. |
| assets/images/sources.json | private import manifest / provenance metadata | Dimensions to attachment metadata; source document/page evidence private; no public media rights notes. |
| assets/fonts/made_tommy/*, futura-pt/* | same relative theme asset directories | Current local OTF/TTF and weights preserved, user web rights confirmed; no conversions or public third-party sharing in this phase. |
| scripts/build_site.py | static baseline renderer; PHP replaces runtime responsibility later | Do not remove static recovery version. Python build not required for WP content updates. |
| scripts/check_site.py | existing static baseline checks + later WP integration checks | Keep baseline checker; its .html/JSON assertions are not tests of CMS routes. --production currently fails on Project 06 by design. |
| package.json | static baseline scripts; optional theme checks later | No React/Node framework build dependency. Do not run static regeneration against future CMS output. |
| vercel.json / .vercelignore | static Preview configuration only | Not a PHP hosting plan; no Vercel changes in Phase 1. |
| references, prototype, review, .agents, .vercel, local .env | outside public WordPress/theme deployment | No restricted media, private PDFs, credentials, skills, review or old prototypes shipped. |

## projects.json — every source field

| Source field | Destination / transformation |
|---|---|
| id | Immutable private import source ID → project post ID lookup. |
| slug | Core post_name; preserve current routes and contact-context slug. |
| title | Core post_title. |
| client | Confirmed fallback name text unless documented Client-ID mapping exists; no text-based automatic join. |
| summary | Short summary meta, or one controlled core excerpt; choose one canonical location. Recommended core excerpt. |
| services[] | service term relationships by stable source slug → term ID map. |
| listingThumbnail | Listing attachment + usage alt/caption + fit enum; authoritative listing source. |
| thumbnail | Legacy fallback only if listingThumbnail absent; no duplicate editable thumbnail field. |
| heroMedia | Hero attachment + type/alt/caption. Fallback to thumbnail only if approved and verified. |
| media[] | Ordered gallery usage rows; exclude repeated hero src for public gallery as renderer currently does. Preserve contextual alt/caption/fit. |
| year | Nullable verified year; all current values null; no invented full date. |
| order | menu_order; preserve current order and Next sequence. |
| published | Core status; true still imports as draft initially pending import validation. false stays draft. |
| featured | Boolean meta retained; no current homepage Selected Work output. |
| presentation | Existing landscape/portrait/digital enum; controls native-media styles. |
| detailUrl | Not staff URL meta: use get_permalink; legacy value retained in private redirect manifest. |
| projectType | Optional bounded text, currently Website for Ravo Film; no new category UI. |
| source | Private provenance/rights notes. |
| documentationNote | Public controlled documentation text. |
| isPlaceholder | Private guard; development-only placeholder excluded from all public output. |
| developmentOnly | Private guard, absent normalizes false; true fixture kept local-only or unpublished. |
| mediaStatus | Private permission enum, absent/new defaults pending, never implicit approved. |

Nested media objects currently carry `type`, `src`, `alt`, `caption`, optional `fit`. Map src through approved attachment lookup, not string replacement. Keep fit only where applicable; empty caption remains absent output. Global attachment approval metadata applies to Service/About/Hero/Client uses too.

| Current project ID → slug | Import/publication treatment |
|---|---|
| brand-content → short-form-brand-content | Genuine approved work; local draft then eligible publish after QA. |
| tid-group → tid-group | Genuine approved website; retain confirmed client-name fallback. |
| short-form → stories-in-the-moment | Genuine approved work; portrait presentation. |
| ravo-film → ravo-film | Genuine verified website; never associate to the Ravo marquee record without documentation. |
| ai-content → ai-assisted-content | Genuine approved AI-assisted material; keep disclosure. |
| brand-showcase → brand-showcase | Tommy factual record remains unpublished/restricted; no restricted source photo import, no public single. |
| layout-placeholder-06 → project-06 | Omit from production import; local test fixture only or guarded draft. Its two test service assignments are not real capability evidence. |

## services.json — every source field

| Source field | Destination / transformation |
|---|---|
| id, slug | service term slug; equal in all eight current records; store private original ID if needed for importer. |
| title | Term name. |
| shortDescription | Intro term meta. |
| description | Term description, rendered as controlled text. |
| deliverables[] | Ordered capability rows; free/core variant can use one line per entry. |
| platforms[] | Ordered text entries. Current renderer suppresses summary platforms when subsection passages exist. |
| formats[] | Optional ordered text rows, currently Event Management. |
| subsections[] | Ordered title/text/icon-key rows; icon path mapped to trusted theme asset. |
| media[] | Approved attachment usage rows (type/src/alt/caption), preserving order. |
| relatedProjects[] | Validate IDs against Projects and services[] during dry run; derive future Related Work from taxonomy, no editable duplicate list. Report conflicts instead of silently choosing labels. |
| order | Integer term meta; index numbering derived. |
| published | Term visible flag, not WP post status; all eight initially visible. |
| source.document, source.pages[] | Private provenance fields/import evidence. |

Eight slugs remain unchanged; broader editorial groups are not taxonomy terms. Existing media is absent for several services including Event Management; leave empty, not stock/generated replacement.

## clients.json — every source field

| Source field | Destination / transformation |
|---|---|
| id | Stable slug and private source ID map to client post ID. |
| name | Client title. |
| image | Approved Logo attachment ID; preserve original artwork/colors. |
| order | menu_order. |
| published | Core draft/publish plus controlled visibility; imported logos are confirmed approved, publication after validation. |
| projects[] | Documented project-ID references resolved to canonical Project Client-ID relationship, reverse list derived. All 18 arrays currently empty; infer nothing. |

No current website URL field exists; optional future Client website starts empty. No client classification based on brand-name similarity.

## site.json — all top-level branches and nested content

| Source | Destination / treatment |
|---|---|
| name | Company Name Site Setting. |
| logo, logoLight | Approved theme-owned brand assets; no everyday logo customization. |
| navigation[].label/url | Fixed controlled route list; labels preserved and .html targets replaced by WordPress home/archive/page permalinks. No arbitrary menu structures. |
| hero.headline[], introduction, description | Controlled Home Page fields; two headline slots. |
| hero.action.label/url | Editable label; target becomes fixed Work archive URL. |
| home.clientsTitle/servicesTitle/servicesAction | Home section labels; content itself derives from Client/Service records. |
| work.title/introduction | Archive-scoped content settings. |
| work.note | Preserve optional source text; currently unrendered, no new section. |
| services.title/introduction/indexTitle | Services Page fields; panels from terms. |
| about.title/introduction | About Page intro. |
| about.philosophyTitle/philosophyText | Fixed philosophy fields. |
| about.approachTitle/approachText/media | Fixed human-led fields and approved attachment usage metadata. |
| about.steps[].title/text | Four controlled working-step pairs; not Flexible Content. |
| about.source | Private provenance metadata. |
| contact.title/introduction | Contact Page opening, controlled line breaks. |
| contact.email/phone/address/whatsapp/socialLinks[].label/url | Canonical reusable Site Settings, not repeated page fields. Empty WhatsApp/social omitted. |
| contact.form.title/note | Contact form supporting fields. Current prototype banner is hardcoded and must retain wording/state initially; resolve to one state-driven notice during approved conversion. |
| contact.form.endpoint | Developer-controlled WordPress same-origin handler configuration later; remains unset initially. |
| contact.form.submitLabel/notSureLabel/servicePlaceholder | Named controlled copy fields; options derive from visible Services + not-sure sentinel. |
| contact.form.states.* | Named UI messages; preserve all six states, truthful success semantics and fallback canonical email. |
| contact.form.fields[].name/type/required/autocomplete | Theme-owned fixed field schema; preserve current names/order/limits rather than expose a form builder. |
| contact.form.fields[].label/placeholder | Named staff-editable copy fields; empty placeholders stay absent. |
| footer.contactLabel/contactAction | Legacy unrendered keys; no new footer CTA. Keep in static baseline only unless future request needs them. |
| footer.copyright/note/privacyNotice.label/text | Reusable Site Settings and eventual approved Privacy reference. |
| labels.* | Minimal controlled UI strings with code defaults; PHP serializes only required public strings. |
| notFound.title/message/homeLabel/workLabel | Scoped 404 text settings, fixed targets. |

Hero collage source is outside site.json today (renderer hardcoded); seed the proposed Home artwork field explicitly from the approved WebP. No artwork generation, JSON edit or import now.

## Import procedure to implement later

1. Dry-run source validation: unique IDs/slugs, known service IDs, media whitelist/permission, current genuine counts, no restricted filenames/public references; report undocumented client links and relationship inconsistencies.
2. Build deterministic source-ID maps; create Services before Projects, Clients before optional documented project links. Upload only approved material using a private source-path → attachment-ID manifest, deduplicated across usages.
3. Create controlled page/settings records and five genuine Project drafts. Keep nulls null, missing arrays empty, and public/private notes separate. Retain source IDs/legacy routes privately for reconciliation.
4. Validate every relation/media placement and public output, then publish approved genuine records. Exclude Project 06 and restricted entries from counts, related queries, Next, contact context, REST/search/feed/sitemap.
5. Document imported record/attachment counts and exceptions. Import is explicit and one-time; re-run only against approved IDs with backup and conflict report. Never overwrite staff edits silently.
6. Test .html redirect mappings and query/hash preservation on staging before any production cutover. Content/database/uploads are backed up separately from theme Git commits.

## Phase 3 mapping overrides — free ACF

This is the active mapping profile; earlier Gallery/Repeater/Options mappings remain future upgrade intent. No content import was performed.

| Static source concept | Actual Phase 3 destination / handling |
|---|---|
| projects.title / slug | `project` title / post_name; `/work/{slug}/` |
| projects.client | `client` Post Object ID when documented; otherwise verified `client_name`, never display-name matching |
| projects.summary / year | `short_summary` Textarea / optional `project_year`; no invented dates |
| projects.services[] | Actual `service` term assignments via ACF `services`; eight individual services remain canonical |
| projects.order | `sort_order` numeric post meta, superseding proposed menu_order |
| projects.listingThumbnail / fit | `listing_thumbnail` Image ID / `listing_fit` cover or contain |
| projects.heroMedia | `hero_media` Image ID; video requires a later approved extension |
| projects.media[] | Five optional `project_image_1`…`project_image_5` IDs, preserving ordered additional media; omit repeated hero; do not silently drop source items above the temporary limit |
| projects.featured / published / mediaStatus | `featured` boolean / core post status / `media_status` enum; no redundant visible flag |
| clients.name / logo / visible / order / website | Client title / `client_logo` ID / `visible` / `sort_order` / `website_url`; `logo_status` independent from project permission |
| services intro / description / capabilities / platforms / formats | `service_intro`, `service_description`, `capabilities`, `platforms`, `formats`; plain Textarea, list fields one item per line |
| services.media[] | One `service_media` ID available; preserve any additional source items separately until compatible expansion is approved |
| services rich platform subgroups | Newline names cannot encode all richer descriptions/icons; retain source structured data until an explicit migration preserves them |
| services order / visibility | Term `sort_order` / `visible` |
| site.json global settings | Existing structured data/theme code for now; future Pro Options Page, one canonical source |
| rights metadata | Attachment `media_status` + private `internal_media_note`; Project approval separate; no restricted originals in public uploads |

`archive-project.php` currently retains the Phase 2 Work shell, not a CMS grid. `/work/` is now the Project archive route; the existing empty Work Page is retained locally for recovery until Phase 4. `/services/`, `/about/`, `/contact/` remain Page routes; Home keeps its existing static presentation. Rewrite rules flush only at activation/deactivation, not every request. No production redirects have been configured.

Phase 4 output must resolve attachment IDs with WordPress APIs, apply approved-media helpers, preserve native ratios and source alt/captions, escape all public values, use numeric `sort_order` with deterministic ID tie-breaks, and keep query-string service navigation separate from public taxonomy archives. Refer to [current field model](CMS-DATA-MODEL.md#phase-3-implemented-specification--free-acf-authoritative) before importing.

## Phase 4 actual mapping — Ravo Film only

| Source / verified instruction | Local WordPress result |
|---|---|
| projects.json `ravo-film` title/slug | Project **18**, Ravo Film; `/work/ravo-film/`; draft validated before local publication |
| User's explicit Ravo Film Client instruction | New admin-only Client **17**, `source_id=ravo-film`; selected by Project Post Object. Not automatically joined to JSON Client `ravo` |
| `services[]` website-digital-solutions | Existing `service` term **7**, saved as actual term assignment; no extra services |
| summary | `short_summary`, exact verified website-creation sentence |
| year / featured / order | Empty `project_year` / false `featured` / numeric `sort_order=4` |
| listingThumbnail / heroMedia / repeated media item | One approved attachment **16**, original 789×1276 WebP, reused for listing/Hero/core featured image; contain listing; duplicate Hero omitted from five additional slots |
| original alt / caption | Existing descriptive alt retained; empty caption remains absent |
| mediaStatus | Project and attachment separately approved |
| id / source / presentation / documentationNote | Private `source_id`, `_showmakers_provenance`; bounded `presentation=digital`; public factual `documentation_note` disclaimer. These import metadata values have no new Admin design controls |
| Client logo / website URL | Empty; no verified association to the separate Ravo marquee asset or documented URL assumed |

CMS now owns local Work archive/card/detail, counts, service→Related Work availability, Next eligibility and Contact's project context list. Public records come only from published approved real Projects. JSON Projects are not fallback cards and are not merged into the collection; this prevents duplicate Ravo Film and leaked placeholders. Before publication the archive is empty; after publication it has one record. Static `work/ravo-film.html` remains outside the LocalWP serving root as a reference, not a second CMS route.

Home uses its untouched approved static template. About stays its existing Phase 2 shell with original static About as reference; it was not migrated. Services uses the approved generated-HTML reference snapshot with original Explorer JS, native WP URLs and CMS-derived Related Work actions. Service body/media term fields stay empty in this pilot; existing source JSON remains the authority for future migration. Contact uses the approved static form snapshot plus a CMS-only public context payload and a theme-only source-path adapter; endpoint stays empty and no submission is sent. No Home Clients/What We Do/Hero CMS conversion occurred. All original JSON and generated static files remain unchanged.

Project→Services resolves term slugs, never display-text joins. Project→Contact uses `project=ravo-film&service=website-digital-solutions`; query variables for the Project CPT and Service taxonomy are disabled to avoid hijacking that Page route. Filtered Work→Project uses `from=website-digital-solutions`; Back returns to the same filter, direct visits return to All. Next omits self when there is no other eligible record. Clean rewrites were refreshed once locally; no production redirect map is applied.

Only the Ravo image was copied into Media Library. Service reference images/icons remain approved theme asset links, not imported attachments or new Project records. The original PNG/WebP/font/JSON sources are preserved; no restricted original, unrelated asset library or placeholder was uploaded. Future staging packaging must materialize the allowlisted symlinks; local database/uploads require their own backup.

Read-only pilot checks and the deliberately one-record local migration helper are in `scripts/`. Import helper refuses existing source/slug matches to protect edits; review existing IDs rather than rerun/overwrite. See the [Phase 4 report](WORDPRESS-MIGRATION-PLAN.md#phase-4-local-pilot-completion--2026-10-05) for QA and next-phase boundaries.

## Phase 5 actual mapping — all five real projects

This section supersedes earlier one-project runtime/count descriptions. Local WordPress is now runtime authority for these five records; JSON and generated HTML remain unchanged migration/reference sources.

| Source ID → native slug | Project ID / order | Client ID | Service term ID | Listing / Hero / supporting attachment IDs |
|---|---|---|---|---|
| brand-content → short-form-brand-content | 21 / 1 | None verified | 5 | 20 / 20 / none |
| tid-group → tid-group | 26 / 2 | 23 TID Group | 7 | 24 / 24 / 25 in project_image_1 |
| short-form → stories-in-the-moment | 29 / 3 | None verified | 5 | 28 / 28 / none |
| ravo-film → ravo-film | 18 / 4 | 17 Ravo Film | 7 | 16 / 16 / none |
| ai-content → ai-assisted-content | 32 / 5 | None verified | 6 | 31 / 31 / none |

All five locally published/approved; every source year remains empty. Source summaries, Featured booleans, listing fit, presentation, documentation notes, ordered supporting media and service assignments preserved. TID Client alone created during Phase 5, deliberately no logo/URL; Ravo Film retained unchanged. No display-name logo joins. Attachment 20/24/25/28/31 are the only new Media Library files; all preserve original bytes and approved source alt/caption. No supporting-media capacity overflow.

Work counts/Related Work/contact payload and Next derive only this eligible CMS set. All5, Media2, Website2, AI1; order 1→2→3→4→5→1. Source URLs remain outside LocalWP, not duplicate public pages. Main is still the static approved baseline. Home marquee/client set, Services Explorer body/media, About reference, Contact prototype/global settings remain for later dedicated phases.

Only runtime refinement: `showmakers_cms_image` portrait max-width now bounds native cap against available width, fixing the inherited TID mobile overflow without altering original source/CSS. See the Phase 5 completion report in WORDPRESS-MIGRATION-PLAN.md for missing verified fields, IDs, QA and phase boundaries. Git does not back up LocalWP content/uploads.


## Phase 6 local Services migration completion — 2026-10-05

Services Explorer and Home What We Do now share `showmakers_visible_services()` over the existing `service` taxonomy. No runtime service-copy JSON fallback. Original HTML/JSON and `services-reference.php` remain inactive migration/reference material. Existing CSS, Explorer JavaScript, typography, CTA artwork, Hero and Client marquee are unchanged.

| Order | Service | Term ID | Stable slug | Visible | Approved Media Library IDs |
|---|---|---:|---|---|---|
| 01 | Business Consulting | 3 | business-consulting | Yes | None; typography-led |
| 02 | Social Media Marketing | 4 | social-media-marketing | Yes | None; typography-led |
| 03 | Media Production | 5 | media-production | Yes | 20, 28 |
| 04 | AI-Enhanced Content Production | 6 | ai-enhanced-content-production | Yes | 31; AI-assisted caption retained |
| 05 | Website & Digital Solutions | 7 | website-digital-solutions | Yes | 25, 24 |
| 06 | Event Management | 8 | event-management | Yes | None; typography-led |
| 07 | SEO & SEM | 9 | seo-sem | Yes | None; typography-led |
| 08 | Influencer Marketing | 10 | influencer-marketing | Yes | None; typography-led |

Source: approved `data/services.json`, reconciled against existing terms and verified media permissions. Migrated Service Intro, Service Description, Capabilities, Platforms, Formats where present, Service Image, Sort Order and Visible. Capabilities/Platforms/Formats store one plain-text item per line; blank lines and surrounding whitespace are removed and output escaped. No invented content or filler images. Reused five existing approved attachments, preserving original file bytes, Project metadata and attachment captions. Per-service image captions preserve the approved usage-specific wording, including AI disclosure.

Free ACF remains 6.8.10. Additive fields preserve the approved richer Services presentation without a custom repeater: optional `service_media_2`, `service_media_caption`, `service_media_2_caption`, and four optional textareas `platform_facebook_description`, `platform_instagram_description`, `platform_xiaohongshu_description`, `platform_tiktok_description`. Existing field names remain stable. The four platform identities/icons and editorial order remain fixed approved presentation; descriptions are CMS-managed. This bounded interim approach does not offer arbitrary platform subgroups or unlimited images. Future Pro: capabilities/platforms/formats can migrate to Repeaters; two service images/captions to a Gallery or image/caption Repeater; four platform passages to a platform Repeater. Global settings remain outside ACF until an explicitly approved Options Page migration.

Visibility: only `visible=1` terms appear in Explorer, Home index, Work filters, public Project service labels/links, and Contact choices/context. A hidden term's Project assignments are retained internally, and those Projects remain eligible in All Work; hiding a Service never deletes relationships. Invalid/hidden Work filter contexts fall back to All through existing validation. Direct hidden Explorer anchors use the first visible service rather than expose the hidden panel. Public Project service wrappers are omitted if no visible service remains. Sort Order is numeric, with term ID as deterministic tie-breaker; staff should retain unique 1–8 values.

Related Work is shown only when real eligible published Projects exist for that term: All=5, Media Production=2, Website & Digital Solutions=2, AI-Enhanced Content Production=1; other five=0 and omit the action. Existing approval/placeholder/development safety checks remain active. No Project/Client relationships changed. Services→Work uses stable `?service=slug`; Contact uses the same visible terms and preselects the slug, preserving the prototype notice and empty submission endpoint. Home renders only names, numbering and `/services/#slug` links—no descriptions/media.

Admin term edit screen reviewed with populated Social Media Marketing content: plain-language labels, one-item-per-line instructions, optional media/caption guidance, ordering and visibility explanations. No Pro field types, gallery, repeater, options page or custom serialization introduced.

QA: all eight panels at 1440/1024/390 compared to approved static Services: matching text/headings, image rectangles and panel dimensions (floating-point rounding only), loaded media, no horizontal overflow. Home same eight ordered links and typography, no overflow at all three widths. Media/Website direct anchors and Home→Website tested; all eight Service→Contact preselected correctly; Media and Website→Related Work selected their two-project filters; Ravo→Website and browser back/forward preserved context. Keyboard ArrowDown/Home/End movement, Enter activation, visible 3px focus, aria-pressed, live status and heading structure passed. Existing reduced-motion media-query branch retained; runtime `?motion=reduce` mode showed the active panel with no console errors. Native OS reduced-motion setting was not changed. Server-rendered anchor/panel fallback retains all content without JavaScript; no information depends on animation.

Read-only `check_wp_services.php` verifies every source field/media mapping, newline sanitation, five unchanged Projects, hidden-term behavior and restricted-media exclusion using metadata filters removed after checks (no database test writes). `check_wp_pilot.php`, `check_wp_frontend.py` (44 public assets and five real detail routes), PHP syntax checks and original `npm run check` passed. Original static development placeholder stays reference-only; no placeholder imported into LocalWP.

Local-host-gated `migrate_wp_services.php` seeds existing empty editorial fields once, validates identities and approved original attachments, writes a local temporary term-metadata checkpoint, and refuses already migrated/staff-edited records. It is not a runtime synchronization tool; future edits happen in WordPress. Database content and uploads are local and are not included in Git.

Remaining static: Services page introductory/global copy, Home Hero/Client marquee/global copy, About shell, Contact prototype copy and global settings. Work/Project and Service editorial content are CMS-driven. No About, backend, SEO, production, Vercel, DNS, Supabase or main changes. Stop after Phase 6. Recommend Client marquee migration next, reusing Clients CPT/logo/visibility/order after matching the verified 18-logo reference; do not start automatically.


## Client / Project / Selected Clients architecture clarification — 2026-10-05

This clarification supersedes earlier automatic Client-marquee assumptions and the Phase 6 recommendation to begin marquee migration next. It is documentation only: Phase 6 is complete; no Client field configuration, logo records, runtime, imagery, motion or accessibility behavior changes here.

Current Home Selected Clients remains the separately curated static source with the same approved 18 logos, order, sizing, pause and reduced-motion behavior. Do not connect it to Client CPT during Services, Home or Project migration. A public Project neither adds its Client to the marquee nor removes a Client with no public Projects. Client CPT is the canonical company/brand entity, currently used primarily for Project relationships.

Permanent relationships:

- Client → optional Projects, derived from Projects referencing a Client; never maintain a second manual Project list.
- Client → optional explicit Selected Clients membership, independent of public Project count, service, Featured state or publication.
- Project → project-specific listing thumbnail, Hero, image slots and future video; never automatically substitute a Client Logo.
- Client Logo Permission ≠ Project Media Permission. Approved Samsung logo with restricted Samsung photography is valid. Approved Project media does not approve a Client Logo.

Future dedicated Client/Marquee phase requires explicit authorization. Target fields: WordPress title (Client Name), existing `client_logo`, existing `logo_status` labelled Logo Approval Status (approved/pending/restricted), dedicated `show_in_marquee` labelled Show in Selected Clients (default false), dedicated `marquee_sort_order` Number, optional `website_url`. Existing generic `visible` and `sort_order` are not automatically reinterpreted as marquee controls. Keep the current stable permission field rather than rename it merely to match the illustrative `logo_approval_status` name.

Future renderer selects eligible published Client records only when Show in Selected Clients is true, logo status is approved and an approved logo exists, ordered by Marquee Sort Order. Attachment safety checks must also permit the logo; never infer Project permission from logo approval. Skip missing/pending/restricted logo items safely and explain their state in Admin. A Project without a Client logo/marquee presence remains publishable; a Client with an approved logo and zero Projects is valid.

Future staff workflow: Clients → Add New → name, clean approved logo, Logo Approval Status, Show in Selected Clients, Marquee Sort Order → Publish. No HTML/JSON/CSS paths or public Project required. Theme normalizes logos in consistent contained containers, preserves aspect ratio, spacing and supplied colors, and never automatically recolors. PNG/WebP supported; SVG only if safely permitted by the installation. Exact equal image dimensions are unnecessary.

Later reconcile the approved 18-logo source against existing Client records, reusing Ravo Film/TID Group only when the identity is documented, with no duplicate records or inferred logo associations. Import/link approved logos, set explicit membership and existing order, then switch the runtime only after equivalent visual/interaction QA. Original source stays until validation. Free ACF is sufficient for these basic future fields; a Pro upgrade may improve gallery/admin UX but must not collapse the separation of permissions or membership.

No Client/Marquee phase starts automatically. About/Home content work may be considered separately after user review while this static marquee remains untouched.


## Client / Selected Clients marquee migration completion — 2026-10-05

This explicitly authorized dedicated phase supersedes the earlier current-static boundary. Home Selected Clients now uses the Client CPT only; it remains independent of Projects. No redesign or other Home content migration. Approved static HTML/JSON/logo assets remain reference material and are not combined with CMS records at runtime.

Client total: **19 published**. Existing **Ravo Film ID 17 reused**, after direct user confirmation that the approved Ravo logo is the same company. Its canonical title, slug and Project relationship remain unchanged; logo alt text stays the approved **Ravo**. Existing **TID Group ID 23 retained**, with no logo and no marquee membership. **17 new Clients** represent the other approved static entries. Two Clients have documented Projects (Ravo Film, TID Group); 17 have zero Projects. No new Project relationships inferred.

| Preserved order value | Approved logo label | Client ID | Logo attachment ID | Client action |
|---:|---|---:|---:|---|
| 0 | Samsung | 35 | 34 | New |
| 1 | Ravo | 17 | 37 | Existing Ravo Film reused; identity confirmed by user |
| 2 | Energizer | 40 | 39 | New |
| 3 | Dasher | 43 | 42 | New |
| 4 | Chowdhury | 46 | 45 | New |
| 5 | Lions International | 49 | 48 | New |
| 6 | Major Harvest | 52 | 51 | New |
| 7 | Bika | 55 | 54 | New |
| 8 | Orvibo | 58 | 57 | New |
| 9 | Module | 61 | 60 | New |
| 10 | StaySmart | 64 | 63 | New |
| 11 | IN Design Lab | 67 | 66 | New |
| 12 | Inno Kitchen | 70 | 69 | New |
| 13 | N4 Container | 73 | 72 | New |
| 14 | The Inn Livehouse | 76 | 75 | New |
| 15 | Yoru | 79 | 78 | New |
| 16 | Sedox Performance | 82 | 81 | New |
| 17 | BamCoal | 85 | 84 | New |

Fields: Client name = core title; existing `client_logo` Image ID, existing `logo_status` Select now labelled **Logo Approval Status**, new `show_in_marquee` True/False default false labelled **Show in Selected Clients**, new `marquee_sort_order` Number labelled **Marquee Sort Order**, existing optional `website_url` retained. Free ACF 6.8.10 only. Existing generic `visible` and `sort_order` definitions/data are retained for compatibility but hidden from Client editing and unused by the marquee. No redundant logo permission field or second Project relationship list.

`showmakers_marquee_clients()` queries published Clients with `show_in_marquee=1`, `logo_status=approved`, and positive `client_logo`; each result must also pass `showmakers_public_client_logo()`. Numeric `marquee_sort_order` ascending, Client ID tie-breaker. There is no Project query, count, service or Featured dependency. Theme `template-parts/clients-marquee.php` preserves original structure/attributes and resolves approved attachment URLs; meaningful attachment alt text falls back to Client name. No automatic external/project links.

Logo safety: actual attachment must be an available supported image with usable image metadata; explicit stored attachment pending/restricted states and known restricted photograph filenames are excluded. A newly uploaded logo with no stored attachment restriction may use its separate Client approval, making the requested staff workflow sufficient. This never writes/approves Project attachment metadata: `showmakers_approved_media()` and Project approval rules remain unchanged. Logo and Project permissions are independent. Imported 18 logos are approved original WebP files with byte-for-byte matching hashes; no source artwork replacement, recoloring, resizing or SVG upload policy change.

Staff: WP Admin → Clients → Add Client → name → approved Client Logo → Logo Approval Status: Approved → enable Show in Selected Clients → Marquee Sort Order → Publish. To remove, disable Show in Selected Clients and Update; logo/Client/Projects remain. Client list columns: Client, Logo Preview, Logo Approval, Selected Clients, Marquee Order, Last Modified. Marquee Order sorting includes relationship-only Clients with missing order metadata. Concise field guidance explains approval, membership, optional URL and lower-number order. Enabled but unavailable/unapproved logos receive an Admin warning/list explanation and no empty public slot.

Before Home switch, candidate HTML was compared to the approved original marquee: identical structure, labels, image attributes/order and artwork basenames, with only delivery URLs changed to Media Library. All 18 source mappings and file hashes validated first. Home runtime replacement was confined to its existing Client section. Hero/artwork, What We Do, fonts, colors, spacing, CSS and JavaScript were unchanged. Original 115-second loop, containment through existing intrinsic aspect-ratio/max-width/max-height rules, generous spacing, edge fades, hover/focus pause and mobile sizes retained. Decorative copy remains aria-hidden and inert with empty alt text; no-JavaScript static fallback retains all logos.

QA: 1440/1024/390 exact logo order, original section dimensions and logo bounds matched the captured pre-migration baseline within 0.01px floating-point tolerance; no page overflow or broken artwork. Lazy images outside the animated viewport load when needed; all 18 were confirmed loaded in static review mode. `?motion=reduce` static layouts tested at all three widths: no animation, no duplicate list, hidden pause button, all 18 usable. Existing native prefers-reduced-motion media-query behavior remains unchanged; OS setting was not changed. Keyboard Enter pause/resume, aria-pressed, 3px focus and focus-within pause passed. Source CSS hover behavior is unchanged.

Actual Ravo Admin saves tested Show in Selected Clients off/on and Logo Approval Pending/Restricted/Approved: public count 17 while excluded, restored to 18 afterward. Read-only `check_wp_clients.php` additionally verifies missing-logo safety, Client with zero Projects, relationship-only TID exclusion, numeric order, explicit attachment restrictions and independent Client/Project approval with filters removed after each check. No fabricated test Clients or Projects. All approved values restored.

Regression: local pre-migration checkpoint compared full Project fields/term assignments, all eight Service fields, and original six Project attachment metadata/captions/file hashes; unchanged. TID Client metadata unchanged. Work All=5, Media Production=2, Website & Digital Solutions=2, AI=1. Work/detail routes, Website filtered Back to Work, Media Related Work, Ravo contextual Contact preselection and empty form endpoint passed. Services and Home What We Do remain WordPress-driven. PHP syntax, free-only field types, `check_wp_clients.php`, updated historical `check_wp_pilot.php`, `check_wp_services.php`, anonymous `check_wp_frontend.py` (44 assets), and original `npm run check` passed. The old pilot's two-Client/no-Ravo-logo assumptions were updated to this approved migration instead of restoring obsolete data.

Intentionally retained importer `scripts/migrate_wp_clients.php` is local-host-gated, accepts one approved source entry, refuses existing migrated/staff-edited membership and requires an explicit verified Ravo identity argument before reusing its record. `scripts/check_wp_clients.php` is a read-only reconciliation/safety check. Temporary checkpoints/candidate files stay outside Git. LocalWP database and upload content are not committed; Git checkpoint contains only code/configuration/docs. Preserve a separate local content/uploads backup for handover.

Remaining static: Hero/Home company copy, About shell, Services page introductory/global copy, Contact prototype copy, header/footer/global settings. Original `data/clients.json`, static `index.html` and approved original logo files remain reference/static-baseline material; the WordPress Home does not render a second static logo list. No Contact backend, SEO, roles, staging/production, main, Vercel, DNS or Supabase changes. Stop here. Recommend About content migration next, preserving its approved presentation; do not start automatically.
