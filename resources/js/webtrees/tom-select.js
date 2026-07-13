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

import { httpGet } from './http';
import { i18n } from './i18n';

/**
 * Initialize a tom-select input
 * @param {Element} element
 * @returns {TomSelect}
 */
export function initializeTomSelect(element) {
  if (element.tomselect) {
    return element.tomselect;
  }

  if (element.dataset.wtUrl) {
    let options = {
      plugins: ['dropdown_input', 'virtual_scroll'],
      maxOptions: false,
      searchField: [], // We filter on the server, so don't filter on the client.
      render: {
        item: (data, escape) => '<div>' + data.text + '</div>',
        option: (data, escape) => '<div>' + data.text + '</div>',
        no_results: (data, escape) => '<div class="no-results">' + i18n.get('No results found') + '</div>',
      },
      firstUrl: query => element.dataset.wtUrl + '&query=' + encodeURIComponent(query),
      load: function (query, callback) {
        httpGet(this.getUrl(query))
          .then(response => response.json())
          .then(json => {
            if (json.nextUrl !== null) {
              this.setNextUrl(query, json.nextUrl + '&query=' + encodeURIComponent(query));
            }
            callback(json.data);
          })
          .catch(callback);
      },
    };

    if (!element.required) {
      options.plugins.push('clear_button');
    }

    return new TomSelect(element, options);
  }

  if (element.multiple) {
    return new TomSelect(element, { plugins: ['caret_position', 'remove_button'] });
  }

  if (!element.required) {
    return new TomSelect(element, { plugins: ['clear_button'] });
  }

  return new TomSelect(element, { });
}

/**
 * Reset a tom-select input to have a single selected option
 * @param {TomSelect} tomSelect
 * @param {string} value
 * @param {string} text
 */
export function resetTomSelect(tomSelect, value, text) {
  tomSelect.clear(true);
  tomSelect.clearOptions();
  tomSelect.addOption({ value: value, text: text });
  tomSelect.refreshOptions();
  tomSelect.addItem(value, true);
  tomSelect.refreshItems();
}

