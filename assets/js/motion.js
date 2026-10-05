// JS panel animation uses the same CSS motion tokens as links and entrances.
export function motionSettings() {
  const style = getComputedStyle(document.documentElement);
  return {
    duration: parseFloat(style.getPropertyValue('--motion-standard')) || 240,
    easing: style.getPropertyValue('--ease-standard').trim() || 'ease-out'
  };
}
export function reducedMotion() {
  return matchMedia('(prefers-reduced-motion:reduce)').matches || document.documentElement.classList.contains('reduce-motion');
}
