// One many-to-many service relationship; URL history is the filter state.
const filters = document.querySelector('.work-filters');
const cards = [...document.querySelectorAll('.portfolio-project')];
const status = document.querySelector('#work-status');
const empty = document.querySelector('#work-empty');
if (filters) {
  const buttons = [...filters.querySelectorAll('button[data-service]')];
  const valid = new Set(['all',...filters.dataset.validServices.split(' ')]);
  function apply(service) {
    if (!valid.has(service)) service = 'all';
    buttons.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.service === service)));
    let count = 0;
    cards.forEach(card => {
      const visible = service === 'all' || (card.dataset.placeholder !== 'true' && card.dataset.services.split(' ').includes(service));
      card.hidden = !visible;
      if (visible && card.dataset.placeholder !== 'true') count++;
      const link=card.querySelector('a[data-project]');
      if(link){const url=new URL(link.href);if(service==='all')url.searchParams.delete('from');else url.searchParams.set('from',service);link.href=url.href;}
    });
    empty.hidden = count !== 0;
    status.textContent = `${count} ${count === 1 ? 'project' : 'projects'} shown.`;
  }
  function fromURL() {
    const url = new URL(location.href);
    const requested = url.searchParams.get('service');
    const service = valid.has(requested) ? requested : 'all';
    if (requested && (service === 'all')) {
      url.searchParams.delete('service');
      history.replaceState(null, '', url);
    }
    apply(service);
  }
  filters.hidden = false;
  fromURL();
  filters.addEventListener('click', event => {
    const button = event.target.closest('button[data-service]');
    if (!button) return;
    const url = new URL(location.href);
    if (button.dataset.service === 'all') url.searchParams.delete('service');
    else url.searchParams.set('service', button.dataset.service);
    if (url.href !== location.href) history.pushState(null, '', url);
    apply(button.dataset.service);
  });
  addEventListener('popstate', fromURL);
}
