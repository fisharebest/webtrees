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

import { requireElement } from '../dom';

/**
 * Initialize HTML block template selectors.
 *
 * @param {ParentNode} root
 */
export function initializeHtmlTemplateConfig(root) {
  root.querySelectorAll('[data-wt-html-template-config]').forEach((container) => {
    if (!(container instanceof HTMLElement)) {
      throw new Error('HTML template config container must be an HTML element.');
    }

    const template = requireElement(container, '#template', HTMLSelectElement, 'HTML template select');
    const html = requireElement(document, '#html', HTMLTextAreaElement, 'HTML content textarea');

    template.addEventListener('change', () => {
      const selected = template.options[template.selectedIndex];

      if (selected === undefined) {
        throw new Error('Expected selected template option.');
      }

      html.value = selected.value;

      if (typeof CKEDITOR === 'undefined' || CKEDITOR.instances.html === undefined) {
        throw new Error('CKEditor instance "html" not found.');
      }

      CKEDITOR.instances.html.setData(html.value);
    });
  });
}
