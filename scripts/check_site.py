"""Check local links, assets and content relationships across generated pages."""
from pathlib import Path
from html.parser import HTMLParser
from urllib.parse import urlsplit,unquote
import json
import re
ROOT=Path(__file__).resolve().parent.parent
class Page(HTMLParser):
 def __init__(self,path):
  super().__init__();self.path=path;self.ids=set();self.links=[];self.nav=[];self.in_nav=False;self.feed(path.read_text())
 def handle_starttag(self,tag,attrs):
  a=dict(attrs)
  if 'id' in a:self.ids.add(a['id'])
  if tag=='nav' and a.get('id')=='primary-navigation':self.in_nav=True
  if tag=='a' and 'href' in a:
   self.links.append(a['href'])
   if self.in_nav:self.nav.append(a['href'])
  if tag in ['img','script'] and 'src' in a:self.links.append(a['src'])
  if tag=='link' and 'href' in a:self.links.append(a['href'])
 def handle_endtag(self,tag):
  if tag=='nav':self.in_nav=False
main_names=['index.html','work.html','services.html','about.html','contact.html']
paths=[ROOT/n for n in main_names+['404.html']]+list((ROOT/'work').glob('*.html'))
pages={p.resolve():Page(p) for p in paths}
errors=[]
for n,p in pages.items():
 if [(p.path.parent/urlsplit(h).path).resolve() for h in p.nav]!=[(ROOT/n['url']).resolve() for n in json.loads((ROOT/'data/site.json').read_text())['navigation']]:errors.append(f'{n}: incorrect main navigation {p.nav}')
 for href in p.links:
  u=urlsplit(href)
  if u.scheme or u.netloc:continue
  target=(p.path.parent/unquote(u.path)).resolve() if u.path else p.path.resolve()
  if not target.is_file():errors.append(f'{n}: missing {href}')
  elif u.fragment and target in pages and u.fragment not in pages[target].ids:errors.append(f'{n}: missing anchor {href}')
services=json.loads((ROOT/'data/services.json').read_text());projects=json.loads((ROOT/'data/projects.json').read_text());ids={s['id'] for s in services};project_ids={p['id'] for p in projects}
assert len(ids)==8,'Eight services required'
for p in projects:
 assert set(p['services'])<=ids,p['id']
 if p.get('published',True):
  if p.get('isPlaceholder'):
   assert p.get('developmentOnly') and not p.get('client') and not p.get('detailUrl'),p['id']
   assert not (ROOT/'work'/f"{p['slug']}.html").exists(),p['id']
  else:assert (ROOT/p['detailUrl']).is_file(),p['id']
 assert (ROOT/p['heroMedia']['src']).is_file(),p['id']
 assert (ROOT/p['listingThumbnail']['src']).is_file(),p['id']
 assert p['listingThumbnail']['fit'] in ('cover','contain'),p['id']
 assert not (p['id']=='brand-showcase' and p['featured']),'Tommy Hilfiger must not be featured on Home'
for s in services:assert set(s['relatedProjects'])<=project_ids,s['id']
assert 'Tommy Hilfiger' not in (ROOT/'index.html').read_text()
assert 'human judgment' in (ROOT/'about.html').read_text().lower()
if errors:raise SystemExit('\n'.join(errors))
assert 'View image' not in (ROOT/'work.html').read_text()
assert 'View image' not in (ROOT/'index.html').read_text()
print('Main and project pages: navigation, links, assets, anchors, project routes, classifications and project-level actions passed.')

placeholders=[p for p in projects if p.get('isPlaceholder') and p.get('published',True)]
print('Development placeholders remaining:', ', '.join(p['id'] for p in placeholders) or 'none')
if '--production' in __import__('sys').argv and placeholders:
 raise SystemExit('Production check failed: replace or remove all development placeholders before launch.')

restricted=['brand-showcase.webp','samsung-experience.webp']
for folder in [ROOT,ROOT/'work',ROOT/'assets',ROOT/'data',ROOT/'prototype']:
 paths=folder.glob('*.html') if folder==ROOT else folder.rglob('*')
 for path in paths:
  if path.is_file() and path.suffix in ['.html','.css','.js','.json'] and path.name not in ['sources.json']:
   text=path.read_text()
   assert not any(name in text for name in restricted),f'Restricted asset reference: {path}'

# Connected routes must agree with approved published data, not copied display labels.
from urllib.parse import parse_qs
published=[p for p in projects if p.get('published',True)]
real=sorted([p for p in published if not p.get('isPlaceholder') and p.get('mediaStatus')=='approved'],key=lambda p:p['order'])
service_links=pages[(ROOT/'services.html').resolve()].links
for service in services:
 slug=service['id']
 assert f'contact.html?service={slug}' in service_links,slug
 has_work=any(slug in p['services'] for p in real)
 assert (f'work.html?service={slug}' in service_links)==has_work,slug
for i,project in enumerate(real):
 page=pages[(ROOT/project['detailUrl']).resolve()]
 inquiry=[parse_qs(urlsplit(h).query) for h in page.links if urlsplit(h).path=='../contact.html' and urlsplit(h).query]
 assert len(inquiry)==1 and inquiry[0]['project']==[project['slug']],project['slug']
 if len(project['services'])==1:assert inquiry[0]['service']==project['services'],project['slug']
 else:assert 'service' not in inquiry[0],project['slug']
 assert '../'+real[(i+1)%len(real)]['detailUrl'] in page.links,project['slug']
 assert 'class="footer-route"' not in page.path.read_text()
contact_html=(ROOT/'contact.html').read_text()
assert 'projectReference' in contact_html and 'sourcePage' in contact_html
assert 'project-06' not in contact_html and 'brand-showcase' not in contact_html
assert 'data-event="contact_submit"' in contact_html
assert 'data-event="hero_view_work"' in (ROOT/'index.html').read_text()
assert 'data-event="project_open"' in (ROOT/'work.html').read_text()
assert '<video' not in ''.join(page.path.read_text() for page in pages.values()),'Review video controls/posters before introducing footage'
print('Connected service/work/inquiry routes, real-project rotation, placeholder exclusion, 404 and analytics hooks passed.')

# UX polish invariants: public totals count approved work, never development cards.
work_html=(ROOT/'work.html').read_text()
assert f'All <span>{len(real):02d}</span>' in work_html
for service in services:
 count=sum(service['id'] in p['services'] for p in real)
 pattern=f'data-service="{service["id"]}"'
 assert (pattern in work_html)==bool(count),service['id']
 if count:
  assert re.search(pattern+r'[^>]*>[^<]+ <span>'+f'{count:02d}'+r'</span>',work_html),service['id']
assert contact_html.index('class="inquiry"')<contact_html.index('class="contact-details"')
assert contact_html.index('id="form-note"')<contact_html.index('id="name"')
for field in ['name','service','goal','email']:
 assert f'aria-describedby="{field}-error"' in contact_html
 assert f'id="{field}-error" hidden' in contact_html
print('UX invariants: verified counts, real-work filters, contact document order and field error associations passed.')
