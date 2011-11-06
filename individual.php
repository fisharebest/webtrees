<?php
// Individual Page
//
// Display all of the information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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

// -- array of GEDCOM elements that will be found but should not be displayed
$nonfacts = array('FAMS', 'FAMC', 'MAY', 'BLOB', 'CHIL', 'HUSB', 'WIFE', 'RFN', '_WT_OBJE_SORT', '');
$nonfamfacts = array(/*'NCHI',*/ 'UID', '');

$controller=new WT_Controller_Individual();

// This page uses jquery.cookie.js to record the sidebar state
$controller->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.cookie.js');

if ($controller->record && $controller->record->canDisplayDetails()) {
	if (safe_GET('action')=='ajax') {
		$controller->ajaxRequest();
		exit;
	}
	$controller->pageHeader();
	if ($controller->record->isMarkedDeleted()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This individual has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onClick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onClick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
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
					'<a href="#" onClick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onClick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'reject') . '</a>'
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
	header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
	$controller->pageHeader();
	echo '<p class="ui-state-error">', WT_I18N::translate('This individual does not exist or you do not have permission to view it.'), '</p>';
	exit;
}

// tell tabs that use jquery that it is already loaded
define('WT_JQUERY_LOADED', 1);

$linkToID=$controller->record->getXref(); // -- Tell addmedia.php what to link to

echo WT_JS_START;
echo 'function show_gedcom_record() {';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->record->getXref(), '", "_blank", "top=0, left=0, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");';
echo '}';
echo 'function showchanges() { window.location="'.$controller->record->getRawUrl().'"; }';

?>

jQuery(document).ready(function() {
	jQuery("#tabs").tabs({
		spinner: '<img src="<?php echo WT_STATIC_URL; ?>images/loading.gif" height="18" border="0" alt="">',
		cache: true
	});
	jQuery("#tabs").tabs("select",jQuery.cookie("indi-tab"));
	jQuery("#tabs").bind("tabsshow", function(event, ui) {
		if (ui.tab.match(/#(.*)$/)) {
			jQuery.cookie("indi-tab", RegExp.$1);
		}
		<?php
		foreach ($controller->tabs as $tab) {
			echo $tab->getJSCallback()."\n";
		}
		?>
	});

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
});
<?php
echo WT_JS_END;
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
		echo strip_tags(WT_Gedcom_Tag::getLabelValue('AGE', get_age_at_event(WT_Date::GetAgeGedcom($bdate), true)), '<span>');
	} elseif ($bdate->isOK() && $ddate->isOK()) {
		// If dead, show age at death
		echo strip_tags(WT_Gedcom_Tag::getLabelValue('AGE', get_age_at_event(WT_Date::GetAgeGedcom($bdate, $ddate), false)), '<span>');
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

	echo
		'</div>', // close header_accordion1
		WT_JS_START,
		'jQuery("#header_accordion1").accordion({',
		' active: 0,',
		' icons: {"header": "ui-icon-triangle-1-s", "headerSelected": "ui-icon-triangle-1-n" },',
		' autoHeight: false,',
		' collapsible: true',
		'});',
		WT_JS_END; //accordion details
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
		if ($tab->canLoadAjax()) {
			// AJAX tabs load only when selected
			echo '<li class="'.$greyed_out.'"><a title="', $tab->getName(), '" href="',$controller->record->getHtmlUrl(),'&amp;action=ajax&amp;module=', $tab->getName(), '">';
		} else {
			// Non-AJAX tabs load immediately
			echo '<li class="'.$greyed_out.'"><a title="', $tab->getName(), '" href="#', $tab->getName(), '">';
		}
		echo '<span title="', $tab->getTitle(), '">', $tab->getTitle(), '</span></a></li>';
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
	'<div id="sidebar">'; // sidebar code
require './sidebar.php';
echo
	'</div>',  // close #sidebar
	'<a href="#" id="separator" title="', WT_I18N::translate('Click here to open or close the sidebar'), '"></a>',//clickable element to open/close sidebar
	'<div style="clear:both;">&nbsp;</div></div>', // close #main	
	// =======================================footer and other items 
	WT_JS_START,
	'var catch_and_ignore; function paste_id(value) {catch_and_ignore = value;}',
	WT_JS_END;
