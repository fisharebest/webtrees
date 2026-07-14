'use strict';

import { requireDatasetValue, requireElement } from '../dom';

/**
 * Initialize anniversaries list "show more" buttons.
 */
export function initializeAnniversariesListPage (root) {
  root.querySelectorAll('[data-wt-anniversaries-show-more]').forEach((element) => {
    if (!(element instanceof HTMLButtonElement)) {
      throw new Error('Anniversaries show-more control must be a button.');
    }

    if (element.dataset.wtAnniversariesShowMoreInitialized === '1') {
      return;
    }

    element.dataset.wtAnniversariesShowMoreInitialized = '1';

    element.addEventListener('click', (event) => {
      const button = event.currentTarget;

      if (!(button instanceof HTMLButtonElement)) {
        return;
      }

      const blockSelector = requireDatasetValue(button, 'wtAnniversariesBlock', 'anniversaries block selector');
      const block = requireElement(document, blockSelector, HTMLElement, 'anniversaries block');

      block.querySelectorAll('.d-none').forEach((hidden) => {
        hidden.classList.remove('d-none');
      });

      button.remove();
    });
  });
}

