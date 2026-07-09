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
  if (element !== null) {
    const caretPos = element.selectionStart + text.length;
    const textBefore = element.value.substring(0, element.selectionStart);
    const textAfter = element.value.substring(element.selectionEnd);
    element.value = textBefore + text + textAfter;
    element.setSelectionRange(caretPos, caretPos);
    element.focus();
  }
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

