import { getContent } from './data.js';
const reduced=matchMedia('(prefers-reduced-motion:reduce)'),preview=new URLSearchParams(location.search).get('motion')==='reduce';
const section=document.querySelector('.clients'),track=document.querySelector('.marquee-track'),list=document.querySelector('#client-list'),control=document.querySelector('#marquee-control');
const reduce=()=>reduced.matches||preview;
document.querySelector('.hero').classList.add('is-ready');
let paused=false;
function motion(){
 section.classList.toggle('is-static',reduce());control.hidden=reduce();
 if(!reduce()&&!track.querySelector('.logo-copy')){const copy=list.cloneNode(true);copy.removeAttribute('id');copy.classList.add('logo-copy');copy.setAttribute('aria-hidden','true');copy.setAttribute('inert','');copy.querySelectorAll('img').forEach(img=>img.alt='');track.append(copy);}
}
motion();reduced.addEventListener('change',motion);
let labels={pause:'Pause logos',resume:'Resume logos'};
getContent('site').then(s=>labels=s.labels).catch(()=>{});
control.addEventListener('click',()=>{paused=!paused;section.dataset.paused=String(paused);control.setAttribute('aria-pressed',String(paused));control.querySelector('.control-label').textContent=paused?labels.resume:labels.pause;control.querySelector('.pause-symbol').textContent=paused?'▷':'Ⅱ';});
let inView=false;const visibility=()=>{track.style.animationPlayState=(!inView||document.hidden)?'paused':'';};
new IntersectionObserver(entries=>{inView=entries[0].isIntersecting;visibility();},{rootMargin:'80px'}).observe(section);
document.addEventListener('visibilitychange',visibility);
