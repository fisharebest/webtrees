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

/**
 * @param {{
 *   class_name: string,
 *   content_html: string,
 *   action_attribute: string,
 *   close_on_backdrop?: boolean,
 *   on_close?: () => void,
 * }} options
 * @returns {{
 *   dialog: HTMLDialogElement,
 *   addActionListener: (action_name: string, handler: () => void) => void,
 *   close: () => void,
 *   findRequired: (selector: string) => Element,
 *   open: () => void,
 * }}
 */
export function createDialogModal(options) {
  const {
    action_attribute,
    class_name,
    close_on_backdrop = true,
    content_html,
    on_close = null,
  } = options;

  const dialog = document.createElement('dialog');
  dialog.className = class_name;
  dialog.innerHTML = content_html;

  document.body.append(dialog);

  function runCloseHandler() {
    if (typeof on_close === 'function') {
      on_close();
    }
  }

  function close() {
    if (dialog.open) {
      dialog.close();
    }

    runCloseHandler();
  }

  dialog.addEventListener('cancel', () => runCloseHandler());

  if (close_on_backdrop) {
    dialog.addEventListener('click', (event) => {
      if (event.target === dialog) {
        close();
      }
    });
  }

  return {
    dialog,

    addActionListener(action_name, handler) {
      const element = dialog.querySelector('[' + action_attribute + '="' + action_name + '"]');

      if (element === null) {
        throw new Error('Dialog action "' + action_name + '" does not exist.');
      }

      element.addEventListener('click', handler);
    },

    close,

    findRequired(selector) {
      const element = dialog.querySelector(selector);

      if (element === null) {
        throw new Error('Dialog element "' + selector + '" does not exist.');
      }

      return element;
    },

    open() {
      if (!dialog.open) {
        dialog.showModal();
      }
    },
  };
}

