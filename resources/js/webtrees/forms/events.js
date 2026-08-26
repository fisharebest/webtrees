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

import { requireDatasetValue } from '../dom';

/**
 * Initialize event-occured checkboxes that mirror to hidden GEDCOM fields.
 *
 * @param {ParentNode} root
 */
export function initializeEventCheckboxControls(root) {
  root.querySelectorAll('input[data-wt-event-checkbox-target]').forEach((checkbox) => {
    if (!(checkbox instanceof HTMLInputElement) || checkbox.type !== 'checkbox') {
      throw new Error('Event checkbox control must be a checkbox input element.');
    }

    const targetId = requireDatasetValue(checkbox, 'wtEventCheckboxTarget', 'event checkbox target ID');
    const target = document.getElementById(targetId);

    if (!(target instanceof HTMLInputElement)) {
      throw new Error('Event checkbox target not found for ID: ' + targetId);
    }

    checkbox.addEventListener('change', () => {
      target.value = checkbox.checked ? 'Y' : '';
    });
  });
}

/**
 * Initialize EVENTS_RECORDED selectors that mirror selections to a hidden input.
 *
 * @param {ParentNode} root
 */
export function initializeEventsRecordedSelectors(root) {
  root.querySelectorAll('select.wt-events-recorded-select').forEach((select) => {
    if (!(select instanceof HTMLSelectElement)) {
      throw new Error('Events-recorded control must be a select element.');
    }

    const target = select.nextElementSibling;

    if (!(target instanceof HTMLInputElement)) {
      throw new Error('Events-recorded hidden target must be the next input element.');
    }

    const syncValue = () => {
      target.value = Array.from(select.selectedOptions).map((option) => option.value).join(',');
    };

    select.addEventListener('change', syncValue);
    syncValue();
  });
}
