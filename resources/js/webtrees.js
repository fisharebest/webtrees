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
  httpGet,
  httpPost,
  initializeGallery,
  initializeWebtreesPage,
  pasteAtCursor,
  persistentToggle,
  reformatLatitude,
  reformatLongitude,
  setColorTheme,
  textareaPatterns,
  watchForColorThemeChanges,
} from './webtrees/index';

// Runtime contract: this bundle augments a shared global object so existing templates and inline handlers can call webtrees.* APIs.

const webtrees = getWebtreesGlobal(window);

(function (webtrees) {
  const lang = document.documentElement.lang;

  // Identify the script used by some text.
  const scriptRegexes = {
    Han: /[\u3400-\u9FCC]/,
    Grek: /[\u0370-\u03FF]/,
    Cyrl: /[\u0400-\u04FF]/,
    Hebr: /[\u0590-\u05FF]/,
    Arab: /[\u0600-\u06FF]/
  };

  /**
   * Tidy the whitespace in a string.
   * @param {string} str
   * @returns {string}
   */
  function trim (str) {
    return str.replace(/\s+/g, ' ').trim();
  }

  webtrees.httpGet = httpGet;
  webtrees.httpPost = httpPost;
  webtrees.autocomplete = autocomplete;
  webtrees.buildLeafletJsMap = buildLeafletJsMap;
  webtrees.confirmDialog = confirmDialog;
  webtrees.createDialogModal = createDialogModal;
  webtrees.pasteAtCursor = pasteAtCursor;
  webtrees.persistentToggle = persistentToggle;
  webtrees.reformatLatitude = reformatLatitude;
  webtrees.reformatLongitude = reformatLongitude;
  webtrees.textareaPatterns = textareaPatterns;
  webtrees.setColorTheme = setColorTheme;
  webtrees.watchForColorThemeChanges = watchForColorThemeChanges;
  webtrees.initializeGallery = initializeGallery;

  /**
   * Simple replacement for jQuery().load() - fetch HTML, insert into an element, and execute any scripts.
   *
   * @param {Element} element
   * @param {string} url
   * @param {FormData|null} data
   */
  webtrees.load = async function (element, url, data = null) {
    const headers = {
      'accept': 'text/html',
      'x-requested-with': 'XMLHttpRequest',
    };

    if (data !== null) {
      headers['x-csrf-token'] = document.head.querySelector('meta[name=csrf]').getAttribute('content');
    }

    const response = await fetch(url, {
      body: data,
      method: data === null ? 'GET' : 'POST',
      headers: new Headers(headers),
    });

    const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
    const scripts = Array.from(doc.querySelectorAll('script'));

    // Don't insert scripts into the document.  We will execute them directly.
    scripts.forEach(script => script.remove());

    // Replace innerHTML with the loaded HTML.
    element.replaceChildren(...doc.body.childNodes);

    // Execute scripts sequentially
    for (const node of scripts) {
      const script = document.createElement('script');

      for (const attr of node.attributes) {
        script.setAttribute(attr.name, attr.value);
      }

      if (node.src) {
        await new Promise(resolve => {
          script.onload = resolve;
          script.onerror = resolve;
          document.body.appendChild(script);
        });
      } else {
        script.textContent = node.textContent;
        document.body.appendChild(script);
      }

      // Remove the script we just executed to reduce clutter.
      script.remove();
    }
  }


  /**
   * Look for non-latin characters in a string.
   * @param {string} str
   * @returns {string}
   */
  webtrees.detectScript = function (str) {
    for (const script in scriptRegexes) {
      if (str.match(scriptRegexes[script])) {
        return script;
      }
    }

    return 'Latn';
  };

  /**
   * In some languages, the SURN uses a male/default form, but NAME uses a gender-inflected form.
   * @param {string} surname
   * @param {string} sex
   * @returns {string}
   */
  function inflectSurname (surname, sex) {
    if (lang === 'pl' && sex === 'F') {
      return surname
        .replace(/ski$/, 'ska')
        .replace(/cki$/, 'cka')
        .replace(/dzki$/, 'dzka')
        .replace(/żki$/, 'żka');
    }

    return surname;
  }

  /**
   * Build a NAME from a NPFX, GIVN, SPFX, SURN and NSFX parts.
   * Assumes the language of the document is the same as the language of the name.
   * @param {string} npfx
   * @param {string} givn
   * @param {string} spfx
   * @param {string} surn
   * @param {string} nsfx
   * @param {string} sex
   * @returns {string}
   */
  webtrees.buildNameFromParts = function (npfx, givn, spfx, surn, nsfx, sex) {
    const usesCJK = webtrees.detectScript(npfx + givn + spfx + givn + surn + nsfx) === 'Han';
    const separator = usesCJK ? '' : ' ';
    const surnameFirst = usesCJK || ['hu', 'jp', 'ko', 'vi', 'zh-Hans', 'zh-Hant'].indexOf(lang) !== -1;
    const patronym = ['is'].indexOf(lang) !== -1;
    const slash = patronym ? '' : '/';

    // GIVN and SURN may be a comma-separated lists.
    npfx = trim(npfx);
    givn = trim(givn.replace(/,/g, separator));
    spfx = trim(spfx);
    surn = inflectSurname(trim(surn.replace(/,/g, separator)), sex);
    nsfx = trim(nsfx);

    const surname_separator = spfx.endsWith('\'') || spfx.endsWith('‘') ? '' : ' ';

    const surname = trim(spfx + surname_separator + surn);

    const name = surnameFirst ? slash + surname + slash + separator + givn : givn + separator + slash + surname + slash;

    return trim(npfx + separator + name + separator + nsfx);
  };

  /**
   * @param {Element} datefield
   * @param {string} dmy
   */
  webtrees.reformatDate = function (datefield, dmy) {
    const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
    const hijri_months = ['MUHAR', 'SAFAR', 'RABIA', 'RABIT', 'JUMAA', 'JUMAT', 'RAJAB', 'SHAAB', 'RAMAD', 'SHAWW', 'DHUAQ', 'DHUAH'];
    const hebrew_months = ['TSH', 'CSH', 'KSL', 'TVT', 'SHV', 'ADR', 'ADS', 'NSN', 'IYR', 'SVN', 'TMZ', 'AAV', 'ELL'];
    const french_months = ['VEND', 'BRUM', 'FRIM', 'NIVO', 'PLUV', 'VENT', 'GERM', 'FLOR', 'PRAI', 'MESS', 'THER', 'FRUC', 'COMP'];
    const jalali_months = ['FARVA', 'ORDIB', 'KHORD', 'TIR', 'MORDA', 'SHAHR', 'MEHR', 'ABAN', 'AZAR', 'DEY', 'BAHMA', 'ESFAN'];

    let datestr = datefield.value;
    // if a date has a date phrase marked by () this has to be excluded from altering
    let datearr = datestr.split('(');
    let datephrase = '';
    if (datearr.length > 1) {
      datestr = datearr[0];
      datephrase = datearr[1];
    }

    // Gedcom dates are upper case
    datestr = datestr.toUpperCase();
    // Gedcom dates have no leading/trailing/repeated whitespace
    datestr = datestr.replace(/\s+/g, ' ');
    datestr = datestr.replace(/(^\s)|(\s$)/, '');
    // Gedcom dates have spaces between letters and digits, e.g. "01JAN2000" => "01 JAN 2000"
    datestr = datestr.replace(/(\d)([A-Z])/g, '$1 $2');
    datestr = datestr.replace(/([A-Z])(\d)/g, '$1 $2');

    // Shortcut for quarter format, "Q1 1900" => "BET JAN 1900 AND MAR 1900".
    datestr = datestr.replace(/^Q ([1-4]) (\d\d\d\d)$/, function (match, c1, c2) {
      return 'BET ' + months[c1 * 3 - 3] + ' ' + c2 + ' AND ' + months[c1 * 3 - 1] + ' ' + c2;
    });

    // Shortcuts for @#Dxxxxx@ 01 01 1400
    datestr = datestr.replace(/(@#DHIJRI@|HIJRI)( \d?\d )(\d?\d)( \d?\d?\d?\d)/, function (match,c1, c2, c3, c4) {
      return '@#DHIJRI@' + c2 + hijri_months[c3 - 1] + c4;
    });
    datestr = datestr.replace(/(@#DJALALI@|JALALI)( \d?\d )(\d?\d)( \d?\d?\d?\d)/, function (match,c1, c2, c3, c4) {
      return '@#DJALALI@' + c2 + jalali_months[c3 - 1] + c4;
    });
    datestr = datestr.replace(/(@#DHEBREW@|HEBREW)( \d?\d )(\d?\d)( \d?\d?\d?\d)/, function (match,c1, c2, c3, c4) {
      return '@#HEBREW@' + c2 + hebrew_months[c3 - 1] + c4;
    });
    datestr = datestr.replace(/(@#DFRENCH R@|FRENCH)( \d?\d )(\d?\d)( \d?\d?\d?\d)/, function (match,c1, c2, c3, c4) {
      return '@#DFRENCH R@' + c2 + french_months[c3 - 1] + c4;
    });

    // All digit dates
    datestr = datestr.replace(/(\d\d)(\d\d)(\d\d)(\d\d)/g, function (match, c1, c2, c3, c4) {
      if (c1 > '12' && c3 <= '12' && c4 <= '31') {
        return c4 + ' ' + months[c3 - 1] + ' ' + c1 + c2;
      }
      if (c1 <= '31' && c2 <= '12' && c3 > '12') {
        return c1 + ' ' + months[c2 - 1] + ' ' + c3 + c4;
      }
      return match;
    });

    // e.g. 17.11.1860, 2 4 1987, 3/4/2005, 1999-12-31. Use locale settings since DMY order is ambiguous.
    datestr = datestr.replace(/(\d+)([ ./-])(\d+)(\2)(\d+)/g, function (match, c1, c2, c3, c4, c5) {
      c1 = parseInt(c1, 10);
      c2 = parseInt(c3, 10);
      c3 = parseInt(c5, 10);
      let yyyy = new Date().getFullYear();
      let yy = yyyy % 100;
      let cc = yyyy - yy;
      if ((dmy === 'DMY' || c1 > 13 && c3 > 31) && c1 <= 31 && c2 <= 12) {
        return c1 + ' ' + months[c2 - 1] + ' ' + (c3 >= 100 ? c3 : (c3 <= yy ? c3 + cc : c3 + cc - 100));
      }
      if ((dmy === 'MDY' || c2 > 13 && c3 > 31) && c1 <= 12 && c2 <= 31) {
        return c2 + ' ' + months[c1 - 1] + ' ' + (c3 >= 100 ? c3 : (c3 <= yy ? c3 + cc : c3 + cc - 100));
      }
      if ((dmy === 'YMD' || c1 > 31) && c2 <= 12 && c3 <= 31) {
        return c3 + ' ' + months[c2 - 1] + ' ' + (c1 >= 100 ? c1 : (c1 <= yy ? c1 + cc : c1 + cc - 100));
      }
      return match;
    });

    datestr = datestr
      // Shortcuts for date ranges
      .replace(/^[>]([\w ]+)$/, 'AFT $1')
      .replace(/^[<]([\w ]+)$/, 'BEF $1')
      .replace(/^([\w ]+)[-]$/, 'FROM $1')
      .replace(/^[-]([\w ]+)$/, 'TO $1')
      .replace(/^[~]([\w ]+)$/, 'ABT $1')
      .replace(/^[*]([\w ]+)$/, 'EST $1')
      .replace(/^[#]([\w ]+)$/, 'CAL $1')
      .replace(/^([\w ]+) ?- ?([\w ]+)$/, 'BET $1 AND $2')
      .replace(/^([\w ]+) ?~ ?([\w ]+)$/, 'FROM $1 TO $2')
      // Convert full months to short months
      .replace(/JANUARY/g, 'JAN')
      .replace(/FEBRUARY/g, 'FEB')
      .replace(/MARCH/g, 'MAR')
      .replace(/APRIL/g, 'APR')
      .replace(/JUNE/g, 'JUN')
      .replace(/JULY/g, 'JUL')
      .replace(/AUGUST/g, 'AUG')
      .replace(/SEPTEMBER/g, 'SEP')
      .replace(/OCTOBER/, 'OCT')
      .replace(/NOVEMBER/g, 'NOV')
      .replace(/DECEMBER/g, 'DEC')
      // Americans enter dates as SEP 20, 1999
      .replace(/(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\.? (\d\d?)[, ]+(\d\d\d\d)/g, '$2 $1 $3')
      // Apply leading zero to day numbers
      .replace(/(^| )(\d [A-Z]{3,5} \d{4})/g, '$10$2');

    if (datephrase) {
      datestr = datestr + ' (' + datephrase;
    }

    // Only update it if is has been corrected - otherwise input focus
    // moves to the end of the field unnecessarily
    if (datefield.value !== datestr) {
      datefield.value = datestr;
    }
  };

  let monthLabels = [];
  monthLabels[1] = 'January';
  monthLabels[2] = 'February';
  monthLabels[3] = 'March';
  monthLabels[4] = 'April';
  monthLabels[5] = 'May';
  monthLabels[6] = 'June';
  monthLabels[7] = 'July';
  monthLabels[8] = 'August';
  monthLabels[9] = 'September';
  monthLabels[10] = 'October';
  monthLabels[11] = 'November';
  monthLabels[12] = 'December';

  let monthShort = [];
  monthShort[1] = 'JAN';
  monthShort[2] = 'FEB';
  monthShort[3] = 'MAR';
  monthShort[4] = 'APR';
  monthShort[5] = 'MAY';
  monthShort[6] = 'JUN';
  monthShort[7] = 'JUL';
  monthShort[8] = 'AUG';
  monthShort[9] = 'SEP';
  monthShort[10] = 'OCT';
  monthShort[11] = 'NOV';
  monthShort[12] = 'DEC';

  let daysOfWeek = [];
  daysOfWeek[0] = 'S';
  daysOfWeek[1] = 'M';
  daysOfWeek[2] = 'T';
  daysOfWeek[3] = 'W';
  daysOfWeek[4] = 'T';
  daysOfWeek[5] = 'F';
  daysOfWeek[6] = 'S';

  let weekStart = 0;

  /**
   * @param {string} jan
   * @param {string} feb
   * @param {string} mar
   * @param {string} apr
   * @param {string} may
   * @param {string} jun
   * @param {string} jul
   * @param {string} aug
   * @param {string} sep
   * @param {string} oct
   * @param {string} nov
   * @param {string} dec
   * @param {string} sun
   * @param {string} mon
   * @param {string} tue
   * @param {string} wed
   * @param {string} thu
   * @param {string} fri
   * @param {string} sat
   * @param {number} day
   */
  webtrees.calLocalize = function (jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dec, sun, mon, tue, wed, thu, fri, sat, day) {
    monthLabels[1] = jan;
    monthLabels[2] = feb;
    monthLabels[3] = mar;
    monthLabels[4] = apr;
    monthLabels[5] = may;
    monthLabels[6] = jun;
    monthLabels[7] = jul;
    monthLabels[8] = aug;
    monthLabels[9] = sep;
    monthLabels[10] = oct;
    monthLabels[11] = nov;
    monthLabels[12] = dec;
    daysOfWeek[0] = sun;
    daysOfWeek[1] = mon;
    daysOfWeek[2] = tue;
    daysOfWeek[3] = wed;
    daysOfWeek[4] = thu;
    daysOfWeek[5] = fri;
    daysOfWeek[6] = sat;

    if (day >= 0 && day < 7) {
      weekStart = day;
    }
  };

  /**
   * @param {string} dateDivId
   * @param {string} dateFieldId
   * @returns {boolean}
   */
  webtrees.calendarWidget = function (dateDivId, dateFieldId) {
    let dateDiv = document.getElementById(dateDivId);
    let dateField = document.getElementById(dateFieldId);

    if (dateDiv.style.visibility === 'visible') {
      dateDiv.style.visibility = 'hidden';
      return false;
    }
    if (dateDiv.style.visibility === 'show') {
      dateDiv.style.visibility = 'hide';
      return false;
    }

    /* JavaScript calendar functions only work with precise gregorian dates "D M Y" or "Y" */
    let greg_regex = /(?:(\d*) ?(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) )?(\d+)/i;
    let match = greg_regex.exec(dateField.value);
    let date;
    if (match) {
      let day   = match[1] || '1';
      let month = match[2] || 'JAN'
      let year  = match[3];
      date = new Date(day + ' ' + month + ' ' + year);
    } else {
      date = new Date();
    }

    dateDiv.innerHTML = calGenerateSelectorContent(dateFieldId, dateDivId, date);
    if (dateDiv.style.visibility === 'hidden') {
      dateDiv.style.visibility = 'visible';
      return false;
    }
    if (dateDiv.style.visibility === 'hide') {
      dateDiv.style.visibility = 'show';
      return false;
    }

    return false;
  };

  /**
   * @param {string} dateFieldId
   * @param {string} dateDivId
   * @param {Date} date
   * @returns {string}
   */
  function calGenerateSelectorContent (dateFieldId, dateDivId, date) {
    let i, j;
    let content = '<table border="1"><tr>';
    content += '<td><select class="form-select" id="' + dateFieldId + '_daySelect" onchange="return webtrees.calUpdateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');">';
    for (i = 1; i < 32; i++) {
      content += '<option value="' + i + '"';
      if (date.getDate() === i) {
        content += ' selected="selected"';
      }
      content += '>' + i + '</option>';
    }
    content += '</select></td>';
    content += '<td><select class="form-select" id="' + dateFieldId + '_monSelect" onchange="return webtrees.calUpdateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');">';
    for (i = 1; i < 13; i++) {
      content += '<option value="' + i + '"';
      if (date.getMonth() + 1 === i) {
        content += ' selected="selected"';
      }
      content += '>' + monthLabels[i] + '</option>';
    }
    content += '</select></td>';
    content += '<td><input class="form-control" type="text" id="' + dateFieldId + '_yearInput" size="5" value="' + date.getFullYear() + '" onchange="return webtrees.calUpdateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');" /></td></tr>';
    content += '<tr><td colspan="3">';
    content += '<table width="100%">';
    content += '<tr>';
    j = weekStart;
    for (i = 0; i < 7; i++) {
      content += '<td ';
      content += 'class="descriptionbox"';
      content += '>';
      content += daysOfWeek[j];
      content += '</td>';
      j++;
      if (j > 6) {
        j = 0;
      }
    }
    content += '</tr>';

    let tdate = new Date(date.getFullYear(), date.getMonth(), 1);
    let day = (7 + tdate.getDay() - weekStart) % 7;
    let daymilli = 1000 * 60 * 60 * 24;
    tdate = tdate.getTime() - (day * daymilli) + (daymilli / 2);
    tdate = new Date(tdate);

    for (j = 0; j < 6; j++) {
      content += '<tr>';
      for (i = 0; i < 7; i++) {
        content += '<td ';
        if (tdate.getMonth() === date.getMonth()) {
          if (tdate.getDate() === date.getDate()) {
            content += 'class="descriptionbox"';
          } else {
            content += 'class="optionbox"';
          }
        } else {
          content += 'style="background-color:#EAEAEA; border: solid #AAAAAA 1px;"';
        }
        content += '><a href="#" onclick="return webtrees.calDateClicked(\'' + dateFieldId + '\', \'' + dateDivId + '\', ' + tdate.getFullYear() + ', ' + tdate.getMonth() + ', ' + tdate.getDate() + ');">';
        content += tdate.getDate();
        content += '</a></td>';
        let datemilli = tdate.getTime() + daymilli;
        tdate = new Date(datemilli);
      }
      content += '</tr>';
    }
    content += '</table>';
    content += '</td></tr>';
    content += '</table>';

    return content;
  }

  /**
   * @param {string} dateFieldId
   * @param {number} year
   * @param {number} month
   * @param {number} day
   * @returns {boolean}
   */
  function calSetDateField (dateFieldId, year, month, day) {
    let dateField = document.getElementById(dateFieldId);
    dateField.value = (day < 10 ? '0' : '') + day + ' ' + monthShort[month + 1] + ' ' + year;
    return false;
  }

  /**
   * @param {string} dateFieldId
   * @param {string} dateDivId
   * @returns {boolean}
   */
  webtrees.calUpdateCalendar = function (dateFieldId, dateDivId) {
    let dateSel = document.getElementById(dateFieldId + '_daySelect');
    if (!dateSel) {
      return false;
    }
    let monthSel = document.getElementById(dateFieldId + '_monSelect');
    if (!monthSel) {
      return false;
    }
    let yearInput = document.getElementById(dateFieldId + '_yearInput');
    if (!yearInput) {
      return false;
    }

    let month = parseInt(monthSel.options[monthSel.selectedIndex].value, 10);
    month = month - 1;

    let date = new Date(yearInput.value, month, dateSel.options[dateSel.selectedIndex].value);
    calSetDateField(dateFieldId, date.getFullYear(), date.getMonth(), date.getDate());

    let dateDiv = document.getElementById(dateDivId);
    if (!dateDiv) {
      alert('no dateDiv ' + dateDivId);
      return false;
    }
    dateDiv.innerHTML = calGenerateSelectorContent(dateFieldId, dateDivId, date);

    return false;
  };

  /**
   * @param {string} dateFieldId
   * @param {string} dateDivId
   * @param {number} year
   * @param {number} month
   * @param {number} day
   * @returns {boolean}
   */
  webtrees.calDateClicked = function (dateFieldId, dateDivId, year, month, day) {
    calSetDateField(dateFieldId, year, month, day);
    webtrees.calendarWidget(dateDivId, dateFieldId);
    return false;
  };
  /**
   * Initialize a tom-select input
   * @param {Element} element
   * @returns {TomSelect}
   */
  webtrees.initializeTomSelect = function (element) {
    if (element.tomselect) {
      return element.tomselect;
    }

    if (element.dataset.wtUrl) {
      let options = {
        plugins: ['dropdown_input', 'virtual_scroll'],
        maxOptions: false,
        searchField: [], // We filter on the server, so don't filter on the client.
        render: {
          item: (data, escape) => '<div>' + data.text + '</div>',
          option: (data, escape) => '<div>' + data.text + '</div>',
          no_results: (data, escape) => '<div class="no-results">' + element.dataset.wtI18nNoResults + '</div>',
        },
        firstUrl: query => element.dataset.wtUrl + '&query=' + encodeURIComponent(query),
        load: function (query, callback) {
          webtrees.httpGet(this.getUrl(query))
            .then(response => response.json())
            .then(json => {
              if (json.nextUrl !== null) {
                this.setNextUrl(query, json.nextUrl + '&query=' + encodeURIComponent(query));
              }
              callback(json.data);
            })
            .catch(callback);
        },
      };

      if (!element.required) {
        options.plugins.push('clear_button');
      }

      return new TomSelect(element, options);
    }

    if (element.multiple) {
      return new TomSelect(element, { plugins: ['caret_position', 'remove_button'] });
    }

    if (!element.required) {
      return new TomSelect(element, { plugins: ['clear_button'] });
    }

    return new TomSelect(element, { });
  }

  /**
   * Reset a tom-select input to have a single selected option
   * @param {TomSelect} tomSelect
   * @param {string} value
   * @param {string} text
   */
  webtrees.resetTomSelect = function (tomSelect, value, text) {
    tomSelect.clear(true);
    tomSelect.clearOptions();
    tomSelect.addOption({ value: value, text: text });
    tomSelect.refreshOptions();
    tomSelect.addItem(value, true);
    tomSelect.refreshItems();
  };

  /**
   * Toggle the visibility/status of INDI/FAM/SOUR/REPO/OBJE selectors
   *
   * @param {Element} select
   * @param {Element} container
   */
  webtrees.initializeIFSRO = function(select, container) {
    select.addEventListener('change', function () {
      // Show only the selected selector.
      container.querySelectorAll('.select-record').forEach(element => element.classList.add('d-none'));
      container.querySelectorAll('.select-' + select.value).forEach(element => element.classList.remove('d-none'));

      // Enable only the selected selector (so that disabled ones do not get submitted).
      container.querySelectorAll('.select-record select').forEach(element => {
        element.disabled = true;
        if (element.matches('.tom-select')) {
          element.tomselect.disable();
        }
      });
      container.querySelectorAll('.select-' + select.value + ' select').forEach(element => {
        element.disabled = false;
        if (element.matches('.tom-select')) {
          element.tomselect.enable();
        }
      });
    });
  };

  /**
   * Save a form using ajax, for use in modals
   *
   * @param {Event} event
   */
  webtrees.createRecordModalSubmit = function (event) {
    event.preventDefault();
    const form = event.target;
    const modal = document.getElementById('wt-ajax-modal')
    const modal_content = modal.querySelector('.modal-content');
    const select = document.getElementById(modal_content.dataset.wtSelectId);

    webtrees.httpPost(form.action, new FormData(form))
      .then(response => response.json())
      .then(json => {
        if (select && json.value !== '') {
          // This modal was activated by the "create new" button in a select edit control.
          webtrees.resetTomSelect(select.tomselect, json.value, json.text);

          bootstrap.Modal.getInstance(modal).hide();
        } else {
          // Show the success/fail message in the existing modal.
          modal_content.innerHTML = json.html;
        }
      })
      .catch(error => {
        modal_content.innerHTML = error;
      });
  };

}(webtrees));

initializeWebtreesPage({
  confirmDialog: webtrees.confirmDialog,
  httpPost: webtrees.httpPost,
  initializeGallery: webtrees.initializeGallery,
  initializeTomSelect: webtrees.initializeTomSelect,
  load: webtrees.load,
  pasteAtCursor: webtrees.pasteAtCursor,
  persistentToggle: webtrees.persistentToggle,
  resetTomSelect: webtrees.resetTomSelect,
  setColorTheme: webtrees.setColorTheme,
  watchForColorThemeChanges: webtrees.watchForColorThemeChanges,
});
