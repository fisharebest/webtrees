<?php
/**
*  Manage Servers Page
*
*  Allow a user the ability to manage servers i.e. allowing, banning, deleting
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
*
* Modifications Copyright (c) 2010 Greg Roach
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
* @author rbennett
*/

define('WT_SCRIPT_NAME', 'manageservers.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

print_header(i18n::translate('Manage sites'));
//-- only allow managers here
if (!WT_USER_GEDCOM_ADMIN) {
	echo i18n::translate('<b>Access Denied</b><br />You do not have access to this resource.');
	//-- display messages as to why the editing access was denied
	if (!WT_USER_GEDCOM_ADMIN) echo "<br />".i18n::translate('This user name cannot edit this GEDCOM.');
	echo "<br /><br /><div class=\"center\"><a href=\"javascript: ".i18n::translate('Close Window')."\" onclick=\"window.close();\">".i18n::translate('Close Window')."</a></div>";
	print_footer();
	exit;
}

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
			$errorBanned=i18n::translate('Invalid IP address.');
		}
		if ($action=='addSearch') {
			$errorSearch=i18n::translate('Invalid IP address.');
		}
	}
	$action='showForm';
}

?>

<script language="JavaScript" type="text/javascript">
<!--
function showSite(siteID) {
	buttonShow = document.getElementById("buttonShow_"+siteID);
	siteDetails = document.getElementById("siteDetails_"+siteID);
	if (siteDetails.style.display=='none') {
		buttonShow.innerHTML='<?php echo i18n::translate('Hide Details'); ?>';
		siteDetails.style.display='block';
	} else {
		buttonShow.innerHTML='<?php echo i18n::translate('Show Details'); ?>';
		siteDetails.style.display='none';
	}
}
//-->
</script>

<?php
// Search Engine IP address table
echo '<p class="center"><input TYPE="button" VALUE="', i18n::translate('Return to Administration page'), '" onclick="javascript:window.location=\'admin.php\'" /></p>',
	'<h2 class="center">', i18n::translate('Manage sites'), '</h2>',
	'<table class="width66" align="center">',
	'<tr><td>',
	'<form name="searchengineform" action="manageservers.php" method="post">',
	'<table class="width100" align="center">',
		'<tr>',
		'<td class="facts_label"><b>', i18n::translate('Manually mark Search Engines by IP'), '</b>'. help_link('help_manual_search_engines'), '</td>',
		'</tr>',
		'<tr>',
		'<td class="facts_value">',
			'<table align="center">';

			$sql="SELECT ip_address, comment FROM `##ip_address` WHERE category='search-engine' ORDER BY INET_ATON(ip_address)";
			$index=0;
			$search_engines=WT_DB::prepare($sql)->fetchAssoc();
			foreach ($search_engines as $ip_address=>$ip_comment) {
				echo '<tr><td>';
				if (isset($WT_IMAGES["remove"])) {
					echo '<input type="image" src="', $WT_IMAGES["remove"], '" alt="', i18n::translate('Delete'), '" name="deleteSearch" value="', $ip_address, '">';
				} else {
					echo '<button name="deleteSearch" value="', $ip_address, '" type="submit">', i18n::translate('Remove'), '</button>';
				}
				echo '</td><td><span dir="ltr"><input type="text" name="address', ++$index, '" size="16" value="', $ip_address, '" readonly /></span></td>';
				echo '<td><input type="text" name="comment', ++$index, '" size="60" value="', $ip_comment, '" readonly /></td></tr>';
			}
			echo '<tr><td valign="top"><input name="action" type="hidden" value="addSearch"/>';
			if (isset($WT_IMAGES["add"])) {
				echo '<input type="image" src="', $WT_IMAGES["add"], '" alt="', i18n::translate('Add'), '">';
			} else {
				echo '<input type="submit" value="', i18n::translate('Add'), '" />';
			}
			echo '</td><td valign="top"><span dir="ltr"><input type="text" id="txtAddIp" name="address" size="16"  value="', empty($errorSearch) ? '':$address, '" /></span></td>';
			echo '<td><input type="text" id="txtAddComment" name="comment" size="60"  value="" />';
			echo '<br />', i18n::translate('You may enter a comment here.'), '</td></tr>';

			if (!empty($errorSearch)) {
				echo '<tr><td colspan="2"><span class="warning">';
				echo $errorSearch;
				echo '</span></td></tr>';
				$errorSearch = '';
			}
	echo '</table></td></tr></table></form></td></tr></table>';
?>

<!-- Banned IP address table -->
<table class="width66" align="center">
<tr>
	<td>
	<form name="banIPform" action="manageservers.php" method="post">
	<table class="width100" align="center">
		<tr>
		<td class="facts_label">
			<b><?php echo i18n::translate('Ban Sites by IP'); ?></b>
			<?php echo help_link('help_banning'); ?>
		</td>
		</tr>
		<tr>
		<td class="facts_value">
			<table align="center">
<?php
$sql="SELECT ip_address, comment FROM `##ip_address` WHERE category='banned' ORDER BY INET_ATON(ip_address)";
$banned=WT_DB::prepare($sql)->fetchAssoc();
foreach ($banned as $ip_address=>$ip_comment) {
	echo '<tr><td>';
	if (isset($WT_IMAGES["remove"])) {
		echo '<input type="image" src="', $WT_IMAGES["remove"], '" alt="', i18n::translate('Delete'), '" name="deleteBanned" value="', $ip_address, '">';
	} else {
		echo '<button name="deleteBanned" value="', $ip_address, '" type="submit">', i18n::translate('Remove'), '</button>';
	}
	echo '</td><td><span dir="ltr"><input type="text" name="address', ++$index, '" size="16" value="', $ip_address, '" readonly /></span></td>';
	echo '<td><input type="text" name="comment', ++$index, '" size="60" value="', $ip_comment, '" readonly /></td></tr>';
}
echo '<tr><td valign="top"><input name="action" type="hidden" value="addBanned"/>';
if (isset($WT_IMAGES["add"])) {
	echo '<input type="image" src="', $WT_IMAGES["add"], '" alt="', i18n::translate('Add'), '">';
} else {
	echo '<input type="submit" value="', i18n::translate('Add'), '" />';
}
echo '</td><td valign="top"><span dir="ltr"><input type="text" id="txtAddIp" name="address" size="16"  value="', empty($errorBanned) ? '':$address, '" /></span></td>';
echo '<td><input type="text" id="txtAddComment" name="comment" size="60"  value="" />';
echo '<br />', i18n::translate('You may enter a comment here.'), '</td></tr>';

if (!empty($errorBanned)) {
	echo '<tr><td colspan="2"><span class="warning">';
	echo $errorBanned;
	echo '</span></td></tr>';
	$errorBanned = '';
}
echo '</table></td></tr></table></form></td></tr></table>';
print_footer();
