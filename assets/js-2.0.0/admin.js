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

/**
 * Hide/show the feedback labels for a privacy option.
 *
 * @param id     the control to change
 * @param who    "visitors", "members" or "managers"
 * @param access true or false
 */
function setPrivacyFeedback (sel, who, access) {
  var formGroup = $(sel).closest('.form-group');

  if (access) {
    $('.' + who, formGroup).addClass('badge-success').removeClass('badge-default');
    $('.' + who + ' i', formGroup).addClass('fa-check').removeClass('fa-times');
  } else {
    $('.' + who, formGroup).addClass('badge-default').removeClass('badge-success');
    $('.' + who + ' i', formGroup).addClass('fa-times').removeClass('fa-check');
  }
}

/**
 * Update all the privacy feedback labels.
 */
function updatePrivacyFeedback () {
  var requireAuthentication = parseInt($('[name=REQUIRE_AUTHENTICATION]').val(), 10);
  var showDeadPeople = parseInt($('[name=SHOW_DEAD_PEOPLE]').val(), 10);
  var hideLivePeople = parseInt($('[name=HIDE_LIVE_PEOPLE]').val(), 10);
  var showLivingNames = parseInt($('[name=SHOW_LIVING_NAMES]').val(), 10);
  var showPrivateRelationships = parseInt($('[name=SHOW_PRIVATE_RELATIONSHIPS]').val(), 10);

  setPrivacyFeedback('[name=REQUIRE_AUTHENTICATION]', 'visitors', requireAuthentication === 0);
  setPrivacyFeedback('[name=REQUIRE_AUTHENTICATION]', 'members', true);

  setPrivacyFeedback('[name=SHOW_DEAD_PEOPLE]', 'visitors', requireAuthentication === 0 && (showDeadPeople >= 2 || hideLivePeople === 0));
  setPrivacyFeedback('[name=SHOW_DEAD_PEOPLE]', 'members', showDeadPeople >= 1 || hideLivePeople === 0);

  setPrivacyFeedback('[name=HIDE_LIVE_PEOPLE]', 'visitors', requireAuthentication === 0 && hideLivePeople === 0);
  setPrivacyFeedback('[name=HIDE_LIVE_PEOPLE]', 'members', true);

  setPrivacyFeedback('[name=SHOW_LIVING_NAMES]', 'visitors', requireAuthentication === 0 && showLivingNames >= 2);
  setPrivacyFeedback('[name=SHOW_LIVING_NAMES]', 'members', showLivingNames >= 1);
  setPrivacyFeedback('[name=SHOW_LIVING_NAMES]', 'managers', showLivingNames >= 0);

  setPrivacyFeedback('[name=SHOW_PRIVATE_RELATIONSHIPS]', 'visitors', requireAuthentication === 0 && showPrivateRelationships >= 1);
  setPrivacyFeedback('[name=SHOW_PRIVATE_RELATIONSHIPS]', 'members', showPrivateRelationships >= 1);
}

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
  // Activate the privacy feedback labels.
  updatePrivacyFeedback();
  $('[name=REQUIRE_AUTHENTICATION], [name=HIDE_LIVE_PEOPLE], [name=SHOW_DEAD_PEOPLE], [name=SHOW_LIVING_NAMES], [name=SHOW_PRIVATE_RELATIONSHIPS]').on('change', function () {
    updatePrivacyFeedback();
  });

  // Import from file on server/computer
  $('#import-server-file').on('focus', function () {
    $('#import-server').prop('checked', true);
  });
  $('#import-computer-file').on('focus', function () {
    $('#import-computer').prop('checked', true);
  });
});
