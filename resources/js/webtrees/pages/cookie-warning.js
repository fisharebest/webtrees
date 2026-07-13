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
 * Initialize cookie warning banners.
 *
 * @param {ParentNode} root
 */
export function initializeCookieWarnings(root = document) {
  root.querySelectorAll('[data-wt-cookie-warning]').forEach((warning) => {
    if (!(warning instanceof HTMLElement)) {
      throw new Error('Cookie warning container must be an HTML element.');
    }

    if (warning.dataset.wtCookieWarningInitialized === '1') {
      return;
    }

    warning.dataset.wtCookieWarningInitialized = '1';

    const key = requireDatasetValue(warning, 'wtCookieWarningKey', 'cookie warning storage key');
    const dismissButton = requireElement(warning, '[data-wt-cookie-warning-dismiss]', HTMLButtonElement, 'cookie warning dismiss button');

    if (localStorage.getItem(key) !== 'ok') {
      warning.classList.add('show');
    }

    dismissButton.addEventListener('click', () => {
      localStorage.setItem(key, 'ok');
    });
  });
}

