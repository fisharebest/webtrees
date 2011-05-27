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

$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;

// -- array of GEDCOM elements that will be found but should not be displayed
$nonfacts = array('FAMS', 'FAMC', 'MAY', 'BLOB', 'CHIL', 'HUSB', 'WIFE', 'RFN', '_WT_OBJE_SORT', '');

$nonfamfacts = array(/*'NCHI',*/ 'UID', '');

$controller=new WT_Controller_Individual();
$controller->init();

// tell tabs that use jquery that it is already loaded
define('WT_JQUERY_LOADED', 1);

// We have finished writing session data, so release the lock
Zend_Session::writeClose();

print_header($controller->getPageTitle());

if (!$controller->indi) {
	echo '<b>', WT_I18N::translate('Unable to find record with ID'), '</b><br /><br />';
	print_footer();
	exit;
} else if (!$controller->indi->canDisplayName()) {
	echo '<div class="facts_value" >';
	print_privacy_error();
	echo '</div>';
	print_footer();
	exit;
}

$linkToID=$controller->pid; // -- Tell addmedia.php what to link to
echo WT_JS_START; ?>
// javascript function to open a window with the raw gedcom in it
function show_gedcom_record(shownew) {
	fromfile="";
	if (shownew=="yes") fromfile='&fromfile=1';
	var recwin = window.open("gedrecord.php?pid=<?php echo $controller->pid; ?>"+fromfile, "_blank", "top=50,left=50,width=600,height=400,scrollbars=1,scrollable=1,resizable=1");
}
<?php if (WT_USER_CAN_EDIT) { ?>
function showchanges() {
	window.location = '<?php echo $controller->indi->getRawUrl(); ?>&show_changes=yes';
}
<?php } ?>

jQuery('#main').addClass('use-sidebar'); // Show
jQuery('#main').removeClass('use-sidebar'); // Hide
jQuery('#main').toggleClass('use-sidebar'); // Toggle

var tabCache = new Array();

jQuery(document).ready(function() {
	// TODO: change images directory when the common images will be deleted.
	jQuery('#tabs').tabs({ spinner: '<img src=\"images/loading.gif\" height=\"18\" border=\"0\" alt=\"\" />' });
	jQuery("#tabs").tabs({ cache: true });
	var $tabs = jQuery('#tabs');
	jQuery('#tabs').bind('tabsshow', function(event, ui) {
		var selectedTab = ui.tab.name;
		tabCache[selectedTab] = true;
	<?php
	foreach ($controller->tabs as $tab) {
		echo $tab->getJSCallback()."\n";
	}
	?>
	});

	// sidebar settings 
	// Variables
	var objMain			= jQuery('#main');
	var objTabs			= jQuery('#indi_left');
	var objBar			= jQuery('#sidebar');
	var objSeparator	= jQuery('#separator');
	// Show sidebar
	function showSidebar(){
		objMain.addClass('use-sidebar');
		objSeparator.css('height', objBar.outerHeight() + 'px');
		jQuery.cookie('sidebar-pref', 'use-sidebar', { expires: 30 });
	}
	// Hide sidebar
	function hideSidebar(){
		objMain.removeClass('use-sidebar');
		objSeparator.css('height', objTabs.outerHeight() + 'px');
		jQuery.cookie('sidebar-pref', null, { expires: 30 });
	}
	// Sidebar separator
	objSeparator.click(function(e){
		e.preventDefault();
		if ( objMain.hasClass('use-sidebar') ){
			hideSidebar();
		} else {
			showSidebar();
		}
	});
;
	// Load preference
	if ( jQuery.cookie('sidebar-pref') == null ){
		objMain.addClass('use-sidebar');
		objSeparator.css('height', objTabs.outerHeight() + 'px');
	} else {
		objSeparator.css('height', objBar.outerHeight() + 'px');
	}
	
});
<?php
echo WT_JS_END;
// ===================================== header area
if ((empty($SEARCH_SPIDER))&&($controller->accept_success)) {
	echo '<strong>', WT_I18N::translate('Changes successfully accepted into database'), '</strong><br />';
}
if ($controller->indi->isMarkedDeleted()) {
	echo '<span class="error">', WT_I18N::translate('This record has been marked for deletion upon admin approval.'), '</span>';
}
echo '<div id="main" class="use-sidebar sidebar-at-right">'; //overall page container
echo '<div id="indi_left">',
	'<div id="indi_header">';
	if ($controller->indi->canDisplayDetails()) {
	$globalfacts=$controller->getGlobalFacts();
		echo '<div id="header_accordion1">', // contain accordions for names
			'<h3 class="name_one ', $controller->getPersonStyle($controller->indi), '"><span>', $controller->indi->getFullName(), '</span>'; // First name accordion element
				if (WT_USER_IS_ADMIN) {
					$user_id=get_user_from_gedcom_xref(WT_GED_ID, $controller->pid);
					if ($user_id) {
						$user_name=get_user_name($user_id);
						echo '<span> - <a href="admin_users.php?action=edituser&amp;username='.$user_name.'">'.$user_name.'</span></a>';
					}
				}
				// If living display age
				if (!$controller->indi->isDead()) {
					$bdate=$controller->indi->getBirthDate();
					$age = WT_Date::GetAgeGedcom($bdate);
					if ($age!='') echo '<span class="age">('.WT_I18N::translate('Age').' '.get_age_at_event($age, true).')</span>';
				}
				// Display summary birth/death info.
				echo '<span id="dates">', $controller->indi->getBirthDeathYears(), '</span>';
				//Display gender icon
				$nameSex = array('NAME', 'SEX');
				foreach ($globalfacts as $key=>$value) {
					$fact = $value->getTag();
					if (in_array($fact, $nameSex)) {
						if ($fact=="SEX") $controller->print_sex_record($value);
					}
				}
			echo '</h3>';
			//Display name details
				$nameSex = array('NAME', 'SEX');
				foreach ($globalfacts as $key=>$value) {
					if ($key == 0) {
					// First name
						$fact = $value->getTag();
						if (in_array($fact, $nameSex)) {
							if ($fact=="NAME") $controller->print_name_record($value);
						}
					}
				}
			//Display name details
				$nameSex = array('NAME', 'SEX');
				foreach ($globalfacts as $key=>$value) {
					if ($key != 0) {
						// 2nd and more names
						$fact = $value->getTag();
						if (in_array($fact, $nameSex)) {
							if ($fact=="NAME") $controller->print_name_record($value);
						}
					}
				}
		echo '</div>'; // close header_accordion1
		echo WT_JS_START,'jQuery("#header_accordion1").accordion({active:0, icon:false, autoHeight:false, collapsible: true});',	WT_JS_END; //accordion details
		// Display highlight image
		echo '<div id="indi_mainimage">';// highlighted image
		if ($MULTI_MEDIA && $controller->canShowHighlightedObject()) {
			echo $controller->getHighlightedObject();
		}
		echo '</div>'; // close #indi_mainimage
	}
echo '</div>';// close #indi_header
// ===================================== main content tabs
if (!$controller->indi->canDisplayDetails()) {
	echo '<div id="tabs" >';
	print_privacy_error();
	echo '</div>'; //close #tabs
	print_footer();
	exit;
}
foreach ($controller->tabs as $tab) {
	echo $tab->getPreLoadContent();
}
$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
echo '<div id="tabs" >';
echo '<ul>';
foreach ($controller->tabs as $tab) {
	if ($tab->hasTabContent()) {
		if ($tab->getName()==$controller->default_tab) {
			// Default tab loads immediately
			echo '<li><a title="', $tab->getName(), '" href="#', $tab->getName(), '">';
		} else if ($tab->canLoadAjax()) {
			// AJAX tabs load later
			echo '<li><a title="', $tab->getName(), '" href="',$controller->indi->getHtmlUrl(),'&amp;action=ajax&amp;module=', $tab->getName(), '">';
		} else {
			// Non-AJAX tabs load immediately (search engines don't load ajax)
			echo '<li><a title="', $tab->getName(), '" href="#', $tab->getName(), '">';
		}
		echo '<span title="', $tab->getTitle(), '">', $tab->getTitle(), '</span></a></li>';
	}
}
echo '</ul>';
foreach ($controller->tabs as $tab) {
	if ($tab->hasTabContent()) {
		if ($tab->getName()==$controller->default_tab || !$tab->canLoadAjax()) {
			echo '<div id="', $tab->getName(), '">';
			echo $tab->getTabContent();
			echo '</div>'; // close each tab div
		}
	}
}
echo '</div>', // close #tabs
	'</div>';//close indi_left
// ===================================== sidebar area
echo '<div id="sidebar">'; // sidebar code
require './sidebar.php';
echo
	'</div>',  // close #sidebar
	'<a href="#" id="separator" title="', WT_I18N::translate('Click here to open or close the sidebar'), '"></a>',//clickable element to open/close sidebar
	'<div style="clear:both;">&nbsp;</div></div>', // close #main	
// =======================================footer and other items 
WT_JS_START,
'var catch_and_ignore; function paste_id(value) {catch_and_ignore = value;}',
'if (typeof toggleByClassName == "undefined") {',
'alert("webtrees.js: A javascript function is missing.  Please clear your Web browser cache");',
'}',
'jQuery("html, body").animate({scrollTop: jQuery("#header").offset().top});', // scroll the page to top
WT_JS_END;
print_footer();