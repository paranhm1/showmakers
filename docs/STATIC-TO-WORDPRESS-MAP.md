# Static → WordPress migration map

Architecture proposal only; no import or conversion performed. Baseline: `static-approved-v1` / `19e1be22a7952f655003abab5cc1344ac35fbb9b`. Target choices and media eligibility are in [migration plan](WORDPRESS-MIGRATION-PLAN.md); detailed field properties are in [CMS model](CMS-DATA-MODEL.md).

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
| services[] | sm_service term relationships by stable source slug → term ID map. |
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
| id, slug | sm_service term slug; equal in all eight current records; store private original ID if needed for importer. |
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
