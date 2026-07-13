'use strict';

import { requireElement } from '../../webtrees/dom';
import { requireDatasetValue } from '../../webtrees/index';

/**
 * Initialize the media admin page.
 */
export function initializeMediaPage () {
  const page = document.getElementById('admin-media-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const form = requireElement(page, '#admin-media-form', HTMLFormElement, 'admin media form in #admin-media-page');
  const modalForm = requireElement(page, '#modal-create-media-from-file-form', HTMLFormElement, 'create-media modal form in #admin-media-page');
  const fileInput = requireElement(page, '#file', HTMLInputElement, 'create-media file input in #admin-media-page');

  form.addEventListener('change', (event) => {
    const target = event.target;

    if (!(target instanceof Element)) {
      return;
    }

    const shouldSubmit = target.matches('input[name="files"], input[name="subfolders"], select[name="media_folder"]');

    if (!shouldSubmit) {
      return;
    }

    form.submit();
  });

  page.addEventListener('click', (event) => {
    const target = event.target instanceof Element
      ? event.target.closest('[data-wt-create-media-url][data-wt-create-media-file]')
      : null;

    if (!(target instanceof HTMLAnchorElement)) {
      return;
    }

    event.preventDefault();

    modalForm.action = requireDatasetValue(target, 'wtCreateMediaUrl', 'create media action URL');
    fileInput.value = requireDatasetValue(target, 'wtCreateMediaFile', 'create media file path');

    const modalElement = requireElement(document, '#modal-create-media-from-file', HTMLElement, 'create-media modal element');
    bootstrap.Modal.getOrCreateInstance(modalElement).show();
  });
}

