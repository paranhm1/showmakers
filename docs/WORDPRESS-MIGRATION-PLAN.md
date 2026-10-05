# ShowMakers WordPress CMS migration plan

Status: Phase 1 architecture proposal; implementation requires review and approval. Prepared 2026-10-05.

## Protected baseline and scope

Approved static commit: `19e1be22a7952f655003abab5cc1344ac35fbb9b`. Annotated recovery tag: `static-approved-v1`, message: “ShowMakers approved static frontend before WordPress CMS migration”. `main` remains the approved static frontend; `wordpress-cms` contains planning work only. `hero-addon` remains a recovery/reference branch.

Authority: AGENTS.md → ShowMakers Brand Guidelines/approved design → references/VERIFIED-CONTENT.md → current approved frontend → advisory UX skills. Preserve the current DOM structure, classes, responsive rules, fonts, colors, navigation, CTA geometry, content hierarchy and progressive enhancement. Do not regenerate a design system. No Elementor, page builder, headless WordPress, React, Supabase or custom external admin.

This phase installs nothing, migrates no records and creates no PHP or database schema. Hosting, email, DNS, CRM and Vercel are untouched. Tag and branch preparation plus these three documents are the only deliverables.

## Current architecture audit

The repository is a dependency-free static site. `package.json` calls Python 3: build regenerates HTML; dev regenerates and serves localhost; check reads generated HTML/data and validates paths, relationships and UX invariants. Do not run the build to make planning changes. JSON is authoring input; HTML is generated output; `scripts/build_site.py` is the shared renderer.

| Area | Current implementation | Classification / migration consequence |
|---|---|---|
| Home | `index.html`: Hero with approved collage → Selected clients → What we do → footer | Layout fixed; hero text in site JSON; artwork reference currently hardcoded in renderer; clients/services generated from records. No Selected Work section currently exists. |
| Work | `work.html`: yellow intro, 4:3 3/2/1-column cards, service filters | Data-driven cards; published records include one clearly labelled development fixture. Public totals count only 5 approved genuine records, not 6 cards. Visible filter buttons appear only for populated genuine services; other valid service URLs retain an honest empty state. |
| Detail | Five `work/*.html` pages | Shared native-ratio media template, optional client identity, summary, service links, documentation note, real-project Next loop, contextual inquiry. Not a fabricated results/case-study template. |
| Services | `services.html`: eight panels and numbered index | Content from service JSON; anchored fallback; JS selects one panel. Platforms, capability lists, optional media and related Work actions. |
| About | `about.html` | Fixed intro/philosophy/human-led production/working-steps composition; content and approved on-set image from site JSON. |
| Contact | `contact.html` | Fixed conversational form first, details second; six editable-content input definitions plus two hidden context fields. Endpoint empty, no requests/storage; prototype notice and honest unavailable state. |
| Shared | Header/footer, inline SVG CTA helper, media rendering, page intro, skip link | Repeated output generated centrally; reusable PHP template parts later. Preserve existing roles, IDs and data attributes. |
| 404 | `404.html` | Generated approved fallback; Python server does not automatically use it for missing routes. WordPress must render a true HTTP 404. |
| Assets | `assets/css`, `assets/js`, `assets/images`, `assets/fonts` | Global tokens/components; seven page styles; eight JS modules; provenance manifest, approved logos/media, hero PNG/WebP and font binaries. No current rendered video elements. |

Data inventory: `data/projects.json` has 7 records (5 approved published real entries, 1 published development-only fixture, 1 unpublished restricted Tommy Hilfiger entry); `data/services.json` has 8; `data/clients.json` has 18 visible approved logos. `data/site.json` supplies company/page/UI/form copy. `assets/images/sources.json` supplies provenance/dimensions, not portfolio records.

README is a chronological record, not a reliable description of the latest UI. Earlier notes about homepage work previews, 16:10 cards, six-project counts, obsolete service filters, project dialogs and unconfirmed fonts are superseded by current code/data and user confirmations. Current licensing permission for MADE TOMMY/FUTURA PT was explicitly confirmed by the user before Preview deployment; old local-only README/CSS comments were not changed in this phase. Keep commercial license evidence privately for handover, without distributing license credentials.

### Dynamic behavior to preserve

- `global.js`: menu state, Escape/outside dismissal, breakpoint reset, `motion=reduce` review switch. No live analytics collector is installed; `data-event` hooks remain inert integration points.
- `home.js`: fast staged text/CTA reveal, one 115-second logo loop, pause control, hover/focus pause, reduced-motion/static fallback, offscreen/background pause. It alone fetches site JSON via `data.js` for pause/resume labels. Replace this fetch with theme-rendered minimal label configuration; no headless API or full public site JSON is necessary.
- `work.js`: filter from `?service=<slug>`, history/popstate/refresh, validated empty results, placeholder exclusion from filtered results/counts, `?from=<service>` propagation to detail links.
- `services.js`: anchors become keyboard-accessible buttons; active region hidden state, status announcement, hash navigation, short token-based reveal and mobile scroll adjustment when selected heading is outside view. All content remains readable without JS.
- `project.js`: Back to Work restores validated service context. Replace its hardcoded service whitelist with the actual stable term-slug list; do not match labels.
- `contact.js`: validates fields, reads/clears `service` and `project` URL context, supports removal, associates field errors, maintains states. Future endpoint must be supplied explicitly; current no-send behavior persists until backend approval. `sourcePage` is inferred context, not verified referral history.
- `motion.js`: reads existing CSS timing/easing and reduced-motion flags. Keep module loading/import paths; conventional non-module enqueue would break imports.

## Recommended WordPress architecture

Traditional WordPress renders HTML on the server using one classic custom theme. WordPress Admin owns content; Media Library owns approved uploads; theme code owns frontend layout. Core posts/terms/meta/options are sufficient; no custom database tables needed.

Recommended content model:

1. `project` CPT: public archive `/work/`, singles `/work/<slug>/`.
2. `client` CPT: admin-managed, no standalone public archive/single/search/feed; approved visible records supply the marquee and documented client associations.
3. `sm_service` non-hierarchical taxonomy attached to projects: eight controlled terms with editable term metadata. Its admin label is Services; it supplies the entire Services Explorer, not just tags. No duplicate Service CPT. Public term archives are disabled; public entry point remains `/services/#<slug>`. Staff can edit term content and visibility, but adding/deleting/renaming slugs requires administrator review. Taxonomy assignment is many-to-many. Term visibility controls Services presentation, not automatic rewriting of project associations.
4. Controlled Home, Services, About and Contact Pages; Work is the Project archive, not a duplicate Page competing for `/work/`.
5. Site Settings options for reusable contact/company/footer data. Branding tokens remain code. Inquiry storage, if chosen later, is a private CPT, not a public content type.

WordPress supports custom taxonomies with their own admin interfaces; term metadata avoids maintaining a second service identity. See [WordPress taxonomy handbook](https://developer.wordpress.org/plugins/taxonomies/working-with-custom-taxonomies/). This is a recommendation for approval, not a registration performed here.

Use stable source IDs in private migration metadata and immutable public service slugs. Store relationships as term/post IDs; resolve URLs via WordPress functions. Projects have multiple service terms and an optional Client record reference. Client/project name matching is never sufficient evidence for a relationship. `clients.json.projects[]` is empty for every current client: do not equate Ravo with Ravo Film. Preserve known client names as project-level text when no documented Client-record match exists; do not add a marquee logo implicitly. Related Work comes from project-to-service assignments; current `relatedProjects[]` is a consistency check, not a second writable relation.

## Proposed source layout (not created yet)

Future repository directories: `wordpress/theme/showmakers/` and `wordpress/plugins/showmakers-content/`. Existing static files remain at root until conversion has passed review.

```text
wordpress/theme/showmakers/
  style.css                 Theme identity only; approved CSS remains in assets
  functions.php             Setup, includes, hooks and conditional asset enqueue
  index.php                 Required safe theme fallback
  front-page.php            Fixed Home sections
  header.php / footer.php   Same navigation, skip link, brand and footer structure
  page.php                  Safe controlled fallback; no free-form layout builder
  page-services.php         All service panels + progressively enhanced selector
  page-about.php / page-contact.php
  archive-project.php / single-project.php / 404.php
  template-parts/
    page-intro.php / project-card.php / project-media.php / service-panel.php
  inc/
    assets.php              Versioning, module scripts, page-specific assets
    content.php             Read adapters, eligible content queries, route helpers
    helpers.php             Escaping, arrow SVG and shared presentation helpers
  assets/                   Approved css/js/images/fonts layout retained
wordpress/plugins/showmakers-content/
  showmakers-content.php    Project/Client registration and service taxonomy
  inc/
    fields.php              Field schema, validation and field-provider integration
    settings.php            Controlled Site Settings and safe defaults
    permissions.php         Staff capabilities and slug/media restrictions
    media.php               Approval guards across render and public data output
```

No files above are implementation deliverables in Phase 1. Separate a small site-owned content plugin from the theme so records/capabilities do not disappear when a theme is temporarily switched. This is not a proprietary plugin and creates no alternative admin. Contact handling is added later as a module only when approved. Field definition files can use ACF Local JSON in source control if ACF is chosen; do not bundle the paid plugin or license key.

Preserve DOM selectors (`.hero`, `.portfolio-project`, `.service-section`, `#inquiry-form`, navigation IDs), semantic heading levels, inline SVG arrows and accessibility attributes. Use `wp_head`, `wp_footer`, body classes and `aria-current` with an intentional mapping to existing `.page-*` classes; account for the authenticated WP admin bar without changing the public design. Asset helpers must generate absolute theme/media URLs: relative `assets/` or `data/` paths fail at nested WordPress routes. Enqueue global/components styles once, then exactly the appropriate page style and existing ES modules. Fingerprint with file modification/version information. Ensure `global.js`/`home.js` remain module-safe and scoped to matching elements.

## Custom fields recommendation and decision gate

Recommend **ACF Pro, if ShowMakers approves the license/dependency**, for staff-friendly ordered media and platform rows, About steps and Site Settings. Use only prescribed fields, not Flexible Content, ACF Blocks or arbitrary layout controls. Native WP fields handle titles, slugs and publishing; service taxonomy handles classifications. ACF is a field editor, not a frontend builder.

| Approach | What it covers | Cost/maintenance implication |
|---|---|---|
| Core + custom meta UI | All required post/term/options fields; custom media selector and ordered attachment-ID list; typed list inputs | No paid license, but more site-owned admin UI/validation and accessibility code to maintain. Raw core custom-field boxes are inadequate for staff. |
| Free ACF + small core admin additions | Standard text/number/image/select/taxonomy/Post Object/relationship controls; term fields; newline lists | Custom ordered-gallery/repeated-row UI and Settings API options screen still needed; no claim that free ACF includes Pro Gallery/Repeater/Options Pages. |
| ACF Pro (recommended pending approval) | Gallery, Repeater, Options Pages simplify existing ordered collections and settings | Paid license, renewal/update and supported environment activation must be decided. Read adapters should keep content safe if the field UI is unavailable; never silently fabricate defaults on plugin failure. |

[ACF's official FAQ](https://www.advancedcustomfields.com/resources/frequently-asked-questions) and [Options Page documentation](https://www.advancedcustomfields.com/resources/options-page/) identify premium features. Do not install or commit any edition now. Confirm the precise purchased edition and activation policy before implementation; no price or license entitlement is assumed. Core metadata registration supports a plugin-independent storage schema ([WordPress post metadata API](https://developer.wordpress.org/reference/functions/register_post_meta/)).

## Staff permissions and editability

Create a scoped `showmakers_editor` role using explicit capabilities rather than giving broad Administrator or stock Editor privileges. Allow CRUD/publish/order for Projects and Clients; edit approved page fields; upload approved media; assign existing service terms; edit service descriptions/order/visibility; edit approved contact/footer settings via a dedicated capability. Protect immutable slugs/classifications and brand assets. Do not grant `manage_options`, plugin install/activate/delete, theme switching/editing, `edit_theme_options`, arbitrary HTML or code editing. No navigation structure editor is required for the fixed four-item navigation.

Separate taxonomy capabilities: staff can assign terms and edit their content, while create/delete and slug changes are administrator-only (screen/save validation must enforce this, not just hide controls). Page permissions should restrict staff to Home/Services/About/Contact and an approved Privacy page; a hidden menu alone is not access control. Scope settings and inquiry access individually. Administrator/developer handles updates, field schema, code, rights policy and configuration. The site plugin must check capabilities and nonces on writes, sanitize input and escape output. No inquiry access exists until the future storage decision.

## Media workflow and safety

Staff select attachment IDs for thumbnails, hero media, gallery rows and approved page imagery; the theme resolves URLs/srcsets/sizes and controls crop. Work stays 4:3, 3/2/1 columns; `cover` versus `contain` is preserved (Ravo Film stays contain). Details retain native ratios, portrait max widths and full website captures. Hero keeps the existing source's right-side CSS crop/mask, intrinsic 2048×768 ratio and eager loading; desktop/mobile sizes remain code-controlled. Replacement artwork should match the approved composition; do not expose masks/spacing as fields.

Gallery rows carry attachment ID, type, contextual alt/caption and order. Shared attachment alt can be a default; usage-specific captions/alt must survive import without altering unrelated placements. Deduplicate approved uploads by source-path manifest; preserve differing captions per use. Image is the only active public media type in V1. A future video control is reserved, but playback changes require separate approval.

Default new media/project status is pending. Public listing/single/Next/related/context queries require published, non-development, non-placeholder and approved status. Also check each selected attachment's approval in every public renderer, API response/feed/sitemap/search and public data attribute. Invalid selections remain unpublished or use a clearly labelled safe missing-media treatment; never fallback to a restricted original. Do not publish internal provenance/rights notes or expose them through public REST meta.

**Render flags do not secure uploaded files.** WordPress upload URLs can remain directly reachable independently of a project's status. Restricted Tommy Hilfiger `brand-showcase.webp` and Samsung `samsung-experience.webp` must stay outside the public Media Library, theme, webroot and deployment bundle. Pending confidential uploads also need private storage or must wait outside WordPress until approved; default pending is a rendering guard, not a private-file guarantee. Avoid building an elaborate private-file product in V1. Restriction of already-public files requires removal/protection of originals and derivatives, cache/CDN purge and link audit, not only changing a flag.

Approved client logo permissions remain independent. Samsung's approved logo may remain in the 18-logo collection despite project-photo restrictions. No claims, results or relationship links are inferred from logos. Import five genuine projects as drafts initially and publish after validation; keep Tommy's factual record draft/restricted without restricted media import. Do not import Project 06 as published: omit from production content; keep only a separate local test fixture or draft with developmentOnly, excluded from all public queries. A production migration should show five verified projects unless new verified work is supplied.

## Contact backend plan (later)

Keep the current empty endpoint/prototype behavior until a separately approved backend phase. Recommend a small WordPress same-origin handler, not an external backend. Preserve six visible fields (`name`, `company`, `service`, `goal`, `email`, `additionalDetails`) and `projectReference`/`sourcePage` context. Serve a real no-JS POST path alongside JS enhancement when enabled; adapt current `onsubmit=false` and disabled control only in that approved phase.

Server validates lengths, required fields, email, service whitelist including not-sure and eligible project reference; treats hidden context as untrusted. Use anti-spam measures, nonce/CSRF controls appropriate to anonymous requests, rate limiting and safe message construction. Nonces are not spam protection. Do not trust request input for recipients, headers or source history. Notify the configured sales address with a site-controlled From and sanitized Reply-To. Configure authenticated SMTP/transactional delivery separately; `wp_mail` acceptance alone does not prove inbox delivery. Report accepted processing truthfully; failures must not show a fabricated success.

Optional private `sm_inquiry` CPT storage is a later decision; do not build CRM, approval workflows or custom tables. If enabled, assign timestamp/status server-side and limit access to named staff. Decide retention, privacy text, export/erasure, logs, backups and spam disposition before collecting real inquiries. No budget/privacy checkbox or new field is added automatically. Production mail credentials are hosting configuration, never repository content or staff-editable text fields. Staging must use captured mail or a test recipient, not live sales delivery.

## URLs and connected UX

| Current | Proposed | Handling later |
|---|---|---|
| `/index.html` | `/` | 301; logo links use home URL |
| `/work.html` | `/work/` | 301 preserving approved query parameters |
| `/work/<slug>.html` (five genuine entries) | `/work/<slug>/` | Explicit 301 mapping; preserve `from` |
| `/services.html#<service>` | `/services/#<service>` | Redirect path; browser retains fragment; server never receives hash. Verify actual host/browser behavior. |
| `/about.html`, `/contact.html` | `/about/`, `/contact/` | 301; preserve service/project context on Contact |
| Missing routes | Theme `404.php` | HTTP 404; no blanket redirect to Home |
| Restricted/fixture project routes | No public equivalent | Do not create detail routes. Legacy withdrawn routes remain 404 (or agreed 410); no fabricated case study. |

Retain `/work/?service=<service-slug>`, detail `?from=<service-slug>` and `/contact/?service=<slug>&project=<project-slug>`. Do not register `service` as a WordPress taxonomy query var: a collision could turn Work into a term archive. Use `sm_service` internally with no competing public rewrite/query route. Service slugs remain unchanged. For multi-service projects, keep project context without auto-selecting one service. Next Project wraps only eligible real projects. Unknown service/project context must safely clear; zero-result states stay honest. No service single pages, industry/media taxonomy or three capability-pillar taxonomy is needed.

Plan a server-rendered valid Work filter on direct URL requests, with the current buttons/history behavior as enhancement; confirm parity of no-JS handling and accessible statuses in QA. Optional future pagination must not silently truncate the current client-side filter dataset; postpone until a genuine collection-size requirement. No redirects or WordPress routes are implemented in this phase.

## Fonts and assets

Keep local `@font-face` rules, `font-display:swap`, actual embedded weights and unchanged filenames. Current real directory is `assets/fonts/made_tommy/` (underscore), not the previously described hyphen form; FUTURA uses `assets/fonts/futura-pt/`. MADE TOMMY solid OTF: 250/300/400/500/700/800/900; FUTURA TTF: 300/400/450/500/600/700/800. Outline fonts are unused. No WOFF2 files are currently supplied. Preserve percent-encoded spaces and relative CSS-to-font paths when assets move into the theme; fonts load from the same website origin, not a public CDN. User confirmed web-embedding rights; license documentation is a private operational record, not a reason to change fonts. Verify actual font loading after migration, not just computed family names.

Theme includes brand assets and required deployment font binaries, not company PDFs, restricted media, skill folders, review screenshots, production uploads, wp-config, mail credentials or database dumps. Media Library uploads and WordPress content belong to storage/database backups, not Git. Original hero PNG stays in the repository; frontend uses its approved optimized WebP.

## Local and Git workflow

After architecture approval, user installs [LocalWP from its official source](https://localwp.com/help-docs/getting-started/installing-local/), then creates an isolated local WordPress site (local database only). Choose supported PHP/WordPress versions matching future hosting rather than fixing an unverified version here. No installation is performed now.

Keep source in this repository's future `wordpress/` folders on `wordpress-cms`; use a reviewed feature branch for each implementation step. LocalWP `app/public/wp-content/themes/showmakers` should link/copy only the theme folder; its plugins folder receives the site-owned content plugin after approval. Keep LocalWP core, uploads, database and configuration outside the Git checkout. Use symlinks where the environment supports them; a documented sync script is optional later. Do not move the whole static root into wp-content or use a broad Git worktree that imports database files. Existing static root remains a baseline preview; use LocalWP's own URL for the CMS version.

Theme/code changes: feature branch → Git → WordPress staging → review → approved integration into wordpress-cms; eventual merge into main/release only when explicitly approved. Until that milestone main stays static. Staff content changes: WordPress Admin → database/uploads → backups; they do not create Git commits or require a theme rebuild. Import once with source-ID mapping and dry run; never overwrite later staff edits by rerunning a static JSON seed indiscriminately.

## Staging and production plan

Later provision WordPress-capable staging with access restriction/noindex and protected backups; Vercel's static Preview is a visual baseline, not PHP/database hosting. Confirm host, PHP, media storage, backup/restore, HTTPS and mail facilities before deploying the CMS. Deploy only theme/plugin code; content imports and uploads are separately reviewed. Preserve license activation limits and stage mail capture. Do not submit staging to search engines or enable analytics automatically.

Review at 1440/1024/768/430/390px, compare against static-approved-v1: headings/copy/spacing/colors/fonts/hero crop, nav/current-page states, logo pause/reduced motion, Work counts/filters/history/direct links, Next/Back/context, every service panel/keyboard/mobile behavior, Contact safe states, 404 HTTP status, image loading, no-JS content, media-rights output guards and staff permission checks. Audit public REST/search/feed/sitemap and raw asset URLs for restricted/draft leakage. Use production placeholder gate on the static source as a known content check; create WordPress integration checks later rather than relying on static path assertions to test PHP.

Production cutover is a separately authorized task: backup and restore rehearsal, migration freeze/copy rules, URLs/redirects, approved privacy/content, tested mail, license evidence, final rights review, HTTPS and hosting verification, explicit domain/DNS approval, post-cutover smoke test and rollback plan. Static tag recovers theme-source baseline; it does not replace database/uploads backups. No production readiness is declared now.

## Decisions still requiring approval

- Service taxonomy with term content and Client CPT model; no Service CPT duplicate.
- ACF Pro licensing versus free/core admin implementation; approve before fields are implemented.
- WordPress host/staging environment and supported PHP/WordPress versions.
- Mail provider, privacy text, optional inquiry storage/retention and authorized inquiry staff.
- Final operational font-license record and higher-resolution media when available; existing confirmed permissions remain valid.

Missing dates, results, client mappings, social URLs and detailed project scope do not block local theme work. Leave absent fields empty and omit output; do not infer business facts. Main blockers for public launch are placeholder removal, provisional copy/privacy approval, contact delivery decisions, hosting/cutover authorization and rights-safe media workflow.

## Recommended implementation sequence

1. Approve this model and field-provider decision; user prepares isolated LocalWP environment.
2. Create theme shell/global header/footer/assets, minimal site-owned content registration and read adapters. Compare approved public shell before importing content.
3. Seed eight service terms, approved Clients and controlled site/page fields into local drafts so Home and connected queries have stable IDs. Build Home preserving Hero/marquee/service-index output.
4. Projects CPT fields and Work archive; five genuine local draft records, rights guards, filters and honest zero states. Project details, Next/Back and Contact context.
5. Complete Services Explorer/term editor, connected actions and Client marquee management; no duplicate service/project relationships.
6. About controlled content; Contact frontend retains inactive mode.
7. Separately approved Contact handler/mail and optional private inquiry storage.
8. Rehearsed content import, media deduplication, source-ID report, staff permissions and editorial training.
9. WordPress staging, full parity/rights/permissions/email QA and staff review.
10. Separately approved production cutover and rollback monitoring.

Exact next task: after approval, establish an isolated LocalWP site and implement only the custom theme shell (header/footer, approved global CSS/fonts, module loading and safe fallback), with the minimal site-owned content registration needed for subsequent work. Do not begin that task automatically.


## Phase 2 implementation progress — local theme shell

Architecture approved by the user. LocalWP site confirmed at `/Users/myungje/Local Sites/showmakers-local/app/public`; actual configured domain is `showmakers-local.local` (not the initially requested `showmakers.local`). WordPress 7.1.2 and configured PHP 8.2.29. Credentials were not read or recorded.

Actual theme source: `wordpress/wp-content/themes/showmakers/`. Local installation: `/Users/myungje/Local Sites/showmakers-local/app/public/wp-content/themes/showmakers`, symlinked to this repository theme. This supersedes the earlier illustrative `wordpress/theme/` path. The user activated the theme manually; runtime QA subsequently completed.

The source includes metadata, functions, header/footer, static front-page, safe index, branded 404, four routing-ready page shells and asset enqueue helpers. No CPT/taxonomy/ACF/settings/handler exists. Home uses approved static markup; no JSON is imported into WordPress. Shell pages explicitly identify deferred content instead of pretending to be completed CMS pages. No contact form is activated.

Assets: relative symlinks reuse the original CSS, font directory, approved client/hero images, brand logos and shared global JS without duplicating font binaries or changing static files. Theme-only home.js omits the static site.json fetch and keeps the same pause/resume defaults and motion behavior. Original fonts and CSS-to-font paths remain unchanged. Native WordPress style enqueue and script-module enqueue handle public URLs and file-time versions. Core wp_head/wp_footer/body_class/wp_body_open remain; core assets were not broadly dequeued.

These repository-relative asset symlinks are a local-development arrangement, **not a standalone theme ZIP**. A future staging packaging step must materialize the allowlisted linked assets (without references, rights manifests, restricted sources or production uploads). Do not copy the theme directory alone and expect external symlink targets to be deployed. Git tracks theme source/symlink entries; LocalWP core/config/database/cache/uploads/credentials remain outside Git.

Launch workflow: open LocalWP, select ShowMakers Local, Start Site, then Open Site at the actual domain. The developer edits this repository and the theme link reflects changes immediately. User activates ShowMakers through Appearance → Themes. For navigation smoke tests, four empty local Pages were created with LocalWP’s bundled WP-CLI: work (ID 7), services (8), about (9), contact (10). They use page-specific shells and are not JSON/content migration. No existing sample/privacy pages were removed. The Work Page is temporary for Phase 2 and must be removed/replaced by the Project archive during Phase 3 to avoid rewrite collision. Home front-page.php renders the fixed static Home even before a dedicated Home Page is configured.

Phase 2 verification completed after manual activation. PHP lint, linked-asset existence and original static checks passed. At 1440px and 390px, header/Hero/headline/support/artwork/Clients/What We Do/footer dimensions and x positions match the approved static baseline exactly. All y positions differ only by the normal logged-in admin-bar offset (32px desktop, 46px mobile); anonymous Home has no admin bar. No public-layout fix or core style dequeue was needed. Hero height is 558px desktop / 540.9375px mobile, collage 540.445×430px desktop / 231.695×180px mobile. No horizontal overflow or failed images observed. Marquee position differs naturally with animation time, not content or geometry.

Native font readiness checks passed for MADE TOMMY 700/500 and FUTURA PT 400/500; all 14 declared font binaries return HTTP 200 and shared global CSS is byte-identical to the approved source. ES module tags load correctly; Home reveal delays remain 0.1/0.5/0.65 seconds. Mobile menu open/Escape close, actual navigation to Services and marquee pause were exercised. Existing motion=reduce review mode produced immediate Hero and 18 static logos with no animation/overflow; the unchanged CSS also preserves prefers-reduced-motion. Browser Home/404 logs had no warnings/errors. Anonymous HTTP checks confirmed Home and all four shell routes 200; shell output has correct current-page navigation and shared footer. A missing route produces the branded 404 and actual HTTP 404. Screenshots were saved locally at /tmp/showmakers-wordpress-home-1440.png and /tmp/showmakers-wordpress-home-390.png, not committed.

Known intended differences: native WP document titles use the local site name; logged-in users retain the WP toolbar; inner routes are explicitly incomplete page shells, not migrated full pages. Service anchors on Home target the future Explorer; individual service panels/anchor destinations are deferred to Phase 3. Current shell Contact has no form/handler; the original static Contact remains unchanged and inactive. Local theme is not a standalone distributable until asset symlinks are materialized by the future packaging step. Existing static files/main/static-approved-v1 remain unchanged.
