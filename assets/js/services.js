import {motionSettings, reducedMotion} from './motion.js';
// Progressive enhancement of panels rendered from services.json by the shared builder.
// The same renderer can receive WordPress records; JavaScript contains no service copy.
const explorer=document.querySelector('#service-explorer');
if(explorer){
 const index=explorer.querySelector('.service-navigation');
 const anchors=[...index.querySelectorAll('a')];
 const panels=[...explorer.querySelectorAll('.service-section')];
 const status=explorer.querySelector('#service-status');
 const reduced=matchMedia('(prefers-reduced-motion:reduce)');
 const valid=anchors.length===panels.length&&anchors.every(a=>panels.some(p=>p.id===a.hash.slice(1)));
 if(valid){
  const buttons=anchors.map(anchor=>{
   const button=document.createElement('button');
   button.type='button';button.innerHTML=anchor.innerHTML;
   button.dataset.service=anchor.hash.slice(1);
   button.setAttribute('aria-controls',button.dataset.service);
   anchor.replaceWith(button);return button;
  });
  // A button group rather than tabs: all eight controls are discoverable with Tab.
  index.setAttribute('role','group');
  let selected;
  function select(id,{announce=true,animate=true}={}){
   const panel=panels.find(p=>p.id===id);if(!panel||id===selected)return;
   panels.forEach(p=>{p.getAnimations().forEach(a=>a.cancel());p.hidden=p!==panel;});
   buttons.forEach(b=>b.setAttribute('aria-pressed',String(b.dataset.service===id)));
   selected=id;
   if(animate&&!reducedMotion()){
    panel.animate([{opacity:.2,transform:'translateY(5px)'},{opacity:1,transform:'none'}],motionSettings());
   }
   if(announce)status.textContent=panel.querySelector('h2').textContent+' selected. Service details updated.';
  }
  buttons.forEach((button,i)=>{
   button.addEventListener('click',()=>{
    select(button.dataset.service);
    requestAnimationFrame(()=>requestAnimationFrame(()=>{
    if(matchMedia('(max-width:800px)').matches){
     const heading=panels.find(p=>p.id===button.dataset.service).querySelector('h2');
     const rect=heading.getBoundingClientRect();
     const header=document.querySelector('.header-surface');
     const style=getComputedStyle(header);
     const offset=['sticky','fixed'].includes(style.position)?header.getBoundingClientRect().height:0;
     if(rect.top<offset+16||rect.bottom>innerHeight-32){
      const target=heading.closest('.service-heading');
      scrollTo({top:scrollY+target.getBoundingClientRect().top-offset-24,behavior:'instant'});
     }
    }
    }));
    history.replaceState(null,'','#'+button.dataset.service);
   });
   button.addEventListener('keydown',event=>{
    let next;
    if(event.key==='ArrowDown')next=(i+1)%buttons.length;
    if(event.key==='ArrowUp')next=(i+buttons.length-1)%buttons.length;
    if(event.key==='Home')next=0;if(event.key==='End')next=buttons.length-1;
    if(next!==undefined){event.preventDefault();buttons[next].focus();}
   });
  });
  select(panels.some(p=>p.id===location.hash.slice(1))?location.hash.slice(1):panels[0].id,{announce:false,animate:false});
  explorer.classList.add('is-enhanced');
  const followHash=()=>select(location.hash.slice(1)||panels[0].id,{animate:false});
  addEventListener('hashchange',followHash);addEventListener('popstate',followHash);
  reduced.addEventListener('change',()=>{if(reduced.matches)panels.forEach(p=>p.getAnimations().forEach(a=>a.cancel()));});
 }
}
