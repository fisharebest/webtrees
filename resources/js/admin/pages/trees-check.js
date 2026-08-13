'use strict';

import { hideElements, requireDatasetValue, requireElement } from '../../webtrees/index';

/**
 * Initialize the trees-check admin page.
 */
export function initializeTreesCheckPage () {
  const page = document.getElementById('admin-trees-check-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const list = requireElement(page, 'ul.list-group', HTMLUListElement, 'trees-check list in #admin-trees-check-page');

  list.addEventListener('click', (event) => {
    const button = event.target instanceof Element
      ? event.target.closest('button')
      : null;

    if (!(button instanceof HTMLButtonElement)) {
      return;
    }

    const item = button.closest('li[data-wt-tag]');

    if (!(item instanceof HTMLLIElement)) {
      return;
    }

    const tag = requireDatasetValue(item, 'wtTag', 'trees-check tag');

    event.preventDefault();
    event.stopPropagation();

    // Much quicker to hide elements than remove them.
    hideElements(page.querySelectorAll('[data-wt-tag=' + CSS.escape(tag) + ']'));
  });
}

