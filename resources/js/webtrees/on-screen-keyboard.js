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
 * Initialize the on-screen keyboard handlers.
 *
 * @param {object} dependencies
 * @param {ParentNode} root
 */
export function initializeOnScreenKeyboard(dependencies, root) {
  const { hideElements, pasteAtCursor, showElements } = dependencies;

  let osk_focus_element = null;

  root.querySelectorAll('.wt-osk-trigger').forEach((trigger) => {
    if (trigger.dataset.wtOskInitialized === '1') {
      return;
    }

    trigger.dataset.wtOskInitialized = '1';

    trigger.addEventListener('click', function () {
      // When a user clicks the icon, set focus to the corresponding input
      osk_focus_element = document.getElementById(this.dataset.wtId);

      if (osk_focus_element === null) {
        throw new Error('On-screen keyboard trigger target not found: #' + this.dataset.wtId);
      }

      osk_focus_element.focus();
      showElements(document.querySelectorAll('.wt-osk'));
    });
  });

  root.querySelectorAll('.wt-osk-script-button').forEach((button) => {
    if (button.dataset.wtOskInitialized === '1') {
      return;
    }

    button.dataset.wtOskInitialized = '1';

    button.addEventListener('change', function () {
      document.querySelectorAll('.wt-osk-script').forEach((script) => {
        script.hidden = true;
      });

      document.querySelectorAll('.wt-osk-script-' + this.dataset.wtOskScript).forEach((script) => {
        script.hidden = false;
      });
    });
  });

  root.querySelectorAll('.wt-osk-shift-button').forEach((button) => {
    if (button.dataset.wtOskInitialized === '1') {
      return;
    }

    button.dataset.wtOskInitialized = '1';

    button.addEventListener('click', () => {
      const key_container = document.querySelector('.wt-osk-keys');

      if (key_container === null) {
        throw new Error('On-screen keyboard key container not found: .wt-osk-keys');
      }

      key_container.classList.toggle('shifted');
    });
  });

  root.querySelectorAll('.wt-osk-keys').forEach((keys) => {
    if (keys.dataset.wtOskInitialized === '1') {
      return;
    }

    keys.dataset.wtOskInitialized = '1';

    keys.addEventListener('click', (event) => {
      const key_button = event.target.closest('.wt-osk-key');

      if (key_button === null || !keys.contains(key_button)) {
        return;
      }

      let key = key_button.firstChild?.nodeValue;
      const shift_state = document.querySelector('.wt-osk-shift-button')?.classList.contains('active') ?? false;
      const shift_key = key_button.querySelector('sup');

      if (shift_state && shift_key !== null) {
        key = shift_key.innerText;
      }

      if (typeof key !== 'string' || key.length === 0) {
        throw new Error('On-screen keyboard key has no text value.');
      }

      if (osk_focus_element === null) {
        return;
      }

      pasteAtCursor(osk_focus_element, key);

      if ((document.querySelector('.wt-osk-pin-button')?.classList.contains('active') ?? false) === false) {
        hideElements(document.querySelectorAll('.wt-osk'));
      }

      osk_focus_element.dispatchEvent(new Event('input'));
    });
  });

  root.querySelectorAll('.wt-osk-close').forEach((close_button) => {
    if (close_button.dataset.wtOskInitialized === '1') {
      return;
    }

    close_button.dataset.wtOskInitialized = '1';

    close_button.addEventListener('click', () => {
      hideElements(document.querySelectorAll('.wt-osk'));
    });
  });
}

