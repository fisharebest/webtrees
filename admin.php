<?php
/**
 * Welcome page for the administration module
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'admin.php');

require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

// Only managers can access this page
if (!WT_USER_GEDCOM_ADMIN) {
	// TODO: Check if we are a manager in *any* gedcom, not just the current one
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

print_header(WT_I18N::translate('Administration'));

// Check for updates
$latest_version_txt=fetch_latest_version();
if ($latest_version_txt) {
	list($latest_version, $earliest_version, $download_url)=explode('|', $latest_version_txt);
	// If the latest version is newer than this version, show a download link.
	if (version_compare(WT_VERSION, $latest_version)<=0) {
		// A newer version is available.  Make a link to it
		$latest_version='<a href="'.$download_url.'" style="font-weight:bold; color:red;">'.$latest_version.'</a>';
	}
} else {
	// Cannot determine the latest version
	$latest_version='-';
}

// Load all available gedcoms
$all_gedcoms = get_all_gedcoms();

// Display a series of "blocks" of general information, vary according to admin or manager.
echo '<div id="about">';
echo
	'<h2>', WT_I18N::translate('About webtrees'), '</h2>',
	'<p>', WT_I18N::translate('Welcome to the <b>webtrees</b> administration page. This page provides access to all the site and family tree configuration settings as well as providing some useful information blocks. Administrators can upgrade to the lastest version with a single click, whenever the page reports that a new version is available.'), '</p>',
	'<p>' ,WT_I18N::translate('Your installed  version of <b>webtrees</b> is: %s', WT_VERSION_TEXT),'</p>';
if (version_compare(WT_VERSION, $latest_version)>0) {
	echo '<p>' ,WT_I18N::translate('The latest stable <b>webtrees</b> version is: %s', $latest_version), '&nbsp;<span class="accepted">' ,WT_I18N::translate('No upgrade required.'), '</span></p>';
} else {
	echo '<p class="warning">' ,WT_I18N::translate('We recommend you click here to upgrade to the latest stable webtrees version: %s', $latest_version), '</p>';
}
echo '</div>';

echo '<div id="x">';

echo '<div id="block1">';
$stats=new WT_Stats(WT_GEDCOM);
	$totusers  =0;       // Total number of users
	$warnusers =0;       // Users with warning
	$applusers =0;       // Users who have not verified themselves
	$nverusers =0;       // Users not verified by admin but verified themselves
	$adminusers=0;       // Administrators
	$userlang  =array(); // Array for user languages
	$gedadmin  =array(); // Array for managers

echo
	'<h2>', WT_I18N::translate('User information'), '</h2>';

foreach(get_all_users() as $user_id=>$user_name) {
	$totusers = $totusers + 1;
	if (((date("U") - (int)get_user_setting($user_id, 'reg_timestamp')) > 604800) && !get_user_setting($user_id, 'verified')) {
		$warnusers++;
	} else {
		if (get_user_setting($user_id, 'comment_exp')) {
			if ((strtotime(get_user_setting($user_id, 'comment_exp')) != "-1") && (strtotime(get_user_setting($user_id, 'comment_exp')) < time("U"))) {
				$warnusers++;
			}
		}
	}
	if (!get_user_setting($user_id, 'verified_by_admin') && get_user_setting($user_id, 'verified')) {
		$nverusers++;
	}
	if (!get_user_setting($user_id, 'verified')) {
		$applusers++;
	}
	if (get_user_setting($user_id, 'canadmin')) {
		$adminusers++;
	}
	foreach ($all_gedcoms as $ged_id=>$ged_name) {
		if (get_user_gedcom_setting($user_id, $ged_id, 'canedit')=='admin') {
			$title=PrintReady(strip_tags(get_gedcom_setting($ged_id, 'title')));
			if (isset($gedadmin[$title])) {
				$gedadmin[$title]["number"]++;
			} else {
				$gedadmin[$title]["name"] = $title;
				$gedadmin[$title]["number"] = 1;
				$gedadmin[$title]["ged"] = $ged_name;
			}
		}
	}
	if ($user_lang=get_user_setting($user_id, 'language')) {
		if (isset($userlang[$user_lang]))
			$userlang[$user_lang]["number"]++;
		else {
			$userlang[$user_lang]["langname"] = Zend_Locale::getTranslation($user_lang, 'language', WT_LOCALE);
			$userlang[$user_lang]["number"] = 1;
		}
	}
}	

echo
	'<table>',
	'<tr><td>', WT_I18N::translate('Total number of users'), '</td><td>', $totusers, '</td></tr>',
	'<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="useradmin.php?action=listusers&amp;filter=adminusers">', WT_I18N::translate('Administrators'), '</a></td><td>', $adminusers, '</td></tr>',
	'<tr><td colspan="2">', WT_I18N::translate('Managers'), '</td></tr>';
foreach ($gedadmin as $key=>$geds) {
	echo '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="useradmin.php?action=listusers&amp;filter=gedadmin&amp;ged='.rawurlencode($geds['ged']), '">', $geds['name'], '</a></td><td>', $geds['number'], '</td></tr>';
}
echo '<tr><td>';
if ($warnusers == 0) {
	echo WT_I18N::translate('Users with warnings');
} else {
	echo '<a href="useradmin.php?action=listusers&amp;filter=warnings">', WT_I18N::translate('Users with warnings'), '</a>';
}
echo '</td><td>', $warnusers, '</td></tr><tr><td>';
if ($applusers == 0) {
	echo WT_I18N::translate('Unverified by User');
} else {
	echo '<a href="useradmin.php?action=listusers&amp;filter=usunver">', WT_I18N::translate('Unverified by User'), '</a>';
}
echo '</td><td>', $applusers, '</td></tr><tr><td>';
if ($nverusers == 0) {
	echo WT_I18N::translate('Unverified by Administrator');
} else {
	echo '<a href="useradmin.php?action=listusers&amp;filter=admunver">', WT_I18N::translate('Unverified by Administrator'), '</a>';
}
echo '</td><td>', $nverusers, '</td></tr>';
echo '<tr><td colspan="2">', WT_I18N::translate('Users\' languages'), '</td></tr>';
foreach ($userlang as $key=>$ulang) {
	echo '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="useradmin.php?action=listusers&amp;filter=language&amp;usrlang=', $key, '">', $ulang['langname'], '</a></td><td>', $ulang['number'], '</td></tr>';
}
echo
	'</tr>',
	'<tr><td colspan="2">', WT_I18N::translate('Users Logged In'), '</td></tr>',
	'<tr><td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;', $stats->_usersLoggedIn(), '</td></tr>',
	'</table>';
echo '</div>'; // id=block2

echo
	'<div id="block3">',
	'<h2>', WT_I18N::translate('Recent changes'), '</h2>',
	'<div id="changes">';
$n=0;
foreach ($all_gedcoms as $ged_id=>$gedcom) {
	if ($ged_id==WT_GED_ID) {
		$accordian_element=$n;
	}
	++$n;
	echo 
		'<h3>', get_gedcom_setting($ged_id, 'title'), '</h3>',
		'<div>',
		'<table>',
		'<tr><td>&nbsp;</td><td><u>', WT_I18N::translate('Today'), '</u></td><td><u>', WT_I18N::translate('This week'), '</u></td><td><u>', WT_I18N::translate('This month'), '</u></td>',
		'<tr><th>', WT_I18N::translate('Individuals'), '</th><td>', count_changes_today($GEDCOM_ID_PREFIX, $ged_id), '</td><td>', count_changes_week($GEDCOM_ID_PREFIX, $ged_id), '</td><td>', count_changes_month($GEDCOM_ID_PREFIX, $ged_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Families'), '</th><td>', count_changes_today($FAM_ID_PREFIX, $ged_id), '</td><td>', count_changes_week($FAM_ID_PREFIX, $ged_id), '</td><td>', count_changes_month($FAM_ID_PREFIX, $ged_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Sources'), '</th><td>', count_changes_today($SOURCE_ID_PREFIX, $ged_id), '</td><td>', count_changes_week($SOURCE_ID_PREFIX, $ged_id), '</td><td>', count_changes_month($SOURCE_ID_PREFIX, $ged_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Repositories'), '</th><td>', count_changes_today($REPO_ID_PREFIX, $ged_id), '</td><td>', count_changes_week($REPO_ID_PREFIX, $ged_id), '</td><td>', count_changes_month($REPO_ID_PREFIX, $ged_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Media objects'), '</th><td>', count_changes_today($MEDIA_ID_PREFIX, $ged_id), '</td><td>', count_changes_week($MEDIA_ID_PREFIX, $ged_id), '</td><td>', count_changes_month($MEDIA_ID_PREFIX, $ged_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Notes'), '</th><td>', count_changes_today($NOTE_ID_PREFIX, $ged_id), '</td><td>', count_changes_week($NOTE_ID_PREFIX, $ged_id), '</td><td>', count_changes_month($NOTE_ID_PREFIX, $ged_id), '</td></tr>',
		'</table>',
		'</div>';
}
echo
	'</div>', // id=changes
	WT_JS_START,
	'jQuery("#changes").accordion({active:',$accordian_element,', icons:false});',
	WT_JS_END,
	'</div>'; // id=block3

echo
	'<div id="block2">',
	'<h2>', WT_I18N::translate('Family tree statistics'), '</h2>',
	'<div id="tree_stats">';
$n=0;
foreach ($all_gedcoms as $ged_id=>$gedcom) {
	$stats = new WT_Stats($gedcom);
	if ($ged_id==WT_GED_ID) {
		$accordian_element=$n;
	}
	++$n;
	echo
		'<h3>', $stats->gedcomTitle(), '</h3>',
		'<div>',
		'<table>',
		'<tr><th><a href="indilist.php?ged=',  rawurlencode($gedcom), '">',
		WT_I18N::translate('Individuals'), '</a></th><td>', $stats->totalIndividuals(),
		'</td></tr>',
		'<tr><th><a href="famlist.php?ged=',   rawurlencode($gedcom), '">',
		WT_I18N::translate('Families'), '</a></th><td>', $stats->totalFamilies(),
		'</td></tr>',
		'<tr><th><a href="sourlist.php?ged=',  rawurlencode($gedcom), '">',
		WT_I18N::translate('Sources'), '</a></th><td>', $stats->totalSources(),
		'</td></tr>',
		'<tr><th><a href="repolist.php?ged=',  rawurlencode($gedcom), '">',
		WT_I18N::translate('Repositories'), '</a></th><td>', $stats->totalRepositories(),
		'</td></tr>',
		'<tr><th><a href="medialist.php?ged=', rawurlencode($gedcom), '">',
		WT_I18N::translate('Media objects'), '</a></th><td>', $stats->totalMedia(),
		'</td></tr>',
		'<tr><th><a href="notelist.php?ged=',  rawurlencode($gedcom), '">',
		WT_I18N::translate('Notes'), '</a></th><td>', $stats->totalNotes(),
		'</td></tr>',
		'</table>',
		'</div>';
}
echo
	'</div>', // id=tree_stats
	WT_JS_START,
	'jQuery("#tree_stats").accordion({active:',$accordian_element,', icons:false});',
	WT_JS_END,
	'</div>'; // id=block2

echo '</div>'; // id=x
	
print_footer();
