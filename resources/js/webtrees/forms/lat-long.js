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
 * Valid LATI/LONG according to GEDCOM standard.
 *
 * @param {Element} field
 * @param {string} positivePrefix
 * @param {string} negativePrefix
 */
function reformatLatLong(field, positivePrefix, negativePrefix) {
  let text = field.value.toUpperCase();
  text = text.replace(/(^\s*)|(\s*$)/g, '');
  text = text.replace(/ /g, ':');
  text = text.replace(/\+/g, '');
  text = text.replace(/-/g, negativePrefix);
  text = text.replace(/,/g, '.');

  // 0°34'11 => 0:34:11
  text = text.replace(/\u00b0/g, ':');
  text = text.replace(/\u0027/g, ':');

  // 0:34:11.2W => W0.5698
  text = text.replace(/^([0-9]+):([0-9]+):([0-9.]+)(.*)/g, function ($0, $1, $2, $3, $4) {
    let number = parseFloat($1);
    number += ($2 / 60);
    number += ($3 / 3600);
    number = Math.round(number * 1E4) / 1E4;

    return $4 + number;
  });

  // 0:34W => W0.5667
  text = text.replace(/^([0-9]+):([0-9]+)(.*)/g, function ($0, $1, $2, $3) {
    let number = parseFloat($1);
    number += ($2 / 60);
    number = Math.round(number * 1E4) / 1E4;

    return $3 + number;
  });

  // 0.5698W => W0.5698
  text = text.replace(/(.*)([NSEW])$/g, '$2$1');

  // 17.1234 => N17.1234
  if (text && text.charAt(0) !== negativePrefix && text.charAt(0) !== positivePrefix) {
    text = positivePrefix + text;
  }

  field.value = text;
}

/**
 * @param {Element} field
 */
export function reformatLatitude(field) {
  return reformatLatLong(field, 'N', 'S');
}

/**
 * @param {Element} field
 */
export function reformatLongitude(field) {
  return reformatLatLong(field, 'E', 'W');
}

/**
 * Initialize latitude/longitude inputs that should be normalized on change.
 *
 * @param {ParentNode} root
 */
export function initializeLatLongReformatInputs(root = document) {
  root.querySelectorAll('input[data-wt-reformat-latitude]').forEach((element) => {
    if (!(element instanceof HTMLInputElement)) {
      throw new Error('Latitude reformat control must be an input element.');
    }

    if (element.dataset.wtReformatLatitudeInitialized === '1') {
      return;
    }

    element.dataset.wtReformatLatitudeInitialized = '1';

    element.addEventListener('change', () => {
      reformatLatitude(element);
    });
  });

  root.querySelectorAll('input[data-wt-reformat-longitude]').forEach((element) => {
    if (!(element instanceof HTMLInputElement)) {
      throw new Error('Longitude reformat control must be an input element.');
    }

    if (element.dataset.wtReformatLongitudeInitialized === '1') {
      return;
    }

    element.dataset.wtReformatLongitudeInitialized = '1';

    element.addEventListener('change', () => {
      reformatLongitude(element);
    });
  });
}

