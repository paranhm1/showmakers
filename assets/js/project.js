// Service slugs, rather than labels or inferred relationships, carry browsing context.
const back=document.querySelector('.back-to-work');
if(back){
 const valid=new Set(['business-consulting','social-media-marketing','media-production','ai-enhanced-content-production','website-digital-solutions','event-management','seo-sem','influencer-marketing']);
 const from=new URL(location.href).searchParams.get('from');
 if(valid.has(from)){const url=new URL(back.href);url.searchParams.set('service',from);back.href=url.href;}
}
