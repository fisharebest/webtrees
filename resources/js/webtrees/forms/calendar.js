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
 * Initialize localized labels for legacy calendar popup controls.
 *
 * @param {ParentNode} root
 */
export function initializeCalendarLocalization(root) {
  root.querySelectorAll('[data-wt-calendar-localization]').forEach((element) => {
    if (!(element instanceof HTMLElement)) {
      return;
    }

    if (element.dataset.wtCalendarLocalizationInitialized === '1') {
      return;
    }

    element.dataset.wtCalendarLocalizationInitialized = '1';

    const months = JSON.parse(requireDatasetValue(element, 'wtCalendarMonths', 'calendar month labels'));
    const days = JSON.parse(requireDatasetValue(element, 'wtCalendarDays', 'calendar day labels'));
    const firstDay = parseInt(requireDatasetValue(element, 'wtCalendarFirstDay', 'calendar first day'), 10);

    if (!Array.isArray(months) || months.length !== 12 || !months.every((value) => typeof value === 'string')) {
      throw new Error('Expected 12 calendar month labels.');
    }

    if (!Array.isArray(days) || days.length !== 7 || !days.every((value) => typeof value === 'string')) {
      throw new Error('Expected 7 calendar day labels.');
    }

    if (!Number.isInteger(firstDay) || firstDay < 0 || firstDay > 6) {
      throw new Error('Calendar first day must be an integer between 0 and 6.');
    }

    window.webtrees.calLocalize(...months, ...days, firstDay);
  });
}

/**
 * Initialize buttons/links that open calendar widgets.
 *
 * @param {ParentNode} root
 */
export function initializeCalendarWidgetButtons(root) {
  root.querySelectorAll('[data-wt-calendar-widget-button]').forEach((element) => {
    if (!(element instanceof HTMLElement)) {
      throw new Error('Calendar widget control must be an HTML element.');
    }

    if (element.dataset.wtCalendarWidgetInitialized === '1') {
      return;
    }

    element.dataset.wtCalendarWidgetInitialized = '1';

    element.addEventListener('click', (event) => {
      event.preventDefault();

      const dateDivId = requireDatasetValue(element, 'wtCalendarDivId', 'calendar widget div ID');
      const dateFieldId = requireDatasetValue(element, 'wtCalendarInputId', 'calendar widget input ID');

      window.webtrees.calendarWidget(dateDivId, dateFieldId);
    });
  });
}

/**
 * Initialize date inputs that should be normalized on change.
 *
 * @param {ParentNode} root
 */
export function initializeDateReformatInputs(root) {
  root.querySelectorAll('input[data-wt-reformat-date-order]').forEach((element) => {
    if (!(element instanceof HTMLInputElement)) {
      throw new Error('Date reformat control must be an input element.');
    }

    if (element.dataset.wtReformatDateInitialized === '1') {
      return;
    }

    element.dataset.wtReformatDateInitialized = '1';

    element.addEventListener('change', () => {
      window.webtrees.reformatDate(element, requireDatasetValue(element, 'wtReformatDateOrder', 'date reformat order'));
    });
  });
}

