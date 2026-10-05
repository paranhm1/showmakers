// No endpoint is configured in this prototype. No requests or success are simulated.
const form = document.querySelector('#inquiry-form');
if (form) {
  const button = form.querySelector('button[type=submit]');
  const status = document.querySelector('#form-status');
  const messages = JSON.parse(form.dataset.states);
  const controls = [...form.querySelectorAll('input:not([type=hidden]),textarea,select')];
  const service = form.querySelector('#service');
  const context = form.querySelector('#inquiry-context');
  const projects = new Map(JSON.parse(context.dataset.projects).map(project => [project.slug, project]));
  const validServices = new Set([...service.options].map(option => option.value).filter(Boolean));
  const projectInput = form.querySelector('#projectReference');
  const sourceInput = form.querySelector('#sourcePage');
  const contextNote = form.querySelector('#project-context');
  const referenceTitle = form.querySelector('#project-reference-title');
  function sourcePage() {
    sourceInput.value = projectInput.value ? `work/${projectInput.value}.html` :
      new URL(location.href).searchParams.has('service') ? 'services.html' : 'contact.html';
  }
  function readContext() {
    const url = new URL(location.href);
    const requested = url.searchParams.get('service');
    service.value = validServices.has(requested) ? requested : '';
    const project = projects.get(url.searchParams.get('project'));
    projectInput.value = project?.slug || '';
    referenceTitle.textContent = project?.title || '';
    contextNote.hidden = !project;
    if (requested && !validServices.has(requested)) url.searchParams.delete('service');
    if (url.searchParams.has('project') && !project) url.searchParams.delete('project');
    if (url.href !== location.href) history.replaceState(null, '', url);
    sourcePage();
  }
  readContext();
  addEventListener('popstate', readContext);
  service.addEventListener('change', () => {
    const url = new URL(location.href);
    if (validServices.has(service.value)) url.searchParams.set('service', service.value);
    else url.searchParams.delete('service');
    history.replaceState(null, '', url);
    sourcePage();
  });
  form.querySelector('#remove-project').addEventListener('click', () => {
    const url = new URL(location.href);
    url.searchParams.delete('project');
    history.replaceState(null, '', url);
    projectInput.value = '';
    referenceTitle.textContent = '';
    contextNote.hidden = true;
    sourcePage();
    service.focus();
  });
  function setState(state) {
    form.dataset.state = state;
    status.textContent = messages[state] || '';
    form.setAttribute('aria-busy', String(state === 'submitting'));
    button.disabled = state === 'submitting';
  }
  form.noValidate = true;
  button.disabled = false;
  const errors={name:'Please enter your name.',email:'Enter a valid email address.',service:'Please select a service or choose “Not sure yet”.',goal:'Please tell us what you would like to achieve.'};
  function validate(control){
    const invalid=!control.validity.valid||(control.required&&!control.value.trim());
    const message=form.querySelector('#'+control.id+'-error');
    message.textContent=invalid?(errors[control.id]||'Please check this field.'):'';
    message.hidden=!invalid;
    if(invalid)control.setAttribute('aria-invalid','true');else control.removeAttribute('aria-invalid');
    return !invalid;
  }
  controls.forEach(control=>{
    control.addEventListener('blur',()=>{if(control.value.trim()||control.hasAttribute('aria-invalid'))validate(control);});
    for(const event of ['input','change'])control.addEventListener(event,()=>{
      if(control.hasAttribute('aria-invalid'))validate(control);
      if(form.dataset.state!=='submitting')setState('ready');
    });
  });
  form.addEventListener('submit', async event => {
    event.preventDefault();
    if (form.dataset.state === 'submitting') return;
    const invalid=controls.filter(control=>!validate(control));
    if(invalid.length){setState('validationError');invalid[0].focus();return;}
    const endpoint = form.dataset.endpoint;
    if (!endpoint) { setState('unavailable'); return; }
    // Future WordPress endpoint: validate on the server, deliver/store the inquiry,
    // assign createdAt/status there, and return {success:true} only after processing.
    setState('submitting');
    try {
      const payload = Object.fromEntries(new FormData(form));
      const response = await fetch(endpoint, {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
      });
      const result = await response.json();
      if (!response.ok || result.success !== true) throw new Error('Inquiry not accepted');
      setState('success');
      form.reset();
      readContext();
    } catch { setState('error'); }
  });
}
