/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

import { createDialogModal } from './modal';

let confirm_modal = null;
let pending_resolver = null;
let pending_value = null;

/**
 * @returns {{
 *   close: () => void,
 *   message: Element,
 *   open: () => void,
 * }}
 */
function getConfirmModal() {
  if (confirm_modal !== null) {
    return confirm_modal;
  }

  const cancel_label = document.body.dataset.wtI18nConfirmCancel ?? 'Cancel';
  const ok_label = document.body.dataset.wtI18nConfirmOk ?? 'OK';

  const modal = createDialogModal({
    action_attribute: 'data-wt-confirm-action',
    class_name: 'wt-confirm-dialog',
    content_html: [
      '<div class="wt-confirm-shell">',
      '  <p class="wt-confirm-message"></p>',
      '  <div class="wt-confirm-actions">',
      '    <button type="button" class="btn btn-secondary" data-wt-confirm-action="cancel">' + cancel_label + '</button>',
      '    <button type="button" class="btn btn-primary" data-wt-confirm-action="confirm">' + ok_label + '</button>',
      '  </div>',
      '</div>',
    ].join(''),
    on_close: () => {
      if (pending_value === null) {
        resolvePending(false);
      } else {
        resolvePending(pending_value);
        pending_value = null;
      }
    },
  });

  modal.addActionListener('cancel', () => {
    pending_value = false;
    modal.close();
  });

  modal.addActionListener('confirm', () => {
    pending_value = true;
    modal.close();
  });

  confirm_modal = {
    close: () => modal.close(),
    message: modal.findRequired('.wt-confirm-message'),
    open: () => modal.open(),
  };

  return confirm_modal;
}

/**
 * @param {boolean} value
 * @returns {void}
 */
function resolvePending(value) {
  if (pending_resolver !== null) {
    pending_resolver(value);
    pending_resolver = null;
  }

  pending_value = null;
}

/**
 * @param {string} message
 * @returns {Promise<boolean>}
 */
export function confirmDialog(message) {
  if (pending_resolver !== null) {
    return Promise.resolve(false);
  }

  const modal = getConfirmModal();
  modal.message.textContent = message;

  return new Promise((resolve) => {
    pending_resolver = resolve;
    modal.open();
  });
}

