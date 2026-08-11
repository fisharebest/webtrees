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
 * Read the CSRF token from the page meta tag.
 *
 * @returns {string}
 */
function getCsrfToken() {
  const meta = document.head.querySelector('meta[name=csrf]');

  if (meta === null) {
    throw new Error('Missing CSRF token meta tag');
  }

  const token = meta.getAttribute('content');

  if (token === null || token === '') {
    throw new Error('Missing CSRF token value');
  }

  return token;
}

/**
 * Determine whether a URL is same-origin relative to the current page.
 *
 * @param {string|URL|Request} input
 * @returns {boolean}
 */
function isSameOrigin(input) {
  if (input instanceof Request) {
    input = input.url;
  }

  const url = new URL(String(input), document.baseURI);

  return url.origin === window.location.origin;
}

/**
 * Install global interceptors on both fetch() and XMLHttpRequest so that all
 * same-origin POST/PUT/PATCH/DELETE requests automatically include the CSRF
 * token and the X-Requested-With header.
 *
 * This replaces the role previously filled by jQuery's $.ajaxSetup and ensures
 * that any code — including third-party libraries like DataTables (which uses
 * XMLHttpRequest internally) — sends the CSRF token without explicit configuration.
 */
export function configureCsrfInterceptor() {
  const token = getCsrfToken();

  // Intercept fetch() calls.
  const originalFetch = window.fetch;

  window.fetch = function (input, init) {
    init = init || {};

    const method = (init.method || (input instanceof Request ? input.method : 'GET')).toUpperCase();

    if (method !== 'GET' && method !== 'HEAD' && isSameOrigin(input)) {
      const headers = new Headers(init.headers || (input instanceof Request ? input.headers : {}));

      if (!headers.has('X-CSRF-TOKEN')) {
        headers.set('X-CSRF-TOKEN', token);
      }

      if (!headers.has('x-requested-with')) {
        headers.set('x-requested-with', 'XMLHttpRequest');
      }

      init.headers = headers;
    }

    return originalFetch.call(this, input, init);
  };

  // Intercept XMLHttpRequest calls (used by DataTables and other libraries).
  const originalOpen = XMLHttpRequest.prototype.open;
  const originalSend = XMLHttpRequest.prototype.send;

  XMLHttpRequest.prototype.open = function (method, url) {
    this._csrfMethod = method.toUpperCase();
    this._csrfUrl = url;

    return originalOpen.apply(this, arguments);
  };

  XMLHttpRequest.prototype.send = function () {
    if (this._csrfMethod !== 'GET' && this._csrfMethod !== 'HEAD' && isSameOrigin(this._csrfUrl)) {
      this.setRequestHeader('X-CSRF-TOKEN', token);
    }

    return originalSend.apply(this, arguments);
  };
}

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
  const options = {
    body: body,
    method: 'POST',
    credentials: 'same-origin',
    referrerPolicy: 'same-origin',
  };

  return fetch(url, options);
}
