'use strict';

import { initializeFormatExtensions } from '../../webtrees/forms';

/**
 * Initialize the tree export admin page.
 */
export function initializeTreesExportPage () {
  const page = document.getElementById('admin-trees-export-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  initializeFormatExtensions(page);
}

