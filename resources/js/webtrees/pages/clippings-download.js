'use strict';

import { initializeFormatExtensions } from '../forms';

/**
 * Initialize the clippings download page.
 */
export function initializeClippingsDownloadPage (root) {
  const page = root.querySelector('#webtrees-clippings-download-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  initializeFormatExtensions(page);
}

