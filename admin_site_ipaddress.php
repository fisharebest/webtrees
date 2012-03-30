<?php
// Manage Servers Page
//
// Allow a user the ability to manage servers i.e. allowing, banning, deleting
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'admin_site_ipaddress.php');
require './includes/session.php';

$controller=new WT_Controller_Base();
$controller
	->requireAdminLogin()
	->setPageTitle(WT_I18N::translate('Site access'))
	->pageHeader();

require_once WT_ROOT.'includes/functions/functions.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

$action = safe_GET('action');
if (empty($action)) $action = safe_POST('action');
$address = safe_GET('address');
if (empty($address)) $address = safe_POST('address');
$comment = safe_GET('comment');
if (empty($comment)) $comment = safe_POST('comment');
$comment = str_replace(array("\\", "\$", "\""), array("\\\\", "\\\$", "\\\""), $comment);

$deleteBanned = safe_POST('deleteBanned');
if (!empty($deleteBanned)) { // A "remove banned IP" button was pushed
	$action = 'deleteBanned';
	$address = $deleteBanned;
}

$deleteSearch = safe_POST('deleteSearch');
if (!empty($deleteSearch)) { // A "remove search engine IP" button was pushed
	$action = 'deleteSearch';
	$address = $deleteSearch;
}

if (empty($action)) $action = 'showForm';

/*
* Validate input string to be an IP address
*/
function validIP($address) {
	if (!preg_match('/^\d{1,3}\.(\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)$/', $address)) return false;
	$pieces = explode('.', $address);
	foreach ($pieces as $number) {
		if ($number!="*" && $number>255) return false;
	}
	return true;
}

if ($action=='addBanned' || $action=='addSearch' || $action=='deleteBanned' || $action=='deleteSearch') {
	if (validIP($address)) {
		// Even if we are adding a new record, we must delete the existing one first.
		WT_DB::prepare(
			"DELETE FROM `##ip_address` WHERE ip_address=?"
		)->execute(array($address));
		if ($action=='addBanned') {
			WT_DB::prepare(
				"INSERT INTO `##ip_address` (ip_address, category, comment) VALUES (?, ?, ?)"
			)->execute(array($address, 'banned', $comment));
		}
		if ($action=='addSearch') {
			WT_DB::prepare(
				"INSERT INTO `##ip_address` (ip_address, category, comment) VALUES (?, ?, ?)"
			)->execute(array($address, 'search-engine', $comment));
		}
	} else {
		if ($action=='addBanned') {
			$errorBanned=WT_I18N::translate('Invalid IP address.');
		}
		if ($action=='addSearch') {
			$errorSearch=WT_I18N::translate('Invalid IP address.');
		}
	}
	$action='showForm';
}

// Search Engine IP address table
echo
	'<table class="sites">',
	'<tr><td>',
	'<form name="searchengineform" action="', WT_SCRIPT_NAME, '" method="post">',
	'<table>',
	'<tr><th>', WT_I18N::translate('Manually mark Search Engines by IP'). help_link('help_manual_search_engines'), '</th></tr>',
	'<tr>',
	'<td>',
	'<table>';

$sql="SELECT ip_address, comment FROM `##ip_address` WHERE category='search-engine' ORDER BY INET_ATON(ip_address)";
$index=0;
$search_engines=WT_DB::prepare($sql)->fetchAssoc();
foreach ($search_engines as $ip_address=>$ip_comment) {
	echo '<tr><td><span dir="ltr"><input type="text" name="address', ++$index, '" size="16" value="', $ip_address, '" readonly="readonly"></span></td>';
	echo '<td><input type="text" name="comment', ++$index, '" size="60" value="', $ip_comment, '" readonly="readonly"></td><td class="button">';
	echo '<button name="deleteSearch" value="', $ip_address, '" type="submit">', WT_I18N::translate('Remove'), '</button>';
	echo '</td></tr>';
}
echo '<tr><td valign="top"><span dir="ltr"><input type="text" id="txtAddIp" name="address" size="16"  value="', empty($errorSearch) ? '':$address, '"></span></td>';
echo '<td><input type="text" id="txtAddComment" name="comment" size="60"  value="">';
echo '<br>', WT_I18N::translate('You may enter a comment here.'), '</td><td class="button" valign="top"><input name="action" type="hidden" value="addSearch">';
echo '<input type="submit" value="', WT_I18N::translate('Add'), '">';
echo '</td></tr>';

if (!empty($errorSearch)) {
	echo '<tr><td colspan="2"><span class="warning">';
	echo $errorSearch;
	echo '</span></td></tr>';
	$errorSearch = '';
}
echo '</table></td></tr></table></form></td></tr></table>';


// Banned IP address table 
echo '<table class="sites">',
	'<tr><td><form name="banIPform" action="', WT_SCRIPT_NAME, '" method="post">',
	'<table>',
	'<tr><th>', WT_I18N::translate('Ban Sites by IP').help_link('help_banning'), '</th></tr>',
	'<tr><td>',
	'<table>';
$sql="SELECT ip_address, comment FROM `##ip_address` WHERE category='banned' ORDER BY INET_ATON(ip_address)";
$banned=WT_DB::prepare($sql)->fetchAssoc();
foreach ($banned as $ip_address=>$ip_comment) {
	echo '<tr><td><span dir="ltr"><input type="text" name="address', ++$index, '" size="16" value="', $ip_address, '" readonly="readonly"></span></td>',
		 '<td><input type="text" name="comment', ++$index, '" size="60" value="', $ip_comment, '" readonly="readonly"></td><td class="button">',
		 '<button name="deleteBanned" value="', $ip_address, '" type="submit">', WT_I18N::translate('Remove'), '</button>',
		 '</td></tr>';
}
echo '<tr><td valign="top"><span dir="ltr"><input type="text" id="txtAddIp" name="address" size="16"  value="', empty($errorBanned) ? '':$address, '"></span></td>',
	 '<td><input type="text" id="txtAddComment" name="comment" size="60"  value="">',
	 '<br>', WT_I18N::translate('You may enter a comment here.'), '</td><td class="button" valign="top"><input name="action" type="hidden" value="addBanned">',
	 '<input type="submit" value="', WT_I18N::translate('Add'), '">',
	 '</td></tr>';

if (!empty($errorBanned)) {
	echo '<tr><td colspan="2"><span class="warning">';
	echo $errorBanned;
	echo '</span></td></tr>';
	$errorBanned = '';
}
echo '</table></td></tr></table></form></td></tr></table>';
