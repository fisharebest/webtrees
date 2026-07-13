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
 * Simple wrapper around fetch() with our preferred headers.
 *
 * @param {string} url
 *
 * @returns {Promise}
 */
export function httpGet(url) {
  const options = {
    method: 'GET',
    credentials: 'same-origin',
    referrerPolicy: 'same-origin',
    headers: new Headers({
      'x-requested-with': 'XMLHttpRequest',
    })
  };

  return fetch(url, options);
}

/**
 * Simple wrapper around fetch() with our preferred headers.
 *
 * @param {string} url
 * @param {string|FormData} body
 *
 * @returns {Promise}
 */
export function httpPost(url, body = '') {
  const csrfToken = document.head.querySelector('meta[name=csrf]').getAttribute('content');

  const options = {
    body: body,
    method: 'POST',
    credentials: 'same-origin',
    referrerPolicy: 'same-origin',
    headers: new Headers({
      'X-CSRF-TOKEN': csrfToken,
      'x-requested-with': 'XMLHttpRequest',
    })
  };

  return fetch(url, options, body);
}

