'use strict';

import { requireElement } from '../../webtrees/dom';

/**
 * Initialize the components admin page.
 */
export function initializeComponentsPage () {
  const page = document.getElementById('admin-components-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const table = requireElement(page, '.wt-table-components', HTMLTableElement, 'components table in #admin-components-page');

  table.addEventListener('click', (event) => {
    const target = event.target instanceof Element
      ? event.target.closest('td.move')
      : null;

    if (!(target instanceof HTMLTableCellElement)) {
      return;
    }

    event.preventDefault();

    const row = target.closest('tr');

    if (!(row instanceof HTMLTableRowElement)) {
      return;
    }

    if (target.classList.contains('up')) {
      row.previousElementSibling?.before(row);
    } else {
      row.nextElementSibling?.after(row);
    }
  });
}

