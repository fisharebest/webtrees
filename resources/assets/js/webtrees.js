/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

function expand_layer (sid) {
  $('#' + sid + '_img').toggleClass('icon-plus icon-minus');
  $('#' + sid).slideToggle('fast');
  $('#' + sid + '-alt').toggle(); // hide something when we show the layer - and vice-versa
  return false;
}

// Accept the changes to a record - and reload the page
function accept_changes(xref, ged) {
  $.post('index.php', {
    route: 'accept-changes',
    xref: xref,
    ged: ged,
  },
    function () {
      location.reload();
    });
  return false;
}

// Reject the changes to a record - and reload the page
function reject_changes (xref, ged) {
  $.post('index.php', {
    route: 'reject-changes',
    xref: xref,
    ged: ged,
  },
    function () {
      location.reload();
    });
  return false;
}

// Delete a record - and reload the page
function delete_record (message, xref, gedcom) {
  if (confirm(message)) {
    $.post('index.php', {
      route: 'delete-record',
      xref: xref,
      ged: gedcom,
    },
    function () {
      location.reload();
    });
  }
  return false;
}

// Delete a fact - and reload the page
function delete_fact (message, ged, xref, fact_id) {
  if (confirm(message)) {
    $.post('index.php', {
      route: 'delete-fact',
      xref: xref,
      fact_id: fact_id,
      ged: ged
    },
    function () {
      location.reload();
    });
  }
  return false;
}

// Copy a fact to the clipboard
function copy_fact (ged, xref, fact_id) {
  $.post('index.php', {
    route: 'copy-fact',
    xref: xref,
    fact_id: fact_id,
    ged: ged,
  },
    function () {
      location.reload();
    });
  return false;
}

// Paste a fact from the clipboard
function paste_fact (ged, xref, element) {
  $.post('index.php', {
    route: 'paste-fact',
    xref: xref,
    fact_id: $(element).val(), // element is the <select> containing the option
    ged: ged,
  },
    function () {
      location.reload();
    });
  return false;
}

// Delete a user - and reload the page
function delete_user (message, user_id) {
  if (confirm(message)) {
    $.post('index.php', {
      route: 'delete-user',
      user_id: user_id,
    },
      function () {
        location.reload();
      });
  }
  return false;
}

// Masquerade as another user - and reload the page.
function masquerade (user_id) {
  $.post('index.php', {
    route: 'masquerade',
    user_id: user_id,
  },
    function () {
      location.reload();
    });
  return false;
}

var pastefield;
function addmedia_links (field, iid, iname) {
  pastefield = field;
  insertRowToTable(iid, iname);
  return false;
}

function valid_date (datefield) {
  var months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
  var hijri_months = ['MUHAR', 'SAFAR', 'RABIA', 'RABIT', 'JUMAA', 'JUMAT', 'RAJAB', 'SHAAB', 'RAMAD', 'SHAWW', 'DHUAQ', 'DHUAH'];
  var hebrew_months = ['TSH', 'CSH', 'KSL', 'TVT', 'SHV', 'ADR', 'ADS', 'NSN', 'IYR', 'SVN', 'TMZ', 'AAV', 'ELL'];
  var french_months = ['VEND', 'BRUM', 'FRIM', 'NIVO', 'PLUV', 'VENT', 'GERM', 'FLOR', 'PRAI', 'MESS', 'THER', 'FRUC', 'COMP'];
  var jalali_months = ['FARVA', 'ORDIB', 'KHORD', 'TIR', 'MORDA', 'SHAHR', 'MEHR', 'ABAN', 'AZAR', 'DEY', 'BAHMA', 'ESFAN'];

  var datestr = datefield.value;
  // if a date has a date phrase marked by () this has to be excluded from altering
  var datearr = datestr.split('(');
  var datephrase = '';
  if (datearr.length > 1) {
    datestr = datearr[0];
    datephrase = datearr[1];
  }

  // Gedcom dates are upper case
  datestr = datestr.toUpperCase();
  // Gedcom dates have no leading/trailing/repeated whitespace
  datestr = datestr.replace(/\s+/, ' ');
  datestr = datestr.replace(/(^\s)|(\s$)/, '');
  // Gedcom dates have spaces between letters and digits, e.g. "01JAN2000" => "01 JAN 2000"
  datestr = datestr.replace(/(\d)([A-Z])/, '$1 $2');
  datestr = datestr.replace(/([A-Z])(\d)/, '$1 $2');

  // Shortcut for quarter format, "Q1 1900" => "BET JAN 1900 AND MAR 1900". See [ 1509083 ]
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

  // e.g. 17.11.1860, 03/04/2005 or 1999-12-31. Use locale settings where DMY order is ambiguous.
  var qsearch = /^([^\d]*)(\d+)[^\d](\d+)[^\d](\d+)$/i;
  if (qsearch.exec(datestr)) {
    var f0 = RegExp.$1;
    var f1 = parseInt(RegExp.$2, 10);
    var f2 = parseInt(RegExp.$3, 10);
    var f3 = parseInt(RegExp.$4, 10);
    var dmy = 'DMY';
    if (typeof (locale_date_format) !== 'undefined') {
      if (locale_date_format === 'MDY' || locale_date_format === 'YMD') {
        dmy = locale_date_format;
      }
    }
    var yyyy = new Date().getFullYear();
    var yy = yyyy % 100;
    var cc = yyyy - yy;
    if (dmy === 'DMY' && f1 <= 31 && f2 <= 12 || f1 > 13 && f1 <= 31 && f2 <= 12 && f3 > 31) {
      datestr = f0 + f1 + ' ' + months[f2 - 1] + ' ' + (f3 >= 100 ? f3 : (f3 <= yy ? f3 + cc : f3 + cc - 100));
    } else {
      if (dmy === 'MDY' && f1 <= 12 && f2 <= 31 || f2 > 13 && f2 <= 31 && f1 <= 12 && f3 > 31) {
        datestr = f0 + f2 + ' ' + months[f1 - 1] + ' ' + (f3 >= 100 ? f3 : (f3 <= yy ? f3 + cc : f3 + cc - 100));
      } else {
        if (dmy === 'YMD' && f2 <= 12 && f3 <= 31 || f3 > 13 && f3 <= 31 && f2 <= 12 && f1 > 31) {
          datestr = f0 + f3 + ' ' + months[f2 - 1] + ' ' + (f1 >= 100 ? f1 : (f1 <= yy ? f1 + cc : f1 + cc - 100));
        }
      }
    }
  }

  // Shortcuts for date ranges
  datestr = datestr.replace(/^[>]([\w ]+)$/, 'AFT $1');
  datestr = datestr.replace(/^[<]([\w ]+)$/, 'BEF $1');
  datestr = datestr.replace(/^([\w ]+)[-]$/, 'FROM $1');
  datestr = datestr.replace(/^[-]([\w ]+)$/, 'TO $1');
  datestr = datestr.replace(/^[~]([\w ]+)$/, 'ABT $1');
  datestr = datestr.replace(/^[*]([\w ]+)$/, 'EST $1');
  datestr = datestr.replace(/^[#]([\w ]+)$/, 'CAL $1');
  datestr = datestr.replace(/^([\w ]+) ?- ?([\w ]+)$/, 'BET $1 AND $2');
  datestr = datestr.replace(/^([\w ]+) ?~ ?([\w ]+)$/, 'FROM $1 TO $2');

  // Convert full months to short months
  datestr = datestr.replace(/(JANUARY)/, 'JAN');
  datestr = datestr.replace(/(FEBRUARY)/, 'FEB');
  datestr = datestr.replace(/(MARCH)/, 'MAR');
  datestr = datestr.replace(/(APRIL)/, 'APR');
  datestr = datestr.replace(/(MAY)/, 'MAY');
  datestr = datestr.replace(/(JUNE)/, 'JUN');
  datestr = datestr.replace(/(JULY)/, 'JUL');
  datestr = datestr.replace(/(AUGUST)/, 'AUG');
  datestr = datestr.replace(/(SEPTEMBER)/, 'SEP');
  datestr = datestr.replace(/(OCTOBER)/, 'OCT');
  datestr = datestr.replace(/(NOVEMBER)/, 'NOV');
  datestr = datestr.replace(/(DECEMBER)/, 'DEC');

  // Americans frequently enter dates as SEP 20, 1999
  // No need to internationalise this, as this is an english-language issue
  datestr = datestr.replace(/(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\.? (\d\d?)[, ]+(\d\d\d\d)/, '$2 $1 $3');

  // Apply leading zero to day numbers
  datestr = datestr.replace(/(^| )(\d [A-Z]{3,5} \d{4})/, '$10$2');

  if (datephrase) {
    datestr = datestr + ' (' + datephrase;
  }
  // Only update it if is has been corrected - otherwise input focus
  // moves to the end of the field unnecessarily
  if (datefield.value !== datestr) {
    datefield.value = datestr;
  }
}

var menutimeouts = [];

function show_submenu (elementid, parentid) {
  var pagewidth = document.body.scrollWidth + document.documentElement.scrollLeft;
  var element = document.getElementById(elementid);

  if (element && element.style) {
    if (document.all) {
      pagewidth = document.body.offsetWidth;
    } else {
      pagewidth = document.body.scrollWidth + document.documentElement.scrollLeft - 55;
      if (document.documentElement.dir === 'rtl') {
        boxright = element.offsetLeft + element.offsetWidth + 10;
      }
    }

    // -- make sure the submenu is the size of the largest child
    var maxwidth = 0;
    var count = element.childNodes.length;
    for (var i = 0; i < count; i++) {
      var child = element.childNodes[i];
      if (child.offsetWidth > maxwidth + 5) {
        maxwidth = child.offsetWidth;
      }
    }
    if (element.offsetWidth < maxwidth) {
      element.style.width = maxwidth + 'px';
    }
    var pelement, boxright;
    pelement = document.getElementById(parentid);
    if (pelement) {
      element.style.left = pelement.style.left;
      boxright = element.offsetLeft + element.offsetWidth + 10;
      if (boxright > pagewidth) {
        var menuleft = pagewidth - element.offsetWidth;
        element.style.left = menuleft + 'px';
      }
    }

    if (element.offsetLeft < 0) {
      element.style.left = '0px';
    }

    // -- put scrollbars on really long menus
    if (element.offsetHeight > 500) {
      element.style.height = '400px';
      element.style.overflow = 'auto';
    }

    element.style.visibility = 'visible';
  }
  clearTimeout(menutimeouts[elementid]);
  menutimeouts[elementid] = null;
}

function hide_submenu (elementid) {
  if (typeof menutimeouts[elementid] !== 'number') {
    return;
  }
  var element = document.getElementById(elementid);
  if (element && element.style) {
    element.style.visibility = 'hidden';
  }
  clearTimeout(menutimeouts[elementid]);
  menutimeouts[elementid] = null;
}

function timeout_submenu (elementid) {
  if (typeof menutimeouts[elementid] !== 'number') {
    menutimeouts[elementid] = setTimeout("hide_submenu('" + elementid + "')", 100);
  }
}

function statusDisable (sel) {
  var cbox = document.getElementById(sel);
  cbox.checked = false;
  cbox.disabled = true;
}

function statusEnable (sel) {
  var cbox = document.getElementById(sel);
  cbox.disabled = false;
}

function statusChecked (sel) {
  var cbox = document.getElementById(sel);
  cbox.checked = true;
}

var monthLabels = [];
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

var monthShort = [];
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

var daysOfWeek = [];
daysOfWeek[0] = 'S';
daysOfWeek[1] = 'M';
daysOfWeek[2] = 'T';
daysOfWeek[3] = 'W';
daysOfWeek[4] = 'T';
daysOfWeek[5] = 'F';
daysOfWeek[6] = 'S';

var weekStart = 0;

function cal_setMonthNames (jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dec) {
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
}

function cal_setDayHeaders (sun, mon, tue, wed, thu, fri, sat) {
  daysOfWeek[0] = sun;
  daysOfWeek[1] = mon;
  daysOfWeek[2] = tue;
  daysOfWeek[3] = wed;
  daysOfWeek[4] = thu;
  daysOfWeek[5] = fri;
  daysOfWeek[6] = sat;
}

function cal_setWeekStart (day) {
  if (day >= 0 && day < 7) {
    weekStart = day;
  }
}

function calendarWidget (dateDivId, dateFieldId) {
  var dateDiv = document.getElementById(dateDivId);
  var dateField = document.getElementById(dateFieldId);

  if (dateDiv.style.visibility === 'visible') {
    dateDiv.style.visibility = 'hidden';
    return false;
  }
  if (dateDiv.style.visibility === 'show') {
    dateDiv.style.visibility = 'hide';
    return false;
  }

  /* Javascript calendar functions only work with precise gregorian dates "D M Y" or "Y" */
  var greg_regex = /((\d+ (JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) )?\d+)/i;
  var date;
  if (greg_regex.exec(dateField.value)) {
    date = new Date(RegExp.$1);
  } else {
    date = new Date();
  }

  dateDiv.innerHTML = cal_generateSelectorContent(dateFieldId, dateDivId, date);
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

function cal_generateSelectorContent (dateFieldId, dateDivId, date) {
  var i, j;
  var content = '<table border="1"><tr>';
  content += '<td><select class="form-control" id="' + dateFieldId + '_daySelect" onchange="return cal_updateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');">';
  for (i = 1; i < 32; i++) {
    content += '<option value="' + i + '"';
    if (date.getDate() === i) {
      content += ' selected="selected"';
    }
    content += '>' + i + '</option>';
  }
  content += '</select></td>';
  content += '<td><select class="form-control" id="' + dateFieldId + '_monSelect" onchange="return cal_updateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');">';
  for (i = 1; i < 13; i++) {
    content += '<option value="' + i + '"';
    if (date.getMonth() + 1 === i) {
      content += ' selected="selected"';
    }
    content += '>' + monthLabels[i] + '</option>';
  }
  content += '</select></td>';
  content += '<td><input class="form-control" type="text" id="' + dateFieldId + '_yearInput" size="5" value="' + date.getFullYear() + '" onchange="return cal_updateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');" /></td></tr>';
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

  var tdate = new Date(date.getFullYear(), date.getMonth(), 1);
  var day = tdate.getDay();
  day = day - weekStart;
  var daymilli = 1000 * 60 * 60 * 24;
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
      content += '><a href="#" onclick="return cal_dateClicked(\'' + dateFieldId + '\', \'' + dateDivId + '\', ' + tdate.getFullYear() + ', ' + tdate.getMonth() + ', ' + tdate.getDate() + ');">';
      content += tdate.getDate();
      content += '</a></td>';
      var datemilli = tdate.getTime() + daymilli;
      tdate = new Date(datemilli);
    }
    content += '</tr>';
  }
  content += '</table>';
  content += '</td></tr>';
  content += '</table>';

  return content;
}

function cal_setDateField (dateFieldId, year, month, day) {
  var dateField = document.getElementById(dateFieldId);
  if (!dateField) {
    return false;
  }
  if (day < 10) {
    day = '0' + day;
  }
  dateField.value = day + ' ' + monthShort[month + 1] + ' ' + year;
  return false;
}

function cal_updateCalendar (dateFieldId, dateDivId) {
  var dateSel = document.getElementById(dateFieldId + '_daySelect');
  if (!dateSel) {
    return false;
  }
  var monthSel = document.getElementById(dateFieldId + '_monSelect');
  if (!monthSel) {
    return false;
  }
  var yearInput = document.getElementById(dateFieldId + '_yearInput');
  if (!yearInput) {
    return false;
  }

  var month = parseInt(monthSel.options[monthSel.selectedIndex].value, 10);
  month = month - 1;

  var date = new Date(yearInput.value, month, dateSel.options[dateSel.selectedIndex].value);
  cal_setDateField(dateFieldId, date.getFullYear(), date.getMonth(), date.getDate());

  var dateDiv = document.getElementById(dateDivId);
  if (!dateDiv) {
    alert('no dateDiv ' + dateDivId);
    return false;
  }
  dateDiv.innerHTML = cal_generateSelectorContent(dateFieldId, dateDivId, date);

  return false;
}

function cal_dateClicked (dateFieldId, dateDivId, year, month, day) {
  cal_setDateField(dateFieldId, year, month, day);
  calendarWidget(dateDivId, dateFieldId);
  return false;
}

function openerpasteid (id) {
  if (window.opener.paste_id) {
    window.opener.paste_id(id);
  }
  window.close();
}

function paste_id (value) {
  pastefield.value = value;
}

function pastename (name) {
  if (nameElement) {
    nameElement.innerHTML = name;
  }
  if (remElement) {
    remElement.style.display = 'block';
  }
}

function paste_char (value) {
  if (document.selection) {
    // IE
    pastefield.focus();
    document.selection.createRange().text = value;
  } else if (pastefield.selectionStart || pastefield.selectionStart === 0) {
    // Mozilla/Chrome/Safari
    pastefield.value =
      pastefield.value.substring(0, pastefield.selectionStart) +
      value +
      pastefield.value.substring(pastefield.selectionEnd, pastefield.value.length);
    pastefield.selectionStart = pastefield.selectionEnd = pastefield.selectionStart + value.length;
  } else {
    // Fallback? - just append
    pastefield.value += value;
  }

  if (pastefield.id === 'NPFX' || pastefield.id === 'GIVN' || pastefield.id === 'SPFX' || pastefield.id === 'SURN' || pastefield.id === 'NSFX') {
    updatewholename();
  }
}

function ilinkitem (mediaid, type, ged) {
  window.open('inverselink.php?mediaid=' + encodeURIComponent(mediaid) + '&linkto=' + encodeURIComponent(type) + '&ged=' + encodeURIComponent(ged), '_blank', find_window_specs);
  return false;
}

/**
 * Persistant checkbox options to hide/show extra data.

 * @param checkbox_id
 * @param data_selector
 */
function persistent_toggle (checkbox_id, data_selector) {
  var checkbox = document.getElementById(checkbox_id);
  var elements = document.querySelectorAll(data_selector);
  var display = localStorage.getItem(checkbox_id);

  if (!checkbox) {
    return;
  }

  if (display !== '') {
    display = 'none';
  }

  checkbox.checked = (display === '');
  for (var i = 0; i < elements.length; ++i) {
    elements[i].style.display = display;
  }

  checkbox.addEventListener('click', function () {
    console.log(display);
    display = (display === '' ? 'none' : '');
    localStorage.setItem(checkbox_id, display);
    for (var i = 0; i < elements.length; ++i) {
      elements[i].style.display = display;
    }
  });
}

function valid_lati_long (field, pos, neg) {
  // valid LATI or LONG according to Gedcom standard
  // pos (+) : N or E
  // neg (-) : S or W
  var txt = field.value.toUpperCase();
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
    var n = parseFloat($1);
    n += ($2 / 60);
    n += ($3 / 3600);
    n = Math.round(n * 1E4) / 1E4;
    return $4 + n;
  });
  // 0:34W ==> W0.5667
  txt = txt.replace(/^([0-9]+):([0-9]+)(.*)/g, function ($0, $1, $2, $3) {
    var n = parseFloat($1);
    n += ($2 / 60);
    n = Math.round(n * 1E4) / 1E4;
    return $3 + n;
  });
  // 0.5698W ==> W0.5698
  txt = txt.replace(/(.*)([N|S|E|W]+)$/g, '$2$1');
  // 17.1234 ==> N17.1234
  if (txt && txt.charAt(0) !== neg && txt.charAt(0) !== pos) {
    txt = pos + txt;
  }
  field.value = txt;
}

// This is the default way for webtrees to show image galleries.
// Custom themes may use a different viewer.
function activate_colorbox (config) {
  $.extend($.colorbox.settings, {
    // Don't scroll window with document
    fixed: true,
    current: '',
    previous: '\uf048',
    next: '\uf051',
    slideshowStart: '\uf04b',
    slideshowStop: '\uf04c',
    close: '\uf00d'
  });
  if (config) {
    $.extend($.colorbox.settings, config);
  }

  // Trigger an event when we click on an (any) image
  $('body').on('click', 'a.gallery', function () {
    // Enable colorbox for images
    $('a[type^=image].gallery').colorbox({
      photo: true,
      maxWidth: '95%',
      maxHeight: '95%',
      rel: 'gallery', // Turn all images on the page into a slideshow
      slideshow: true,
      slideshowAuto: false,
      // Add wheelzoom to the displayed image
      onComplete: function () {
        // Disable click on image triggering next image
        // https://github.com/jackmoore/colorbox/issues/668
        $('.cboxPhoto').unbind('click');

        wheelzoom(document.querySelectorAll('.cboxPhoto'));
      }
    });

    // Enable colorbox for audio using <audio></audio>, where supported
    // $('html.video a[type^=video].gallery').colorbox({
    //  rel:         'nofollow' // Slideshows are just for images
    // });

    // Enable colorbox for video using <video></video>, where supported
    // $('html.audio a[type^=audio].gallery').colorbox({
    //  rel:         'nofollow', // Slideshows are just for images
    // });

    // Allow all other media types remain as download links
  });
}

// Initialize autocomplete elements.
function autocomplete (selector) {
  // Use typeahead/bloodhound for autocomplete
  $(selector).each(function() {
    $(this).typeahead(null, {
      display: 'value',
      source: new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
          url: this.dataset.autocompleteUrl,
          wildcard: 'QUERY'
        }
      })
    });
  });
}

/* Show / Hide event data for boxes used on charts and elsewhere */
$('body').on('click', '.iconz', function (e) {
  'use strict';
  e.stopPropagation();

  var wrapper = $(this).closest('.person_box_template'),
    inout = wrapper.find('.inout'),
    inout2 = wrapper.find('.inout2'),
    namedef = wrapper.find('.namedef'),
    basestyle = wrapper.attr('class').match(/(box-style[0-2])/)[1];

  function showDetails () {
    wrapper.parent().css('z-index', 100);
    toggleExpanded();
    namedef.addClass('nameZoom');
    inout2.hide(0, function () {
      inout.slideDown();
    });
  }

  function hideDetails () {
    inout.slideUp(function () {
      inout2.show(0);
      namedef.removeClass('nameZoom');
      toggleExpanded();
      wrapper.parent().css('z-index', '');
    });
  }

  function toggleExpanded () {
    wrapper.toggleClass(function () {
      return basestyle + ' ' + basestyle + '-expanded';
    });
  }

  if (!inout.text().length) {
    wrapper.css('cursor', 'progress');
    inout.load('index.php', {route: 'expand-chart-box', xref: wrapper.data('xref'), ged: wrapper.data('tree')}, function () {
      wrapper.css('cursor', '');
      showDetails();
    });
  } else {
    if (wrapper.hasClass(basestyle)) {
      showDetails();
    } else {
      hideDetails();
    }
  }
  wrapper.find('.iconz').toggleClass('icon-zoomin icon-zoomout');
});

/**
 * Insert text at the current cursor position in an input field.
 *
 * @param e The input element.
 * @param t The text to insert.
 */
function insertTextAtCursor (e, t) {
  var scrollTop = e.scrollTop;
  var selectionStart = e.selectionStart;
  var prefix = e.value.substring(0, selectionStart);
  var suffix = e.value.substring(e.selectionEnd, e.value.length);
  e.value = prefix + t + suffix;
  e.selectionStart = selectionStart + t.length;
  e.selectionEnd = e.selectionStart;
  e.focus();
  e.scrollTop = scrollTop;
}

// Send the CSRF token on all AJAX requests
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name=csrf]').attr('content')
  }
});

// Initialisation
$(function () {
  // Page elements that load automaticaly via AJAX.
  // This prevents bad robots from crawling resource-intensive pages.
  $("[data-ajax-url]").each(function () {
    $(this).load($(this).data('ajaxUrl'));
	});

  // Bootstrap tabs - load content dynamically using AJAX
  $('a[data-toggle="tab"][data-href]').on('show.bs.tab', function () {
    $(this.getAttribute('href') + ':empty').load($(this).data('href'));
  });

  // Select2 - format entries in the select list
  function templateOptionForSelect2(data) {
    if (data.loading) {
      // If we're waiting for the server, this will be a "waiting..." message
     return data.text;
    } else {
      // The response from the server is already in HTML, so no need to format it here.
      return data.text;
    }
  }

  // Autocomplete
  autocomplete('input[data-autocomplete-url]');

  // Select2 - activate autocomplete fields
  $('select.select2').select2({
    // Do not escape.
    escapeMarkup: function (x) { return x }
    // Same formatting for both selections and rsult
    //templateResult: templateOptionForSelect2,
    //templateSelection: templateOptionForSelect2
  });

  // If we clear the select (using the "X" button), we need an empty
  // value (rather than no value at all) for inputs with name="array[]"
	$('select.select2').on('select2:unselect', function (evt) {
    $(evt.delegateTarget).append('<option value="" selected="selected"></option>');
	});

  // Datatables - locale aware sorting
  $.fn.dataTableExt.oSort['text-asc'] = function (x, y) {
    return x.localeCompare(y, document.documentElement.lang, {'sensitivity': 'base'});
  };
  $.fn.dataTableExt.oSort['text-desc'] = function (x, y) {
    return y.localeCompare(x, document.documentElement.lang, {'sensitivity': 'base'});
  };

  // DataTables - start hidden to prevent FOUC.
  $('table.datatables').each(function () { $(this).DataTable(); $(this).show(); });

  // Create a new record while editing an existing one.
  // Paste the XREF and description into the Select2 element.
  $('.wt-modal-create-record').on('show.bs.modal', function (event) {
    // Find the element ID that needs to be updated with the new value.
    $('form', $(this)).data('element-id', $(event.relatedTarget).data('element-id'));
    $('form .form-group input:first', $(this)).focus();
  });

  // Submit the modal form using AJAX, and paste the returned record ID/NAME into the parent form.
  $('.wt-modal-create-record form').on('submit', function (event) {
    event.preventDefault();
    var elementId = $(this).data('element-id');
    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: new FormData(this),
      async: false,
      cache: false,
      contentType: false,
      processData: false,
      success: function (data) {
        $('#' + elementId).select2().empty().append(new Option(data.text, data.id)).val(data.id).trigger('change');
      },
      failure: function (data) {
        alert(data.error_message);
      }
    });
    // Clear the form
    this.reset();
    // Close the modal
    $(this).closest('.wt-modal-create-record').modal('hide');
  });

  // Activate the langauge selection menu.
  $('.menu-language').on('click', '[data-language]', function () {
    $.post('index.php', {
      route: 'language',
      language: $(this).data('language')
    }, function () {
      window.location.reload();
    });

    return false;
  });

  // Activate the theme selection menu.
  $('.menu-theme').on('click', '[data-theme]', function () {
    $.post('index.php', {
      route: 'theme',
      theme: $(this).data('theme')
    }, function () {
      window.location.reload();
    });

    return false;
  });

  // Activate the on-screen keyboard
  var osk_focus_element;
  $('.wt-osk-trigger').click(function () {
    // When a user clicks the icon, set focus to the corresponding input
    osk_focus_element = document.getElementById($(this).data('id'));
    osk_focus_element.focus();
    $('.wt-osk').show();

  });

  $('.wt-osk-script-button').change(function() {
    $('.wt-osk-script').prop('hidden', true);
    $('.wt-osk-script-' + $(this).data('script')).prop('hidden', false);
  });
  $('.wt-osk-shift-button').click(function () {
    document.querySelector('.wt-osk-keys').classList.toggle('shifted');
  });
  $('.wt-osk-keys').on('click', '.wt-osk-key', function() {
    var key = $(this).contents().get(0).nodeValue;
    var shift_state = $('.wt-osk-shift-button').hasClass('active');
    var shift_key = $('sup', this)[0];
    if (shift_state && shift_key !== undefined) {
      key = shift_key.innerText;
    }
    if (osk_focus_element !== null) {
      var cursorPos = osk_focus_element.selectionStart;
      var v = osk_focus_element.value;
      var textBefore = v.substring(0,  cursorPos);
      var textAfter  = v.substring(cursorPos, v.length);
      osk_focus_element.value= textBefore + key + textAfter;
      if ($('.wt-osk-pin-button').hasClass('active') === false) {
        $('.wt-osk').hide();
      }
    }
  });

  $('.wt-osk-close').on('click', function () {
    $('.wt-osk').hide();
  });
});
