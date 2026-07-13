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
 * Initialize census date selectors used by the GEDFact assistant.
 *
 * @param {ParentNode} root
 */
export function initializeCensusSelectors(root = document) {
  root.querySelectorAll('select[data-wt-census-selector]').forEach((select) => {
    if (!(select instanceof HTMLSelectElement)) {
      throw new Error('Census selector control must be a select element.');
    }

    if (select.dataset.wtCensusSelectorInitialized === '1') {
      return;
    }

    select.dataset.wtCensusSelectorInitialized = '1';

    const form = select.closest('form');

    if (!(form instanceof HTMLFormElement)) {
      throw new Error('Census selector must be inside a form.');
    }

    select.addEventListener('change', () => {
      const option = select.options[select.selectedIndex];

      if (option === undefined) {
        throw new Error('Expected selected census option.');
      }

      const targetSelector = requireDatasetValue(select, 'wtCensusDateTarget', 'census date target selector');
      const target = requireElement(form, targetSelector, HTMLInputElement, 'census date target input');
      target.value = requireDatasetValue(option, 'wtDate', 'census date value');
    });
  });
}

