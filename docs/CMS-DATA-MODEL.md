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
| Logo | Image attachment ID | Required when visible | clients.image | Staff | Public approved logo only | Preserve original artwork/colors; aspect ratio contained. |
| Visible | Boolean + core publish status | Required, draft default on import | clients.published | Staff | Private control | Show marquee only if publish, visible and approved logo. |
| Sort Order | Integer sort_order post meta | Required | clients.order | Staff | Private control | Broad 18-logo selection; never hardcode only 6–8. |
| Optional Website | Validated URL | Optional | No current source | Staff | Public if later explicitly used | Does not automatically turn existing logos into external links. |
| Logo Permission | approved/pending/restricted enum | Required | VERIFIED-CONTENT.md | Staff/admin | Private | Imported supplied logos approved; independent from project photo rights. |
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
