/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
	var REQUIRE_AUTHENTICATION = parseInt(jQuery("[name=REQUIRE_AUTHENTICATION]").val(), 10);
	var SHOW_DEAD_PEOPLE = parseInt(jQuery("[name=SHOW_DEAD_PEOPLE]").val(), 10);
	var HIDE_LIVE_PEOPLE = parseInt(jQuery("[name=HIDE_LIVE_PEOPLE]").val(), 10);
	var SHOW_LIVING_NAMES = parseInt(jQuery("[name=SHOW_LIVING_NAMES]").val(), 10);
	var SHOW_PRIVATE_RELATIONSHIPS = parseInt(jQuery("[name=SHOW_PRIVATE_RELATIONSHIPS]").val(), 10);

	setPrivacyFeedback("[name=REQUIRE_AUTHENTICATION]", "visitors", REQUIRE_AUTHENTICATION === 0);
	setPrivacyFeedback("[name=REQUIRE_AUTHENTICATION]", "members", true);

	setPrivacyFeedback("[name=SHOW_DEAD_PEOPLE]", "visitors", REQUIRE_AUTHENTICATION === 0 && (SHOW_DEAD_PEOPLE >= 2 || HIDE_LIVE_PEOPLE === 0));
	setPrivacyFeedback("[name=SHOW_DEAD_PEOPLE]", "members", SHOW_DEAD_PEOPLE >= 1 || HIDE_LIVE_PEOPLE === 0);

	setPrivacyFeedback("[name=HIDE_LIVE_PEOPLE]", "visitors", REQUIRE_AUTHENTICATION === 0 && HIDE_LIVE_PEOPLE === 0);
	setPrivacyFeedback("[name=HIDE_LIVE_PEOPLE]", "members", true);

	setPrivacyFeedback("[name=SHOW_LIVING_NAMES]", "visitors", REQUIRE_AUTHENTICATION === 0 && SHOW_LIVING_NAMES >= 2);
	setPrivacyFeedback("[name=SHOW_LIVING_NAMES]", "members", SHOW_LIVING_NAMES >= 1);
	setPrivacyFeedback("[name=SHOW_LIVING_NAMES]", "managers", SHOW_LIVING_NAMES >= 0);

	setPrivacyFeedback("[name=SHOW_PRIVATE_RELATIONSHIPS]", "visitors", REQUIRE_AUTHENTICATION === 0 && SHOW_PRIVATE_RELATIONSHIPS === 1);
	setPrivacyFeedback("[name=SHOW_PRIVATE_RELATIONSHIPS]", "members", SHOW_PRIVATE_RELATIONSHIPS === 1);
}

/**
 * Activate the privacy feedback labels.
 */
jQuery(document).ready(function() {
	updatePrivacyFeedback();
	jQuery("[name=REQUIRE_AUTHENTICATION], [name=HIDE_LIVE_PEOPLE], [name=SHOW_DEAD_PEOPLE], [name=SHOW_LIVING_NAMES], [name=SHOW_PRIVATE_RELATIONSHIPS]").on("change", function () {
		updatePrivacyFeedback();
	});
});
