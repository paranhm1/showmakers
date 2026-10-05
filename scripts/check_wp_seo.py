"""Anonymous local SEO, redirects, heading/image and internal-link audit; no writes."""
from html.parser import HTMLParser
from urllib.request import urlopen,build_opener,HTTPRedirectHandler
from urllib.error import HTTPError
from urllib.parse import urljoin,urlsplit
from concurrent.futures import ThreadPoolExecutor
import json,re
BASE='http://showmakers-local.local/'
slugs=['short-form-brand-content','tid-group','stories-in-the-moment','ravo-film','ai-assisted-content']
paths=['','work/','services/','about/','contact/']+['work/'+s+'/' for s in slugs]
class Doc(HTMLParser):
 def __init__(self):super().__init__();self.meta={};self.canon=[];self.headings=[];self.images=[];self.links=[];self.assets=set();self.ids=set();self.json=[];self.ld=False;self.title=False;self.titles=[]
 def handle_starttag(self,t,attrs):
  a=dict(attrs)
  if a.get('id'):self.ids.add(a['id'])
  if t=='meta':self.meta.setdefault(a.get('name',a.get('property')),[]).append(a.get('content',''))
  if t=='link' and a.get('rel')=='canonical':self.canon.append(a['href'])
  if t=='a' and a.get('href'):self.links.append(a['href'])
  if t in ['h1','h2','h3','h4','h5','h6']:self.headings.append(int(t[1]))
  if t=='img':self.images.append(a)
  if t in ['img','script'] and a.get('src'):self.assets.add(a['src'])
  if t=='link' and a.get('rel') in ['stylesheet','preload']:self.assets.add(a['href'])
  if t=='script' and a.get('type')=='application/ld+json':self.ld=True;self.json.append('')
  if t=='title':self.title=True;self.titles.append('')
 def handle_data(self,d):
  if self.ld:self.json[-1]+=d
  if self.title:self.titles[-1]+=d
 def handle_endtag(self,t):
  if t=='script':self.ld=False
  if t=='title':self.title=False

def get(path):
 with urlopen(urljoin(BASE,path),timeout=30) as r:return r.status,r.read().decode(),r.geturl()
docs={};assets=set();links=set();dimension_gaps=[]
for p in paths+['work/?service=media-production','contact/?project=ravo-film&service=website-digital-solutions','work/ravo-film/?from=website-digital-solutions']:
 status,html,url=get(p);d=Doc();d.feed(html);canonical=urljoin(BASE,p.split('?')[0]);assert status==200
 assert len(d.titles)==1 and 'ShowMakers' in d.titles[0],p
 assert len(d.meta['description'])==1 and d.meta['description'][0],p
 assert d.canon==[canonical] and d.meta['og:url']==[canonical],(p,d.canon)
 assert 'noindex' in d.meta['robots'][0] and 'nofollow' in d.meta['robots'][0],p
 assert d.meta['og:title']==d.titles and d.meta['og:description']==d.meta['description'],p
 assert d.meta['og:type']==['website'] and len(d.meta['og:image'])==1,p
 assets.add(d.meta['og:image'][0]);assets|=d.assets
 assert len(d.json)==1,p
 graph=json.loads(d.json[0])['@graph'];assert [g['@type'] for g in graph]==['Organization','WebSite'],p
 assert graph[0]['email']=='sales@showmakers.org' and graph[0]['telephone']=='+6012-687 8775',p
 assert set(graph[0])=={'@type','@id','name','url','logo','email','telephone','address'},p
 assert d.headings.count(1)==1 and all(b<=a+1 for a,b in zip(d.headings,d.headings[1:])),(p,d.headings)
 assert not any(x in html for x in ['brand-showcase.webp','samsung-experience.webp','internal_media_note','_showmakers_provenance']),p
 for im in d.images:
  assert 'alt' in im,(p,im)
  assert not re.search(r'\.(webp|png|jpg)$',im['alt']),im
  if not im.get('width') or not im.get('height'):dimension_gaps.append((p,im.get('class'),im['src']))
 docs[p.split('?')[0]]=d;links.update(urljoin(url,h) for h in d.links if urlsplit(urljoin(url,h)).netloc==urlsplit(BASE).netloc)
 print('PASS SEO/semantics:',p or '/',d.titles[0])
class NoRedirect(HTTPRedirectHandler):
 def redirect_request(self,*args,**kwargs):return None
opener=build_opener(NoRedirect())
legacy={'index.html':'','work.html':'work/','services.html':'services/','about.html':'about/','contact.html':'contact/'}
legacy.update({'work/'+s+'.html':'work/'+s+'/' for s in slugs})
legacy.update({'work.html?service=media-production&junk=1':'work/?service=media-production','contact.html?project=ravo-film&service=not-sure&junk=1':'contact/?service=not-sure&project=ravo-film','work/ravo-film.html?from=website-digital-solutions':'work/ravo-film/?from=website-digital-solutions','contact.html?project=unknown&service=restricted':'contact/'})
for old,new in legacy.items():
 try:opener.open(urljoin(BASE,old));raise AssertionError(old)
 except HTTPError as e:assert e.code==301 and e.headers['Location']==urljoin(BASE,new),(old,e.code,e.headers.get('Location'))
 assert get(new)[0]==200
 print('PASS 301:',old,'→',new or '/')
for p in ['wp-sitemap.xml','not-a-real-page/','service/media-production/','work/project-06/','work/brand-showcase/']:
 try:get(p);raise AssertionError(p)
 except HTTPError as e:
  assert e.code==404,(p,e.code)
  html=e.read().decode();d=Doc();d.feed(html);assert 'noindex' in d.meta.get('robots',[''])[0],p
  assert not d.canon and not d.json,p
assert 'Disallow: /' in get('robots.txt')[1]
def asset(u):
 with urlopen(urljoin(BASE,u),timeout=30) as r:assert r.status==200,u;return (len(r.read()),u)
with ThreadPoolExecutor(max_workers=4) as pool:sizes=list(pool.map(asset,assets))
for u in sorted(links):
 parsed=urlsplit(u);status,html,_=get(u);assert status==200,u
 if parsed.fragment:
  d=Doc();d.feed(html);assert parsed.fragment in d.ids,(u,'missing anchor')
print('PASS:',len(links),'internal links/anchors;',len(assets),'assets')
print('Image dimension gaps (existing layout/CSS must be reviewed):',dimension_gaps)
print('Largest delivered assets:',sorted(sizes,reverse=True)[:5])
# Compare visible content to preflight captures; transient contact token/signature are generated per request.
from pathlib import Path
for p,name in [('','home'),('work/','work'),('services/','services'),('about/','about'),('contact/','contact')]:
 before=Path('/tmp/showmakers-seo-before-'+name+'.html')
 if before.exists():
  old=before.read_text();new=get(p)[1]
  def visible(s):
   s=s[s.index('<body'):]
   return re.sub(r'(name="contact_(?:nonce|token|signature)" value=")[^"]*',r'\1TOKEN',s)
  assert visible(old)==visible(new),(name,'visible body changed')
  print('PASS: exact unchanged visible body',name)
