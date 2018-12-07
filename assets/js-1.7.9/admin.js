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

/**
 * Hide/show the feedback labels for a privacy option.
 *
 * @param id     the control to change
 * @param who    "visitors", "members" or "managers"
 * @param access true or false
 */
function setPrivacyFeedback(sel, who, access) {
    var form_group = jQuery(sel).closest(".form-group");

    if (access) {
        jQuery("." + who, form_group).addClass("label-success").removeClass("label-default");
        jQuery("." + who + " i", form_group).addClass("fa-check").removeClass("fa-times");
    } else {
        jQuery("." + who, form_group).addClass("label-default").removeClass("label-success");
        jQuery("." + who + " i", form_group).addClass("fa-times").removeClass("fa-check");
    }
}

/**
 * Update all the privacy feedback labels.
 */
function updatePrivacyFeedback() {
    var require_authentication = parseInt(jQuery("[name=REQUIRE_AUTHENTICATION]").val(), 10);
    var show_dead_people = parseInt(jQuery("[name=SHOW_DEAD_PEOPLE]").val(), 10);
    var hide_live_people = parseInt(jQuery("[name=HIDE_LIVE_PEOPLE]").val(), 10);
    var show_living_names = parseInt(jQuery("[name=SHOW_LIVING_NAMES]").val(), 10);
    var show_private_relationships = parseInt(jQuery("[name=SHOW_PRIVATE_RELATIONSHIPS]").val(), 10);

    setPrivacyFeedback("[name=REQUIRE_AUTHENTICATION]", "visitors", require_authentication === 0);
    setPrivacyFeedback("[name=REQUIRE_AUTHENTICATION]", "members", true);

    setPrivacyFeedback("[name=SHOW_DEAD_PEOPLE]", "visitors", require_authentication === 0 && (show_dead_people >= 2 || hide_live_people === 0));
    setPrivacyFeedback("[name=SHOW_DEAD_PEOPLE]", "members", show_dead_people >= 1 || hide_live_people === 0);

    setPrivacyFeedback("[name=HIDE_LIVE_PEOPLE]", "visitors", require_authentication === 0 && hide_live_people === 0);
    setPrivacyFeedback("[name=HIDE_LIVE_PEOPLE]", "members", true);

    setPrivacyFeedback("[name=SHOW_LIVING_NAMES]", "visitors", require_authentication === 0 && show_living_names >= 2);
    setPrivacyFeedback("[name=SHOW_LIVING_NAMES]", "members", show_living_names >= 1);
    setPrivacyFeedback("[name=SHOW_LIVING_NAMES]", "managers", show_living_names >= 0);

    setPrivacyFeedback("[name=SHOW_PRIVATE_RELATIONSHIPS]", "visitors", require_authentication === 0 && show_private_relationships >= 1);
    setPrivacyFeedback("[name=SHOW_PRIVATE_RELATIONSHIPS]", "members", show_private_relationships >= 1);
}

// Onsubmit validation for the import/upload GEDCOM form
function checkGedcomImportForm(message) {
    var old_file = jQuery("#gedcom_filename").val();
    var method   = jQuery("input[name=action]:checked").val();
    var new_file = method === "replace_import" ? jQuery("#import-server-file").val() : jQuery("#import-computer-file").val();

    // Some browsers include c:\fakepath\ in the filename.
    new_file = new_file.replace(/.*[\/\\]/, '');
    if (new_file !== old_file && old_file !== '') {
        return confirm(message);
    } else {
        return true;
    }
}

/**
 * Add handlers to various screen elements
 */
jQuery(document).ready(function() {
    // Activate the privacy feedback labels.
    updatePrivacyFeedback();
    jQuery("[name=REQUIRE_AUTHENTICATION], [name=HIDE_LIVE_PEOPLE], [name=SHOW_DEAD_PEOPLE], [name=SHOW_LIVING_NAMES], [name=SHOW_PRIVATE_RELATIONSHIPS]").on("change", function () {
        updatePrivacyFeedback();
    });

    // Import from file on server/computer
    jQuery("#import-server-file").on("focus", function () {
        jQuery("#import-server").prop("checked", true);
    });
    jQuery("#import-computer-file").on("focus", function () {
        jQuery("#import-computer").prop("checked", true);
    });
});
