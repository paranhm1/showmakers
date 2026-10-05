document.documentElement.classList.add('js');
if (new URLSearchParams(location.search).get('motion') === 'reduce') document.documentElement.classList.add('reduce-motion');
const menu=document.querySelector('.menu-toggle'),nav=document.querySelector('#primary-navigation');
menu.hidden=false;
function closeMenu(focus=false){menu.setAttribute('aria-expanded','false');menu.querySelector('span').textContent='+';nav.classList.remove('is-open');if(focus)menu.focus();}
menu.addEventListener('click',()=>{const open=menu.getAttribute('aria-expanded')!=='true';menu.setAttribute('aria-expanded',String(open));menu.querySelector('span').textContent=open?'−':'+';nav.classList.toggle('is-open',open);});
document.addEventListener('keydown',ev=>{if(ev.key==='Escape'&&menu.getAttribute('aria-expanded')==='true')closeMenu(true);});
document.addEventListener('click',ev=>{if(!ev.target.closest('.site-header'))closeMenu();});
matchMedia('(min-width:601px)').addEventListener('change',()=>closeMenu());


