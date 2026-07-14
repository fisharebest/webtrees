'use strict';

import { requireDatasetValue, requireElement } from '../dom';

/**
 * Initialize the shared AJAX modal.
 */
export function initializeAjaxModalPage (root) {
  const modal = root.querySelector('#wt-ajax-modal');

  if (!(modal instanceof HTMLElement)) {
    return;
  }

  if (modal.dataset.wtAjaxModalInitialized === '1') {
    return;
  }

  modal.dataset.wtAjaxModalInitialized = '1';

  if (document.body.dataset.wtModalSubmitInitialized !== '1') {
    document.body.dataset.wtModalSubmitInitialized = '1';

    document.addEventListener('submit', (event) => {
      const form = event.target;

      if (!(form instanceof HTMLFormElement) || !form.matches('[data-wt-modal-submit]')) {
        return;
      }

      window.webtrees.createRecordModalSubmit(event);
    });
  }

  modal.addEventListener('show.bs.modal', (event) => {
    if (!(event.relatedTarget instanceof HTMLElement)) {
      throw new Error('AJAX modal requires a related target element.');
    }

    const modalContent = requireElement(modal, '.modal-content', HTMLElement, 'AJAX modal content');

    // If we need to paste the result into a tom-select control.
    modalContent.dataset.wtSelectId = event.relatedTarget.dataset.wtSelectId ?? '';

    // Clear existing content (to prevent FOUC) and load new content.
    // The load() function dispatches a 'wt-content-loaded' event, which
    // initializeWhenReady listeners use to initialize new interactive elements.
    modalContent.replaceChildren();

    const url = requireDatasetValue(event.relatedTarget, 'wtHref', 'AJAX modal URL');

    window.webtrees.load(modalContent, url).catch((error) => {
      console.error('Failed to load AJAX modal content', { url, error });
    });
  });
}

