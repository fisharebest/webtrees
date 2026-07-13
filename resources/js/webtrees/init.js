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
import { confirmDialog } from './confirm';
import { hideElements, initializeWhenReady, onDocumentReady, pasteAtCursor, persistentToggle, setColorTheme, showElements, watchForColorThemeChanges } from './dom';
import {
  initializeCalendarLocalization,
  initializeCalendarWidgetButtons,
  initializeCaptchaFields,
  initializeCensusSelectors,
  initializeCheckboxActionButtons,
  initializeCkeditorHtmlEdit,
  initializeCopyButtons,
  initializeDateReformatInputs,
  initializeEditNameAddons,
  initializeEventCheckboxControls,
  initializeEventsRecordedSelectors,
  initializeGeneratedValueButtons,
  initializeLatLongReformatInputs,
  initializeMediaFileFields,
  initializeNoteStructureControls,
  initializePasswordToggles,
  initializeReorderButtons,
  initializeRequiredSelectForms,
  initializeSetupBaseUrlFields,
  initializeSetupMysqlConnection,
  initializeShowMoreButtons,
  initializeSingleMessageDeleteButtons,
  initializeSortableLists,
  initializeSortByDateButtons,
  initializeSubmitOnChangeControls,
  initializeSubmitSelectedUrlButtons,
  initializeSwapIndividualsButtons,
  initializeTextareaPatternForms,
  initializeToggleTargetControls,
} from './forms';
import { initializeDatatables } from './datatables';
import { initializeGallery } from './gallery';
import { httpPost } from './http';
import { initializeOnScreenKeyboard } from './on-screen-keyboard';
import {
  initializeAjaxModalPage,
  initializeAnniversariesListPage,
  initializeCensusAssistantPage,
  initializeClippingsDownloadPage,
  initializeCookieWarnings,
  initializeDescendancySidebarPage,
  initializeEditBlocksPage,
  initializeFanChartMaps,
  initializeHourglassCharts,
  initializeHtmlTemplateConfig,
  initializeIndividualPageTabs,
  initializePedigreeMapPage,
  initializePlaceHierarchyMapPage,
  initializePlacesTabMapPage,
  initializeRandomMediaSlideshow,
  initializeSearchResultsPage,
  initializeStatisticsChartCustomPage,
  initializeStatisticsChartPage,
  initializeTimelineChartPage,
} from './pages';

/**
 * Initialize all page behavior for the webtrees public bundle.
 *
 * Functions defined in webtrees.js (load, initializeTomSelect, resetTomSelect)
 * are accessed via window.webtrees at runtime since they are part of the same bundle.
 */
export function initializeWebtreesPage() {
  if (typeof window.webtreesLegacy?.configureAjaxCsrf === 'function') {
    window.webtreesLegacy.configureAjaxCsrf();
  }

  initializeDatatables();

  onDocumentReady(function () {
    // Set light/dark mode
    if (document.documentElement.dataset.bsTheme === 'auto') {
      setColorTheme();
      watchForColorThemeChanges();
    }

    // Page elements that load automatically via AJAX.
    // This prevents bad robots from crawling resource-intensive pages.
    document.querySelectorAll('[data-wt-ajax-url]').forEach(function (element) {
      window.webtrees.load(element, element.dataset.wtAjaxUrl);
    });
  });

  initializeWhenReady(function () {
    initializeAjaxModalPage();
    initializeAnniversariesListPage();
    initializeCensusAssistantPage();
    initializeCalendarLocalization();
    initializeCalendarWidgetButtons();
    initializeCaptchaFields();
    initializeCheckboxActionButtons();
    initializeCkeditorHtmlEdit();
    initializeCookieWarnings();
    initializeCopyButtons();
    initializeClippingsDownloadPage();
    initializeDateReformatInputs();
    initializeDescendancySidebarPage();
    initializeEditNameAddons();
    initializeEditBlocksPage();
    initializeEventCheckboxControls();
    initializeEventsRecordedSelectors();
    initializeFanChartMaps();
    initializeGeneratedValueButtons();
    initializeHtmlTemplateConfig();
    initializeHourglassCharts();
    initializeIndividualPageTabs();
    initializeRandomMediaSlideshow();
    initializeLatLongReformatInputs();
    initializeMediaFileFields();
    initializeNoteStructureControls();
    initializePedigreeMapPage();
    initializePasswordToggles();
    initializePlaceHierarchyMapPage();
    initializePlacesTabMapPage();
    initializeReorderButtons();
    initializeRequiredSelectForms();
    initializeCensusSelectors();
    initializeSetupBaseUrlFields();
    initializeSetupMysqlConnection();
    initializeShowMoreButtons();
    initializeSingleMessageDeleteButtons();
    initializeStatisticsChartCustomPage();
    initializeStatisticsChartPage();
    initializeSortableLists();
    initializeSortByDateButtons();
    initializeSubmitOnChangeControls();
    initializeSubmitSelectedUrlButtons();
    initializeTimelineChartPage();
    initializeToggleTargetControls();
    initializeSwapIndividualsButtons();
    initializeSearchResultsPage();
    initializeTextareaPatternForms();

    // Autocomplete
    autocomplete('input[data-wt-autocomplete-url]');

    initializeGallery();

    document.querySelectorAll('.tom-select').forEach(element => window.webtrees.initializeTomSelect(element));

    // If we clear the select (using the "X" button), we need an empty value
    // (rather than no value at all) for (non-multiple) selects with name="array[]"
    document.querySelectorAll('select.tom-select:not([multiple])')
      .forEach(function (element) {
        if (element.dataset.wtTomSelectClearInitialized === '1') {
          return;
        }

        element.dataset.wtTomSelectClearInitialized = '1';

        element.addEventListener('clear', function () {
          window.webtrees.resetTomSelect(element.tomselect, '', '');
        });
      });


    // Save button/checkbox state between pages
    document.querySelectorAll('[data-wt-persist]')
      .forEach((element) => persistentToggle(element));

    initializeOnScreenKeyboard({ hideElements, pasteAtCursor, showElements });
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

  /**
   * @param {MouseEvent} event
   * @param {HTMLAnchorElement|HTMLButtonElement} target
   * @returns {Promise<boolean>}
   */
  const handleConfirmClick = async (event, target) => {
    const skip_confirm = target.dataset.wtConfirmBypass === '1';

    if (skip_confirm) {
      delete target.dataset.wtConfirmBypass;
    }

    if (!('wtConfirm' in target.dataset) || skip_confirm) {
      return false;
    }

    event.preventDefault();

    const confirmed = await confirmDialog(target.dataset.wtConfirm);

    if (!confirmed) {
      return true;
    }

    if (target instanceof HTMLButtonElement && target.type === 'submit' && target.form !== null) {
      target.form.requestSubmit(target);
    } else {
      target.dataset.wtConfirmBypass = '1';
      target.click();
    }

    return true;
  };

  /**
   * @param {MouseEvent} event
   * @param {HTMLAnchorElement|HTMLButtonElement} target
   */
  const handlePostClick = (event, target) => {
    if (!('wtPostUrl' in target.dataset)) {
      return;
    }

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
  };

  /**
   * @param {MouseEvent} event
   * @param {HTMLAnchorElement|HTMLButtonElement} target
   */
  const handleFullscreenClick = (event, target) => {
    if (!('wtFullscreen' in target.dataset)) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    const element = target.closest(target.dataset.wtFullscreen);

    if (element === null) {
      throw new Error('Fullscreen target element not found for selector: ' + target.dataset.wtFullscreen);
    }

    if (document.fullscreenElement === element) {
      document.exitFullscreen()
        .catch((error) => alert(error));
    } else {
      element.requestFullscreen()
        .catch((error) => alert(error));
    }
  };

  // Convert data-wt-* attributes into useful behavior.
  document.addEventListener('click', async (event) => {
    const target = event.target.closest('a,button');

    if (target === null) {
      return;
    }

    if (await handleConfirmClick(event, target)) {
      return;
    }

    if ('wtPostUrl' in target.dataset && 'wtFullscreen' in target.dataset) {
      throw new Error('Element cannot use both data-wt-post-url and data-wt-fullscreen.');
    }

    handlePostClick(event, target);
    handleFullscreenClick(event, target);
  });
}
