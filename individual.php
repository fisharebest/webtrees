<?php
// Individual Page
//
// Display all of the information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2011  PGV Development Team.  All rights reserved.
//
// Sidebar controls courtesy of http://devheart.org/articles/jquery-collapsible-sidebar-layout/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

define('WT_SCRIPT_NAME', 'individual.php');
require './includes/session.php';
$controller=new WT_Controller_Individual();
$controller->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.cookie.js');// This page uses jquery.cookie.js to record the sidebar state
$controller->addInlineJavaScript('var catch_and_ignore; function paste_id(value) {catch_and_ignore = value;}'); // For the "find" links
	
// -- array of GEDCOM elements that will be found but should not be displayed
$nonfacts = array('FAMS', 'FAMC', 'MAY', 'BLOB', 'CHIL', 'HUSB', 'WIFE', 'RFN', '_WT_OBJE_SORT', '');
$nonfamfacts = array(/*'NCHI',*/ 'UID', '');

if ($controller->record && $controller->record->canDisplayDetails()) {
	if (safe_GET('action')=='ajax') {
		$controller->ajaxRequest();
		exit;
	}
	// Generate the sidebar content *before* we display the page header,
	// as the clippings cart needs to have write access to the session.
	$sidebar_html=$controller->getSideBarContent();

	$controller->pageHeader();
	if ($controller->record->isMarkedDeleted()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This individual has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This individual has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	} elseif (find_updated_record($controller->record->getXref(), WT_GED_ID)!==null) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This individual has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This individual has been edited.  The changes need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	}
} elseif ($controller->record && $controller->record->canDisplayName()) {
	// Just show the name.
	$controller->pageHeader();
	echo '<h2>', $controller->record->getFullName(), '</h2>';
	echo '<p class="ui-state-highlight">', WT_I18N::translate('The details of this individual are private.'), '</p>';
	exit;
} else {
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
	$controller->pageHeader();
	echo '<p class="ui-state-error">', WT_I18N::translate('This individual does not exist or you do not have permission to view it.'), '</p>';
	exit;
}

$linkToID=$controller->record->getXref(); // -- Tell addmedia.php what to link to

$callbacks='';
foreach ($controller->tabs as $tab) {
  $callbacks.=$tab->getJSCallback()."\n";
}
$controller->addInlineJavaScript('
	jQuery("#tabs").tabs({
		spinner: "<img src=\"'.WT_STATIC_URL.'images/loading.gif\" height=\"18\" alt=\"\">",
		cache: true
	});
	jQuery("#tabs").tabs("select",jQuery.cookie("indi-tab"));
	jQuery("#tabs").bind("tabsshow", function(event, ui) {
		jQuery.cookie("indi-tab", ui.panel.id);'.$callbacks.
	'});

	// sidebar settings 
	// Variables
	var objMain			= jQuery("#main");
	var objTabs			= jQuery("#indi_left");
	var objBar			= jQuery("#sidebar");
	var objSeparator	= jQuery("#separator");
	// Adjust header dimensions
	function adjHeader(){
		var indi_header_div = document.getElementById("indi_header").offsetWidth - 20;
		var indi_mainimage_div = document.getElementById("indi_mainimage").offsetWidth +20;
		var header_accordion_div = document.getElementById("header_accordion1");
		header_accordion_div.style.width = indi_header_div - indi_mainimage_div +"px";

		jQuery(window).bind("resize", function(){
			var indi_header_div = document.getElementById("indi_header").offsetWidth - 20;
			var indi_mainimage_div = document.getElementById("indi_mainimage").offsetWidth +20;
			var header_accordion_div = document.getElementById("header_accordion1");
			header_accordion_div.style.width = indi_header_div - indi_mainimage_div +"px";
		 });
	}
	// Show sidebar
	function showSidebar(){
		objMain.addClass("use-sidebar");
		objSeparator.css("height", objBar.outerHeight() + "px");
		jQuery.cookie("hide-sb", null);
	}
	// Hide sidebar
	function hideSidebar(){
		objMain.removeClass("use-sidebar");
		objSeparator.css("height", objTabs.outerHeight() + "px");
		jQuery.cookie("hide-sb", "1");
	}
	// Sidebar separator
	objSeparator.click(function(e){
		e.preventDefault();
		if ( objMain.hasClass("use-sidebar") ){
			hideSidebar();
			adjHeader();
		} else {
			showSidebar();
			adjHeader();
		}
	});
	// Load preference
	if (jQuery.cookie("hide-sb")=="1"){
		hideSidebar();
	} else {
		showSidebar();
	}
	adjHeader();
	jQuery("#main").css("visibility", "visible");
	
	function show_gedcom_record() {
		var recwin=window.open("gedrecord.php?pid='. $controller->record->getXref(). '", "_blank", edit_window_specs);
	}	
	function showchanges(){window.location="'.$controller->record->getRawUrl().'";}

	jQuery("#header_accordion1").accordion({
		active: 0,
		icons: {"header": "ui-icon-triangle-1-s", "headerSelected": "ui-icon-triangle-1-n" },
		autoHeight: false,
		collapsible: true
	});

');

// ===================================== header area
echo
	'<div id="main" class="use-sidebar sidebar-at-right" style="visibility:hidden;">', //overall page container
	'<div id="indi_left">',
	'<div id="indi_header">';
if ($controller->record->canDisplayDetails()) {
	echo '<div id="indi_mainimage">'; // Display highlight image
	if ($controller->canShowHighlightedObject()) {
		echo $controller->getHighlightedObject();
	}
	echo '</div>'; // close #indi_mainimage
	$globalfacts=$controller->getGlobalFacts();
	echo '<div id="header_accordion1">'; // contain accordions for names
	echo '<h3 class="name_one ', $controller->getPersonStyle($controller->record), '"><span>', $controller->record->getFullName(), '</span>'; // First name accordion header
	$bdate=$controller->record->getBirthDate();
	$ddate=$controller->record->getDeathDate();
	echo '<span class="header_age">';
	if ($bdate->isOK() && !$controller->record->isDead()) {
		// If living display age
		echo WT_Gedcom_Tag::getLabelValue('AGE', get_age_at_event(WT_Date::GetAgeGedcom($bdate), true), '<span>');
	} elseif ($bdate->isOK() && $ddate->isOK()) {
		// If dead, show age at death
		echo WT_Gedcom_Tag::getLabelValue('AGE', get_age_at_event(WT_Date::GetAgeGedcom($bdate, $ddate), false), '<span>');
	}
	echo '</span>';
	// Display summary birth/death info.
	echo '<span id="dates">', $controller->record->getLifeSpan(), '</span>';
	//Display gender icon
	foreach ($globalfacts as $key=>$value) {
		$fact = $value->getTag();
		if ($fact=="SEX") $controller->print_sex_record($value);
	}
	echo '</h3>'; // close first name accordion header	
	//Display name details
	foreach ($globalfacts as $key=>$value) {
		$fact = $value->getTag();
		if ($fact=="NAME") $controller->print_name_record($value);
	}
	echo '</div>'; // close header_accordion1
}
echo '</div>';// close #indi_header
// ===================================== main content tabs
foreach ($controller->tabs as $tab) {
	echo $tab->getPreLoadContent();
}
echo '<div id="tabs">';
echo '<ul>';
foreach ($controller->tabs as $tab) {
	if ($tab->isGrayedOut()) {
		$greyed_out='rela';
	} else {
		$greyed_out='';
	}
	if ($tab->hasTabContent()) {
		// jQueryUI/tabs.  The title attribute is used to uniquely identify each
		// tab.  We need this identifier, so that we can remember/restore the last
		// tab used.  Hence we must use the tab's name (not a numeric index, which
		// will change from page to page).  But the title must also be a valid CSS
		// id, which means that we cannot use the tab's title/description.  (The
		// documentation suggests simply replacing spaces with underscores, but
		// this will only work for English.)  We can wrap the tab's title in its
		// own <div title="">, but jQueryUI gives the <a> element padding, which
		// shows the correct title on the text but the wrong title on the padding.
		// So,... move the padding from the <a> to the internal <div>
		echo '<li class="'.$greyed_out.'"><a title="', $tab->getName(), '" href="';
		if ($tab->canLoadAjax()) {
			// AJAX tabs load only when selected
			echo $controller->record->getHtmlUrl(),'&amp;action=ajax&amp;module=', $tab->getName();
		} else {
			// Non-AJAX tabs load immediately
			echo '#', $tab->getName();
		}
		echo '"><div title="', $tab->getDescription(), '">', $tab->getTitle(), '</div></a></li>';
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
	'</div>', //close indi_left
	$sidebar_html,
	'<a href="#" id="separator" title="', WT_I18N::translate('Click here to open or close the sidebar'), '"></a>',//clickable element to open/close sidebar
	'<div style="clear:both;">&nbsp;</div></div>'; // close #main
