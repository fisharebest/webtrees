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

import autoComplete from '@tarekraafat/autocomplete.js';

/**
 * Build the fetch URL for an autocomplete query.
 *
 * @param {HTMLElement} element
 * @param {string} query
 * @returns {string}
 */
function buildUrl(element, query) {
  const baseUrl = element.dataset.wtAutocompleteUrl;
  const symbol = baseUrl.includes('?') ? '&' : '?';
  let url = baseUrl + symbol + 'query=' + encodeURIComponent(query);

  if (element.dataset.wtAutocompleteExtra === 'SOUR') {
    const nestedFields = element.closest('.wt-nested-edit-fields');

    if (!(nestedFields instanceof HTMLElement)) {
      throw new Error('SOUR autocomplete failed: missing nested field container.');
    }

    let row_group = nestedFields.previousElementSibling;
    while (row_group !== null && row_group.querySelector('select') === null) {
      row_group = row_group.previousElementSibling;
    }

    if (row_group === null) {
      throw new Error('SOUR autocomplete failed: could not find the source selector field.');
    }

    const sourceSelect = row_group.querySelector('select');

    if (!(sourceSelect instanceof HTMLSelectElement)) {
      throw new Error('SOUR autocomplete failed: source selector field is invalid.');
    }

    const selected = sourceSelect.options[sourceSelect.selectedIndex];

    if (selected === undefined) {
      throw new Error('SOUR autocomplete failed: source selector has no selected value.');
    }

    const extra = selected.value.replace(/@/g, '');
    url += '&extra=' + encodeURIComponent(extra);
  }

  return url;
}

/**
 * Initialize autocomplete elements.
 *
 * @param {string} selector
 * @param {ParentNode} root
 */
export function autocomplete(selector, root) {
  root.querySelectorAll(selector).forEach((element) => {
    if (!(element instanceof HTMLInputElement)) {
      return;
    }

    new autoComplete({
      selector: () => element,
      data: {
        src: async (query) => {
          const url = buildUrl(element, query);
          const response = await fetch(url);
          return await response.json();
        },
        keys: ['value'],
      },
      threshold: 2,
      debounce: 200,
      resultsList: {
        class: 'autoComplete_list',
        maxResults: 10,
        noResults: false,
      },
      resultItem: {
        class: 'autoComplete_result',
        highlight: true,
      },
      events: {
        input: {
          selection: (event) => {
            element.value = event.detail.selection.value.value;
          },
        },
      },
    });
  });
}
