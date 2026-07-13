'use strict';

import { confirmDialog } from '../../webtrees/confirm';
import { requireDatasetValue, requireElement } from '../../webtrees/dom';
import { httpPost } from '../../webtrees/http';

/**
 * Initialize the fix level-0 media admin page.
 */
export function initializeFixLevel0MediaPage () {
  const page = document.getElementById('admin-fix-level-0-media-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const table = requireElement(page, '.wt-fix-table', HTMLTableElement, 'fix-level-0-media table in #admin-fix-level-0-media-page');
  const actionUrl = requireDatasetValue(table, 'wtFixActionUrl', 'wtFixActionUrl on .wt-fix-table');

  table.addEventListener('click', async (event) => {
    const target = event.target instanceof Element
      ? event.target.closest('.wt-fix-button')
      : null;

    if (!(target instanceof HTMLElement)) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    const message = requireDatasetValue(target, 'confirm', 'confirm message on .wt-fix-button');
    const confirmed = await confirmDialog(message);

    if (!confirmed) {
      return;
    }

    const body = new FormData();
    body.set('fact_id', requireDatasetValue(target, 'factId', 'factId on .wt-fix-button'));
    body.set('indi_xref', requireDatasetValue(target, 'individualXref', 'individualXref on .wt-fix-button'));
    body.set('obje_xref', requireDatasetValue(target, 'mediaXref', 'mediaXref on .wt-fix-button'));
    body.set('tree_id', requireDatasetValue(target, 'treeId', 'treeId on .wt-fix-button'));

    await httpPost(actionUrl, body);
    $(table).DataTable().ajax.reload(null, false);
  });
}

