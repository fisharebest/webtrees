/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

// Onsubmit validation for the import/upload GEDCOM form
function checkGedcomImportForm (message) {
  var oldFile = $('#gedcom_filename').val();
  var method = $('input[name=action]:checked').val();
  var newFile = method === 'replace_import' ? $('#import-server-file').val() : $('#import-computer-file').val();

// Some browsers include c:\fakepath\ in the filename.
  newFile = newFile.replace(/.*[/\\]/, '');
  if (newFile !== oldFile && oldFile !== '') {
    return window.confirm(message);
  } else {
    return true;
  }
}

/**
 * Add handlers to various screen elements
 */
$(document).ready(function () {
  // Import from file on server/computer
  $('#import-server-file').on('focus', function () {
    $('#import-server').prop('checked', true);
  });
  $('#import-computer-file').on('focus', function () {
    $('#import-computer').prop('checked', true);
  });
});
