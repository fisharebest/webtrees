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
 * Insert text at the current cursor position in a text field.
 *
 * @param {Element|null} element
 * @param {string} text
 */
export function pasteAtCursor(element, text) {
  if (element === null) {
    return;
  }

  if (!(element instanceof HTMLInputElement || element instanceof HTMLTextAreaElement)) {
    console.warn('pasteAtCursor() expects an input or textarea element.', element);
    return;
  }

  const selectionStart = element.selectionStart ?? element.value.length;
  const selectionEnd = element.selectionEnd ?? selectionStart;
  const caretPos = selectionStart + text.length;
  const textBefore = element.value.substring(0, selectionStart);
  const textAfter = element.value.substring(selectionEnd);

  element.value = textBefore + text + textAfter;
  element.setSelectionRange(caretPos, caretPos);
  element.focus();
}

/**
 * Make bootstrap "collapse" elements persistent.
 *
 * @param {HTMLElement} element
 */
export function persistentToggle(element) {
  const key = 'state-of-' + element.dataset.wtPersist;
  const previousState = localStorage.getItem(key);

  // Accordion buttons have aria-expanded. Checkboxes are checked/unchecked.
  const currentState = element.getAttribute('aria-expanded') ?? element.checked.toString();

  // Previously selected? Select again now.
  if (previousState !== null && previousState !== currentState) {
    element.click();
  }

  // Remember state for the next page load.
  element.addEventListener('click', function () {
    if (element.type === 'checkbox') {
      localStorage.setItem(key, element.checked.toString());
    }

    if (element.type === 'button') {
      localStorage.setItem(key, element.getAttribute('aria-expanded'));
    }
  });
}

/**
 * Set the active color theme.
 */
export function setColorTheme() {
  if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
    document.documentElement.dataset.bsTheme = 'dark';
  } else {
    document.documentElement.dataset.bsTheme = 'light';
  }
}

/**
 * Watch for system color theme changes.
 */
export function watchForColorThemeChanges() {
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => setColorTheme());
}

/**
 * Run a callback once the document is ready.
 *
 * @param {() => void} callback
 */
export function onDocumentReady(callback) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', callback, { once: true });
  } else {
    callback();
  }
}

/**
 * Run initialization when the document is ready, then re-run after dynamic content is loaded.
 *
 * The callback receives the root element to scan — on initial page load this is document.body,
 * and after AJAX loads it is the specific element that received new content.
 * This means callbacks only need to scan within the given root, eliminating the need for
 * double-initialization guards.
 *
 * @param {(root: ParentNode) => void} callback
 */
export function initializeWhenReady(callback) {
  onDocumentReady(() => {
    callback(document.body);

    document.addEventListener('wt-content-loaded', (event) => {
      const root = event.detail?.root ?? document.body;
      callback(root);
    });
  });
}

/**
 * Notify all initializeWhenReady listeners that new content has been loaded.
 *
 * Call this after inserting dynamic HTML content into the page.
 *
 * @param {Element} root - the element that received new content
 */
export function notifyContentLoaded(root) {
  document.dispatchEvent(new CustomEvent('wt-content-loaded', { detail: { root } }));
}

/**
 * Require exactly one matching element of the expected type under a root element.
 *
 * @param {ParentNode} root
 * @param {string} selector
 * @param {typeof Element} type
 * @param {string} description
 * @returns {Element}
 */
export function requireElement(root, selector, type, description) {
  const elements = root.querySelectorAll(selector);

  if (elements.length !== 1) {
    throw new Error('Expected exactly one ' + description + '.');
  }

  const element = elements[0];

  if (!(element instanceof type)) {
    throw new Error('Invalid element type for ' + description + '.');
  }

  return element;
}

/**
 * Require a dataset value from an element.
 *
 * @param {HTMLElement} element
 * @param {string} key
 * @param {string} description
 * @returns {string}
 */
export function requireDatasetValue(element, key, description) {
  const value = element.dataset[key];

  if (value === undefined || value === '') {
    throw new Error('Missing required dataset value for ' + description + '.');
  }

  return value;
}

/**
 * Show a list of elements by clearing inline display style.
 *
 * @param {Iterable<Element>} elements
 */
export function showElements(elements) {
  for (const element of elements) {
    element.style.removeProperty('display');
  }
}

/**
 * Hide a list of elements using inline display style.
 *
 * @param {Iterable<Element>} elements
 */
export function hideElements(elements) {
  for (const element of elements) {
    element.style.display = 'none';
  }
}
