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

let tiny_mce_load_promise = null;

/**
 * @param {string} script_url
 * @returns {Promise<void>}
 */
function loadTinyMceScript(script_url) {
  if (tiny_mce_load_promise === null) {
    tiny_mce_load_promise = new Promise((resolve, reject) => {
      if (typeof window.tinymce !== 'undefined') {
        resolve();
        return;
      }

      const script = document.createElement('script');
      script.src = script_url;
      script.type = 'text/javascript';
      script.onload = () => resolve();
      script.onerror = () => reject(new Error('Failed to load TinyMCE bundle.'));
      document.head.appendChild(script);
    });
  }

  return tiny_mce_load_promise;
}

/**
 * Initialize TinyMCE for textarea.html-edit fields.
 *
 * @param {ParentNode} root
 */
export function initializeTinymceHtmlEdit(root) {
  const editors = root.querySelectorAll('textarea.html-edit');

  if (editors.length === 0) {
    return;
  }

  if (document.querySelector('[data-wt-tinymce-loader]') === null) {
    return;
  }

  const loader = requireElement(document, '[data-wt-tinymce-loader]', HTMLElement, 'TinyMCE loader configuration');
  const script_url = requireDatasetValue(loader, 'wtTinymceScript', 'TinyMCE bundle script URL');
  const language = requireDatasetValue(loader, 'wtTinymceLanguage', 'TinyMCE language');
  const editors_to_initialize = [];

  editors.forEach((editor) => {
    if (!(editor instanceof HTMLTextAreaElement)) {
      throw new Error('TinyMCE target must be a textarea element.');
    }

    if (editor.id === '') {
      throw new Error('TinyMCE target textarea must have an ID.');
    }

    const existing_editor = window.tinymce?.get(editor.id) ?? null;

    if (existing_editor !== null) {
      return;
    }

    if (editor.dataset.wtTinymceInitializing === '1') {
      return;
    }

    editor.dataset.wtTinymceInitializing = '1';
    editors_to_initialize.push(editor);
  });

  if (editors_to_initialize.length === 0) {
    return;
  }

  loadTinyMceScript(script_url).then(() => {
    if (typeof window.tinymce === 'undefined') {
      throw new Error('TinyMCE global is not available after loading the bundle.');
    }

    editors_to_initialize.forEach((editor) => {
      if (!(editor instanceof HTMLTextAreaElement)) {
        throw new Error('TinyMCE target must be a textarea element.');
      }

      const page_style = window.getComputedStyle(document.body);
      const content_style = 'body {' +
        'font-family:' + page_style.fontFamily + ';' +
        'font-size:' + page_style.fontSize + ';' +
        'line-height:' + page_style.lineHeight + ';' +
        '}';

      const init_promise = window.tinymce.init({
        target: editor,
        license_key: 'gpl',
        language: language,
        // Skin/content CSS are bundled in vendor.min.css.
        skin: false,
        content_css: false,
        content_style: content_style,
        menubar: false,
        branding: false,
        promotion: false,
        convert_urls: false,
        entity_encoding: 'raw',
        plugins: 'advlist autolink code image link lists media nonbreaking searchreplace table visualblocks visualchars',
        toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough subscript superscript forecolor backcolor removeformat | bullist numlist blockquote | link image media table | code | searchreplace visualblocks visualchars nonbreaking',
        font_size_formats: '0.75rem 0.875rem 1rem 1.125rem 1.25rem 1.5rem 2rem',
        color_default_foreground: '#000000',
        color_default_background: '#FFFF00',
        setup: (tiny_mce_editor) => {
          tiny_mce_editor.on('change input undo redo', () => {
            tiny_mce_editor.save();
          });
        },
      });

      Promise.resolve(init_promise).catch((error) => {
        console.error('TinyMCE initialization failed', { editor_id: editor.id, error });
      }).finally(() => {
        delete editor.dataset.wtTinymceInitializing;
      });
    });
  }).catch((error) => {
    editors_to_initialize.forEach((editor) => {
      delete editor.dataset.wtTinymceInitializing;
    });

    console.error('TinyMCE initialization failed', { error });
  });
}
