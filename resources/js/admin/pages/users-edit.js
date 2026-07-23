'use strict';

import { i18n } from '../../webtrees/i18n';
import { requireElement } from '../../webtrees/index';

/**
 * Initialize the user edit admin page.
 */
export function initializeUsersEditPage () {
  const page = document.getElementById('admin-users-edit-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const immediateFamilyMessage = i18n.gettext('You must specify an individual record before you can restrict the user to their immediate family.');

  page.addEventListener('change', (event) => {
    const target = event.target;

    if (!(target instanceof HTMLSelectElement)) {
      return;
    }

    if (!target.id.startsWith('RELATIONSHIP_PATH_LENGTH')) {
      return;
    }

    const treeId = target.id.substring('RELATIONSHIP_PATH_LENGTH'.length);
    const individualField = requireElement(page, `#${CSS.escape('gedcomid' + treeId)}`, HTMLElement, `gedcomid field for tree ${treeId}`);

    if (!(individualField instanceof HTMLInputElement || individualField instanceof HTMLSelectElement)) {
      throw new Error('Expected gedcomid field to be an input/select control.');
    }

    const individualValue = individualField.value;

    if (individualValue !== '' || target.value === '0') {
      return;
    }

    alert(immediateFamilyMessage);
    target.value = '0';
  });
}

