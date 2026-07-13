'use strict';

import { requireElement } from '../dom';

/**
 * Initialize the edit blocks page.
 */
export function initializeEditBlocksPage () {
  const form = document.getElementById('edit-blocks');

  if (!(form instanceof HTMLFormElement)) {
    return;
  }

  if (form.dataset.wtEditBlocksInitialized === '1') {
    return;
  }

  form.dataset.wtEditBlocksInitialized = '1';

  const currentBlocks = requireElement(form, '#current-blocks', HTMLElement, 'current blocks container');
  const mainBlocks = requireElement(form, '#main-blocks', HTMLElement, 'main blocks container');
  const sideBlocks = requireElement(form, '#side-blocks', HTMLElement, 'side blocks container');
  const availableBlocks = requireElement(form, '#available-blocks', HTMLElement, 'available blocks container');

  new Sortable(mainBlocks, {
    group: 'blocks',
    handle: '.wt-icon-drag-handle',
    animation: 150,
    pull: 'clone',
  });

  new Sortable(sideBlocks, {
    group: 'blocks',
    handle: '.wt-icon-drag-handle',
    animation: 150,
    pull: 'clone',
  });

  new Sortable(availableBlocks, {
    group: {
      name: 'blocks',
      pull: 'clone',
      put: false,
    },
    handle: '.wt-icon-drag-handle',
    animation: 150,
    sort: false,
  });

  currentBlocks.addEventListener('click', (event) => {
    if (!(event.target instanceof Element) || event.target.closest('.wt-icon-delete') === null) {
      return;
    }

    const block = event.target.closest('.wt-block');

    if (block instanceof Element && block.parentNode !== null) {
      block.parentNode.removeChild(block);
    }
  });

  form.addEventListener('submit', () => {
    mainBlocks.querySelectorAll('input').forEach((element) => {
      element.setAttribute('name', 'main[]');
    });

    sideBlocks.querySelectorAll('input').forEach((element) => {
      element.setAttribute('name', 'side[]');
    });
  });
}

