/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

    const surname = trim(spfx + separator + surn);

    const name = surnameFirst ? slash + surname + slash + separator + givn : givn + separator + slash + surname + slash;

    return trim(npfx + separator + name + separator + nsfx);
  };

  // Insert text at the current cursor position in a text field.
  webtrees.pasteAtCursor = function (element, text) {
    if (element !== null) {
      const caret_pos = element.selectionStart + text.length;
      const textBefore = element.value.substring(0, element.selectionStart);
      const textAfter = element.value.substring(element.selectionEnd);
      element.value = textBefore + text + textAfter;
      element.setSelectionRange(caret_pos, caret_pos);
      element.focus();
    }
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
    if (datestr.match(/^Q ([1-4]) (\d\d\d\d)$/)) {
      datestr = 'BET ' + months[RegExp.$1 * 3 - 3] + ' ' + RegExp.$2 + ' AND ' + months[RegExp.$1 * 3 - 1] + ' ' + RegExp.$2;
    }

    // Shortcut for @#Dxxxxx@ 01 01 1400, etc.
    if (datestr.match(/^(@#DHIJRI@|HIJRI)( \d?\d )(\d?\d)( \d?\d?\d?\d)$/)) {
      datestr = '@#DHIJRI@' + RegExp.$2 + hijri_months[parseInt(RegExp.$3, 10) - 1] + RegExp.$4;
    }
    if (datestr.match(/^(@#DJALALI@|JALALI)( \d?\d )(\d?\d)( \d?\d?\d?\d)$/)) {
      datestr = '@#DJALALI@' + RegExp.$2 + jalali_months[parseInt(RegExp.$3, 10) - 1] + RegExp.$4;
    }
    if (datestr.match(/^(@#DHEBREW@|HEBREW)( \d?\d )(\d?\d)( \d?\d?\d?\d)$/)) {
      datestr = '@#DHEBREW@' + RegExp.$2 + hebrew_months[parseInt(RegExp.$3, 10) - 1] + RegExp.$4;
    }
    if (datestr.match(/^(@#DFRENCH R@|FRENCH)( \d?\d )(\d?\d)( \d?\d?\d?\d)$/)) {
      datestr = '@#DFRENCH R@' + RegExp.$2 + french_months[parseInt(RegExp.$3, 10) - 1] + RegExp.$4;
    }

    // All digit dates
    datestr = datestr.replace(/(\d\d)(\d\d)(\d\d)(\d\d)/g, function () {
      if (RegExp.$1 > '12' && RegExp.$3 <= '12' && RegExp.$4 <= '31') {
        return RegExp.$4 + ' ' + months[RegExp.$3 - 1] + ' ' + RegExp.$1 + RegExp.$2;
      }
      if (RegExp.$1 <= '31' && RegExp.$2 <= '12' && RegExp.$3 > '12') {
        return RegExp.$1 + ' ' + months[RegExp.$2 - 1] + ' ' + RegExp.$3 + RegExp.$4;
      }
      return RegExp.$1 + RegExp.$2 + RegExp.$3 + RegExp.$4;
    });

    // e.g. 17.11.1860, 2 4 1987, 3/4/2005, 1999-12-31. Use locale settings since DMY order is ambiguous.
    datestr = datestr.replace(/(\d+)([ ./-])(\d+)(\2)(\d+)/g, function () {
      let f1 = parseInt(RegExp.$1, 10);
      let f2 = parseInt(RegExp.$3, 10);
      let f3 = parseInt(RegExp.$5, 10);
      let yyyy = new Date().getFullYear();
      let yy = yyyy % 100;
      let cc = yyyy - yy;
      if ((dmy === 'DMY' || f1 > 13 && f3 > 31) && f1 <= 31 && f2 <= 12) {
        return f1 + ' ' + months[f2 - 1] + ' ' + (f3 >= 100 ? f3 : (f3 <= yy ? f3 + cc : f3 + cc - 100));
      }
      if ((dmy === 'MDY' || f2 > 13 && f3 > 31) && f1 <= 12 && f2 <= 31) {
        return f2 + ' ' + months[f1 - 1] + ' ' + (f3 >= 100 ? f3 : (f3 <= yy ? f3 + cc : f3 + cc - 100));
      }
      if ((dmy === 'YMD' || f1 > 31) && f2 <= 12 && f3 <= 31) {
        return f3 + ' ' + months[f2 - 1] + ' ' + (f1 >= 100 ? f1 : (f1 <= yy ? f1 + cc : f1 + cc - 100));
      }
      return RegExp.$1 + RegExp.$2 + RegExp.$3 + RegExp.$4 + RegExp.$5;
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

    /* Javascript calendar functions only work with precise gregorian dates "D M Y" or "Y" */
    let greg_regex = /(?:(\d*) ?(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) )?(\d+)/i;
    let date;
    if (greg_regex.exec(dateField.value)) {
      let day   = RegExp.$1 || '1';
      let month = RegExp.$2 || 'JAN'
      let year  = RegExp.$3;
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
    content += '<td><select class="form-control" id="' + dateFieldId + '_daySelect" onchange="return webtrees.calUpdateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');">';
    for (i = 1; i < 32; i++) {
      content += '<option value="' + i + '"';
      if (date.getDate() === i) {
        content += ' selected="selected"';
      }
      content += '>' + i + '</option>';
    }
    content += '</select></td>';
    content += '<td><select class="form-control" id="' + dateFieldId + '_monSelect" onchange="return webtrees.calUpdateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');">';
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
    let day = tdate.getDay();
    day = day - weekStart;
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
   * Persistent checkbox options to hide/show extra data.
   * @param {string} element_id
   */
  webtrees.persistentToggle = function (element_id) {
    const element = document.getElementById(element_id);
    const key = 'state-of-' + element_id;
    const state = localStorage.getItem(key);

    if (element instanceof HTMLInputElement && element.type === 'checkbox') {
      // Previously selected?
      if (state === 'true') {
        element.click();
      }

      // Remember state for the next page load.
      element.addEventListener('change', function () {
        localStorage.setItem(key, element.checked);
      });
    }
  };

  /**
   * @param {Element} field
   * @param {string} pos
   * @param {string} neg
   */
  function reformatLatLong (field, pos, neg) {
    // valid LATI or LONG according to Gedcom standard
    // pos (+) : N or E
    // neg (-) : S or W
    let txt = field.value.toUpperCase();
    txt = txt.replace(/(^\s*)|(\s*$)/g, ''); // trim
    txt = txt.replace(/ /g, ':'); // N12 34 ==> N12.34
    txt = txt.replace(/\+/g, ''); // +17.1234 ==> 17.1234
    txt = txt.replace(/-/g, neg); // -0.5698 ==> W0.5698
    txt = txt.replace(/,/g, '.'); // 0,5698 ==> 0.5698
    // 0°34'11 ==> 0:34:11
    txt = txt.replace(/\u00b0/g, ':'); // °
    txt = txt.replace(/\u0027/g, ':'); // '
    // 0:34:11.2W ==> W0.5698
    txt = txt.replace(/^([0-9]+):([0-9]+):([0-9.]+)(.*)/g, function ($0, $1, $2, $3, $4) {
      let n = parseFloat($1);
      n += ($2 / 60);
      n += ($3 / 3600);
      n = Math.round(n * 1E4) / 1E4;
      return $4 + n;
    });
    // 0:34W ==> W0.5667
    txt = txt.replace(/^([0-9]+):([0-9]+)(.*)/g, function ($0, $1, $2, $3) {
      let n = parseFloat($1);
      n += ($2 / 60);
      n = Math.round(n * 1E4) / 1E4;
      return $3 + n;
    });
    // 0.5698W ==> W0.5698
    txt = txt.replace(/(.*)(NSEW])$/g, '$2$1');
    // 17.1234 ==> N17.1234
    if (txt && txt.charAt(0) !== neg && txt.charAt(0) !== pos) {
      txt = pos + txt;
    }
    field.value = txt;
  }

  /**
   * @param {Element} field
   */
  webtrees.reformatLatitude = function (field) {
    return reformatLatLong(field, 'N', 'S');
  };

  /**
   * @param {Element} field
   */
  webtrees.reformatLongitude = function (field) {
    return reformatLatLong(field, 'E', 'W');
  };

  /**
   * Initialize autocomplete elements.
   * @param {string} selector
   */
  webtrees.autocomplete = function (selector) {
    // Use typeahead/bloodhound for autocomplete
    $(selector).each(function () {
      const that = this;
      $(this).typeahead(null, {
        display: 'value',
        limit: 10,
        minLength: 2,
        source: new Bloodhound({
          datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: {
            url: this.dataset.autocompleteUrl,
            replace: function (url, uriEncodedQuery) {
              if (that.dataset.autocompleteExtra === 'SOUR') {
                let row_group = that.closest('.form-group').previousElementSibling;
                while (row_group.querySelector('select') === null) {
                  row_group = row_group.previousElementSibling;
                }
                const element = row_group.querySelector('select');
                const extra   = element.options[element.selectedIndex].value.replace(/@/g, '');
                const symbol  = (url.indexOf("?") > 0) ? '&' : '?';
                return url.replace(/(%7B|{)query(%7D|})/, uriEncodedQuery) + symbol + 'extra=' + encodeURIComponent(extra);
              }
              return url.replace(/(%7B|{)query(%7D|})/, uriEncodedQuery);
            },
            wildcard: '{query}'
          }
        })
      });
    });
  };

  /**
   * Create a LeafletJS map from a list of providers/layers.
   * @param {string} id
   * @param {object} config
   * @returns Map
   */
  webtrees.buildLeafletJsMap = function (id, config) {
    const zoomControl = new L.control.zoom({
      zoomInTitle: config.i18n.zoomIn,
      zoomoutTitle: config.i18n.zoomOut,
    });

    let defaultLayer = null;

    for (let [, provider] of Object.entries(config.mapProviders)) {
      for (let [, child] of Object.entries(provider.children)) {
        if ('bingMapsKey' in child) {
          child.layer = L.tileLayer.bing(child);
        } else {
          child.layer = L.tileLayer(child.url, child);
        }
        if (provider.default && child.default) {
          defaultLayer = child.layer;
        }
      }
    }

    if (defaultLayer === null) {
      console.log('No default map layer defined - using the first one.');
      let defaultLayer = config.mapProviders[0].children[0].layer;
    }


    // Create the map with all controls and layers
    return L.map(id, {
      zoomControl: false,
    })
      .addControl(zoomControl)
      .addLayer(defaultLayer)
      .addControl(L.control.layers.tree(config.mapProviders, null, {
        closedSymbol: config.icons.expand,
        openedSymbol: config.icons.collapse,
      }));

  };
}(window.webtrees = window.webtrees || {}));

// Send the CSRF token on all AJAX requests
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name=csrf]').attr('content')
  }
});

/**
 * Initialisation
 */
$(function () {
  // Page elements that load automatically via AJAX.
  // This prevents bad robots from crawling resource-intensive pages.
  $('[data-ajax-url]').each(function () {
    $(this).load($(this).data('ajaxUrl'));
  });

  // Autocomplete
  webtrees.autocomplete('input[data-autocomplete-url]');

  // Select2 - activate autocomplete fields
  const lang = document.documentElement.lang;
  const select2_languages = {
    'zh-Hans': 'zh-CN',
    'zh-Hant': 'zh-TW'
  };
  $('select.select2').select2({
    language: select2_languages[lang] || lang,
    // Needed for elements that are initially hidden.
    width: '100%',
    // Do not escape - we do it on the server.
    escapeMarkup: function (x) {
      return x;
    }
  });

  // If we clear the select (using the "X" button), we need an empty value
  // (rather than no value at all) for (non-multiple) selects with name="array[]"
  $('select.select2:not([multiple])')
    .on('select2:unselect', function (evt) {
      $(evt.delegateTarget).html('<option value="" selected></option>');
    });

  // Datatables - locale aware sorting
  $.fn.dataTableExt.oSort['text-asc'] = function (x, y) {
    return x.localeCompare(y, document.documentElement.lang, { sensitivity: 'base' });
  };
  $.fn.dataTableExt.oSort['text-desc'] = function (x, y) {
    return y.localeCompare(x, document.documentElement.lang, { sensitivity: 'base' });
  };

  // DataTables - start hidden to prevent FOUC.
  $('table.datatables').each(function () {
    $(this).DataTable();
    $(this).removeClass('d-none');
  });

  // Save button state between pages
  document.querySelectorAll('[data-toggle=button][data-persist]').forEach((element) => {
    // Previously selected?
    if (localStorage.getItem('state-of-' + element.dataset.persist) === 'T') {
      element.click();
    }
    // Save state on change
    element.addEventListener('click', (event) => {
      // Event occurs *before* the state changes, so reverse T/F.
      localStorage.setItem('state-of-' + event.target.dataset.persist, event.target.classList.contains('active') ? 'F' : 'T');
    });
  });

  // Activate the on-screen keyboard
  let osk_focus_element;
  $('.wt-osk-trigger').click(function () {
    // When a user clicks the icon, set focus to the corresponding input
    osk_focus_element = document.getElementById($(this).data('id'));
    osk_focus_element.focus();
    $('.wt-osk').show();
  });
  $('.wt-osk-script-button').change(function () {
    $('.wt-osk-script').prop('hidden', true);
    $('.wt-osk-script-' + $(this).data('script')).prop('hidden', false);
  });
  $('.wt-osk-shift-button').click(function () {
    document.querySelector('.wt-osk-keys').classList.toggle('shifted');
  });
  $('.wt-osk-keys').on('click', '.wt-osk-key', function () {
    let key = $(this).contents().get(0).nodeValue;
    let shift_state = $('.wt-osk-shift-button').hasClass('active');
    let shift_key = $('sup', this)[0];
    if (shift_state && shift_key !== undefined) {
      key = shift_key.innerText;
    }
    webtrees.pasteAtCursor(osk_focus_element, key);
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
  const form = event.target;

  if (form.reportValidity()) {
    form.addEventListener('submit', (event) => {
      if (form.classList.contains('form-is-submitting')) {
        event.preventDefault();
      }

      form.classList.add('form-is-submitting');
    });
  }
});

// Convert data-confirm and data-post-url attributes into useful behavior.
document.addEventListener('click', (event) => {
  const target = event.target.closest('a,button');

  if (target === null) {
    return;
  }

  if ('confirm' in target.dataset && !confirm(target.dataset.confirm)) {
    event.preventDefault();
    return;
  }

  if ('postUrl' in target.dataset) {
    const token = document.querySelector('meta[name=csrf]').content;

    fetch(target.dataset.postUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-with': 'XMLHttpRequest',
      },
    }).then(() => {
      if ('reloadUrl' in target.dataset) {
        // Go somewhere else.  e.g. home page after logout.
        document.location = target.dataset.reloadUrl;
      } else {
        // Reload the current page. e.g. change language.
        document.location.reload();
      }
    }).catch((error) => {
      alert(error);
    });
  }
});
