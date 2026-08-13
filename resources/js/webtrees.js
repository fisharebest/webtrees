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

'use strict';

import {
  autocomplete,
  buildLeafletJsMap,
  confirmDialog,
  createDialogModal,
  getWebtreesGlobal,
  hideElements,
  httpGet,
  httpPost,
  i18n,
  initializeWhenReady,
  onDocumentReady,
  pasteAtCursor,
  persistentToggle,
  reformatLatitude,
  reformatLongitude,
  showElements,
  textareaPatterns,
  initializeWebtreesPage,
} from './webtrees/index';

import { load } from './webtrees/html-loader';
import { buildNameFromParts, detectScript } from './webtrees/name-builder';
import { calendarWidget, calDateClicked, calLocalize, calUpdateCalendar, reformatDate } from './webtrees/calendar-widget';
import { initializeTomSelect, resetTomSelect } from './webtrees/tom-select';
import { createRecordModalSubmit, initializeIFSRO } from './webtrees/record-modal';

// Runtime contract: this bundle augments a shared global object so existing
// templates, ajax-modal.js, and admin modules can call webtrees.* APIs.

const webtrees = getWebtreesGlobal(window);

(function (webtrees) {
  // --- Global API: utilities used by admin modules and third-party code ---

  webtrees.httpGet = httpGet;
  webtrees.httpPost = httpPost;
  webtrees.i18n = i18n;
  webtrees.autocomplete = autocomplete;
  webtrees.buildLeafletJsMap = buildLeafletJsMap;
  webtrees.confirmDialog = confirmDialog;
  webtrees.createDialogModal = createDialogModal;
  webtrees.initializeWhenReady = initializeWhenReady;
  webtrees.onDocumentReady = onDocumentReady;
  webtrees.pasteAtCursor = pasteAtCursor;
  webtrees.persistentToggle = persistentToggle;
  webtrees.hideElements = hideElements;
  webtrees.reformatLatitude = reformatLatitude;
  webtrees.reformatLongitude = reformatLongitude;
  webtrees.showElements = showElements;
  webtrees.textareaPatterns = textareaPatterns;

  // --- Global API: HTML loading ---

  webtrees.load = load;

  // --- Global API: name building ---

  webtrees.buildNameFromParts = buildNameFromParts;
  webtrees.detectScript = detectScript;

  // --- Global API: calendar widget ---

  webtrees.calLocalize = calLocalize;
  webtrees.calendarWidget = calendarWidget;
  webtrees.calUpdateCalendar = calUpdateCalendar;
  webtrees.calDateClicked = calDateClicked;
  webtrees.reformatDate = reformatDate;

  // --- Global API: TomSelect ---

  webtrees.initializeTomSelect = initializeTomSelect;
  webtrees.resetTomSelect = resetTomSelect;

  // --- Global API: record modals ---

  webtrees.createRecordModalSubmit = createRecordModalSubmit;
  webtrees.initializeIFSRO = initializeIFSRO;

}(webtrees));

// Initialize the page — no arguments needed; init.js imports what it uses directly.
initializeWebtreesPage();
