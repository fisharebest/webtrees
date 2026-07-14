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
 * Initialize drag-and-drop sortable lists.
 *
 * @param {ParentNode} root
 */
export function initializeSortableLists(root) {
  root.querySelectorAll('[data-wt-sortable-list]').forEach((list) => {
    if (!(list instanceof HTMLElement)) {
      throw new Error('Sortable list must be an HTML element.');
    }

    if (list.dataset.wtSortableListInitialized === '1') {
      return;
    }

    list.dataset.wtSortableListInitialized = '1';

    new Sortable(list, {
      handle: list.dataset.wtSortableHandle || '.card-header',
    });
  });
}

/**
 * Initialize "sort by date" reorder buttons.
 *
 * @param {ParentNode} root
 */
export function initializeSortByDateButtons(root) {
  root.querySelectorAll('[data-wt-sort-by-date-button]').forEach((button) => {
    if (!(button instanceof HTMLButtonElement)) {
      throw new Error('Sort-by-date control must be a button element.');
    }

    if (button.dataset.wtSortByDateButtonInitialized === '1') {
      return;
    }

    button.dataset.wtSortByDateButtonInitialized = '1';

    button.addEventListener('click', () => {
      const listSelector = requireDatasetValue(button, 'wtSortList', 'sortable list selector');
      const sortKey = button.dataset.wtSortKey || 'wtSortByDate';
      const list = requireElement(document, listSelector, HTMLElement, 'sortable list');

      const items = Array.from(list.querySelectorAll('.wt-sortable-item'));

      items.sort((x, y) => {
        if (!(x instanceof HTMLElement) || !(y instanceof HTMLElement)) {
          throw new Error('Sortable items must be HTML elements.');
        }

        const xValue = Number(requireDatasetValue(x, sortKey, 'sort key on sortable item'));
        const yValue = Number(requireDatasetValue(y, sortKey, 'sort key on sortable item'));

        return Math.sign(xValue - yValue);
      }).forEach((item) => list.appendChild(item));
    });
  });
}

/**
 * Initialize click handlers for reorder action buttons (first/up/down/last).
 *
 * @param {ParentNode} root
 */
export function initializeReorderButtons(root) {
  const body = root instanceof Document ? root.body : root.querySelector('body') ?? document.body;

  if (!(body instanceof HTMLBodyElement) || body.dataset.wtReorderButtonsInitialized === '1') {
    return;
  }

  body.dataset.wtReorderButtonsInitialized = '1';

  body.addEventListener('click', (event) => {
    const target = event.target instanceof Element ? event.target.closest('.wt-btn-reorder') : null;

    if (!(target instanceof HTMLButtonElement)) {
      return;
    }

    const item = target.closest('.wt-sortable-item');
    const list = target.closest('.wt-sortable-list');

    if (!(item instanceof HTMLElement)) {
      throw new Error('Reorder button must be inside a sortable item.');
    }

    if (!(list instanceof HTMLElement)) {
      throw new Error('Reorder button must be inside a sortable list.');
    }

    if (target.matches('.wt-btn-reorder-first')) {
      list.insertBefore(item, list.childNodes[0] ?? null);
    }

    if (target.matches('.wt-btn-reorder-previous') && item.previousElementSibling) {
      list.insertBefore(item, item.previousElementSibling);
    }

    if (target.matches('.wt-btn-reorder-next') && item.nextElementSibling) {
      list.insertBefore(item.nextElementSibling, item);
    }

    if (target.matches('.wt-btn-reorder-last')) {
      list.insertBefore(item, null);
    }
  });
}

