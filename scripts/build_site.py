"""Render static pages from editable JSON with one shared header/footer.
This temporary renderer can become CMS templates; no client JS is needed for content.
"""
from pathlib import Path
from html import escape
import json
import hashlib
import re
import struct
from urllib.parse import urlencode
ROOT=Path(__file__).resolve().parent.parent
load=lambda name:json.loads((ROOT/'data'/f'{name}.json').read_text())
site=load('site');projects=sorted([p for p in load('projects') if p.get('published',True)],key=lambda p:p['order']);services=sorted([s for s in load('services') if s.get('published',True)],key=lambda s:s['order']);clients=sorted([c for c in load('clients') if c.get('published',True)],key=lambda c:c['order'])
e=lambda v:escape(str(v),quote=True)
service_map={s['id']:s for s in services}
detail_projects=[p for p in projects if not p.get('isPlaceholder') and p.get('mediaStatus')=='approved']
def arrow_icon(kind='long'):
 # One shared shaft/head geometry. Compact variants shorten the shaft only.
 end=60 if kind=='long' else 20
 return f'<svg class="ui-arrow" viewBox="0 0 {end+4} 16" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter"><path d="M2 8H{end}M{end-6} 2L{end} 8L{end-6} 14"/></svg>'
def link(label,url,cls='arrow-link',variant='long',event=''):
 hook=f' data-event="{e(event)}"' if event else ''
 icon=arrow_icon(variant) if 'arrow-link' in cls else ''
 modifier=' cta-nudge' if variant=='short' and icon else ''
 return f'<a class="{cls}{modifier}" href="{e(url)}"{hook}>{e(label)}{icon}</a>'
def contextual_url(page,**context):return page+'?'+urlencode(context)
image_dimensions={m['file']:(m['width'],m['height']) for m in json.loads((ROOT/'assets/images/sources.json').read_text())}
for png in (ROOT/'assets/images/placeholders').glob('*.png'):
 with png.open('rb') as stream:
  header_bytes=stream.read(24)
 if header_bytes[:8]==b'\x89PNG\r\n\x1a\n':image_dimensions[str(png.relative_to(ROOT))]=struct.unpack('>II',header_bytes[16:24])
def image(m,cls='',lazy=True):
 if m['src'].startswith('references/') or Path(m['src']).name in ('brand-showcase.webp','samsung-experience.webp'):raise ValueError('Restricted media cannot be rendered')
 dimensions=image_dimensions.get(m['src']);native=(f' style="max-width:{dimensions[0]}px"' if dimensions and dimensions[1]>dimensions[0]*1.4 else '');size=f'width="{dimensions[0]}" height="{dimensions[1]}" ' if dimensions else ''
 return f'<img class="{cls}" src="{e(m["src"])}" alt="{e(m["alt"])}" '+size+('loading="lazy" ' if lazy else '')+f'decoding="async"{native}>'
def media(m):return '<figure>'+image(m)+(f'<figcaption>{e(m["caption"])}</figcaption>' if m.get('caption') else '')+'</figure>'
def nav(page):return ''.join(f'<a href="{e(n["url"])}" data-event="nav_{e(Path(n["url"]).stem)}" '+('aria-current="page" ' if n['url']==page+'.html' else '')+f'>{e(n["label"])}</a>' for n in site['navigation'])
def brand(variant='dark'):
 src=site['logoLight'] if variant=='light' else site['logo'];width,height=(681,609) if variant=='light' else (838,342)
 return f'<a class="brand brand-{variant}" href="index.html" aria-label="ShowMakers home"><img src="{e(src)}" width="{width}" height="{height}" alt="ShowMakers"></a>'
def header(page):return f'<div class="header-surface"><header class="site-header shell">{brand()}<button hidden class="menu-toggle" aria-expanded="false" aria-controls="primary-navigation">{e(site["labels"]["menu"])} <span aria-hidden="true">+</span></button><nav id="primary-navigation" aria-label="Primary navigation">{nav(page)}</nav></header></div>'
def footer(page):
 c=site['contact'];f=site['footer']
 privacy=f'<details class="privacy-notice"><summary>{e(f["privacyNotice"]["label"])}</summary><p>{e(f["privacyNotice"]["text"])} <a href="mailto:{e(c["email"])}">{e(c["email"])}</a>.</p></details>'
 social=''.join(link(x['label'],x['url'],'text-link') for x in c['socialLinks'])
 route='' # Shared footer stays navigation/contact focused; contextual actions live in content.
 return f'<footer class="site-footer">{route}<div class="shell footer-inner">{brand("light")}<address>{e(c["address"])}<br><a href="mailto:{e(c["email"])}">{e(c["email"])}</a><br><a href="tel:{e(c["phone"].replace(" ","").replace("-",""))}">{e(c["phone"])}</a></address><nav class="footer-links" aria-label="Footer navigation">{nav(page)}{social}</nav></div><div class="shell footer-bottom"><span>{e(f["copyright"])}</span><span>{e(f["note"])}</span>{privacy}</div></footer>'
def project_url(p):return p.get('detailUrl') or 'work/'+p['slug']+'.html'
def project_identity(p):
 client=next((c for c in clients if p['id'] in c.get('projects',[])),None)
 if not p.get('client') or (not client and p['client']==p['title']):return ''
 return '<div class="project-client">'+(f'<img src="{e(client["image"])}" alt="{e(client["name"])}">' if client else e(p['client']))+'</div>'
def project_services(p):return ' · '.join(f'<a href="services.html#{e(id)}">{e(service_map[id]["title"])}</a>' for id in p['services'])
def portfolio_entry(p,number):
 thumbnail=p.get('listingThumbnail') or p['thumbnail'];fit='contain' if thumbnail.get('fit')=='contain' else 'cover'
 if not (ROOT/thumbnail['src']).is_file():
  thumbnail={'src':'assets/images/placeholders/project-image-pending.png','alt':'ShowMakers placeholder: project image pending.'};fit='contain'
 placeholder=p.get('isPlaceholder',False) or p.get('mediaStatus')!='approved'
 labels=' · '.join(e(service_map[id]['title']) for id in p['services'])
 pending='<p class="placeholder-status">Development placeholder · Project details pending</p>' if placeholder else ''
 cue='' if placeholder else '<span class="project-arrow" aria-hidden="true">'+arrow_icon('short')+'</span>'
 if placeholder:labels='UI test services: '+labels
 content=f'<div class="portfolio-media fit-{fit}">{image(thumbnail,lazy=number>2)}</div><div class="portfolio-info"><div class="portfolio-title"><h2>{e(p["title"])}</h2>{cue}</div>{pending}<div class="portfolio-meta"><p>{labels}</p></div></div>'
 card=f'<div class="portfolio-card is-placeholder">{content}</div>' if placeholder else f'<a class="portfolio-card" href="{e(project_url(p))}" data-event="project_open" data-project="{e(p["slug"])}">{content}</a>'
 return f'<article class="portfolio-project" id="{e(p["slug"])}" data-services="{e(" ".join(p["services"]))}" data-placeholder="{str(placeholder).lower()}">{card}</article>'
def project_detail(p,number):
 hero=p.get('heroMedia') or p['thumbnail']
 extras=[m for m in p['media'] if m['src']!=hero['src']]
 gallery='<div class="detail-gallery">'+''.join(media(m) for m in extras)+'</div>' if extras else ''
 hero_caption=f'<figcaption>{e(hero["caption"])}</figcaption>' if hero.get('caption') else ''
 next_project=detail_projects[number%len(detail_projects)]
 context={'project':p['slug']}
 if len(p['services'])==1:context['service']=p['services'][0]
 inquiry=contextual_url('contact.html',**context)
 return f'<div class="shell project-detail detail-{e(p["presentation"])}">{link(site["labels"]["backToWork"],"work.html","text-link back-to-work")}<div class="detail-heading"><span class="editorial-number">{number:02d}</span>{project_identity(p)}<h1>{e(p["title"])}</h1><p>{e(p["summary"])}</p><div class="project-services"><span>Services</span><br>{project_services(p)}</div></div><figure class="detail-hero">{image(hero,lazy=False)}{hero_caption}</figure>{gallery}<p class="detail-documentation">{e(p["documentationNote"])}</p><div class="detail-route">{link(site["labels"]["nextProject"],project_url(next_project),variant="short")}{link(site["labels"]["startProject"],inquiry,"arrow-link",event="project_start_inquiry")}</div></div>'
def page_intro(title,text):return f'<div class="page-intro-surface"><div class="shell page-intro"><h1>{e(title)}</h1><p>{e(text)}</p></div></div>'
def home():
 h=site['hero'];c=site['home']
 hero=f'<section class="hero" aria-labelledby="hero-title"><div class="shell hero-inner"><div class="hero-intro"><h1 id="hero-title">'+''.join(f'<span>{e(x)}</span>' for x in h['headline'])+f'</h1></div><div class="hero-support"><div class="hero-explanation"><p class="hero-introduction">{e(h["introduction"])}</p><p>{e(h["description"])}</p></div>{link(h["action"]["label"],h["action"]["url"],event="hero_view_work")}</div></div></section>'
 logos=''.join(f'<li><img src="{e(x["image"])}" alt="{e(x["name"])}" width="120" height="64" loading="lazy"></li>' for x in clients)
 marquee=f'<section class="clients is-static" aria-labelledby="clients-title"><div class="shell clients-heading"><h2 id="clients-title">{e(c["clientsTitle"])}</h2><button hidden id="marquee-control" aria-pressed="false"><span aria-hidden="true" class="pause-symbol">Ⅱ</span> <span class="control-label">{e(site["labels"]["pause"])}</span></button></div><div class="marquee" aria-label="ShowMakers clients"><div class="marquee-track"><ul class="logo-list" id="client-list">{logos}</ul></div></div></section>'
 items=''.join(f'<li><a href="services.html#{e(s["id"])}"><span class="home-service-number">{i:02d}</span><span>{e(s["title"])}</span></a></li>' for i,s in enumerate(services,1))
 preview=f'<section class="shell home-services" aria-labelledby="home-services-title"><div class="home-services-heading"><h2 id="home-services-title">{e(c["servicesTitle"])}</h2>{link(c["servicesAction"],"services.html","arrow-link home-services-route")}</div><ol class="home-service-index">{items}</ol></section>'
 return hero+marquee+preview

def work():
 c=site['work'];available=[s for s in services if any(s['id'] in p['services'] for p in detail_projects)]
 controls=f'<button type="button" data-event="work_filter" data-service="all" aria-pressed="true">All <span>{len(detail_projects):02d}</span></button>'
 controls+=''.join(f'<button type="button" data-event="work_filter" data-service="{e(s["id"])}" aria-pressed="false">{e(s["title"])} <span>{sum(s["id"] in p["services"] for p in detail_projects):02d}</span></button>' for s in available)
 return page_intro(c['title'],c['introduction'])+f'<div class="shell work-index"><div class="work-filters" data-valid-services="{e(chr(32).join(service_map))}" role="group" aria-label="Filter projects by service" hidden>'+controls+'</div><p class="visually-hidden" id="work-status" role="status" aria-live="polite"></p><section class="work-collection" aria-label="Project collection">'+''.join(portfolio_entry(p,i) for i,p in enumerate(projects))+f'</section><p id="work-empty" hidden>{e(site["labels"]["emptyWork"])}</p><noscript><p>All projects are shown. Service filters require JavaScript.</p></noscript></div>'

def services_page():
 c=site['services'];index='<nav class="service-navigation" aria-label="Service index"><p>'+e(c['indexTitle'])+'</p>'+''.join(f'<a href="#{e(s["id"])}"><span>{i+1:02d}</span>{e(s["title"])}</a>' for i,s in enumerate(services))+'</nav>'
 entries=[]
 for i,s in enumerate(services):
  lists='<div class="service-details"><div><h3>'+e(site['labels']['provides'])+'</h3><ul>'+''.join(f'<li>{e(x)}</li>' for x in s['deliverables'])+'</ul></div>'
  for field in ['platforms','formats']:
   if s.get(field) and not (field=='platforms' and s['subsections']):lists+='<div><h3>'+e(site['labels'][field])+'</h3><p>'+ ' · '.join(e(x) for x in s[field])+'</p></div>'
  lists+='</div>'
  sub='<div class="platform-passages">'+''.join(f'<div class="platform-entry"><img src="{e(x["icon"])}" width="26" height="26" alt="" aria-hidden="true"><div><h3>{e(x["title"])}</h3><p>{e(x["text"])}</p></div></div>' for x in s['subsections'])+'</div>' if s['subsections'] else ''
  visuals='<div class="service-media '+('website-media' if s['id']=='website-digital-solutions' else '')+'">'+''.join(media(m) for m in s['media'])+'</div>' if s['media'] else ''
  related=link(site['labels']['relatedWork'],contextual_url('work.html',service=s['id']),'arrow-link',event='service_related_work') if any(s['id'] in p['services'] for p in detail_projects) else ''
  related='<div class="service-actions">'+related+link(site['labels']['startProject'],contextual_url('contact.html',service=s['id']),'arrow-link',event='service_start_inquiry')+'</div>'
  entries.append(f'<section class="service-section" id="{e(s["id"])}" aria-labelledby="heading-{e(s["id"])}"><div class="service-heading"><span class="service-number">{i+1:02d}</span><h2 id="heading-{e(s["id"])}">{e(s["title"])}</h2></div><p class="service-positioning">{e(s["shortDescription"])}</p><p class="service-description">{e(s["description"])}</p>{lists}{sub}{visuals}{related}</section>')
 return page_intro(c['title'],c['introduction'])+'<div class="shell services-layout" id="service-explorer">'+index+'<div class="service-panels">'+''.join(entries)+'</div><p class="visually-hidden" id="service-status" role="status" aria-live="polite"></p></div>'
def about():
 a=site['about']
 approach_title=e(a['approachTitle']).replace('Hands-on','<span class="keep-word">Hands-on</span>')
 return page_intro(a['title'],a['introduction'])+f'<section class="shell about-philosophy"><h2>{e(a["philosophyTitle"])}</h2><p>{e(a["philosophyText"])}</p></section><section class="approach"><div class="shell approach-inner"><div class="approach-copy"><h2>{approach_title}</h2><p>{e(a["approachText"])}</p></div>{image(a["media"],"approach-image")}</div></section><section class="shell working-steps" aria-label="Our working approach">'+''.join(f'<div><span class="editorial-number">{i+1:02d}</span><h2>{e(x["title"])}</h2><p>{e(x["text"])}</p></div>' for i,x in enumerate(a['steps']))+'</section>'
def contact():
 c=site['contact'];form=c['form'];fields=[]
 for f in form['fields']:
  name=e(f['name']);required=' required' if f.get('required') else '';placeholder=f' placeholder="{e(f.get("placeholder",""))}"';autocomplete=f' autocomplete="{e(f["autocomplete"])}"' if f.get('autocomplete') else ''
  attrs=f'id="{name}" name="{name}" aria-describedby="{name}-error"'+required
  if f['type']=='select':
   options=f'<option value="" disabled selected>{e(form["servicePlaceholder"])}</option>'+''.join(f'<option value="{e(s["id"])}">{e(s["title"])}</option>' for s in services)+f'<option value="not-sure">{e(form["notSureLabel"])}</option>'
   control=f'<select {attrs}>{options}</select>'
  elif f['type']=='textarea':control=f'<textarea {attrs} rows="2" maxlength="6000"{placeholder}></textarea>'
  else:control=f'<input {attrs} type="{e(f["type"])}"{autocomplete}{placeholder} maxlength="254">'
  qualifier='' if f.get('required') else '<span class="field-optional">Optional</span>'
  fields.append(f'<div class="conversation-field field-{name}"><label for="{name}">{e(f["label"])}{qualifier}</label>{control}<p class="field-error" id="{name}-error" hidden></p></div>')
 opening=f'<section class="contact-opening"><div class="shell"><h1>'+''.join(f'<span>{e(line)}</span>' for line in c['title'].splitlines())+'</h1><p>'+''.join(f'<span>{e(line)}</span>' for line in c['introduction'].splitlines())+'</p></div></section>'
 optional=(link('WhatsApp',c['whatsapp']) if c['whatsapp'] else '')+''.join(link(x['label'],x['url']) for x in c['socialLinks'])
 states=e(json.dumps(form['states']))
 context=e(json.dumps([{'slug':p['slug'],'title':p['title'],'services':p['services']} for p in detail_projects]))
 context_ui=f'<div id="inquiry-context" data-projects="{context}"><div id="project-context" hidden><p>{e(site["labels"]["projectReference"])}: <strong id="project-reference-title"></strong></p><button type="button" class="text-button" id="remove-project">{e(site["labels"]["removeReference"])}</button></div><input type="hidden" name="projectReference" id="projectReference" value=""><input type="hidden" name="sourcePage" id="sourcePage" value="contact.html"></div>'
 notice_description=' aria-describedby="form-note"' if not form.get('endpoint') else ''
 notice='' if form.get('endpoint') else f'<div class="submission-notice"><p id="form-note">Online inquiry submission is currently being prepared. You can contact us directly by email in the meantime.</p><a href="mailto:{e(c["email"])}">{e(c["email"])}</a></div>'
 return opening+f'<div class="shell contact-layout"><section class="inquiry" aria-labelledby="inquiry-title"><h2 class="visually-hidden" id="inquiry-title">{e(form["title"])}</h2><form id="inquiry-form"{notice_description} data-endpoint="{e(form.get("endpoint") or "")}" data-states="{states}" data-state="ready" onsubmit="return false">'+notice+context_ui+''.join(fields)+f'<div class="inquiry-actions"><button class="cta-reveal" type="submit" data-event="contact_submit" disabled>{e(form["submitLabel"])}{arrow_icon()}</button><p id="form-status" role="status" aria-live="polite" aria-atomic="true"></p><noscript><p>Use the email address above to send your inquiry. This form does not send without JavaScript and a connected delivery service.</p></noscript></div></form></section><aside class="contact-details" aria-label="Contact information"><p>Prefer email?</p><a class="contact-email" href="mailto:{e(c["email"])}">{e(c["email"])}</a><div class="contact-utility"><a href="tel:{e(c["phone"].replace(" ","").replace("-",""))}">{e(c["phone"])}</a><address>{e(c["address"])}</address>{optional}</div></aside></div>'
def not_found():
 c=site['notFound']
 return f'<section class="shell not-found"><h1>{e(c["title"])}</h1><p>{e(c["message"])}</p><div>{link(c["homeLabel"],"index.html")}{link(c["workLabel"],"work.html")}</div></section>'
def document(name,title,content,page=None):
 page=page or ('home' if name=='index' else name)
 html=f'<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><meta name="theme-color" content="'+('#F9E64F' if name=='index' else '#FFFFFF')+f'"><title>ShowMakers | {e(title)}</title><link rel="stylesheet" href="assets/css/global.css"><link rel="stylesheet" href="assets/css/components.css"><link rel="stylesheet" href="assets/css/pages/{page}.css"><script type="module" src="assets/js/global.js"></script>'+('<script type="module" src="assets/js/home.js"></script>' if name=='index' else '')+('<script type="module" src="assets/js/services.js"></script>' if name=='services' else '')+('<script type="module" src="assets/js/work.js"></script>' if name=='work' and page=='work' else '')+('<script type="module" src="assets/js/contact.js"></script>' if name=='contact' else '')+('<script type="module" src="assets/js/project.js"></script>' if page=='project' else '')+f'</head><body class="page-{page}"><a class="skip-link" href="#main">Skip to content</a>{header(name)}<main id="main">{content}</main>{footer(name)}</body></html>'
 return re.sub(r'((?:href|src)=")(assets/(?:css|js)/[^"]+)(")',lambda m:m[1]+m[2]+'?v='+hashlib.sha256((ROOT/m[2]).read_bytes()).hexdigest()[:10]+m[3],html)+'\n'
for name,render in [('index',home),('work',work),('services',services_page),('about',about),('contact',contact),('404',not_found)]:
 title='Marketing ideas. Made to happen.' if name=='index' else name.title()
 (ROOT/f'{name}.html').write_text(document(name,title,render()))
(ROOT/'work').mkdir(exist_ok=True)
active_detail_ids={p['id'] for p in detail_projects}
for p in load('projects'):
 if p['id'] not in active_detail_ids:
  stale=ROOT/'work'/f"{p['slug']}.html"
  if stale.exists():stale.unlink()
for i,p in enumerate(detail_projects):
 html=document('work',p['title'],project_detail(p,i+1),'project')
 # Detail documents live one directory below the root; preserve local fragments.
 html=re.sub(r'((?:href|src)=")([^"]+)(")',lambda m:m[1]+('../'+m[2] if not re.match(r'(?:[a-z]+:|/|#)',m[2]) else m[2])+m[3],html)
 (ROOT/'work'/f'{p["slug"]}.html').write_text(html)
print('Built five main pages and data-driven project detail pages')
