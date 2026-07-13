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
 * Fetch HTML, insert it into an element, and execute any scripts.
 *
 * @param {Element} element
 * @param {string} url
 * @param {FormData|null} data
 */
export async function load(element, url, data = null) {
  const headers = {
    'accept': 'text/html',
    'x-requested-with': 'XMLHttpRequest',
  };

  if (data !== null) {
    headers['x-csrf-token'] = document.head.querySelector('meta[name=csrf]').getAttribute('content');
  }

  const response = await fetch(url, {
    body: data,
    method: data === null ? 'GET' : 'POST',
    headers: new Headers(headers),
  });

  const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
  const scripts = Array.from(doc.querySelectorAll('script'));

  // Don't insert scripts into the document.  We will execute them directly.
  scripts.forEach(script => script.remove());

  // Replace innerHTML with the loaded HTML.
  element.replaceChildren(...doc.body.childNodes);

  // Execute scripts sequentially
  for (const node of scripts) {
    const script = document.createElement('script');

    for (const attr of node.attributes) {
      script.setAttribute(attr.name, attr.value);
    }

    if (node.src) {
      await new Promise(resolve => {
        script.onload = resolve;
        script.onerror = resolve;
        document.body.appendChild(script);
      });
    } else {
      script.textContent = node.textContent;
      document.body.appendChild(script);
    }

    // Remove the script we just executed to reduce clutter.
    script.remove();
  }
}

