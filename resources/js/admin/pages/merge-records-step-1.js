'use strict';

import { requireElement } from '../../webtrees/dom';

/**
 * Initialize merge records step 1 admin page.
 */
export function initializeMergeRecordsStep1Page () {
  const page = document.getElementById('admin-merge-records-step-1-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const select = requireElement(page, '#record-type', HTMLSelectElement, 'record type select in #admin-merge-records-step-1-page');
  const form = requireElement(page, '#merge-step-1', HTMLFormElement, 'merge step 1 form in #admin-merge-records-step-1-page');

  window.webtrees.initializeIFSRO(select, form);
}

