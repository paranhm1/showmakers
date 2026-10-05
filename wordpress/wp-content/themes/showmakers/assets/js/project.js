// Service slugs, rather than labels or inferred relationships, carry browsing context.
const back=document.querySelector('.back-to-work');
if(back){
 const valid=new Set(back.dataset.validServices.split(' '));
 const from=new URL(location.href).searchParams.get('from');
 if(valid.has(from)){const url=new URL(back.href);url.searchParams.set('service',from);back.href=url.href;}
}
