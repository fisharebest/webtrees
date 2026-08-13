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

import { requireDatasetValue } from '../dom';

/**
 * Initialize hourglass chart expand/collapse arrow behavior.
 *
 * @param {ParentNode} root
 */
export function initializeHourglassCharts(root) {
  root.querySelectorAll('[data-wt-hourglass-chart]').forEach((chart) => {
    if (!(chart instanceof HTMLElement)) {
      throw new Error('Hourglass chart container must be an HTML element.');
    }

    chart.addEventListener('click', (event) => {
      const arrow = event.target instanceof Element ? event.target.closest('.hourglass-arrow[data-wt-chart-xref]') : null;

      if (!(arrow instanceof HTMLElement)) {
        return;
      }

      event.preventDefault();

      const url = requireDatasetValue(arrow, 'wtChartXref', 'hourglass chart URL');

      if (!(arrow.parentElement instanceof HTMLElement)) {
        throw new Error('Hourglass arrow must be inside a container element.');
      }

      window.webtrees.load(arrow.parentElement, url).catch((error) => {
        console.error('Failed to load hourglass chart branch', { url, error });
      });
    });
  });
}
