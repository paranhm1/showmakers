# ShowMakers CMS data model

Historical Phase 1 model followed by implementation updates. The Phase 3 free-ACF specification and subsequent Phase 4 amendment below define the current implementation. See [migration plan](WORDPRESS-MIGRATION-PLAN.md) for architecture and decisions; [source map](STATIC-TO-WORDPRESS-MAP.md) for exhaustive JSON mapping.

Storage uses WordPress posts, terms, meta and options. IDs are WordPress record/attachment IDs, never staff-entered filesystem paths. “Required” means necessary for publication, not for saving a draft. Public fields render only for eligible records; “private” means explicitly excluded from public HTML/data/REST. Public media approval does not imply private storage security for other uploads.

## PROJECT — `project` custom post type

| Field name | Type | Required/optional | Source | CMS editability | Public/private | Notes |
|---|---|---|---|---|---|---|
| Project Name | Core title | Required | projects.title | Staff | Public | Preserve descriptive titles when original campaign name is unknown. |
| Slug | Core post_name | Required | projects.slug | Admin-controlled after import | Public | Stable route/context identity; redirect needed if changed. |
| Source ID | Immutable string meta | Required for imported records | projects.id | Import/developer only | Private | Distinct from slug: brand-content → short-form-brand-content. |
| Client record | Optional Client post ID | Optional | clients.projects documented association | Staff | Public identity when documented | No display-text matching; all current client associations empty. |
| Client name fallback | Text | Optional | projects.client | Staff | Public | TID Group/Ravo Film can keep confirmed names without invented marquee associations. Show Client-record name when relationship exists, else text; no duplicate canonical client name. |
| Short Summary | Textarea | Required | projects.summary | Staff | Public | No inferred results, dates or scope. |
| Services | Multiple service term IDs | Required for genuine publish | projects.services[] | Staff assigns existing terms | Public | Eight individual services, many-to-many; no pillars taxonomy. |
| Listing Thumbnail | Image attachment ID + usage alt/caption | Required for approved publish | projects.listingThumbnail | Staff | Public approved media only | Separate from detail hero; safe missing-media handling. |
| Listing Fit | Enum cover/contain | Required, default cover | listingThumbnail.fit | Staff bounded choice | Public presentation | Preserve Ravo Film contain; no custom CSS input. |
| Hero Media | Attachment ID + usage alt/caption/type | Required for detail publish | projects.heroMedia | Staff | Public approved media only | Current type image; future video requires reviewed playback behavior. |
| Gallery / Media | Ordered rows: attachment ID, type, alt, caption, fit | Optional | projects.media[] excluding repeated hero | Staff add/replace/reorder | Public approved items only | Keep native ratios. Empty gallery renders no section. |
| Featured | Boolean | Optional, default false | projects.featured | Staff | Private control | Retain data for future controlled usage; current Home has no project feature section. |
| Published | Core status | Required, draft default | projects.published | Staff publish with validation | Public only if publish + eligible | No redundant published meta flag. Import as draft first. |
| Sort Order | Integer sort_order post meta | Required, default 0 | projects.order | Staff | Private control | Deterministic tie-break by ID; drives archive and real-project Next loop. |
| Media Status | approved/pending/restricted enum | Required, default pending | projects.mediaStatus | Staff; restricted release admin-reviewed | Private control | Guard all public queries/media renderers. Does not secure direct upload URLs. |
| Attachment permission | Approved/pending/restricted metadata on each selected media record | Required, pending default | Verified asset permissions + import manifest | Staff/admin per rights policy | Private | Asset guard shared with service/about/client media. Restricted originals not uploaded publicly. |
| Development Only | Boolean | Required, default false | projects.developmentOnly | Developer/admin | Private | Excluded from every public query irrespective of post status. |
| Placeholder | Boolean | Required, default false | projects.isPlaceholder | Developer/admin | Private | Fixture not a real published project; no live detail/Next/context. |
| Project Year / Date | Nullable year integer; date only with full verified date | Optional | projects.year (all null) | Staff | Public only when verified | Do not convert an unknown year to a made-up January 1 date. |
| Project Type | Optional text | Optional | projects.projectType (Ravo Film Website) | Staff | Public if current template uses it | No new taxonomy/filter; current detail does not display a new type section. |
| Detail Presentation | landscape/portrait/digital enum | Required | projects.presentation | Staff bounded choice | Public presentation | Existing templates/styles; not a layout builder. |
| Documentation Note | Textarea | Optional | projects.documentationNote | Staff | Public | Current public limitations statement; distinct from private rights notes. |
| Source / Rights Notes | Private text/meta | Optional | projects.source, VERIFIED-CONTENT.md | Staff/admin | Private | Provenance and restrictions never sent to public scripts. |

Eligibility: publish AND mediaStatus approved AND not developmentOnly AND not placeholder; each referenced media asset must also pass permission checks. Gallery cannot cause an approved project to leak restricted attachments. Unknown client/date stay empty. A project name never proves Client-logo association.

## SERVICE — `service` taxonomy terms with content fields

Services is an editable Admin area, not a separate CPT synchronized to taxonomy. No independent service single/term archive. A visibility flag replaces the proposed Published field because terms have no native post-status publishing lifecycle; content remains available privately for relationship integrity.

| Field name | Type | Required/optional | Source | CMS editability | Public/private | Notes |
|---|---|---|---|---|---|---|
| Service Name | Core term name | Required | services.title | Staff | Public | Preserve eight documented capabilities. |
| Slug | Core term slug | Required | services.id/slug | Admin-controlled | Public | Stable hash/filter/context ID; id equals slug for all eight now. |
| Intro | Textarea term meta | Required | services.shortDescription | Staff | Public | Existing positioning paragraph. |
| Description | Core term description/plain paragraphs | Required | services.description | Staff | Public | No uncontrolled rich layout/shortcodes. |
| Capabilities | Ordered text rows | Required | services.deliverables[] | Staff | Public | No claims inferred beyond source. |
| Platforms | Ordered text rows | Optional | services.platforms[] | Staff | Public | Omitted where detailed platform passages already cover them. |
| Platform Passages | Ordered title/text/icon-key rows | Optional | services.subsections[] | Staff | Public | Select trusted existing SVG icon keys, not raw SVG uploads. |
| Formats | Ordered text rows | Optional | services.formats[] | Staff | Public | Event Management current formats. |
| Media | Ordered approved attachment + alt/caption/type rows | Optional | services.media[] | Staff | Public approved only | No fabricated replacement image when empty. |
| Sort Order | Integer term meta | Required | services.order | Staff | Private control | Numbered index derives from order, not stored duplicate numbering. |
| Visible | Boolean term meta | Required, true for imported eight | services.published | Staff | Private control | Hide Explorer/home entry without deleting term or its project relationships. |
| Related Work | Derived project query | Not a separate field | project service assignments | Not independently editable | Public eligible projects only | Current services.relatedProjects checked during import, not stored twice. |
| Source / Rights Notes | Text + source pages | Optional | services.source | Staff/admin | Private | Not copied into public structured data. |

Fixed initial slugs: `business-consulting`, `social-media-marketing`, `media-production`, `ai-enhanced-content-production`, `website-digital-solutions`, `event-management`, `seo-sem`, `influencer-marketing`. No permanent three-group pillar classification. Staff edits service copy without changing these keys.

## CLIENT — `client` custom post type, non-public routes

| Field name | Type | Required/optional | Source | CMS editability | Public/private | Notes |
|---|---|---|---|---|---|---|
| Client Name | Core title | Required | clients.name | Staff | Public in approved logo context | Genuine clients confirmed by supplied reference. |
| Stable ID / Slug | Core slug + immutable source ID | Required | clients.id | Admin/import | Public identity / private source ID | No standalone client pages. |
| Client Logo | Image attachment ID (`client_logo`) | Required only for future marquee rendering | clients.image | Staff | Public approved logo only | Independent of Project media; preserve artwork/colors and aspect ratio. |
| Show in Selected Clients — future | Dedicated Boolean `show_in_marquee` | Default false | Explicit staff curation | Staff | Private control | Not inferred from generic `visible`, Project existence or publication. Current marquee remains static. |
| Marquee Sort Order — future | Number `marquee_sort_order` | Required for future marquee selection | Existing curated clients.order | Staff | Private control | Preserve approved 18-logo order; do not infer from Project order. |
| Optional Website | Validated URL | Optional | No current source | Staff | Public if later explicitly used | Does not automatically turn existing logos into external links. |
| Logo Approval Status | approved/pending/restricted enum, existing `logo_status` | Required | VERIFIED-CONTENT.md | Staff/admin | Private | Semantically separate from Project and attachment media_status; no mutual approval inference. |
| Associated Projects | Derived reverse Client relation | Not separate storage | clients.projects[] if documented | Set relationship on Project | Public only for documented relationship | No current associations; validate imported references explicitly. |

Website/archive visibility is not equivalent to Admin availability: client records remain admin-managed but exposed publicly only through eligible theme output. No logo implies endorsement, results or campaign responsibilities.

## SITE SETTINGS — reusable options and controlled Page fields

Storage column is encoded in notes: **Options** are reusable; **Page** fields belong to the specified page. This table does not propose a single monolithic options object for every page.

| Field name | Type | Required/optional | Source | CMS editability | Public/private | Notes |
|---|---|---|---|---|---|---|
| Company Name | Text option | Required | site.name | Staff | Public | Options. |
| Sales Email | Email option | Required | site.contact.email | Staff | Public | One source for Contact/footer/fallback messages; separate from mail credentials. |
| Phone | Text option | Optional | site.contact.phone | Staff | Public | Generate sanitized tel URL in code. |
| Address | Textarea option | Optional | site.contact.address | Staff | Public | Shared Contact/footer. |
| WhatsApp | Validated URL option | Optional | site.contact.whatsapp = null | Staff | Public when confirmed | Omit when blank; do not infer from phone. |
| Social Links | Ordered label/URL rows | Optional | site.contact.socialLinks = [] | Staff | Public when confirmed | Instagram only when provided; no invented URL. |
| Footer Identity / Note | Text options | Required identity / optional note | site.footer.copyright/note | Staff | Public | Preserve prototype note until approved replacement. |
| Privacy Notice | Text + optional future approved page link | Required before inquiry collection | site.footer.privacyNotice | Staff | Public | Pending current text must not be treated as final policy. |
| Hero Headline | Two fixed text fields | Required | site.hero.headline[0..1] | Staff | Public | Home Page; typography/line slots fixed; preview longer text for overflow. |
| Hero Introduction / Description | Text/textarea | Required | site.hero.introduction/description | Staff | Public | Home Page; no layout controls. |
| Hero CTA Label | Text | Required | site.hero.action.label | Staff | Public | Home Page; target locked to Work route. |
| Hero Artwork | Approved decorative attachment ID | Optional; seed existing collage | Renderer: assets/images/hero/showmakers-hero-collage.webp | Staff selects approved compatible artwork | Public | Home Page; same mask/crop/positions; empty alt/aria-hidden; not real portfolio evidence. |
| Clients / Services section labels | Text fields | Required | site.home.clientsTitle/servicesTitle/servicesAction | Staff | Public | Home Page; record visibility/order edited in Client/Service admin. |
| Work Page Intro | Title + introduction text | Required | site.work.title/introduction | Staff | Public | Options scoped to archive since Work has no Page record. |
| Work explanatory note | Textarea | Optional | site.work.note | Staff | Private unless current output includes it | Not currently rendered; do not add a section during migration. |
| Services Page Intro / Index Label | Text fields | Required | site.services.title/introduction/indexTitle | Staff | Public | Services Page. |
| About Intro | Title/textarea | Required | site.about.title/introduction | Staff | Public | About Page. |
| About Philosophy | Heading/textarea | Required | site.about.philosophyTitle/philosophyText | Staff | Public | Fixed section. |
| About Human-led Approach | Heading/textarea + approved image + alt/caption | Required | site.about.approachTitle/approachText/media | Staff | Public approved media | Fixed section and current image behavior. |
| About Steps | Four fixed ordered title/text pairs | Required | site.about.steps[] | Staff | Public | Preserve current Think/Create/Execute/Review composition; no arbitrary blocks. |
| About Source | Private text | Optional | site.about.source | Staff/admin | Private | Provenance only. |
| Contact Opening | Headline with controlled line breaks / supporting text | Required | site.contact.title/introduction | Staff | Public | Contact Page; fixed composition. |
| Form Title / Supporting Note | Text/textarea | Required | site.contact.form.title/note | Staff | Public | Current renderer additionally hardcodes prototype notice; unify with controlled template state, not duplicate inconsistent notices. |
| Form Labels / Placeholders / Action Label | Named text fields | Required where used | site.contact.form.fields[].label/placeholder; submitLabel/servicePlaceholder/notSureLabel | Staff | Public | Six fixed field IDs/order/types and required flags remain code; no form builder. |
| UI Labels / Status Messages | Named text fields with safe defaults | Required | site.labels, site.contact.form.states | Staff text only | Public | Minimal render config, never entire site JSON. Shared messages interpolate canonical sales email. |
| 404 Copy | Named title/message/action text | Required | site.notFound | Staff via scoped settings | Public | Routes/layout code-owned. |
| Navigation / Brand Logos / Colors / Fonts | Route definitions and code assets | Required | site.navigation/logo/logoLight, CSS | Developer only | Public output | Not everyday CMS settings. Keep four approved items; map to permalinks in code. |
| Form Endpoint / Delivery Recipient / Security | Server configuration | Optional until backend approved | site.contact.form.endpoint = null | Admin/developer only | Endpoint public when enabled; credentials private | Not arbitrary staff-editable URL. No setup in Phase 1. |

Future field editor must constrain rich input/line breaks rather than expose layout, raw scripts/CSS or arbitrary embeds. Staff preview/content validation should catch oversized headlines without silently changing the design system.

## CONTACT INQUIRY — future, optional private `sm_inquiry` CPT

Not implemented; only needed if inquiry storage is approved. Email-only processing does not require this content type. Contains personal data; no public routes, REST fields, search/feed/sitemap entries or public IDs beyond an opaque acknowledgement if needed.

| Field name | Type | Required/optional | Source | CMS editability | Public/private | Notes |
|---|---|---|---|---|---|---|
| Inquiry ID | Server ID | Required if stored | WordPress | No | Private | Never client-assigned. |
| Name | Sanitized text | Required | form.name | Restricted inquiry staff | Private | Existing 254-character limit. |
| Company | Sanitized text | Optional | form.company | Restricted inquiry staff | Private | Existing 254-character limit. |
| Service | Validated term ID or not-sure sentinel | Required | form.service | Restricted inquiry staff | Private | Unknown IDs rejected; not-sure is not a ninth service. |
| Goal | Plain text | Required | form.goal | Restricted inquiry staff | Private | Existing 6000-character limit. |
| Email | Validated email | Required | form.email | Restricted inquiry staff | Private | Safe Reply-To; not From/recipient injection. |
| Additional Details | Plain text | Optional | form.additionalDetails | Restricted inquiry staff | Private | Existing 6000-character limit. |
| Project Reference | Eligible Project ID + optional submitted slug snapshot | Optional | form.projectReference | Restricted inquiry staff | Private | Server revalidates; no development/restricted project context. |
| Source Context | Allowlisted route string | Optional | form.sourcePage | Read-only | Private | Inferred context, not actual referrer history. Adapt .html routes to WP. |
| Created At | Server timestamp | Required | Server | Read-only | Private | Never trust a submitted timestamp. |
| Status | Minimal new/handled enum | Required if storage chosen | Server/admin | Authorized inquiry staff | Private | No speculative approval/CRM workflow. |
| Notification Outcome | Minimal delivery processing state | Optional | Server | Read-only | Private | Transport acceptance is not confirmed inbox delivery; no sensitive payload in public logs. |

Retention, mail transport, privacy policy and who can access stored inquiries require approval before collection. No IP tracking, analytics, budget field, testimonials or performance-result structure is introduced.

## Phase 3 implemented specification — FREE ACF (authoritative)

Official free Advanced Custom Fields 6.8.10 is active locally. No Pro dependency, Repeater, Gallery, Flexible Content, Clone, Options Page or ACF Block is used. The earlier tables describe the eventual model; this section defines the fields actually available now. No real projects or clients have been imported.

### Registered structures and relationships

- `project`: public CPT; title, featured image and revisions; REST enabled. Archive `/work/`, singles `/work/{slug}/`. No author/editor/excerpt UI; `short_summary` is the canonical summary. Core status controls publishing, without a duplicate visible flag.
- `client`: admin-only CPT; title and revisions. No public query, archive, single rewrite or REST collection.
- `service`: nonhierarchical Project taxonomy, multiple assignments; no public term archive/query variable. This intentionally supersedes the original `sm_service` name; disabling its query variable preserves existing `?service=` filter syntax. The three editorial capability groups are not taxonomy terms.
- Eight structural terms only: `business-consulting`, `social-media-marketing`, `media-production`, `ai-enhanced-content-production`, `website-digital-solutions`, `event-management`, `seo-sem`, `influencer-marketing`. Activation inserts missing terms without overwriting existing fields. New unapproved slugs are rejected; existing slugs stay stable on edit. Administrator retains term-management authority; staff permissions remain a later task.
- Ordering: `sort_order` Number on all three types, lower first; Project/Client use post meta, Service uses term meta. Do not also use `menu_order`. Later frontend queries should sort numerically then by stable ID. Admin Project/Client lists support numeric sorting.

### Actual fields

| Group | Stored field names and free field types |
|---|---|
| Project Information | `client` Post Object (one Client ID, optional); `client_name` Text (verified fallback only); `short_summary` Textarea; `project_year` optional Number; `sort_order` Number |
| Project Relationships | `services` Taxonomy multi-select, returning IDs and saving/loading actual `service` terms |
| Project Media | `listing_thumbnail` Image ID; `listing_fit` Select cover/contain; `hero_media` Image ID; `project_image_1` through `project_image_5` optional Image IDs |
| Project Display / Governance | `featured` True/False; `media_status` Select approved/pending/restricted, default pending; `internal_media_note` private Textarea |
| Client | Core title; `client_logo` Image ID; `visible` True/False default false; `sort_order` Number; `website_url` URL; separate `logo_status` Select approved/pending/restricted default pending |
| Service term | `service_intro`, `service_description`, `capabilities`, `platforms`, `formats` Textarea; `service_media` Image ID; `sort_order` Number; `visible` True/False |
| Attachment permission | `media_status` Select approved/pending/restricted default pending; `internal_media_note` private Textarea |

Additional project media uses five optional numbered image slots rather than post content: the current frontend is image-led, so bounded fields are easier to understand and preserve order without accepting arbitrary layout markup. Empty slots are omitted. This is temporary, not a custom gallery framework. Hero is currently an Image, not a File/video playback system. Capabilities/platforms/formats use one item per line, plain text, not serialized rows. Later renderers must split on line boundaries, trim and omit blank values.

### Configuration and staff workflow

Plugin: `wordpress/wp-content/plugins/showmakers-content/`; modules: `inc/types.php`, `inc/fields.php`, `inc/admin.php`, `inc/media.php`. Four versioned groups live in `acf-json/group_showmakers_{projects,clients,services,media}.json`. ACF loads JSON directly; save paths are scoped to these groups so unrelated ACF configuration is not redirected. JSON sync is not required to render the local definitions. Never commit license keys.

Staff workflow: create/edit Clients first when a relationship is documented; create Project draft; select one Client and any relevant individual Services; enter verified summary/year; choose images from Media Library; set Project and each attachment approval independently; adjust sort order; review before publishing. Unknown Client/year/media can remain empty. Use Services under Projects to edit term content. Admin tabs separate information, relationships, media, display and governance; help text explains public use and unknown facts.

Projects list: Project, Client, Services, Status, Featured, Sort Order, Media Status, Last Modified. Clients list: Client, small Logo preview, Visible, Sort Order, Last Modified. Custom CPT/taxonomy capabilities are granted only to the existing Administrator at activation. Future staff role should receive selected content/media capabilities, without plugin/theme editors or site configuration; that role is not created now. Core revisions are supported and `sort_order` is registered for revisions; do not assume every ACF value has full historical restore coverage without testing the later editorial workflow.

### Media and public safety

`showmakers_public_project()` requires published + approved and excludes development/placeholder flags. `showmakers_approved_media()` requires an approved attachment and rejects known restricted `brand-showcase` / `samsung-experience` filename prefixes. `showmakers_project_images()` returns only approved IDs for eligible projects. Archive/single main queries, anonymous REST, sitemap queries and mixed public query results exclude ineligible Projects. `showmakers_public_client_logo()` independently requires a published visible Client, approved logo permission and approved attachment. Client approval never grants project-photo permission.

All four ACF groups have public REST exposure disabled; internal notes are never emitted by this plugin. Text fields are sanitized; Admin output is escaped. Phase 4 must use the guards consistently, escape text/attributes/URLs and allow only reviewed rich text through `wp_kses_post` if introduced. An attachment approval flag does **not** protect its raw upload URL or WordPress attachment endpoint. Never upload restricted originals to the public Media Library; keep them outside deployable assets. Known-name rejection is an additional safeguard, not a substitute for rights review. Static approved presentation remains separate until Phase 4.

### Future Pro upgrade (not performed)

- `capabilities` and `platforms` concepts may become Repeaters. Back up and explicitly transform newline strings into rows before changing field types; preserve stable conceptual names and validate frontend output.
- Numbered project image IDs migrate in slot order into a Gallery (`project_gallery`), preserving attachment IDs/alt/captions. Retain old fields during validation and remove only after a confirmed migration.
- `service_media` currently holds one image. Existing source media arrays and richer platform descriptions must not be truncated during Phase 4: retain their existing structured source until an approved compatible field extension or Pro upgrade is available.
- Global copy/contact/footer/hero settings remain in existing structured data/theme code, not duplicated into ACF. A future Pro Options Page can receive these settings through one explicit migration.
- No SEO plugin or competing SEO fields are added. Future Yoast/Rank Math integration owns title, description, social image, canonical, noindex, sitemap and schema. Map summary/listing image to the chosen plugin; clean project slugs and core featured-image support already exist.

## Phase 4 local pilot amendment

One Ravo Film Project (18), distinct Ravo Film Client (17) and existing website-digital-solutions term (7) are connected; one approved image (16) is reused. The eight term bodies and other records remain unmigrated. Public queries/context use only eligible CMS Projects; an authorized, specifically requested draft preview may show approved draft media before publication. Project `query_var=false` preserves Contact's `?project=` context; native rewrites retain `/work/{slug}/`.

Import-only metadata now present: `source_id` and `_showmakers_provenance` privately identify the source; bounded `presentation` retains original media composition; `documentation_note` is the existing **public** factual disclaimer, separate from private `internal_media_note`. These are seeded metadata, not additional staff-facing ACF design fields. Staff edit title/summary/Client/Services/images/order/approval through existing free fields; future editing of the seeded public disclaimer needs a reviewed simple editorial field, not a layout builder. No Pro field or extra content taxonomy was introduced. See the Phase 4 map/plan for the exact runtime transition and known remaining static sources.


## Phase 6 free-ACF Service additions — 2026-10-05

Existing Service field names/term IDs remain stable. To preserve approved two-image and four-platform Services content, add optional `service_media_2` (Image ID), `service_media_caption` and `service_media_2_caption` (Textarea), and `platform_facebook_description`, `platform_instagram_description`, `platform_xiaohongshu_description`, `platform_tiktok_description` (Textarea). This is a bounded temporary free-ACF model, not a custom repeater. Capabilities/Platforms/Formats remain newline textareas. Usage captions do not overwrite attachment captions. Approved media guard applies to both image slots. Only Visible services appear publicly; Project term assignments survive hiding.

Future Pro: newline lists → Repeaters, image slots → Gallery/image-caption Repeater, platform descriptions → platform Repeater, explicitly approved globals → Options Page. Keep stable concepts and provide a deliberate migration; do not silently reinterpret existing field values. See Phase 6 completion in WORDPRESS-MIGRATION-PLAN.md for exact ID/media map, source and QA.


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


## About Page CMS migration completion — 2026-10-05

Canonical entity: existing published WordPress **Page ID 9**, title About, slug `about`, public URL `/about/`. No About CPT, taxonomy or page builder. This completion supersedes earlier references to the remaining About shell. Local About runtime now reads Page metadata only; original `about.html` and `data/site.json` remain approved reference material, not a second runtime source.

Actual approved sections migrated 1:1:

| Section | Approved source | CMS fields |
|---|---|---|
| Yellow intro: Ideas need people. | Static About / `site.about.title`, `introduction` | `about_hero_heading`, `about_hero_intro` |
| White brand philosophy | Static About / `site.about.philosophyTitle`, `philosophyText` | `about_philosophy_heading`, `about_philosophy_body` |
| Charcoal people/execution statement | Static About / `site.about.approachTitle`, `approachText` | `about_approach_heading`, `about_approach_body` |
| Supporting on-set production photo | Approved `assets/images/work/on-set.webp`; 2026 Profile page 23 | `about_approach_image`, `about_approach_image_alt` |
| White Think / Create / Execute / Review steps | Static About / `site.about.steps`; editorial summary of existing material | `about_step_1_heading`, `about_step_1_body` through `about_step_4_heading`, `about_step_4_body` |

No separate About CTA exists in the approved baseline; none added. Shared header/footer remain unchanged. Approved copy preserves marketing agency positioning. Profile pages 6–7 support the brand goals/audience, human-led creative/execution and internal-review narrative; no new claims, dates, metrics or team facts. Brand Guidelines remain visual authority.

**About Page Content** (`group_showmakers_about`) has 16 fields: 14 required Text/Textarea copy fields plus optional Image ID and optional contextual alt Text. All use free ACF 6.8.10; four explicit step pairs match the fixed layout, so no Repeater/Gallery/Options Page workaround or new dependency. Plain text is sanitized on save and escaped on output. Required whitespace-only copy fails ACF validation; incomplete existing metadata is highlighted by an Admin notice. H1 falls back to the canonical Page title if external data is incomplete; empty headings/paragraphs/sections are omitted safely without static-copy fallback. Image absence does not fabricate a substitute. Empty contextual alt falls back to the Media Library image description.

Image attachment **92**: approved on-set WebP, original **576 × 1024**, hash identical to the approved source file. Alt: “A camera operator on a ShowMakers production set.” General production material, no specific finished-project association. Private source/provenance records retained in local metadata; no new public caption because the baseline has none. Existing shared approved-media guard suppresses pending/restricted/unavailable imagery. Native dimensions, lazy loading and existing CSS cropping retained; no replacement/upscaled source or restricted Tommy Hilfiger/Samsung photography.

Staff workflow: **WP Admin → Pages → About → About Page Content → edit text/select approved Production Image → Update**. Labels/help/order follow intro, philosophy, people/execution, image/description, steps 1–4. Required copy prevents accidental blank saves. Native classic field editor applies only to Page 9, avoiding an unused block canvas; no editor plugin or other Page editor change. Theme retains section composition, palette, MADE TOMMY/FUTURA PT, responsive sizing, whitespace, image crop, numbering, motion and protected Hands-on word grouping. Staff do not edit HTML/CSS/JS. Local JSON targets Page 9 only; a future installation with a different canonical About ID must deliberately rebind this location and the About-only editor target.

QA: approved static main markup/content matched the CMS candidate apart from image delivery URL. Live 1440/1024/390 comparisons matched typography, colors, section/copy/image/step bounds and footer transition within 0.1px; no horizontal overflow. All 18 heading/paragraph/number text items matched. One H1, six logical H2s, meaningful image alt, unchanged contrast and keyboard focus outline (3px) verified. About has no animation dependency for readable copy; all content is present in server HTML. Actual Admin whitespace-required validation blocked save, then original content was restored and successfully saved. Read-only missing image, restricted/pending image, alt fallback and markup sanitization checks passed.

Regression: full pre-migration Project/Client/attachment metadata and Service term metadata matched afterward (excluding transient editor locks). Home Hero/artwork and What We Do unchanged; 18 approved Client logos/order unchanged. Work counts 5 / Media 2 / Website 2 / AI 1; all 8 Services remain WordPress-driven. Browser Home → Media Service → Related Work → Project → filtered Back to Work passed; Website filter → Ravo → contextual Contact passed with Website selected, Ravo reference and empty submission endpoint. Existing pilot, Services, Clients checks, anonymous frontend checks (46 image/script/stylesheet assets), original static checks and PHP syntax passed. No Contact backend or broader content migration.

Local Page/Media values live in LocalWP DB/uploads, excluded from Git. Temporary seed/checkpoint/fault-injection scripts remain outside Git. Code checkpoint includes theme/plugin/ACF Local JSON, existing frontend regression check and these migration documents only. Keep a separate local content/uploads backup for handover.

Remaining static/global content: Home Hero and supporting company copy, Services introductory/global copy, Contact prototype copy, header/footer/contact/social settings and Privacy Notice. About no longer belongs to that list. Broader Home/global settings, Contact backend, SEO, roles, staff handover, staging/production, main, Vercel, DNS and Supabase are deferred. Recommend **Home remaining copy/global content** as the next separately approved scope before Contact backend; no ACF Options Page is introduced. Stop after this About checkpoint; do not start the next phase automatically.


## Home remaining copy / Services intro migration — 2026-10-05

Completed narrow page-copy migration; no design or global settings migration. Clean preflight on `wordpress-cms` at `e29dae77e3eb38b67eb25e4c894f66dd40b51ae3`; ShowMakers theme/content plugin and official free ACF active. No Home Page existed: `page_on_front=0`, theme-only homepage. Created canonical published **Home Page 95** and assigned `show_on_front=page`, `page_on_front=95`. This necessary local Page binding preserves `/` and the existing front-page template; no builder/CPT or new public section. Existing **Services Page 8** retains `/services/`.

| Group / Page | Field | Required | Exact migrated wording |
|---|---|---|---|
| Home Page Content / 95 | `home_hero_line_1` | Yes | Marketing ideas. |
| Home Page Content / 95 | `home_hero_line_2` | Yes | Made to happen. |
| Home Page Content / 95 | `home_hero_introduction` | No | We’re ShowMakers. |
| Home Page Content / 95 | `home_hero_supporting_copy` | No | A marketing agency bringing strategy, creative thinking and hands-on execution together. |
| Home Page Content / 95 | `home_hero_cta_label` | No | View Work |
| Services Page Content / 8 | `services_page_heading` | Yes | Services |
| Services Page Content / 8 | `services_page_intro` | No | Marketing thinking, creative content and execution across channels. Eight individual services, connected by the needs of your brand. |

Source: approved current runtime templates, matching static `index.html`/`services.html`. No rewriting/unsupported claims. Two Hero span fields preserve editorial line structure without stored HTML/manual breaks. Plain Text/Textarea only, sanitized on save and escaped on output. Whitespace-only required headings rejected. Missing optional text/CTA omits its element; no empty explanation/support wrapper. Missing all required Hero lines falls back to Page title; Services heading falls back to Page title. Admin warnings flag incomplete heading metadata. Actual Home whitespace save was blocked and approved value restored/saved successfully.

Staff: Pages → Home → Home Page Content → edit → Update; fields ordered Hero Line 1, Hero Line 2, Introduction, Supporting Copy, Hero CTA Label. Pages → Services → Services Page Content → Services Heading, Services Introduction → Update. Plain labels/help observed in actual Admin. Native classic field editor applies only to those two Pages (existing About setting preserved). JSON groups `group_showmakers_home`, `group_showmakers_services_page` scoped to Page IDs 95/8. Future installation must deliberately rebind location/editor IDs and front-page assignment if IDs differ; Git alone does not transport local content or Reading settings.

Theme still controls Hero artwork/delivery/crop/dimensions/semantics/reveal, typography, yellow surface, section order, spacing, CTA SVG and `/work/` destination. No image uploads/changes. What We Do names/order/visibility remain Service-taxonomy-driven. Selected Clients remains independent Client-CPT-driven with 18 approved logos/order. Section labels “Selected clients”, “What we do”, “All Services” and Explorer UI prompt remain theme interface labels, not duplicated page fields. Individual descriptions/capabilities/platforms/media belong only to Service terms. Shared Footer/contact/social/Privacy Notice/global content remains deferred; no Options Page.

Runtime templates read Page metadata only for migrated wording; existing file name `home-static.php` is retained to avoid unnecessary template renaming, but Hero copy is CMS-owned. Original static HTML/JSON retained for rollback/reference, never concatenated with CMS wording. Exact pre/post main markup comparison passed for Home and Services, including artwork, marquee and Explorer.

QA: actual viewport widths 1440/1024/390 verified for both pages; approved static/CMS Hero/intro/Explorer dimensions matched within 0.1px, no horizontal overflow, one H1 each, original fonts/colors/wrapping. Mobile screenshots visually reviewed. Services keyboard ArrowDown moves focus between existing controls. Existing JS/CSS, reduced-motion and marquee behavior unchanged. All migrated copy remains server-rendered. Read-only empty optional Home text/CTA test omitted support wrapper/action safely; all required filters reject whitespace.

Regression: checkpoint comparison confirms About Page 9/16 fields/image92, all existing Project/Client/attachment metadata and eight Service term records unchanged (excluding transient edit locks; only two new Services Page fields added). Work All5/Media2/Website2/AI1, Client marquee18/order, Services8, Home taxonomy index intact. Existing pilot, Services, Clients and anonymous frontend tests passed (46 public assets); static `npm run check` and changed PHP syntax passed. Contact context data/empty endpoint preserved. No footer/backend/SEO/roles/manual/staging/production/DNS/Vercel/Supabase/main changes.

Local Page/meta/Reading settings reside in LocalWP DB; no DB/uploads or temporary migration/debug scripts committed. Git scope is theme/content-plugin code, two free ACF JSON groups and these three documents. Keep local content backup separately. Next recommendation: **Contact + global company/footer content**, with scope clarified separately for content versus submission; SEO preparation follows final public content/settings. Do not begin either automatically. No staging environment is created; eventual deployment remains LocalWP QA → checklist → later production → production QA.


## Contact backend / shared company and Footer settings — 2026-10-05

This completion supersedes earlier inactive Contact/global-static boundaries. Clean `wordpress-cms` baseline `141ef5a197949c673fd35e7f3ee08ac9cb9ed10a`; main remains `19e1be22a7952f655003abab5cc1344ac35fbb9b`. Existing theme, content plugin, ACF Free and LocalWP confirmed. No redesign or production/staging deployment.

Canonical globals: one native WordPress option **`showmakers_site_settings`**, managed at **WP Admin → ShowMakers Settings → Save Changes** with `manage_options`. Native Settings API nonce/capability handling; not an ACF Pro Options Page or public Settings Page. Stored values are plain text; no secrets or SMTP fields. Email required/valid, no CR/LF, max254; phone optional valid phone characters/max64/min7 digits; address optional/max600; Footer strings optional/max160/200. Invalid values retain previous setting with an Admin error. Tel destination derived from phone, never an independently drifting setting.

| Option member | Migrated approved value | Runtime consumers |
|---|---|---|
| `contact_email` | sales@showmakers.org | Contact email link, Footer link, pending Privacy Notice contact, form feedback and trusted enquiry recipient |
| `phone` | +6012-687 8775 | Contact and Footer |
| `address` | No.13-2, First Floor, Jalan Radin Bagus 6, Bandar Baru Sri Petaling, 57000 Kuala Lumpur. | Contact and Footer |
| `footer_copyright` | ShowMakers E&M | Existing bottom company wording; no legal-name/year invention |
| `footer_note` | Visual prototype · Provisional copy | Existing bottom note |

Email/phone/address corroborated by **2026 Company Profile page 37**, matching approved runtime/static data. Footer strings migrated exactly. No active social links exist, so no social settings/icons were created. Footer navigation/logo/layout, Privacy Notice label and currently pending text stay theme-controlled. Pending Privacy Notice is not represented as an approved legal policy. Original static sources remain reference only; shared values have no duplicate runtime template copies.

Contact canonical **Page 10** (`/contact/`), free JSON group `group_showmakers_contact_page` / **Contact Page Content**. Fields in display order: required `contact_heading_line_1` = “LET’S MAKE”; required `contact_heading_line_2` = “SOMETHING HAPPEN.”; optional `contact_intro_line_1` = “Have a project in mind?”; optional `contact_intro_line_2` = “Tell us a little about it.” All Text, sanitized/escaped, no HTML or WYSIWYG. Required whitespace is rejected by the existing scoped page-copy validator; missing both headings falls back to core Page title with Admin warning; optional empty intro lines/wrapper omitted. Pages → Contact → Contact Page Content → Update, native classic fields for Page10 only; existing Page9/8/95 editor behavior unchanged. Future install must rebind scoped Page IDs if different.

Actual conversational form inventory retained: required Name (`name`,max254), optional Company (`company`,max254), required Service (`service`,visible term slug or `not-sure`), required goal/message (`goal`,max6000), required Email (`email`,max254), optional Additional Details (`additionalDetails`,max6000). Existing `projectReference` and `sourcePage` remain contextual fields, not new visible questions. Form labels/placeholders/field order/arrow/inline error design remain theme-owned.

Backend: **POST `/wp-admin/admin-ajax.php`, action `showmakers_contact`**, registered for anonymous and logged-in visitors in `showmakers-content/inc/contact.php`. FormData fetch carries WordPress action nonce and signed timestamp/UUID. Server authoritative validation rejects malformed/non-string inputs, oversized HTTP bodies (>32KB), missing/whitespace required values, invalid/CRLF email, unknown/hidden service, unavailable/restricted/development Project and disallowed source path. Context is looked up against actual eligible Projects/visible services; raw query strings are never treated as verified titles. The existing reserved `project` query conflict remains fixed.

CSRF: action nonce plus same-origin check where browser Origin is present. Anonymous nonce is not authentication or sole spam control. Signed form token allows 2 seconds–2 hours; hidden honeypot rejects nonempty values. Contact sends no-cache headers; future host/CDN caching must exclude Contact so short-lived tokens remain fresh. Rate limit: **5 processed attempts per 15 minutes** using temporary HMAC of server `REMOTE_ADDR` and WP salt (no untrusted forwarded IP); only counter/expiry stored, no raw IP/profile/message. Success marker keyed by signed random token prevents repeat send for2h; brief atomic option lock/second check prevents concurrent duplicate sends. Lock stores expiry only and is removed in `finally`; stale same-token locks can be reclaimed after60s. Failed mail is retryable; success provides a new signed token. Shared-network clients may share a rate limit and can use direct email.

Recipient comes only from trusted `contact_email`; user-supplied recipient ignored. `wp_mail()` plain UTF-8 email: **ShowMakers Website Enquiry — [Name / Company]**, submitted name/email/company/service/goal/additional details, verified Project title if present, allowed source path. Reply-To is validated submitter email; no user-controlled From/Cc/Bcc/attachments. Service entity names decoded for readable plain email. No third-party email API, SMTP plugin/credentials, Inquiry CPT/table/CRM/Supabase or persistent submission record. Application does not intentionally log form bodies to debug files/Git/analytics. Local Mailpit stores synthetic QA messages as its deliberate development catcher; no real customer data used.

Responses: structured HTTP status, `success`, safe message and per-field errors. Only `wp_mail` acceptance returns success; false produces503, never fake success. Browser maps server errors to existing field-error / aria-describedby / aria-invalid, retains inputs on failure, disables duplicate clicks while pending, resets only on accepted send and retains approved query context. Status uses existing polite live region. No-JS: submit stays disabled and noscript directs visitors to the Contact email; server security does not depend on JS. Removed temporary “submission being prepared” notice only after backend/mail verification. Success reports “sent”, not guaranteed delivery to a production inbox.

Local transport: existing PHP sendmail points to LocalWP **Mailpit SMTP127.0.0.1:10001**, UI/API10000. Anonymous HTTP and logged-in browser submissions were actually captured; recipient sales@showmakers.org, clear subject, Ravo Film / Website service context and Reply-To verified. No external test delivery. `scripts/check_wp_contact.php` intercepts mail only for local safety tests: valid form, missing name, invalid/CRLF email, invalid service, oversized message, forged/expired nonce/token, cross-origin, unavailable project/source, honeypot, fast submission, repeated request/rate limit, duplicate, ignored recipient override/XSS removal, mail false and settings invalid recipient retention. Temporary synthetic limits are cleaned up. UI server-invalid `qa@localhost` showed inline email error/aria-invalid, preserved input/no overflow; normal browser send showed real accepted success.

1440/1024/390: actual viewport widths verified; same field dimensions/spacing/order and exact Footer bounds as approved static. Intentional change: removing temporary notice moves first field up112px; no form redesign. One H1, headings/alt semantics, live errors and focus retained. Long198-character synthetic email tested at390 with no Contact/Footer/page overflow and same canonical value in both places, then approved values restored. Minimal WordPress-only wrap rules protect editable Footer links. Existing CSS/motion design unchanged.

Regression: full checkpoint confirms Home95, About9/16 fields/on-set92, Service term metadata, Project/Client/media records unchanged except transient edit locks and the four new Contact fields. Home artwork unchanged; taxonomy What We Do and Explorer8 intact; Client marquee18 approved order intact; Work All5/Media2/Website2/AI1, all detail routes, filter/context data safe. Existing static, pilot, Services, Clients, updated anonymous frontend (46 public assets) and new Contact tests passed; changed PHP/JS syntax checked. Historical anonymous Contact assertion was updated from inactive endpoint to the approved WordPress backend rather than restoring obsolete UI.

Production remains unverified: Hostinger external inbox delivery/transport/sender authentication must be tested after later approved deployment. `wp_mail` success means transport acceptance, not confirmed inbox delivery ([WordPress reference](https://developer.wordpress.org/reference/functions/wp_mail/)). No production SMTP/DNS/domain changes here. Privacy Notice content/retention approval remains pending before launch; no new legal policy fabricated.

Git includes plugin/theme/settings/backend/ACF JSON/tests/docs only. Local settings/Page data and catcher messages stay outside Git; no DB/uploads/credentials. Original static reference files retained. No SEO, roles/manual, newsletter, Inquiry storage, staging or production. Recommend **SEO preparation next**, after this review, while Privacy Notice and production email verification remain explicit pre-launch items; staff roles/manual follows the settled CMS. Stop; no next phase starts automatically.

## Technical SEO preparation — native WordPress + free ACF

One project-owned adapter (`showmakers-content/inc/seo.php`) uses WordPress title/robots/sitemap hooks; **no SEO plugin installed**. Inventory contained ACF Free and showmakers-content only. A full free SEO plugin would add a second editor/settings surface for a small, deliberately constrained catalogue; native core already supplies sitemap/robots/title APIs, and three optional free-ACF fields meet current staff needs. No scoring, AI copy generation, analytics, social accounts, LocalBusiness or redirect plugin. If a dedicated SEO plugin is adopted later, remove/replace this adapter first; never enable competing metadata/schema/sitemap systems.

`group_showmakers_seo` (ACF Local JSON) adds **Search and Sharing** on Pages/Projects: optional `seo_title` (Text), `seo_description` (Textarea), `seo_social_image` (Image ID). No Pro features or required SEO approval workflow. Staff: Edit Page/Project → optional SEO Title / Meta Description / Social Image → Update. Titles/descriptions sanitized and output escaped. These fields do not rewrite the visible page copy. Unapproved/non-allowlisted Pages remain noindex even when fields are filled.

Default titles: Home `ShowMakers | Marketing Agency`; Work `Our Work | ShowMakers`; Services `Marketing Services | ShowMakers`; About `About ShowMakers | Marketing Agency`; Contact `Contact ShowMakers`; Project `{existing Project title} | ShowMakers`. Description defaults use Home supporting copy, Services introduction, About introduction, joined Contact introductory lines, or Project `short_summary`; Work uses its approved introduction “Selected projects from ShowMakers.” No internal notes, results, dates or claims are generated. Empty Project summary produces no description rather than fabricated copy.

Canonical and `og:url` are runtime URLs from WordPress `home_url()` / Project permalinks, stripped of filter/context/query state. One canonical per eligible public page; `/work/?service=…` canonicalizes to `/work/`; Contact project/service context canonicalizes to `/contact/`. Frontend filtering, history and contextual prefill remain independent and intact. No permanent local hostname stored in SEO data.

Open Graph includes title, description when available, canonical URL, website type, site name, image dimensions and alt. Optional sharing image must pass **attachment `media_status=approved`**, restricted-filename guard and file existence check; Project fallback is approved hero media, then existing ShowMakers logo (838×342). Pending/restricted media is never selected. Existing logo is a safe fallback, not a newly designed optimized social card; platform cropping may vary. No fictional Twitter/X handles or social identities. Client logo approval does not approve Project media.

One coherent JSON-LD graph: `Organization` + `WebSite`, stable canonical-home IDs and publisher reference. Brand name ShowMakers and existing logo; email/telephone/address read only from public ShowMakers Settings (sales@showmakers.org, +6012-687 8775, verified Sri Petaling address; 2026 profile p37). No admin email/internal IDs/notes, founding year, staff count, ratings, hours, coordinates, awards or unsupported service area. Organization chosen over LocalBusiness because no additional verified local-business facts are required for this phase.

Core XML sitemap uses only Posts provider subtypes `page` and `project`: five approved Pages (Home95, Work7, Services8, About9, Contact10) and five eligible published Projects. Native publish query plus existing approved/non-placeholder/non-development Project restrictions remain authoritative. No Posts, users/authors, Client CPT, Service taxonomy, attachments, drafts/private/non-allowlisted pages. Client CPT and Service taxonomy retain internal-only registrations and all relationships. Native attachment pages are already disabled (`wp_attachment_pages_enabled=0`); existing image permalink redirects301 to the original image, without disrupting Media Library.

Local visibility was audited as `blog_public=1` and corrected to **0**. Adapter also enforces noindex/nofollow and robots `Disallow: /` for local/development/staging environments or local hosts, independent of accidental visibility changes. No staging was created. Local sitemap is intentionally disabled (HTTP404). Read-only core provider + XML-renderer projection proves the exact ten future public entries without enabling LocalWP indexing. Production URLs/indexability require later launch QA.

Explicit legacy redirect map and launch checklist: see `STATIC-TO-WORDPRESS-MAP.md` and `WORDPRESS-MIGRATION-PLAN.md`. Public SEO overrides do not make private content public. **Approved Privacy Notice remains a production-launch blocker** because Contact processes personal information through email; no legal policy is invented here.

## Focused enhancement: one short Project video

Existing Projects default to **Image** without bulk metadata updates. Added free-ACF fields in the existing Project Media tab:

| Field | Type / behavior |
| --- | --- |
| `project_media_type` | Select `image` / `video`, default `image`; missing/legacy value renders Image. |
| `project_video` | File attachment ID, MP4 only; shown with native ACF conditional logic when type=Video. |
| `project_video_poster` | Image attachment ID, approved poster; shown when type=Video. |

Existing `listing_thumbnail`, `hero_media`, Additional Image1–5 and individual Service assignments remain unchanged. Work cards remain static thumbnails/4:3/current grid; there is no Video filter or archive autoplay. Video detail replaces only the existing Hero media slot, preserving content flow and native horizontal/vertical ratio; vertical media is centered and capped at540px/native width. Additional images remain available.

Plugin-owned `inc/video.php` validates an actual Media Library MP4 attachment, extension, file existence, parser metadata and MIME. WordPress's raw video parser duration avoids its rounded stored-length loophole: reliably measured duration>10s is rejected with “Project videos should be 10 seconds or shorter.” This validation is scoped to Project usage, never all Media Library uploads. Unknown duration receives a manual-confirmation admin warning; no transcoding, size-increasing settings or external-video URLs. H.264,1080p or smaller, ≤10s, preferably below20–30MB; **current LocalWP effective upload maximum is2MB** (PHP upload_max_filesize2M, post_max_size8M). The lower actual server limit wins; Hostinger limits are a later production task.

Video and poster fields are not globally required, allowing incomplete Drafts. ACF provides field errors for invalid selections/publication; a core publication guard keeps incomplete/invalid Video Projects in Draft even if ACF is bypassed. Publication requires valid approved MP4 and available approved image poster. Public playback additionally requires the Project's normal Approved/published/non-placeholder/non-development eligibility and approved video attachment. Authorized exact Draft preview may show approved media, but never anonymous Draft SEO output. Approval is not raw-file access control: restricted/confidential originals must stay outside public uploads.

Public player starts muted, inline and looping via progressive JS only after reduced-motion preference is known. `preload="metadata"`, explicit dimensions/aspect ratio and poster reserve space; no unrelated videos load on other Projects or Work. A labelled ≥44px keyboard-accessible Play/Pause button uses existing brand tokens/FUTURA body typography and a visible focus outline. Browser autoplay refusal leaves manual Play available. Reduced motion starts paused on the poster with autoplay/loop disabled; manual playback is allowed without looping. Changing preference pauses playback; backgrounding the page pauses it. No JavaScript: native controls remain, with no autoplay/loop. Media-load errors show the approved poster and announce unavailability. Missing/unapproved video uses approved poster, then approved Hero image, otherwise omits media cleanly. Client logos/approval/marquee are unrelated.

SEO adapter retains titles/descriptions/canonicals/schema/sitemap. For Video Projects, approved custom social image remains optional; automatic fallback is approved video poster, approved listing thumbnail, then existing brand logo. MP4 is never `og:image`, preload metadata or schema; no VideoObject added. Restricted/pending images/videos do not enter public media output. Essential spoken information **requires captions**; current simple player is for visual portfolio clips without essential dialogue. Do not publish dialogue-dependent clips until an approved caption-capable implementation/content is supplied; no invented captions.

Staff workflow: Projects → Add/Edit Project → Media → Project Media Type=Video → keep/select approved Listing Thumbnail → upload/select Project Video → upload/select approved Video Poster → Media Governance → Project Media Status=Approved → Preview → Publish/Update. Approve the actual video and poster attachments in Media Library → Edit → Media Permission as well. Save Draft while preparing assets. AI-assisted content currently has no actual approved MP4 and remains Image; staff may follow this workflow when the real file is ready. No automatic video generation/substitution or Client relationship changes.

## Technical SEO follow-up after approved video workflow

Current baseline `efca84cd2d669942cda01abefde26cd31f50c6ab`; this focused follow-up retains the already implemented **single native SEO system**, not a second plugin. The free [The SEO Framework](https://wordpress.org/plugins/autodescription/) alternative was reviewed for its metadata, canonical, social, sitemap and schema features. A new plugin would require replacing the current adapter and migrating its existing optional ACF editing/defaults; adding it alongside would violate single ownership. Existing native solution already meets this site's approved requirements, so no plugin, account, extension, tracking, verification or paid subscription was added. This is preservation of the existing approved architecture rather than a second initial SEO installation.

Current **Project social-image priority supersedes earlier fallback descriptions**: approved custom social image → approved Video Poster for Video type → approved Listing Thumbnail → approved Hero Image → existing ShowMakers logo. Every candidate must be an actual image attachment, Approved, available on disk and not a restricted filename. MP4 can never be an image candidate even if attachment permission is Approved. Missing/pending/restricted candidates are skipped. No video URL/frame extraction/preload link/VideoObject/media sitemap introduced. Image and Video Project playback, Client-logo separation, all fields and visible content remain unchanged.

Site Title `ShowMakers Local` is intentional development naming; Tagline is empty. Public SEO titles, `og:site_name`, Organization and WebSite explicitly use ShowMakers, so the local suffix never appends to intended public titles. At authorized production launch use approved ShowMakers Site Title/empty or approved Tagline, final HTTPS host and production visibility settings from the migration checklist. No LocalWP hostname is stored in permanent SEO values.

User-confirmed real-upload video workflow is approved. Existing manual-test Media Library asset is retained untouched; the current five published Projects remain Image in the audited database. No actual Project was changed for this SEO follow-up. All original Page/Project/Client/Service data match the prior checkpoint; the newer manual-test attachment is the only pre-existing addition since the earlier phase. It is not a public Project or sitemap entry.
