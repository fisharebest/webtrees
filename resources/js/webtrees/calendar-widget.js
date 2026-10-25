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
export function calLocalize(jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dec, sun, mon, tue, wed, thu, fri, sat, day) {
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
}

/**
 * @param {string} dateFieldId
 * @param {string} dateDivId
 * @param {Date} date
 * @returns {string}
 */
function calGenerateSelectorContent(dateFieldId, dateDivId, date) {
  let i, j;
  let content = '<table border="1"><tr>';
  content += '<td><select class="form-select" id="' + dateFieldId + '_daySelect" data-wt-calendar-update="1">';
  for (i = 1; i < 32; i++) {
    content += '<option value="' + i + '"';
    if (date.getDate() === i) {
      content += ' selected="selected"';
    }
    content += '>' + i + '</option>';
  }
  content += '</select></td>';
  content += '<td><select class="form-select" id="' + dateFieldId + '_monSelect" data-wt-calendar-update="1">';
  for (i = 1; i < 13; i++) {
    content += '<option value="' + i + '"';
    if (date.getMonth() + 1 === i) {
      content += ' selected="selected"';
    }
    content += '>' + monthLabels[i] + '</option>';
  }
  content += '</select></td>';
  content += '<td><input class="form-control" type="text" id="' + dateFieldId + '_yearInput" size="5" value="' + date.getFullYear() + '" data-wt-calendar-update="1" /></td></tr>';
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
      content += '><a href="#" data-wt-calendar-date="1" data-wt-calendar-year="' + tdate.getFullYear() + '" data-wt-calendar-month="' + tdate.getMonth() + '" data-wt-calendar-day="' + tdate.getDate() + '">';
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
function calSetDateField(dateFieldId, year, month, day) {
  let dateField = document.getElementById(dateFieldId);
  dateField.value = (day < 10 ? '0' : '') + day + ' ' + monthShort[month + 1] + ' ' + year;
  return false;
}

/**
 * @param {Element} container
 * @param {string} dateFieldId
 * @param {string} dateDivId
 */
function bindCalendarUpdateHandlers(container, dateFieldId, dateDivId) {
  container.querySelectorAll('[data-wt-calendar-update]').forEach((element) => {
    if (element instanceof HTMLInputElement || element instanceof HTMLSelectElement) {
      element.addEventListener('change', () => {
        calUpdateCalendar(dateFieldId, dateDivId);
      });
    }
  });

  container.querySelectorAll('[data-wt-calendar-date]').forEach((element) => {
    if (!(element instanceof HTMLAnchorElement)) {
      return;
    }

    element.addEventListener('click', (event) => {
      event.preventDefault();

      const year = Number(element.dataset.wtCalendarYear);
      const month = Number(element.dataset.wtCalendarMonth);
      const day = Number(element.dataset.wtCalendarDay);

      if (!Number.isFinite(year) || !Number.isFinite(month) || !Number.isFinite(day)) {
        throw new Error('Calendar date link is missing date data.');
      }

      calDateClicked(dateFieldId, dateDivId, year, month, day);
    });
  });
}

/**
 * @param {string} dateDivId
 * @param {string} dateFieldId
 * @returns {boolean}
 */
export function calendarWidget(dateDivId, dateFieldId) {
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
  bindCalendarUpdateHandlers(dateDiv, dateFieldId, dateDivId);
  if (dateDiv.style.visibility === 'hidden') {
    dateDiv.style.visibility = 'visible';
    return false;
  }
  if (dateDiv.style.visibility === 'hide') {
    dateDiv.style.visibility = 'show';
    return false;
  }

  return false;
}

/**
 * @param {string} dateFieldId
 * @param {string} dateDivId
 * @returns {boolean}
 */
export function calUpdateCalendar(dateFieldId, dateDivId) {
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
  bindCalendarUpdateHandlers(dateDiv, dateFieldId, dateDivId);

  return false;
}

/**
 * @param {string} dateFieldId
 * @param {string} dateDivId
 * @param {number} year
 * @param {number} month
 * @param {number} day
 * @returns {boolean}
 */
export function calDateClicked(dateFieldId, dateDivId, year, month, day) {
  calSetDateField(dateFieldId, year, month, day);
  calendarWidget(dateDivId, dateFieldId);
  return false;
}

/**
 * @param {Element} datefield
 * @param {string} dmy
 */
export function reformatDate(datefield, dmy) {
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
}

