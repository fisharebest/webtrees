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

let ckeditorLoadPromise = null;

/**
 * Initialize CKEditor for textarea.html-edit fields.
 *
 * @param {ParentNode} root
 */
export function initializeCkeditorHtmlEdit(root) {
  const editors = root.querySelectorAll('textarea.html-edit');

  if (editors.length === 0) {
    return;
  }

  const loader = requireElement(document, '[data-wt-ckeditor-loader]', HTMLElement, 'CKEditor loader configuration');
  const basePath = requireDatasetValue(loader, 'wtCkeditorBasePath', 'CKEditor base path');
  const scriptUrl = requireDatasetValue(loader, 'wtCkeditorScript', 'CKEditor script URL');
  const language = requireDatasetValue(loader, 'wtCkeditorLanguage', 'CKEditor language');

  if (ckeditorLoadPromise === null) {
    ckeditorLoadPromise = new Promise((resolve, reject) => {
      if (typeof CKEDITOR !== 'undefined') {
        resolve();
        return;
      }

      const script = document.createElement('script');
      script.src = scriptUrl;
      script.type = 'text/javascript';
      script.onload = () => resolve();
      script.onerror = () => reject(new Error('Failed to load CKEditor script.'));
      document.head.appendChild(script);
    });
  }

  ckeditorLoadPromise.then(() => {
    if (typeof CKEDITOR === 'undefined') {
      throw new Error('CKEditor global is not available after loading script.');
    }

    window.CKEDITOR_BASEPATH = basePath;
    CKEDITOR.env.isCompatible = true;
    CKEDITOR.config.language = language;
    CKEDITOR.config.removePlugins = 'forms,newpage,preview,print,save,templates,flash,iframe';
    CKEDITOR.config.extraAllowedContent =
      'area[shape,coords,href,target,alt,title];' +
      'map[name];' +
      'img[usemap];' +
      'audio[controls,src];' +
      'video[controls,height,poster,src,width];' +
      '*{*}(*)';
    CKEDITOR.config.entities = false;

    editors.forEach((editor) => {
      if (!(editor instanceof HTMLTextAreaElement)) {
        throw new Error('CKEditor target must be a textarea element.');
      }

      if (editor.id === '') {
        throw new Error('CKEditor target textarea must have an ID.');
      }

      if (CKEDITOR.instances[editor.id] === undefined) {
        CKEDITOR.replace(editor.id);
      }
    });
  }).catch((error) => {
    console.error('CKEditor initialization failed', { error });
  });
}

