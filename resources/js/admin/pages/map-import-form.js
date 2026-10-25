'use strict';

import { requireElement } from '../../webtrees/dom';

/**
 * Initialize the map import form admin page.
 */
export function initializeMapImportFormPage () {
  const page = document.getElementById('admin-map-import-form-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const importClient = requireElement(page, '#import-client', HTMLInputElement, 'client source radio in #admin-map-import-form-page');
  const importClientFile = requireElement(page, '#import-client-file', HTMLInputElement, 'client file input in #admin-map-import-form-page');
  const importServer = requireElement(page, '#import-server', HTMLInputElement, 'server source radio in #admin-map-import-form-page');
  const importServerFile = requireElement(page, '#import-server-file', HTMLSelectElement, 'server file select in #admin-map-import-form-page');

  importClientFile.addEventListener('focus', () => {
    importClient.checked = true;
  });

  importServerFile.addEventListener('focus', () => {
    importServer.checked = true;
  });
}

