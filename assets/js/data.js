// Content boundary: replace these JSON reads with CMS/API responses later.
const cache = new Map();
export function getContent(collection) {
  if (!cache.has(collection)) cache.set(collection, fetch(`data/${collection}.json`).then(r => {
    if (!r.ok) throw new Error(`Content unavailable: ${collection}`);
    return r.json();
  }));
  return cache.get(collection);
}
export const escapeHTML = value => String(value).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
