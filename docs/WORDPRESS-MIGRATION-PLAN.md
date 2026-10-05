# ShowMakers WordPress CMS migration plan

Status: Phase 6 Services and Home What We Do CMS connection completed with official free ACF. Earlier sections remain history; the latest Phase 6 completion section is authoritative. Updated 2026-10-05.

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
3. `service` non-hierarchical taxonomy attached to projects: eight controlled terms with editable term metadata. Its admin label is Services; it supplies the entire Services Explorer, not just tags. No duplicate Service CPT. Public term archives are disabled; public entry point remains `/services/#<slug>`. Staff can edit term content and visibility, but adding/deleting/renaming slugs requires administrator review. Taxonomy assignment is many-to-many. Term visibility controls Services presentation, not automatic rewriting of project associations.
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

Retain `/work/?service=<service-slug>`, detail `?from=<service-slug>` and `/contact/?service=<slug>&project=<project-slug>`. Do not register `service` as a WordPress taxonomy query var: a collision could turn Work into a term archive. Use `service` internally with no competing public rewrite/query route. Service slugs remain unchanged. For multi-service projects, keep project context without auto-selecting one service. Next Project wraps only eligible real projects. Unknown service/project context must safely clear; zero-result states stay honest. No service single pages, industry/media taxonomy or three capability-pillar taxonomy is needed.

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

## Phase 3 completion — 2026-10-05

The user replaced the Pro prerequisite with official free ACF. The content plugin is implemented and active on LocalWP (`showmakers-local.local`, WordPress 7.1.2, PHP 8.2.29, ACF 6.8.10). The repository plugin directory is symlinked into the local installation; it is not an independently packaged production install.

Actual structures: `project` / `client` CPTs and `service` taxonomy. Four Local JSON groups are stored inside the plugin. See the authoritative [implemented model](CMS-DATA-MODEL.md#phase-3-implemented-specification--free-acf-authoritative) for names, relationships, ordering, permissions, staff workflow, media guards and Pro upgrade plan. Earlier Pro-oriented planning tables are future intent, not active dependencies.

Free adaptations: five optional project images; newline capability/platform/format lists; one Service image; no ACF Options Page. Existing global data stays in its current source. No actual company content has migrated. Exactly eight service terms were seeded as required structural vocabulary, without descriptions or portfolio media.

The theme only adds Project thumbnail support and a temporary `archive-project.php` forwarding to the existing Phase 2 Work shell. This prevents CPT registration from replacing `/work/` with the generic fallback. No brand/design/static content was changed. The old empty Work Page can be retired during Phase 4 after archive integration is verified; it is not removed here.

Validation: PHP syntax checks; free-only JSON types; all four JSON groups loaded; Project→one Client / multiple Services saved; thumbnail/hero/five image slots and Client logo IDs saved; Project/attachment pending/approved/restricted checks; numeric ordering; Service newline fields and image saved/restored; clean `/work/{slug}/` sample permalink and `/work/` archive configuration. Temporary draft records and temporary attachment were removed after tests; no fake public content remains. Frontend and Admin inspection are recorded in the final Phase 3 report.

No new staff role, Contact backend, SEO plugin, Inquiry CPT, business import, production deployment, DNS, Vercel, Supabase or environment variables. Main/static-approved-v1 stay unchanged. Commit and push only `wordpress-cms` using `ShowMakers: add WordPress content model`.

Next recommended Phase 4: migrate verified assets/content as drafts using stable source IDs; first validate Ravo Film and one Client against the approved static presentation; integrate archive/single and existing page components without redesign; preserve placeholder honesty, restricted-media exclusion and current filter/navigation behavior. Resolve richer service media/platform mapping without losing source content. Review frontend equivalence before bulk publishing. Do not begin without approval.

## Phase 4 local pilot completion — 2026-10-05

Scope is limited to one real Ravo Film Project, one distinct Ravo Film Client, and the existing Website & Digital Solutions relationship. This supersedes the earlier recommendation to proceed directly to wider import. No remaining Projects/Clients, development fixture, restricted media, Home or About content was migrated.

### Local records and media

- Project ID **18**, slug `ravo-film`, initially Draft; published only in LocalWP after Admin/data and 1440px/390px draft-preview validation. Public local URL: `http://showmakers-local.local/work/ravo-film/`.
- Client ID **17**, title Ravo Film, admin-only, published/visible for future reuse. Project's single `client` Post Object references this ID. No logo or website URL is inferred from the separate `clients.json` Ravo record; its empty project association remains untouched. Logo permission stays pending and no empty logo renders publicly.
- Existing `service` term ID **7**, `website-digital-solutions`; no duplicate term or extra service assignment. All eight terms remain structural vocabulary. Service body/capability/media fields were deliberately not imported in this relationship-first test.
- Attachment ID **16**: only `assets/images/work/ravo-film.webp` imported, approved, 789×1276, source alt retained. Original upload SHA-256 matches the repository asset. Listing, Hero and core featured image reuse that attachment. Listing fit is contain; no repeated Hero copied into additional image fields. All five optional slots remain empty. No new artwork, download, upscaling or restricted upload.
- Summary is exactly “ShowMakers created the Ravo Film website.” Year stays empty; Featured false; `sort_order` 4 retained. Source identity/provenance and bounded presentation are import metadata; public `documentation_note` is the existing factual documentation disclaimer, not the private rights note.

### Runtime transition and routes

`archive-project.php`, `template-parts/project-card.php`, `single-project.php` and `inc/projects.php` render eligible CMS Projects exclusively. They never merge Projects JSON into the archive, filters, Next loop or Contact context. Before publication the CMS archive honestly contained zero projects; afterwards it contains one Ravo Film. JSON and generated static HTML remain intact as reference/recovery sources. The existing empty Work Page is retained but the CPT archive owns `/work/`; its content does not drive this archive.

Publication eligibility requires publish + approved and excludes development/placeholder flags. Each selected attachment must separately be approved. An authorized draft preview may render the requested draft's approved images without including it in public queries/counts/context. Pending/restricted project media and pending/restricted attachments are omitted; internal notes/provenance are not output. Raw public uploads are not private storage; no restricted originals were uploaded.

Project routes use native WordPress clean permalinks. Project `query_var` is now false, alongside Service's false query variable: WordPress otherwise interpreted Contact's `?project=ravo-film` as a Project lookup. Rewrites use `post_type=project&name=...`; rules were flushed once after this registration change, never per request. No production redirects or DNS changes. Core may redirect an unknown `/clients/ravo-film/` path to the existing public Project through its normal 404 URL guessing; there is no Client single/archive/template.

The same approved Work filter JS is reused. Counts and populated buttons derive from real CMS service assignments; the eight terms are the valid slug set. Query URL state, refresh and browser back/forward work; unknown filter falls back to All, known empty service shows the existing honest empty state. Project links carry `from`, and the theme's Project JS validates stable slugs before restoring the Back to Work filter. Direct Project visits return to general Work. Next appears only when another eligible CMS Project exists; it is omitted for this one-record pilot, with the inquiry action kept at its original right-side position.

Phase 2 Services/Contact were incomplete shells, so this integration restores their approved existing HTML presentation as guarded reference template parts. Services keeps all eight existing panels, copy, imagery and the original Explorer script; only Related Work visibility/links now derive from CMS relationships. No service content or unrelated media was imported into WordPress. Approved linked theme assets support the unchanged reference panels. Contact preserves the existing form, prototype notice, validation and empty endpoint. Its serialized context includes only eligible CMS slug/title/service/permalink data; the theme-only adapter uses native WordPress source paths rather than .html. No inquiry handler, email request, database inquiry record or fake success was introduced. The static JSON/HTML remain authoritative for unmigrated copy; the reference template parts are frozen presentation snapshots, not a second CMS authoring system.

### Validation and next decision

Admin inspected Client, service selection, contain thumbnail, Hero, empty extra slots, Featured false, approved state and ordering before publication. Draft and published output retain a single H1, meaningful native document title, approved alt, native media size and clean permalink. Detail geometry matches static at both required widths exactly, apart from the logged-in toolbar offset (32px / 46px). Work card dimensions, typography, circle arrow and spacing match; the shorter archive/filter area is expected with one Project rather than the static reference's five real records plus fixture. Desktop 3 columns / tablet 2 / mobile 1 verified; no horizontal overflow or image failures.

Functional flows A–F passed: general Work→Ravo; filtered Work→Ravo; Website service→Related Work→Ravo; Ravo→Contact with visible Ravo reference and Website service; filtered Back to Work restores filter; direct Back to Work goes to general archive. Invalid/empty filter, refresh, back/forward and mobile service/context flows passed. Contact's valid test submission remained unavailable and clearly stated it was not sent. No endpoint was configured. Browser logs had no warnings/errors.

Read-only scripts: `scripts/check_wp_pilot.php` (relationships, counts, unchanged image, free fields, permission fault injection without record changes); `scripts/check_wp_frontend.py` (anonymous routes, assets, safe Contact payload, no duplicate .html Project). Anonymous Home/Work/filtered Work/Ravo/Services/Contact return 200; 44 distinct public image/script/stylesheet assets passed; old Ravo .html path does not duplicate the CMS page. PHP lint, linked assets and unchanged CSS checks pass; original `npm run check` passes with its existing clearly labelled static development fixture. No static rebuild was performed.

Migration helper: `scripts/migrate_wp_ravo.php`, explicitly local-host gated and refusing existing records rather than overwriting staff edits. It imports only this verified draft and one image. It must not be run automatically or used as a bulk importer. Database records/uploads live in LocalWP outside Git; source code, mapping and validation are versioned, not a database/uploads backup. Existing development symlinks still require materialization for future standalone theme packaging.

After user review, recommend **A: migrate the remaining four approved real Projects as drafts**, using the proven renderer and approval checks, then verify each presentation before local publication. Service body/global editing and packaging remain later work. Do not import restricted/placeholder records or infer Client links. Do not start the next phase automatically.

## Phase 5 local project migration completion — 2026-10-05

Authoritative current state: all five verified real projects are locally published. Each remaining source was imported as a Draft, previewed against its approved static detail at 1440/1024/390, then published and checked in Work before continuing. No bulk project publication or Client-logo import occurred.

| Project / WP ID | Client / ID | Verified Service / term ID | Published | Unique media IDs / count | Media status | Sort order | Missing verified fields |
|---|---|---|---|---|---|---|---|
| Short-form brand content / 21 | Empty | Media Production / 5 | Local yes | 20 / 1 | approved | 1 | Original project name, Client, date, detailed responsibilities, results |
| TID Group / 26 | TID Group / 23 | Website & Digital Solutions / 7 | Local yes | 24,25 / 2 | approved | 2 | Date, extra scope/credits, technology, results; Client logo/URL |
| Stories in the moment. / 29 | Empty | Media Production / 5 | Local yes | 28 / 1 | approved | 3 | Original project name, Client, date, detailed responsibilities, results |
| Ravo Film / 18 | Ravo Film / 17 | Website & Digital Solutions / 7 | Local yes | 16 / 1 | approved | 4 | Date, detailed scope, technology, results; Client logo/URL |
| AI-assisted content / 32 | Empty | AI-Enhanced Content Production / 6 | Local yes | 31 / 1 | approved | 5 | Original project name, Client, date, detailed responsibilities, results |

Client 23 is the only new Client. Existing source-ID/slug/name matches are checked before creation; no duplicate Client was created. Both Clients are admin-only and intentionally have no logo or website URL, with `logo_status=pending`. Images containing visible brand text do not establish a new Client relationship. In particular no Ravo-logo association was inferred for the two unidentified content examples.

Media map: attachment 20 = brand-content.webp; 24 = tid-detail.webp; 25 = tid-home.webp; 28 = short-form.webp; 31 = ai-content.webp; existing 16 = ravo-film.webp. Five new assets, six unique project attachments total. Original SHA-256 integrity verified; source descriptive alt text preserved, AI-assisted caption/disclosure preserved. TID uses 24 for listing/Hero and 25 for `project_image_1`; all other additional slots remain empty, repeated Hero imagery is not duplicated. No project exceeds five supporting-image slots and no source supporting media was dropped. Restricted Tommy/Samsung images, Project 06, pending/hidden records and unrelated media were not imported.

CMS controls Work collection, five detail routes, title/summary/Client/service/media/Featured/order fields, filter counts, Related Work availability, Next sequence and Contact project-context payload. All=5; Media Production=2; Website & Digital Solutions=2; AI-Enhanced Content Production=1. Other five Services have zero real projects and no filter button. Eight existing terms remain unchanged. Featured remains true for Short-form brand content and TID, false for the other three.

Next follows numeric order: Short-form brand content → TID Group → Stories in the moment. → Ravo Film → AI-assisted content → Short-form brand content. Work links to each project passed. All three populated filter controls, Service→Related Work for all three, filtered Back for all three, direct Project→general Work, and TID/AI Project→Contact visible reference/service preselection passed. Project/Service query-var conflict fix remains intact; Contact sending remains disabled.

Responsive QA: Work and all five published details checked at 1440/1024/390. Grid 3/2/1; frames respectively 410.25×307.69, 441.35×331.01, 331×248.25. Detail typography, summary wrapping, image composition/native ratios and spacing match static references apart from the logged-in WP toolbar (32/46px). One inherited static defect was corrected only in the CMS image helper: native portrait width cap now uses `max-width:min(100%,Npx)` so TID's 440px supporting screenshot fits the 331px mobile content area without distortion. Desktop unchanged, original CSS/static HTML unchanged. No final overflow, missing project image, single-H1 violation or PHP warnings found; actual heading/body font-weight load checks pass. Work filter wrapping remains intentional. 49 public image/script/stylesheet resources return 200. Old detail `.html` routes and excluded Project06/Tommy routes return 404. SEO title/description/social metadata and staging redirect policy remain later-phase work; no SEO plugin installed.

Admin list shows five published Projects with Client, Services, Status, Featured, Sort Order, Media Status and Last Modified columns; Client list contains only Ravo Film and TID Group. Read-only source reconciliation checks verify all five field/media mappings and approval guards. The former one-record Phase 4 check assumptions were updated for Phase 5 rather than restoring obsolete empty/one-card UI. The importer is local-host-gated, allowlisted, one-project per invocation, refuses existing Projects and never publishes automatically.

Home/Hero/Selected Clients/What We Do, original JSON/static files, global CSS/fonts/CTA system are untouched. Services descriptions/media still use the reference snapshot, not term-field migration. About remains the Phase 2 shell; Contact remains the safe prototype. Globals stay outside ACF; Free ACF 6.8.10 remains active. No main merge, production/Vercel/DNS/Supabase/backend/SEO changes. Database records/uploads live only in LocalWP and require a separate backup; this Git checkpoint is code/docs, not a database export.

Stop after Phase 5. Recommended next target: dedicated Services Explorer content migration, first resolving its richer platform subgroups and multiple media against free-ACF capacity without losing approved content. Do not begin automatically.


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
