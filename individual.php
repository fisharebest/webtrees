<?php
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
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\IndividualController;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'individual.php');
require './includes/session.php';

$pid = Filter::get('pid', WT_REGEX_XREF);
$record = Individual::getInstance($pid, $WT_TREE);
if (!$record && $WT_TREE->getPreference('USE_RIN')) {
	$record = Individual::getInstance(FunctionsDb::findRin($pid), $WT_TREE);
}
$controller = new IndividualController($record);

if ($controller->record && $controller->record->canShow()) {
	if (Filter::get('action') == 'ajax') {
		$controller->ajaxRequest();

		return;
	}
	// Generate the sidebar content *before* we display the page header,
	// as the clippings cart needs to have write access to the session.
	$sidebar_html = $controller->getSideBarContent();

	$controller->pageHeader();
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This individual has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This individual has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This individual has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This individual has been edited.  The changes need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	}
} elseif ($controller->record && $controller->record->canShowName()) {
	// Just show the name.
	$controller->pageHeader();
	echo '<h2>', $controller->record->getFullName(), '</h2>';
	echo '<p class="ui-state-highlight">', I18N::translate('The details of this individual are private.'), '</p>';

	return;
} else {
	http_response_code(404);
	$controller->pageHeader();
	echo '<p class="ui-state-error">', I18N::translate('This individual does not exist or you do not have permission to view it.'), '</p>';

	return;
}

$controller->addInlineJavascript('
var WT_INDIVIDUAL = (function () {

	var instance,
		jQseparator = jQuery("#separator"),
		jQsidebar = jQuery ("#sidebar");

	function init() {
		jQuery ("#header_accordion1").accordion ({
			active: 0,
			heightStyle: "content",
			collapsible: true
		});

		jQuery ("#tabs").tabs ({
			// Remember the currently selected tab between pages.
			active: sessionStorage.getItem("indi-tab"),
			activate: function (event, ui) {
				sessionStorage.setItem("indi-tab", jQuery(this).tabs("option", "active"));
			},
			// Only load each tab once
			beforeLoad: function (event, ui) {
				if (ui.tab.data ("loaded")) {
					event.preventDefault ();
					return;
				}
				jQuery (ui.panel.selector).append (\'<div class="loading-image"></div>\');
				ui.jqXHR.success (function () {
					ui.tab.data ("loaded", true);
				});
			}
		});

		if (jQsidebar.length) { // Have we got a sidebar ?
			// toggle sidebar visibility
			jQuery ("#main").on ("click", "#separator", function (e) {
				e.preventDefault ();
				jQsidebar.animate ({width: "toggle"}, {
					duration: 300,
					done: function () {
						sessionStorage.setItem("hide-sb", jQsidebar.is(":hidden"));
						jQseparator.toggleClass("separator-hidden separator-visible");
					}
				});
			});

			// Set initial sidebar state
			if (sessionStorage.getItem("hide-sb") === "true") {
				jQsidebar.hide ();
				jQseparator.addClass("separator-hidden");
			} else {
				jQsidebar.show ();
				jQseparator.addClass("separator-visible");
			}
		}
	}

	return {
		getInstance: function () {
			if (!instance) {
				instance = init ();
			}
			return instance;
		}
	};
}) ();
WT_INDIVIDUAL.getInstance ();
');

// ===================================== header area
echo
	'<div id="main">', //overall page container
	'<div id="indi_left">',
	'<div id="indi_header">';
if ($controller->record->canShow()) {
	// Highlight image or silhouette
	echo '<div id="indi_mainimage">', $controller->record->displayImage(), '</div>';
	echo '<div id="header_accordion1">'; // contain accordions for names
	echo '<h3 class="name_one ', $controller->getPersonStyle($controller->record), '"><span>', $controller->record->getFullName(), '</span>'; // First name accordion header
	$bdate = $controller->record->getBirthDate();
	$ddate = $controller->record->getDeathDate();
	echo '<span class="header_age">';
	if ($bdate->isOK() && !$controller->record->isDead()) {
		// If living display age
		echo GedcomTag::getLabelValue('AGE', FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate), true), $controller->record, 'span');
	} elseif ($bdate->isOK() && $ddate->isOK()) {
		// If dead, show age at death
		echo GedcomTag::getLabelValue('AGE', FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate, $ddate), false), $controller->record, 'span');
	}
	echo '</span>';
	// Display summary birth/death info.
	echo '<span id="dates">', $controller->record->getLifeSpan(), '</span>';

	// Display gender icon
	foreach ($controller->record->getFacts() as $fact) {
		if ($fact->getTag() == 'SEX') {
			$controller->printSexRecord($fact);
		}
	}
	echo '</h3>'; // close first name accordion header

	// Display name details
	foreach ($controller->record->getFacts() as $fact) {
		if ($fact->getTag() == 'NAME') {
			$controller->printNameRecord($fact);
		}
	}

	echo '</div>'; // close header_accordion1
}
echo '</div>'; // close #indi_header
// ===================================== main content tabs
foreach ($controller->tabs as $tab) {
	echo $tab->getPreLoadContent();
}
echo '<div id="tabs">';
echo '<ul>';
foreach ($controller->tabs as $tab) {
	if ($tab->isGrayedOut()) {
		$greyed_out = 'rela';
	} else {
		$greyed_out = '';
	}
	if ($tab->hasTabContent()) {
		echo '<li class="' . $greyed_out . '"><a';
		if ($tab->canLoadAjax()) {
			// AJAX tabs load only when selected
			echo ' href="' . $controller->record->getHtmlUrl(), '&amp;action=ajax&amp;module=', $tab->getName() . '"';
			echo ' rel="nofollow"';
		} else {
			// Non-AJAX tabs load immediately
			echo ' href="#', $tab->getName() . '"';
		}
		echo ' title="', $tab->getDescription(), '">', $tab->getTitle(), '</a></li>';
	}
}
echo '</ul>';
foreach ($controller->tabs as $tab) {
	if ($tab->hasTabContent()) {
		if (!$tab->canLoadAjax()) {
			echo '<div id="', $tab->getName(), '">', $tab->getTabContent(), '</div>';
		}
	}
}
echo
	'</div>', // close #tabs
	'</div>'; //close indi_left
	if ($sidebar_html) {
		echo '<div id="separator" title="' . I18N::translate('Click here to open or close the sidebar') . '"></div>' . //clickable element to open/close sidebar
		$sidebar_html;
	}
	echo '</div>'; // close #main

