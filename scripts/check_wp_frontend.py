"""Read-only anonymous checks for the Phase 4 LocalWP pilot; no browser or database writes."""
from concurrent.futures import ThreadPoolExecutor
from html.parser import HTMLParser
from urllib.request import urlopen
from urllib.parse import urljoin,urlsplit
import json
BASE='http://showmakers-local.local/'
class Document(HTMLParser):
 def __init__(self): super().__init__(); self.assets=set(); self.h1=0; self.cards=[]; self.form=None; self.context=[]; self.next=False
 def handle_starttag(self,tag,attrs):
  a=dict(attrs)
  if tag=='h1': self.h1+=1
  if tag in ['img','script'] and a.get('src'): self.assets.add(urljoin(BASE,a['src']))
  if tag=='link' and a.get('rel')=='stylesheet': self.assets.add(urljoin(BASE,a['href']))
  if tag=='article' and 'portfolio-project' in a.get('class',''): self.cards.append(a.get('id'))
  if tag=='form' and a.get('id')=='inquiry-form': self.form=a
  if a.get('id')=='inquiry-context': self.context=json.loads(a['data-projects'])
  if 'Next Project' in a.get('aria-label',''): self.next=True
paths=['','work/','work/?service=website-digital-solutions','work/ravo-film/','services/','contact/?project=ravo-film&service=website-digital-solutions']
def fetch(path):
 with urlopen(urljoin(BASE,path),timeout=20) as r: return path,r.status,r.read().decode()
assets=set()
with ThreadPoolExecutor(max_workers=3) as pool:
 for path,status,html in pool.map(fetch,paths):
  assert status==200,(path,status)
  assert not any(s in html for s in ['Fatal error:','Warning:','brand-showcase.webp','samsung-experience.webp','internal_media_note','_showmakers_provenance']),path
  d=Document();d.feed(html);assets|=d.assets
  assert d.h1==1,(path,d.h1)
  if path.startswith('work/') and 'ravo-film' not in path: assert d.cards==['ravo-film'],(path,d.cards)
  if path=='work/ravo-film/':
   assert 'max-width:789px' in html and 'width="789" height="1276"' in html
   assert 'Next Project' not in html,'No other project is eligible for Next'
   assert '/services/#website-digital-solutions' in html
  if path.startswith('contact/'):
   assert d.form['data-endpoint']=='' and d.form.get('onsubmit')=='return false'
   assert len(d.context)==1 and d.context[0]['slug']=='ravo-film'
   assert d.context[0]['services']==['website-digital-solutions'] and d.context[0]['url'].endswith('/work/ravo-film/')
  print('PASS:',path or '/',status)
def asset_status(url):
 with urlopen(url,timeout=20) as r: assert r.status==200,(url,r.status)
with ThreadPoolExecutor(max_workers=4) as pool: list(pool.map(asset_status,sorted(assets)))
print('PASS:',len(assets),'unique public image/script/stylesheet assets')
for path in ['work/ravo-film.html']:
 try: fetch(path); raise AssertionError('Unexpected duplicate/public route: '+path)
 except Exception as e:
  assert getattr(e,'code',None)==404,(path,e)
  print('PASS: expected 404',path)
# Core may guess an unknown URL and redirect to the public Project with the same slug.
# This is not a Client single route; accept that canonical redirect as well as a 404.
try:
 with urlopen(urljoin(BASE,'clients/ravo-film/'),timeout=20) as r:
  assert r.geturl()==urljoin(BASE,'work/ravo-film/'),r.geturl()
  print('PASS: no Client page; core redirects unknown URL to canonical Project')
except Exception as e:
 assert getattr(e,'code',None)==404,e
 print('PASS: no public Client URL')
