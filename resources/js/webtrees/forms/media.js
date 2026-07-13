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
 * Initialize format/extension UI blocks.
 *
 * Each block should contain radios named "format" with data-wt-extension,
 * and an element marked with data-wt-format-extension.
 *
 * @param {ParentNode} root
 */
export function initializeFormatExtensions(root = document) {
  root.querySelectorAll('[data-wt-format-options]').forEach((container) => {
    if (!(container instanceof HTMLElement)) {
      return;
    }

    const extension = requireElement(container, '[data-wt-format-extension]', HTMLElement, 'format extension target');
    const formatRadios = container.querySelectorAll('[name="format"]');

    if (formatRadios.length === 0) {
      throw new Error('Expected at least one format radio in format options container.');
    }

    const updateExtension = () => {
      const selectedFormat = requireElement(container, '[name="format"]:checked', HTMLInputElement, 'selected format radio');
      extension.innerText = requireDatasetValue(selectedFormat, 'wtExtension', 'format extension value');
    };

    formatRadios.forEach((element) => {
      if (!(element instanceof HTMLInputElement)) {
        throw new Error('Expected format controls to be input elements.');
      }

      element.addEventListener('change', updateExtension);
    });

    // Firefox may restore radio state when navigating back.
    updateExtension();
  });
}

/**
 * Initialize media file location form controls.
 *
 * @param {ParentNode} root
 */
export function initializeMediaFileFields(root = document) {
  root.querySelectorAll('select[data-wt-media-file-location]').forEach((element) => {
    if (!(element instanceof HTMLSelectElement)) {
      throw new Error('Media file location control must be a select element.');
    }

    if (element.dataset.wtMediaFileLocationInitialized === '1') {
      return;
    }

    element.dataset.wtMediaFileLocationInitialized = '1';

    const container = element.closest('form');

    if (!(container instanceof HTMLFormElement)) {
      throw new Error('Media file location control must be inside a form.');
    }

    const updateVisibility = () => {
      const fileLocationRows = container.querySelectorAll('.file-location');

      if (fileLocationRows.length === 0) {
        throw new Error('Expected file location rows in media file form.');
      }

      fileLocationRows.forEach((field) => field.classList.add('d-none'));

      const activeRows = container.querySelectorAll('.file-location-' + element.value);

      if (activeRows.length === 0) {
        throw new Error('Expected file location row for value: ' + element.value);
      }

      activeRows.forEach((field) => field.classList.remove('d-none'));
    };

    element.addEventListener('change', updateVisibility);
    updateVisibility();

    const folder = requireElement(container, '#folder', HTMLInputElement, 'media folder input');

    if (folder.dataset.wtAutocompleteInitialized !== '1') {
      folder.dataset.wtAutocompleteInitialized = '1';
      requireDatasetValue(folder, 'wtAutocompleteUrl', 'media folder autocomplete URL');
      window.webtrees.autocomplete('#' + CSS.escape(folder.id));
    }
  });
}

