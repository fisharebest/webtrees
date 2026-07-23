'use strict';

import { confirmDialog } from '../../webtrees/confirm';
import { requireElement } from '../../webtrees/dom';
import { i18n } from '../../webtrees/i18n';

/**
 * Initialize the trees import admin page.
 */
export function initializeTreesImportPage () {
  const page = document.getElementById('admin-trees-import-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const form = requireElement(page, '#wt-gedcom-import-form', HTMLFormElement, 'GEDCOM import form in #admin-trees-import-page');
  const oldFileInput = requireElement(form, '#gedcom_file', HTMLInputElement, 'existing GEDCOM file input in #wt-gedcom-import-form');
  const importClient = requireElement(form, '#import-client', HTMLInputElement, 'client source radio in #admin-trees-import-page');
  const importClientFile = requireElement(form, '#import-client-file', HTMLInputElement, 'client file input in #admin-trees-import-page');
  const importServer = requireElement(form, '#import-server', HTMLInputElement, 'server source radio in #admin-trees-import-page');
  const importServerFile = requireElement(form, '#import-server-file', HTMLSelectElement, 'server file select in #admin-trees-import-page');
  const message = i18n.gettext('You have selected a GEDCOM file with a different name. Is this correct?');

  form.addEventListener('submit', async (event) => {
    if (form.dataset.wtConfirmBypass === '1') {
      delete form.dataset.wtConfirmBypass;
      return;
    }

    event.preventDefault();

    const method = requireElement(form, 'input[name="source"]:checked', HTMLInputElement, 'selected import source in #wt-gedcom-import-form');
    const selectedMethod = method.value;
    const selectedField = selectedMethod === 'server' ? importServerFile : importClientFile;

    // Some browsers include c:\\fakepath\\ in uploaded file names.
    const oldFile = oldFileInput.value;
    const newFile = selectedField.value.replace(/.*[/\\]/, '');

    if (newFile !== oldFile && oldFile !== '') {
      const confirmed = await confirmDialog(message);

      if (!confirmed) {
        return;
      }
    }

    form.dataset.wtConfirmBypass = '1';
    form.submit();
  });

  importClientFile.addEventListener('focus', () => {
    importClient.checked = true;
  });

  importServerFile.addEventListener('focus', () => {
    importServer.checked = true;
  });
}

