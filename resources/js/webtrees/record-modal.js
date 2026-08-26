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

import { httpPost } from './http';
import { resetTomSelect } from './tom-select';

/**
 * Toggle the visibility/status of INDI/FAM/SOUR/REPO/OBJE selectors
 *
 * @param {Element} select
 * @param {Element} container
 */
export function initializeIFSRO(select, container) {
  select.addEventListener('change', function () {
    // Show only the selected selector.
    container.querySelectorAll('.select-record').forEach(element => element.classList.add('d-none'));
    container.querySelectorAll('.select-' + select.value).forEach(element => element.classList.remove('d-none'));

    // Enable only the selected selector (so that disabled ones do not get submitted).
    container.querySelectorAll('.select-record select').forEach(element => {
      element.disabled = true;
      if (element.matches('.tom-select')) {
        element.tomselect.disable();
      }
    });
    container.querySelectorAll('.select-' + select.value + ' select').forEach(element => {
      element.disabled = false;
      if (element.matches('.tom-select')) {
        element.tomselect.enable();
      }
    });
  });
}

/**
 * Save a form using ajax, for use in modals
 *
 * @param {Event} event
 */
export function createRecordModalSubmit(event) {
  event.preventDefault();
  const form = event.target;
  const modal = document.getElementById('wt-ajax-modal')
  const modal_content = modal.querySelector('.modal-content');
  const select = document.getElementById(modal_content.dataset.wtSelectId);

  httpPost(form.action, new FormData(form))
    .then(response => response.json())
    .then(json => {
      if (select && json.value !== '') {
        // This modal was activated by the "create new" button in a select edit control.
        resetTomSelect(select.tomselect, json.value, json.text);

        bootstrap.Modal.getInstance(modal).hide();
      } else {
        // Show the success/fail message in the existing modal.
        modal_content.innerHTML = json.html;
      }
    })
    .catch(error => {
      modal_content.innerHTML = error;
    });
}

