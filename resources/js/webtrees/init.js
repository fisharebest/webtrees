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

import { autocomplete } from './autocomplete';

/**
 * Register global initialization handlers.
 *
 * @param {object} dependencies
 */
export function initializeWebtreesPage(dependencies) {
  const {
    confirmDialog,
    httpPost,
    initializeTomSelect,
    load,
    pasteAtCursor,
    persistentToggle,
    resetTomSelect,
    setColorTheme,
    watchForColorThemeChanges,
  } = dependencies;

  // Send the CSRF token on all AJAX requests.
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name=csrf]').attr('content')
    }
  });

  // Runtime contract: page-level behavior is initialized from this single DOMContentLoaded hook.
  document.addEventListener('DOMContentLoaded', function () {
    // Set light/dark mode
    if (document.documentElement.dataset.bsTheme === 'auto') {
      setColorTheme();
      watchForColorThemeChanges();
    }

    // Page elements that load automatically via AJAX.
    // This prevents bad robots from crawling resource-intensive pages.
    document.querySelectorAll('[data-wt-ajax-url]').forEach(function (element) {
      load(element, element.dataset.wtAjaxUrl);
    });

    // Autocomplete
    autocomplete('input[data-wt-autocomplete-url]');

    document.querySelectorAll('.tom-select').forEach(element => initializeTomSelect(element));

    // If we clear the select (using the "X" button), we need an empty value
    // (rather than no value at all) for (non-multiple) selects with name="array[]"
    document.querySelectorAll('select.tom-select:not([multiple])')
      .forEach(function (element) {
        element.addEventListener('clear', function () {
          resetTomSelect(element.tomselect, '', '');
        });
      });

    // Datatables - locale-aware sorting
    DataTable.ext.oSort['text-asc'] = (x, y) => x.localeCompare(y, document.documentElement.lang, { sensitivity: 'base' });
    DataTable.ext.oSort['text-desc'] = (x, y) => y.localeCompare(x, document.documentElement.lang, { sensitivity: 'base' });

    // DataTables - start hidden to prevent FOUC.
    document.querySelectorAll('table.datatables').forEach(function (element) {
      new DataTable(element);
      element.classList.remove('d-none');
    });

    // Save button/checkbox state between pages
    document.querySelectorAll('[data-wt-persist]')
      .forEach((element) => persistentToggle(element));

    // Activate the on-screen keyboard
    let osk_focus_element;
    $('.wt-osk-trigger').on('click', function () {
      // When a user clicks the icon, set focus to the corresponding input
      osk_focus_element = document.getElementById(this.dataset.wtId);
      osk_focus_element.focus();
      $('.wt-osk').show();
    });
    $('.wt-osk-script-button').on('change', function () {
      $('.wt-osk-script').prop('hidden', true);
      $('.wt-osk-script-' + this.dataset.wtOskScript).prop('hidden', false);
    });
    $('.wt-osk-shift-button').on('click', function () {
      document.querySelector('.wt-osk-keys').classList.toggle('shifted');
    });
    $('.wt-osk-keys').on('click', '.wt-osk-key', function () {
      let key = $(this).contents().get(0).nodeValue;
      let shift_state = $('.wt-osk-shift-button').hasClass('active');
      let shift_key = $('sup', this)[0];
      if (shift_state && shift_key !== undefined) {
        key = shift_key.innerText;
      }
      pasteAtCursor(osk_focus_element, key);
      if ($('.wt-osk-pin-button').hasClass('active') === false) {
        $('.wt-osk').hide();
      }
      osk_focus_element.dispatchEvent(new Event('input'));
    });

    $('.wt-osk-close').on('click', function () {
      $('.wt-osk').hide();
    });
  });

  // Prevent form re-submission via accidental double-click.
  document.addEventListener('submit', function (event) {
    if (event.target.method === 'POST') {
      if (event.target.classList.contains('form-is-submitting')) {
        event.preventDefault();
      } else {
        event.target.classList.add('form-is-submitting');
      }
    }
  });

  // Convert data-wt-* attributes into useful behavior.
  document.addEventListener('click', async (event) => {
    const target = event.target.closest('a,button');

    if (target === null) {
      return;
    }

    const skip_confirm = target.dataset.wtConfirmBypass === '1';

    if (skip_confirm) {
      delete target.dataset.wtConfirmBypass;
    }

    if ('wtConfirm' in target.dataset && !skip_confirm) {
      event.preventDefault();

      const confirmed = await confirmDialog(target.dataset.wtConfirm);

      if (!confirmed) {
        return;
      }

      if (target instanceof HTMLButtonElement && target.type === 'submit' && target.form !== null) {
        target.form.requestSubmit(target);
      } else {
        target.dataset.wtConfirmBypass = '1';
        target.click();
      }

      return;
    }

    if ('wtPostUrl' in target.dataset) {
      event.preventDefault();

      httpPost(target.dataset.wtPostUrl).then(() => {
        if ('wtReloadUrl' in target.dataset) {
          // Go somewhere else. e.g. the home page after logout.
          document.location = target.dataset.wtReloadUrl;
        } else {
          // Reload the current page. e.g. change language.
          document.location.reload();
        }
      }).catch((error) => {
        alert(error);
      });
    }

    if (('wtFullscreen' in target.dataset)) {
      event.preventDefault();
      event.stopPropagation();

      const element = target.closest(target.dataset.wtFullscreen);

      if (document.fullscreenElement === element) {
        document.exitFullscreen()
          .catch((error) => alert(error));
      } else {
        element.requestFullscreen()
          .catch((error) => alert(error));
      }
    }
  });
}

