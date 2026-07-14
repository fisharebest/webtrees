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
 * Initialize NOTE structure controls for switching between inline/shared notes.
 *
 * @param {ParentNode} root
 */
export function initializeNoteStructureControls(root) {
  root.querySelectorAll('[data-wt-note-structure]').forEach((container) => {
    if (!(container instanceof HTMLElement)) {
      throw new Error('Note-structure container must be an HTML element.');
    }

    const options = requireElement(container, '[data-wt-note-options]', HTMLElement, 'note-structure options');
    const inlineNote = requireElement(container, '[data-wt-note-inline]', HTMLElement, 'inline note container');
    const sharedNote = requireElement(container, '[data-wt-note-shared]', HTMLElement, 'shared note container');
    const inlineField = requireElement(inlineNote, 'textarea', HTMLTextAreaElement, 'inline note textarea');
    const sharedField = requireElement(sharedNote, 'select', HTMLSelectElement, 'shared note select');

    const syncControls = () => {
      const selected = options.querySelector('input[type="radio"]:checked');
      const sharedSelected = selected instanceof HTMLInputElement && selected.value === 'shared';

      inlineNote.classList.toggle('d-none', sharedSelected);
      sharedNote.classList.toggle('d-none', !sharedSelected);

      inlineField.disabled = sharedSelected;
      sharedField.disabled = !sharedSelected;

      if (sharedField.matches('.tom-select') && sharedField.tomselect) {
        if (sharedSelected) {
          sharedField.tomselect.enable();
        } else {
          sharedField.tomselect.disable();
        }
      }
    };

    options.addEventListener('change', syncControls);
    syncControls();
  });
}
