'use strict';

import { requireElement } from '../dom';

/**
 * Initialize the search results page.
 */
export function initializeSearchResultsPage () {
  const page = document.getElementById('webtrees-search-results-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const firstTab = requireElement(page, '.wt-search-results-tabs li button', HTMLButtonElement, 'search results first tab button');
  firstTab.click();
}

