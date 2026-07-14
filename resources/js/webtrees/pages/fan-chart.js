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
 * Initialize fan chart area menu toggles.
 *
 * @param {ParentNode} root
 */
export function initializeFanChartMaps(root) {
  root.querySelectorAll('map[data-wt-fanchart-map]').forEach((map) => {
    if (!(map instanceof HTMLElement)) {
      throw new Error('Fan chart map container must be an HTML element.');
    }

    if (map.dataset.wtFanchartMapInitialized === '1') {
      return;
    }

    map.dataset.wtFanchartMapInitialized = '1';

    map.querySelectorAll('area').forEach((area) => {
      area.addEventListener('click', (event) => {
        event.stopPropagation();
        event.preventDefault();

        const targetSelector = area.hash;

        if (targetSelector === '') {
          throw new Error('Fan chart area is missing target hash selector.');
        }

        const target = requireElement(document, targetSelector, HTMLElement, 'fan chart target menu');
        const display = target.style.display;

        if (!(target.parentElement instanceof HTMLElement)) {
          throw new Error('Fan chart target menu must have a parent container.');
        }

        target.parentElement.querySelectorAll('.fan_chart_menu').forEach((element) => {
          if (element instanceof HTMLElement) {
            element.style.display = 'none';
          }
        });

        if (display !== 'block') {
          target.style.display = 'block';
          target.style.left = Math.max(0, event.pageX - (target.offsetWidth / 2)) + 'px';
          target.style.top = Math.max(0, event.pageY - 1 - target.offsetHeight) + 'px';
        }
      });
    });
  });
}

