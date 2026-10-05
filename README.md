# ShowMakers multipage visual prototype

The current site is at the project root. `prototype/` preserves the earlier single-page version for reference; it is no longer the active preview. No original references or verified source information were deleted.

## Run and edit

- `npm run dev`: regenerate five HTML pages from JSON and serve the root at http://127.0.0.1:8765/.
- `npm run build`: regenerate HTML after editing data while the server is running. Refresh the browser afterward.
- `npm run check`: check local navigation, anchors, asset paths and service/project relationships.

No npm dependencies or JavaScript application framework. Python 3's standard library handles rendering and local serving. The preview remains a temporary frontend for eventual WordPress/CMS integration.

## Structure

```text
index.html                 Home: approved hero, clients, 2 work previews, introduction, service index
work.html                  Work: 5 genuine portfolio/capability entries
services.html              Services: 8 individual service sections and anchored navigation
about.html                 About: positioning, philosophy, human-led approach and production imagery
contact.html               Contact: confirmed details and a prepared, inactive inquiry form
assets/
  css/global.css           Central brand, typography, spacing and accessibility tokens
  css/components.css       Shared navigation, footer, links, metadata and media patterns
  css/pages/*.css          Page-specific responsive compositions
  js/data.js               JSON access boundary for browser enhancements
  js/global.js             Shared menu and inactive-form behavior
  js/home.js               Staged hero and client marquee
  images/                  Genuine PDF-extracted work, logos and provenance manifest
 data/
  site.json                Navigation, hero, page introductions, About, contact, form and UI copy
  projects.json            Portfolio, service IDs, order, published/featured flags, optional detail URL
  services.json            Eight service records, descriptions, activities, platforms, media and related work
  clients.json             One editable logo collection, order, published flags and optional associations
scripts/build_site.py      Shared page renderer, header/footer and project/media patterns
scripts/check_site.py      Link and content relationship checks
references/                Original brand/company material and VERIFIED-CONTENT.md
review/                    Browser review screenshots
prototype/                 Preserved earlier prototype
```

## Content separation

Editable company, project, service, client and contact content lives in JSON. HTML files are generated output; edit JSON and regenerate rather than manually editing repeated HTML. The shared header/footer are defined once in `scripts/build_site.py`. This deliberately renders core content before JavaScript so it remains readable if scripts fail.

The template retains structural markup, CSS class names, semantic headings, form mechanics, a few utility labels (e.g. entry counts and inactive form status), prototype metadata and the fixed five-page route mapping. These are presentation or operational details rather than business claims. Browser controls use shared labels from site data where practical.

Projects support `id`, `slug`, `title`, `client`, `summary`, `services[]`, `featured`, `thumbnail`, `heroMedia`, `media[]`, `year`, `order`, `published`, `presentation` and `detailUrl`. Unknown clients/years are null. Service arrays are many-to-many classifications; no permanent capability pillars were added. `detailUrl` can point to a future project route without changing the project presentation pattern. Filtering is data-ready; no speculative filter UI is built.

## Source and authenticity

2026 Company Profile controls service scope, including pages 9–10 and 11–36. Duplicate general Media Production material is consolidated. Page 22's duplicated AI activity list is not used as Commercial Videography scope. Guarantees and unsupported outcome language are omitted.

Home features short-form brand content (page 21) and TID Group (page 27). Tommy Hilfiger is retained on Work (page 32) with `featured: false`. Additional Work entries include portrait short-form content (page 21) and explicitly labeled AI-assisted content (page 25). Event service imagery uses the Samsung experiential booth (page 34), without inferred results. General About production imagery is from page 23 and is not asserted to match another project.

`assets/images/sources.json` records PDF pages, embedded-image indices and export dimensions. Client artwork preserves the supplied colors; no logo is linked to an undocumented project. No generated replacement mascot or rabbit is used.

## Backend handover

Keep the same content fields and eight service IDs when introducing WordPress records or API responses. Replace the temporary build-time JSON loader with CMS template queries, and render project detail routes from the same records. Retain markup/classes and shared visual patterns.

Backend work will need media-library URLs and responsive derivatives, project publishing/order/feature controls, service relationships, editable site fields, actual project routes, and a contact endpoint with validation/delivery/privacy handling. The current form is explicitly inactive, submission is prevented, and no success response is fabricated. No CMS/admin backend was implemented.

## Remaining content/assets

- Confirm commercial webfont licensing for supplied MADE TOMMY / FUTURA PT files; obtain WOFF2 versions for public deployment. Local brand fonts are active; system fonts are failure fallbacks.
- Original-resolution imagery, clean video frames, video files and vector/high-resolution client logos.
- Confirmed project names, dates, responsibilities and case-study narratives where absent.
- Confirmed social URLs and WhatsApp availability; empty fields are omitted from the site.
- Production inquiry delivery, privacy copy and launch metadata.

## Current verification

See [Work and font pass](review/WORK-FONT-PASS.md) for the current implementation, exact font inventory, project ordering and browser checks. The current Work actions open real project detail pages; the earlier image dialog has been removed. Historical review notes below describe previous passes and are superseded where they refer to image previews or absent fonts.

## Global brand system pass

The five-page architecture and genuine media are retained. `assets/css/global.css` owns the six approved palette values, semantic text/surface/border/focus colors, display/body font stacks, responsive display/H1/H2/H3/project/body/navigation/metadata/caption/CTA/form-label roles, and spacing tokens. `components.css` owns shared patterns; page styles own compositions. No font binaries were found in the supplied assets/references/prototype directories. No official webfonts are loaded or downloaded; the branded names in the stacks are reserved for licensed files supplied later.

Changes: global.css, components.css, all five page styles, data/site.json, scripts/build_site.py, all five regenerated HTML files, this README, and brand review screenshots. Project headings on Work now use H2 beneath its H1; Home retains H3 beneath Selected work. Footer privacy disclosure is explicitly pending confirmed content. Confirmed social URLs remain empty and omitted; the shared renderer will display supplied links consistently.

Removed unused landing-page image hero/caption, project feature/pair, contact CTA/bottom, pending-reveal/revealed, notes-list and nav-contact styles after checking current markup. Consolidated repeated palette/font definitions and conflicting page/mobile type sizes into shared roles. Home hero and marquee styles now live in home.css; About's dark approach composition lives in about.css. The archived prototype was not changed.

Accessibility: charcoal on primary yellow 9.46:1; white on charcoal 12.07:1; charcoal on secondary yellow 7.81:1; form border on white 4.80:1; secondary text on light gray 5.35:1. Secondary yellow on white is only 1.54:1, so it is never text or the sole active/focus indicator. Navigation has underline plus aria-current; keyboard controls have a visible charcoal outline. Small utility copy is at least 13px, input text 16px. Reduced-motion media rules and the preview switch remove staged movement and use static logos.

QA: all five pages checked at 390, 430, 768 and 1440px with one H1 each, no horizontal overflow, no failed loaded images and consistent Privacy Notice presence. Reviewed desktop Home/Work/Services/About and mobile Contact/Home/Services; mobile menu navigation works. Mobile hero gap is 32px. Build and navigation/asset/service-reference checks pass. This is a focused review, not a full accessibility certification.

No page-specific brand deviation remains identified. Site-wide remaining gaps are licensed brand fonts, approved privacy policy copy and social URLs; the contact form remains intentionally inactive pending backend integration. Review images: review/brand-home-desktop.jpg and review/brand-services-mobile.jpg.

## Local brand font activation

Local files supplied by the user are now configured with shared @font-face rules in global.css. No WOFF2 files are available. Original files are unchanged. MADE TOMMY solid OTF weights: Thin 250, Light 300, Regular 400, Medium 500, Bold 700, ExtraBold 800, Black 900. Futura TTF weights: Light 300, Book 400, Medium 450, Demi 500, Heavy 600, Bold 700, ExtraBold 800. These are embedded OS/2 weights, including the nonstandard 250 and 450 values. Display uses actual Bold 700; headings use Medium 500; body uses Book 400 and labels Demi 500. Outline variants are unused. This activation supersedes the previous fallback-only audit. The MADE TOMMY bundled readme and filenames say personal use; these files are restricted to local design evaluation pending confirmation of commercial web embedding rights.

## Editorial design refinement

Preserved the five-page architecture, eight service classifications, genuine company copy, yellow opening and single slow homepage marquee. Local MADE TOMMY and FUTURA PT remain loaded; the refinement brief's missing-font statement is superseded by the preceding font activation.

Shared visual changes: restrained page-title rule, thinner active navigation accent with an independent underline, consistent forward-arrow links, modest yellow hover punctuation, slightly larger Futura body/utility typography, and a quieter footer. Contact focus uses a charcoal outline plus a yellow inset accent. About working-step numbers share the service-marker treatment. Page compositions remain different because their purposes differ.

Divider cleanup: removed all eight service-section top borders, all four platform-entry rules, both Home introduction rules, eight Home service-index rules, four About step rules, footer route and footer-bottom rules. Retained functional input boundaries and the existing marquee separation. Services uses heading groups and passage spacing, with one active index item highlighted by yellow plus underline/aria-current. Scroll updates are passive and do not control scrolling; service content and anchor links work without JavaScript.

Platforms: added Facebook, Instagram, TikTok and Xiaohongshu monochrome SVG marks from Simple Icons, displayed at 26px beside their existing copy rather than in cards. Source metadata, upstream license and disclaimer are in assets/images/platforms/. These identify platforms, not partnerships. Icon URLs are editable subsection fields in services.json. The duplicate plain platform list is omitted where the four detailed platform passages already cover it; service data is preserved.

Portfolio: retained large, unboxed media and concise factual context, with consistent image-preview links rather than fabricated case-study routes. Tommy Hilfiger receives a small text identity; TID Group's title already identifies the client. A small-logo treatment is available in the shared renderer only for an exact client match with an explicitly documented project ID in clients.json. None of the existing approved standalone logo records has such a relationship; no separate client logo was added or inferred from the marquee. Clean approved TID Group/Tommy Hilfiger artwork and documented mappings are needed to publish separate project-logo treatments.

The shared renderer fingerprints CSS/JS URLs by file content so refreshed pages do not mix new markup with stale preview styles. Core content remains server-rendered HTML from JSON, with no framework or new CMS structure.

Validation: build and link/asset/anchor/service-reference checks pass. All five pages checked at 390, 430, 768 and 1440px: one H1, no horizontal overflow, no failed loaded images, four platform icons on Services and no service-section borders. Visually inspected desktop Services/Work/About, mobile Services/Contact/About/Work/Home and tablet Services; official font checks pass. Service anchor highlighting works; all platform files load; Work's new preview action opens the existing dialog and Escape closes it. Reduced-motion preview is static and preserves the mobile hero's 32px gap. No browser errors observed. Screenshots: review/refined-services-desktop.jpg and review/refined-services-mobile.jpg.

Remaining polish needs: project-matched approved logo files and association records, original-resolution project photography/screenshots and video, verified case-study detail, final privacy content and confirmed social links. No further page-specific inconsistency identified in this focused review; Services remains a long-form page because the documented content is retained. The contact form is still intentionally inactive pending backend delivery.

## Interactive Services explorer

Services now uses a single selected panel with a numbered right-side selector above 800px. Below that breakpoint the controls form a vertical list above the selected content, without sideways scrolling. The desktop index is sticky only within the explorer. The page introduction remains brief; no other page was redesigned.

Architecture: the existing Python renderer creates every service panel with one shared template from services.json, including optional platform, media and related-work blocks. services.js progressively converts the fallback anchor index to native buttons and hides inactive panels. It contains no service copy and requires no extra network fetch. WordPress can replace the build-time data query while retaining the markup and controller. Numbers follow the ordered published records; existing service IDs serve as stable slugs and deep-link destinations. No permanent grouping taxonomy or extra CMS structure was introduced.

Fields used: id, title, shortDescription, description, deliverables[], platforms[], formats[], subsections[] (title, text, icon), media[] (src, alt, caption), relatedProjects[], order and published. Related titles/URLs come from projects.json. Content and the eight classifications are unchanged.

Selection immediately replaces all active copy, capability, platform, media and Work-link content. A 240ms fade with a 5px upward placement applies once per change. The native reduced-motion preference and existing review switch bypass movement; a preference change cancels current reveals. URL hashes preserve service deep links and respond to hash/history navigation. No scroll position is controlled by the selector.

Accessibility: native buttons support Tab, Enter and Space; Up/Down and Home/End move focus between controls. aria-pressed and aria-controls identify selection and controlled content; active treatment combines yellow, weight and underline. Panels are named regions with labelled headings; inactive content is removed from focus/accessibility traversal using hidden. A polite status announces selection without reading the entire service. If JavaScript fails, all eight panels and working anchor links remain readable. Tested with a temporary script-free page, then removed that fixture.

QA: all eight selections checked at 1440, 1024, 768, 430 and 390px, with exactly one visible panel and no overflow in all 40 combinations. Correct titles, media counts and four Social Media platform icons confirmed. Tested Enter/Space activation, arrow-key focus, direct Event Management link, reduced-motion selection, and related Work URLs. No browser errors in the final review. Build/link/asset/service-reference checks pass. Screenshots: review/services-explorer-desktop.jpg and review/services-explorer-mobile.jpg.

Imagery: Media Production, AI-Enhanced Content Production, Website & Digital Solutions and Event Management use existing genuine assets. Business Consulting, Social Media Marketing, SEO & SEM and Influencer Marketing have no suitable verified service-specific media in their current records; their panels remain typography-led, with platform marks for Social Media. No stock or fabricated imagery was added.

Files changed: scripts/build_site.py, assets/css/pages/services.css, new assets/js/services.js, assets/js/global.js (obsolete long-form scroll tracker removed), services.html, README.md and review screenshots. The other four generated HTML files only received updated shared-script fingerprints; their design/content remain unchanged. services.json did not require changes for this interaction.

## Current font licensing scope

Current MADE TOMMY / FUTURA PT files are being used for local design evaluation only. Confirm commercial webfont licensing before public deployment.

This scope supersedes earlier descriptions of supplied fonts as licensed assets. No files were uploaded to a CDN, downloaded from unofficial sources, converted, renamed or redistributed. The local server remains bound to 127.0.0.1.

## Current project portfolio

Work now uses numbered wide, portrait and digital project presentations, with native media proportions and project-level links. Five `work/*.html` detail pages share one template and the same project records. See review/WORK-FONT-PASS.md.

## Latest Work listing refinement

The Work listing now uses one 16:10 preview/caption pattern for every project. `listingThumbnail` is separate from `heroMedia` and includes `fit` (cover/contain). Service metadata is plain text. Native ratios remain on detail pages. This supersedes the previous wide/portrait/digital listing notes. See [current refinement report](review/WORK-LISTING-REFINEMENT.md).

## Minimal homepage pass

Home now contains header, yellow hero, selected-client marquee, two selected projects and footer. Removed About preview, eight-service preview, capability line, project descriptions, linked project services and separate View Project actions. Selected work retains Short-form brand content and TID Group, with plain service categories, linked media/title and one arrow cue each. All Work appears beside the Selected work heading. The hero retains its fast staged reveal and future character composition space without placeholder artwork.

Checked 390, 430, 768, 1024 and 1440px: no overflow, two projects, three main sections, no project service links or standalone project CTAs. Project navigation and reduced-motion behavior pass; local link checks pass. Other main pages and project details remain byte-for-byte unchanged. Screenshots: review/home-minimal-desktop.jpg and review/home-minimal-mobile.jpg. The shared footer contact invitation remains intentional; no additional homepage content is recommended.

## Current Home / Work structure

Home is now Hero → Selected clients → What we do → Footer. Work is a filtered 4:3 portfolio grid with 3/2/1 columns. This supersedes earlier Selected Work homepage and long listing notes. See [current report](review/HOME-WORK-STRUCTURE-PASS.md).

## Six-card development grid

Work now renders five genuine portfolio records plus Project 06, an explicit isPlaceholder/developmentOnly layout fixture. All count is 06; Social Media Marketing and Influencer Marketing each include the fixture for UI testing only. Placeholder cards are non-interactive, have no generated detail route, and cannot be reached by Next Project. Three colored placeholder variants are in assets/images/placeholders/. Filter typography is quieter at 14px with 12px counts and tighter spacing. The Work status announcement is visually hidden. Normal checks pass; `python3 scripts/check_site.py --production` intentionally blocks remaining placeholders. The 390/430/768/1024/1440px reviews confirm 1×6 / 2×3 / 3×2 layouts, 4:3 previews and no overflow. Existing genuine records and all other page outputs are unchanged. Current review: review/work-six-desktop.jpg.

## Global color rhythm

All five main pages now pair the approved dark logo/navigation with a yellow opening and one shared charcoal footer. Home retains its yellow hero and quiet white Clients/What we do sections. Work and Services have compact yellow introductions, followed by white portfolio/explorer canvases. About uses yellow statement → white positioning → existing charcoal human-led production section → white working steps. Contact keeps a substantial yellow inquiry opening and a clean white form/details canvas, without floating cards. No content, route, filter membership or media colors changed.

Footer uses white text, muted readable secondary copy, restrained yellow hover/focus and the approved white vertical logo extracted losslessly from Brand Guidelines page 5, image X15 with its original alpha mask. This asset is assets/images/showmakers-logo-light.png, recorded in sources.json and site.logoLight. No logo CSS filters or arbitrary recoloring. Header remains the dark horizontal variant; white detail-page headers also retain it. Details inherit only the shared footer change.

White was retained deliberately for portfolio image fidelity, service reading, form clarity and About's story/steps. Primary yellow is used for Services selected-number markers with weight/underline/aria-pressed; secondary yellow remains a small interaction accent. No mechanical stripe alternation or repeated full-yellow content blocks.

Shared renderer wraps existing page introductions in a full-width brand surface while retaining the inner shell; brand() supports the approved light asset for footer. Shared components.css owns header/footer surfaces. Work/Services/About spacing aligns the surface transitions; Contact intro shell aligns with shared margins. Global palette/font CSS remains unchanged.

Build/link/asset checks pass. All five main pages checked at 390, 430, 768, 1024 and 1440px: no overflow, expected yellow introductions/headers, white text on common charcoal footer and loaded approved logos. Mobile menu is charcoal text on yellow; no logo filters. Contrast: charcoal on primary yellow ~9.46:1; white on charcoal ~12.07:1; active states also use weight/underline/semantic state. Browser logs show no errors. Screenshots: review/color-about-desktop.jpg, color-contact-desktop.jpg, color-home-desktop.jpg, color-services-desktop.jpg, color-services-mobile.jpg, color-work-desktop.jpg.

## Current asset permissions and Ravo Film

Tommy Hilfiger and Samsung project imagery is restricted and removed from frontend rendering, including archived prototype asset links. Private originals were moved under references/restricted-assets; exclude references, review and prototype folders from public deployment. Client-logo permissions remain separate, and the Samsung logo remains in the approved marquee. Tommy Hilfiger's factual record remains unpublished with mediaStatus restricted, safe placeholder fields and no public detail route. Its old route is withdrawn; rebuilding also removes stale generated routes for inactive/placeholder records. Event Management now uses its existing typography without a replacement image. No unrelated photo was substituted.

Ravo Film is verified by direct confirmation and illustrated with original website artwork from 2026 profile page 28 (X12, original alpha, transparent export margins trimmed). New detail route: work/ravo-film.html. Only the confirmed website-creation fact and Website & Digital Solutions service are stated. The unrelated Ravo branded content entries retain their separate identities.

Work has six published entries: Short-form brand content, TID Group, Stories in the moment, Ravo Film, AI-assisted content, Project 06 development placeholder. Counts: All 6; Media Production 2; Website & Digital Solutions 2; AI-Enhanced Content Production 1; Social Media Marketing 1 and Influencer Marketing 1 (both development fixture). No populated Event Management filter remains. Data supports mediaStatus approved/pending/restricted alongside existing published/listingThumbnail/media/isPlaceholder fields. Source-of-truth update: references/VERIFIED-CONTENT.md.

Build and restricted-reference/link/asset checks pass. Reviewed all five main pages and Ravo detail at 390, 430, 768, 1024 and 1440px: no overflow or restricted image elements. Verified Website filter includes TID and Ravo, All restores six, Ravo card opens the real shared detail page, Event Management still selects normally with no images, and Home retains its three main sections. No browser errors. Current screenshot: review/work-permission-update.jpg.

### Conversational Contact page

Contact now uses a yellow MADE TOMMY statement, white sentence-led form in FUTURA PT, quiet verified contact details and the shared charcoal footer without its repeated CTA. Adsorb informed only the conversational principle. Mobile sentences stack above their controls; desktop uses adjacent controls. All controls have 48px minimum height and clear yellow/charcoal focus feedback.

Editable opening, labels, placeholders, optional flags, status messages and endpoint live in `data/site.json`; service options derive from the eight existing service records plus `not-sure`. Fields are `name`, `company` (optional), `service`, `goal`, `email`, `additionalDetails` (optional). No budget or privacy acknowledgement is exposed. The 2026 profile page 37 confirms sales@showmakers.org, +6012-687 8775 and the Sri Petaling address; WhatsApp/social URLs remain unconfirmed and omitted.

`assets/js/contact.js` handles ready, validationError, unavailable, submitting, success and error. Endpoint is empty: validation can run, but valid attempts explicitly report not sent; no inquiry is transmitted or stored. No JavaScript leaves the send button disabled and a no-script email alternative. Future WordPress integration should set the endpoint and revise the prototype note, implement server-side validation/security/delivery and return `{success:true}` only after processing. The six named fields form the JSON payload; `createdAt` and `status` are assigned by the future server. No CRM/admin workflow was built. Approved ShowMakers Privacy Notice content remains required before adding acknowledgement or enabling live collection.

### Connected UX pass

The five main pages and shared project template remain intact. Primary/footer navigation now uses Work, Services, About and Contact; the logo returns Home. Footer is limited to identity, navigation, verified contacts and pending privacy information. Home retains hero, Selected Clients and What We Do only.

Service panels generate one `work.html?service=<slug>` action when an approved published non-placeholder project has that service, plus one `contact.html?service=<slug>` inquiry action for every service. Work offers all eight service filters, including honest zero-result states. Buttons update History API state without reloading; direct load, refresh, back/forward and invalid-value cleanup use the same slug whitelist. Counts and many-to-many filtering derive from project `services[]`; Project 06 remains a clearly marked UI-test placeholder in Social Media/Influencer counts, not verified evidence.

Real project detail service links return to service anchors. Endings contain only Next Project and Start a project. Next follows approved published non-placeholder project ordering and wraps from the last real project to the first. Inquiry URLs include the stable project slug and a service only when the record has one service. Placeholder/restricted/unpublished records cannot participate in detail routes, next rotation or Contact context. Contact embeds a renderer-generated whitelist of public real project slugs/titles/services; query strings never provide arbitrary display copy. Visitors can change service or remove the visible project reference; the URL/payload update accordingly.

Inquiry fields now include `projectReference` and `sourcePage`. `sourcePage` is an inferred contextual route, not referrer tracking: real project context maps to `work/<slug>.html`, service-only context to `services.html`, no context to `contact.html`. It is not proof of actual visit history. The future server should validate all fields, assign createdAt/status and apply security/privacy/delivery requirements. Endpoint remains empty; no submission or tracking occurs. `data-event` hooks cover navigation, hero View Work, service Related Work/inquiry, project opening/inquiry, Work filters and contact submit.

Motion tokens in global.css centralize 180ms links, 240ms service/support transitions, 300ms headline placement, shared easing and the existing 115s marquee. Services JS reads these tokens. Reduced-motion users receive immediate content, static logos, instant service switching and unmoving arrows. No new motion styles or video autoplay were introduced.

Work's first three thumbnails remain eager for the desktop first row; lower thumbnails load lazily. All images now have intrinsic dimensions, including PNG placeholders, and portfolio frames retain 4:3 sizing. Current project frontend assets are about 40–100KB (placeholder 19KB); no destructive source compression or new derivatives were needed. There are no rendered video elements; verified stills remain the portfolio media.

404.html is generated with yellow/charcoal branding, 404, a brief message, Back Home and View Work. No character asset was invented. The static Python preview does not automatically route missing URLs to this file; configure the WordPress 404 template/hosting behavior during integration.

QA covered Media Production and Website/Ravo journeys, service-only inquiry, removable/editable context, Work history/refresh/invalid fallback/zero results, keyboard filter activation and focus, placeholder context rejection, and immediate reduced-motion preview behavior. All five main pages, Ravo detail and 404 were reviewed at 390, 430, 768, 1024 and 1440px with no horizontal overflow. Build/link/asset checks and connected data-route assertions pass. No connected interaction relies on hardcoded display-text matching; service IDs currently are the stable public service slugs. Client logo association lookup also uses the project relationship ID, not matching display names.

### Shared CTA / arrow refinement

Decorative Unicode CTA glyphs were removed from the active five main pages, real project pages and 404. `arrow_icon()` in scripts/build_site.py owns one inline SVG shaft/head geometry with currentColor, no fill and aria-hidden/focusable=false. The long and compact forms differ only in shaft length; templates call the same helper. No external component code was copied and navigation/query destinations remain unchanged.

Type A long links appear in the home hero and All Services action, service Related Work/Start a project actions, real project Start a project and 404 return links. Type B 30px circles appear beside genuine Work project titles, inside the existing single project link; placeholders remain non-clickable and omit them. Type C is Contact Send Inquiry: the SVG has reserved width, revealing on hover/focus without changing button dimensions. Touch and reduced-motion users see it immediately. Next Project uses the compact SVG without a circle and keeps its existing real-project ordering/wrap behavior.

Interaction styles live together in components.css, reuse the existing 180ms/easing tokens, and match hover with focus-visible. Long arrows nudge 5px, circle inner arrows 2px; reduced motion removes both. Yellow-surface arrows remain dark, white-surface arrow feedback uses brand yellow with unchanged labels, and inverse CTA styling is available for charcoal surfaces. Footer navigation and service index remain plain text.

Removed old glyph-span arrow rules, diagonal project-meta arrow rules, obsolete media-action arrow styles, duplicated reduced-motion arrow overrides, Contact's old arrow font-size/hover decoration, and per-home CTA gap overrides. Historical archived prototype/reference text was not treated as the active site.

QA: all five main pages, Ravo detail and 404 at 390/430/768/1024/1440 (35 checks), no overflow, decorative SVGs hidden semantically, no footer/placeholder arrows. Keyboard focus confirms equivalent long-link/card/reveal feedback. Contact reveal remains 269.625×62px before and after focus at 1440; reduced preview shows no transform/transition and an immediately visible submit arrow. Build/link/connected-route checks pass. No decorative Unicode arrow remains in active frontend HTML/CSS/JS or the renderer.
