'use strict';

import { requireDatasetValue, requireElement } from '../../webtrees/index';

/**
 * Keep import progress panels and action rows in sync.
 *
 * @param {HTMLElement} page
 */
function synchronizeImportPanels (page) {
  page.querySelectorAll('[id^="import"]').forEach((element) => {
    if (!(element instanceof HTMLElement)) {
      return;
    }

    if (!element.id.startsWith('import')) {
      return;
    }

    const treeId = element.id.slice('import'.length);

    if (treeId === '') {
      return;
    }

    const actions = requireElement(page, `#${CSS.escape(`actions${treeId}`)}`, HTMLElement, `actions row for import${treeId}`);

    if (element.querySelector('[data-wt-import-complete]') instanceof HTMLElement) {
      element.classList.add('d-none');
      actions.classList.remove('d-none');
    }

    if (element.querySelector('[data-wt-import-fail]') instanceof HTMLElement) {
      actions.classList.remove('d-none');
    }
  });
}

/**
 * @param {HTMLElement} importContainer
 * @param {string} url
 */
function loadImportContainer (importContainer, url) {
  $(importContainer).load(url, {});
}

/**
 * Trigger any deferred import autoload markers.
 *
 * @param {HTMLElement} page
 */
function processImportAutoloads (page) {
  page.querySelectorAll('[data-wt-import-autoload-url]').forEach((element) => {
    if (!(element instanceof HTMLElement)) {
      return;
    }

    if (element.dataset.wtImportAutoloadDone === '1') {
      return;
    }

    const url = requireDatasetValue(element, 'wtImportAutoloadUrl', 'import autoload URL');
    const importContainer = element.closest('[id^="import"]');

    if (!(importContainer instanceof HTMLElement)) {
      return;
    }

    element.dataset.wtImportAutoloadDone = '1';
    loadImportContainer(importContainer, url);
  });
}

/**
 * Initialize the trees admin page.
 */
export function initializeTreesPage () {
  const page = document.getElementById('admin-trees-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  page.addEventListener('click', (event) => {
    const target = event.target instanceof Element
      ? event.target.closest('[data-wt-submit-form]')
      : null;

    if (target instanceof HTMLAnchorElement) {
      event.preventDefault();

      const formId = requireDatasetValue(target, 'wtSubmitForm', 'submit form target ID');
      const form = requireElement(document, `#${CSS.escape(formId)}`, HTMLFormElement, `form #${formId}`);

      form.submit();

      return;
    }

    const continueButton = event.target instanceof Element
      ? event.target.closest('[data-wt-import-continue-url]')
      : null;

    if (!(continueButton instanceof HTMLButtonElement)) {
      return;
    }

    const url = requireDatasetValue(continueButton, 'wtImportContinueUrl', 'import continue URL');
    const importContainer = continueButton.closest('[id^="import"]');

    if (!(importContainer instanceof HTMLElement)) {
      return;
    }

    event.preventDefault();
    loadImportContainer(importContainer, url);
  });

  page.querySelectorAll('[data-wt-import-progress-url]').forEach((element) => {
    if (!(element instanceof HTMLElement)) {
      return;
    }

    const url = requireDatasetValue(element, 'wtImportProgressUrl', 'import progress URL');
    loadImportContainer(element, url);
  });

  processImportAutoloads(page);
  synchronizeImportPanels(page);

  const observer = new MutationObserver(() => {
    processImportAutoloads(page);
    synchronizeImportPanels(page);
  });

  observer.observe(page, {
    childList: true,
    subtree: true,
  });
}

