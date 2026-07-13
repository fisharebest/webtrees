'use strict';

import { initializeFormatExtensions } from '../forms';

/**
 * Initialize the clippings download page.
 */
export function initializeClippingsDownloadPage () {
  const page = document.getElementById('webtrees-clippings-download-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  initializeFormatExtensions(page);
}

