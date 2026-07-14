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

import { requireDatasetValue, requireElement } from '../dom';

/**
 * Initialize forms that require a selected option before submit.
 *
 * @param {ParentNode} root
 */
export function initializeRequiredSelectForms(root) {
  root.querySelectorAll('form[data-wt-require-select]').forEach((form) => {
    if (!(form instanceof HTMLFormElement)) {
      throw new Error('Required-select container must be a form element.');
    }

    const select = requireElement(form, '[data-wt-required-select-control]', HTMLSelectElement, 'required select control');

    form.addEventListener('submit', (event) => {
      if (select.value === '') {
        event.preventDefault();
      }
    });
  });
}

/**
 * Initialize controls that submit their parent form when changed.
 *
 * @param {ParentNode} root
 */
export function initializeSubmitOnChangeControls(root) {
  root.querySelectorAll('[data-wt-submit-on-change]').forEach((element) => {
    if (!(element instanceof HTMLSelectElement)) {
      throw new Error('Submit-on-change control must be a select element.');
    }

    const form = element.closest('form');

    if (!(form instanceof HTMLFormElement)) {
      throw new Error('Submit-on-change control must be inside a form.');
    }

    element.addEventListener('change', () => {
      form.requestSubmit();
    });
  });
}

/**
 * Initialize buttons that submit a form to the selected URL from a dropdown.
 *
 * @param {ParentNode} root
 */
export function initializeSubmitSelectedUrlButtons(root) {
  root.querySelectorAll('[data-wt-submit-selected-url-button]').forEach((button) => {
    if (!(button instanceof HTMLButtonElement)) {
      throw new Error('Submit-selected-URL control must be a button element.');
    }

    button.addEventListener('click', () => {
      const selectSelector = requireDatasetValue(button, 'wtSubmitSelect', 'submit-selected-url select selector');
      const formSelector = requireDatasetValue(button, 'wtSubmitForm', 'submit-selected-url form selector');
      const select = requireElement(document, selectSelector, HTMLSelectElement, 'submit-selected-url select');
      const form = requireElement(document, formSelector, HTMLFormElement, 'submit-selected-url form');

      const selectedOption = select.options[select.selectedIndex];

      if (selectedOption === undefined || selectedOption.value === '') {
        throw new Error('Expected selected option with a target URL.');
      }

      form.action = selectedOption.value;
      form.submit();
    });
  });
}
