// Entry point for ETP Theme scripts.
(function () {
  function initToc(scope) {
    console.log('Initializing TOC');
    const roots = (scope || document).querySelectorAll('.etp-toc');
    roots.forEach((toc) => {
      const toggleBtn = toc.querySelector('.etp-toc__toggle');
      const body = toc.querySelector('.etp-toc__body');
      if (!toggleBtn || !body || toggleBtn.dataset.etpTocReady) return;

      toggleBtn.dataset.etpTocReady = 'true';
      toggleBtn.addEventListener('click', () => {
        console.log('Toggling TOC');
        const collapsed = toc.classList.toggle('is-collapsed');
        toggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initToc(document);
  });

  document.addEventListener('bricks/element/render', (event) => {
    const el = event.detail && event.detail.element ? event.detail.element : null;
    if (el) {
      initToc(el);
    }
  });
})();
