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

import { getWebtreesGlobal } from './global';

const webtrees = getWebtreesGlobal(window);
const catalog = webtrees.i18nCatalog || {};

webtrees.i18nCatalog = catalog;

export const i18n = {
  /**
   * @param {string} key
   * @returns {string}
   */
  gettext(key) {
    if (this.has(key)) {
      return catalog[key];
    }

    console.error('Missing translation for key: ' + key);

    return key;
  },

  /**
   * @param {string} key
   * @returns {boolean}
   */
  has(key) {
    return Object.prototype.hasOwnProperty.call(catalog, key);
  },

  /**
   * @param {string} key
   * @param {string} value
   * @returns {string}
   */
  set(key, value) {
    catalog[key] = value;

    return value;
  },
};

webtrees.i18n = i18n;
