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
 * @version $Id: admin.php 10455 2011-01-13 00:38:31Z nigel $
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

?>
<style type="text/css">
/* general styles */
body {font-family:Trebuchet MS, Tahoma, Verdana, Arial, sans-serif; font-size:12px; margin:10px; min-width:800px;}
input.ui-button { padding:1px 3px;}
input, textarea {	color:black; padding-left:3px; padding-right:3px;}
fieldset {margin:8px 8px 4px 8px;}
legend {font-style:italic; font-weight:bold;	padding:0 5px 5px; align:top;}
img {border:none;}
h3 {margin:0}
h4 {margin:5px 3px; font-weight:normal; font-style:italic;}
.icon {border:none;	padding-left:0pt;padding-right:5pt;}
.warning, .error, .red {font-weight:bold; color:red;}
.accepted {font-weight:bold; color:green;}
.center {text-align:center;}
.ltr {text-align:left;}
.rtl {text-align:right;}
.nowrap {white-space:nowrap;}
.indent {padding-left:15px;}
.parentdeath {border:thin solid red; padding:1px;}
.ui-widget-content a {text-decoration:none;}
.ui-widget-content a.current {color:#E17009; font-weight:bold;}
.ui-widget-content a:hover {color:red; cursor:hand; cursor:pointer;}
.ui-widget-header {padding:5px;}
.css_right {float:right;}
html[dir='rtl'] .css_right {float:left;}

/* Drag-n-drop sorting for modules, etc. */
.sortme {cursor:move;}

/* Page Help links */
#page_help{text-align:right;}
html[dir='rtl'] #page_help{text-align:left;}
#page_help img{ height:24px; margin:-5px; padding:0; width:24px;}

/* HEADER */
#admin_head {position:relative; height:70px; margin-bottom:10px;}
#title {position:absolute; left:47%; top:20px; font-size:18px; font-weight:bold;}
#info {position:absolute; right:10px; bottom:0; text-align:right;}
html[dir='rtl'] #info {right:auto; left:10px; text-align:left;}
#links {position:absolute; left:10px; bottom:0; display:inline; margin-bottom:5px; white-space:nowrap;}
html[dir='rtl'] #links {left:auto; right:10px;}
#links li {display:inline; position:relative;}
#links li ul {display:none; position:absolute; left:0px; top:1.2em; background:url("jquery/images/ui-bg_fine-grain_10_eceadf_60x60.png") repeat scroll 50% 50% #ECEADF; border:1px solid #D9D6C4; z-index:999;}
html[dir='rtl'] #links li ul {left:auto; right:0px;}
#links li ul li {display:block; padding:0 5px; text-align:left;}
 html[dir='rtl'] #links li ul li {text-align:right;}
#links, #links li ul {list-style:none; margin:0; padding:1px;}
#links li:hover > ul {display:block;}

/* SIDE and CONTENT panels */
#admin_menu, #admin_content {min-height:580px;}

/* SIDE MENU */
#admin_menu {padding:10px 0 10px 5px; width:200px; float:left; position:absolute; color:#2E6E9E; white-space:nowrap; overflow:hidden;}
html[dir='rtl'] #admin_menu {padding:10px 5px 10px 0; float:right;}
#admin_menu ul {list-style:none outside none; margin:0 0 0 -30px;}
html[dir='rtl'] #admin_menu ul {margin:0 -30px 0 0;}
#admin_menu li ul {list-style:none outside none; margin:0 0 0 -20px;}
html[dir='rtl'] #admin_menu li ul {margin:0 -20px 0 0;}
#admin_menu li span {font-style:italic;}

/* CONTENT */
#admin_content {margin:0 0 0 220px; padding:10px;}
html[dir='rtl'] #admin_content {margin:0 220px 0 0; padding:10px;}

/* FOOTER */
#admin_footer {float:left; text-align:center; width:100%; margin-top:30px;}
html[dir='rtl'] #admin_footer {float:right; text-align:center;}

/* DASHBOARD BLOCKS */
#content_container {background:OldLace; padding:5px; border:1px inset #D9D6C4; height:570px; overflow:auto;}
#about2 {border-right: 1px solid; float:left; height:565px; text-align:justify; overflow:auto; padding:0 30px 0 10px; width:46%;}
#gen_info {float:left; height:570px; margin-left:30px; paddding:0 10px; width:46%;}
#users2, #trees2, #recent2 {min-height:300px !important;}
#users2 {white-space:nowrap; overflow-x:auto; overflow-y:hidden;}
#users2 table {font-size:90%;}
#users2 ul {list-style-type:none; padding:0px; margin:0 0 0 20px;}
#users2 ul li {background-image:url(images/bullet.png); background-repeat:no-repeat; background-position:0px 0px; padding:0 0 0 14px;}
#users2 td {padding: 0 10px 0 0;}
#users2 td div {font-style:italic; padding-left:20px; overflow:hidden; width:90%;}
#tree_stats th, #recent2 th {padding:0 10px 0 0; text-align:left; white-space:nowrap; font-weight:normal;}
#tree_stats td, #recent2 td {padding:0 10px 0 0; width:30px; text-align:right; white-space:nowrap;}
#tree_stats .ui-accordion-content, #recent2 .ui-accordion-content {padding:1em; min-height:140px;}
#tree_stats.ui-widget, #changes.ui-widget {font-size:100% !important;}
.ui-accordion .ui-accordion-content-active {display:block;}
#trees2 span, #recent2 span {text-decoration:underline;}
<!--
#about, #users , #trees, #recent{background:OldLace; padding:5px; border:1px inset #D9D6C4;}
#tree_stats h3, #recent h3 {text-align:left;}
html[dir='rtl'] #tree_stats h3, html[dir='rtl'] #recent h3 {text-align:right;}
#about, #users, #trees, #recent {float:left; margin: 0 10px 10px 0; height: 274px; overflow:auto; width:46%; }
html[dir='rtl'] #about, html[dir='rtl'] #users, html[dir='rtl'] #trees, html[dir='rtl'] #recent {float:right; margin: 0 0 10px 10px;}
#trees {clear:both;}
html[dir='rtl'] #tree_stats th, html[dir='rtl'] #recent th {padding:0 0 0 ; text-align:right;}
html[dir='rtl'] #tree_stats td, #recent td {padding:0 0 0 10px; text-align:left; white-space:nowrap;}
html[dir='rtl'] #users ul {margin:0 20px 0 0;}
html[dir='rtl'] #users ul li {padding:0 14px 0 0;}
html[dir='rtl'] #users td {padding: 0 0 0 10px;}
-->
</style>

<?php

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

echo '<div id="content_container">';
// Display a series of "blocks" of general information, vary according to admin or manager.
echo '<div id="about2">';
echo
	'<h2>', WT_I18N::translate('About webtrees'), '</h2>',
	'<p>', WT_I18N::translate('Welcome to the <b>webtrees</b> administration page. This page provides access to all the site and family tree configuration settings as well as providing some useful information blocks. Administrators can upgrade to the lastest version with a single click, whenever the page reports that a new version is available.'), '</p>',
	'<p>' ,WT_I18N::translate('Your installed  version of <b>webtrees</b> is: %s', WT_VERSION_TEXT),'</p>';
if (version_compare(WT_VERSION, $latest_version)>0) {
	echo '<p>' ,WT_I18N::translate('The latest stable <b>webtrees</b> version is: %s', $latest_version), '&nbsp;<span class="accepted">' ,WT_I18N::translate('No upgrade required.'), '</span></p>';
} else {
	echo '<p class="warning">' ,WT_I18N::translate('We recommend you click here to upgrade to the latest stable <b>webtrees</b> version: %s', $latest_version), '</p>';
}
echo
	'</div>';
//	'<hr />';

$stats=new WT_Stats(WT_GEDCOM);
	$totusers  =0;       // Total number of users
	$warnusers =0;       // Users with warning
	$applusers =0;       // Users who have not verified themselves
	$nverusers =0;       // Users not verified by admin but verified themselves
	$adminusers=0;       // Administrators
	$userlang  =array(); // Array for user languages
	$gedadmin  =array(); // Array for managers

echo '<div id="gen_info">';
echo '<h2>', WT_I18N::translate('General information'), '</h2>';
echo '<div id="x">';// div x

echo
	'<h2>', WT_I18N::translate('Users'), '</h2>',
	'<div id="users2">'; //id = users

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
			echo '<tr><td><div><a href="useradmin.php?action=listusers&amp;filter=gedadmin&amp;ged='.rawurlencode($geds['ged']), '">', $geds['name'], '</a></div></td><td>', $geds['number'], '</td></tr>';
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
		'<tr><td colspan="2"><div>', $stats->_usersLoggedIn('list'), '</div></td></tr>',
		'</table>';
echo '</div>'; // id = users

echo
	'<h2>', WT_I18N::translate('Family trees'), '</h2>',
	'<div id="trees2">',// id=trees
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
		'<tr><td>&nbsp;</td><td><span>', WT_I18N::translate('Count'), '</span></td></tr>',
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
	'</div>'; // id=trees

echo
	'<h2>', WT_I18N::translate('Recent changes'), '</h2>',
	'<div id="recent2">'; //id=recent
	echo
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
		'<tr><td>&nbsp;</td><td><span>', WT_I18N::translate('Day'), '</span></td><td><span>', WT_I18N::translate('Week'), '</span></td><td><span>', WT_I18N::translate('Month'), '</span></td></tr>',
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
	'</div>'; // id=recent

echo
	'</div>', //id = "x"
	WT_JS_START,
	'jQuery("#x").accordion({collapsible:true, active:false, icons:false});',
	WT_JS_END,
	'</div></div>'; //id = content_container



print_footer();
