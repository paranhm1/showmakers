// Service slugs, rather than labels or inferred relationships, carry browsing context.
const back=document.querySelector('.back-to-work');
if(back){
 const valid=new Set(back.dataset.validServices.split(' '));
 const from=new URL(location.href).searchParams.get('from');
 if(valid.has(from)){const url=new URL(back.href);url.searchParams.set('service',from);back.href=url.href;}
}

// Playback is progressively enhanced: no HTML autoplay before motion preferences are known.
const frame=document.querySelector('[data-project-video]');
if(frame){
 const video=frame.querySelector('video');
 const button=frame.querySelector('.video-toggle');
 const fallback=frame.querySelector('.video-fallback');
 const status=frame.querySelector('.video-status');
 const motion=matchMedia('(prefers-reduced-motion: reduce)');
 const sync=()=>{button.textContent=video.paused?'Play video':'Pause video';};
 video.muted=true;
 video.controls=false;
 button.hidden=false;
 video.addEventListener('play',sync);
 video.addEventListener('pause',sync);
 video.addEventListener('ended',sync);
 video.addEventListener('error',()=>{
  video.hidden=true; if(fallback)fallback.hidden=false; button.hidden=true;
  status.textContent=fallback?'Video unavailable. Poster image shown.':'Video unavailable.';
 });
 const play=()=>video.play().then(()=>{status.textContent='';sync();}).catch(()=>{
  sync();status.textContent='Playback did not start. Use Play video to try again.';
 });
 button.addEventListener('click',()=>{if(video.paused)play();else {video.pause();sync();}});
 const preferences=()=>{
  video.loop=!motion.matches;
  video.autoplay=!motion.matches;
  if(motion.matches){video.pause();video.currentTime=0;}
 };
 preferences();motion.addEventListener('change',preferences);
 document.addEventListener('visibilitychange',()=>{if(document.hidden)video.pause();});
 if(!motion.matches)play();
 sync();
}
