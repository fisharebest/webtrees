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
require_once WT_ROOT.'includes/classes/class_serviceclient.php';

print_header(i18n::translate('Manage sites'));
//-- only allow gedcom admins here
if (!WT_USER_GEDCOM_ADMIN) {
	print i18n::translate('<b>Access Denied</b><br />You do not have access to this resource.');
	//-- display messages as to why the editing access was denied
	if (!WT_USER_GEDCOM_ADMIN) print "<br />".i18n::translate('This user name cannot edit this GEDCOM.');
	print "<br /><br /><div class=\"center\"><a href=\"javascript: ".i18n::translate('Close Window')."\" onclick=\"window.close();\">".i18n::translate('Close Window')."</a></div>\n";
	print_footer();
	exit;
}

$banned = array();
if (file_exists($INDEX_DIRECTORY.'banned.php')) {
	require($INDEX_DIRECTORY.'banned.php');
}
$search_engines = array();
if (file_exists($INDEX_DIRECTORY."search_engines.php")) {
	require($INDEX_DIRECTORY.'search_engines.php');
}
$remoteServers = get_server_list();

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

$deleteServer = safe_POST('deleteServer');
if (!empty($deleteServer)) { // A "remove remote server" button was pushed
	$action = 'deleteServer';
	$address = $deleteServer;
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
			"DELETE FROM {$TBLPREFIX}ip_address WHERE ip_address=?"
		)->execute(array($address));
		if ($action=='addBanned') {
			WT_DB::prepare(
				"INSERT INTO {$TBLPREFIX}ip_address (ip_address, category, comment) VALUES (?, ?, ?)"
			)->execute(array($address, 'banned', $comment));
		}
		if ($action=='addSearch') {
			WT_DB::prepare(
				"INSERT INTO {$TBLPREFIX}ip_address (ip_address, category, comment) VALUES (?, ?, ?)"
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

/**
* Adds a server to the outbound remote linking list
*/
if ($action=='addServer') {
	$serverTitle = safe_POST('serverTitle', '[^<>"%{};]+'); // same as WT_REGEX_NOSCRIPT, but allow ampersand in title
	$serverURL = safe_POST('serverURL', WT_REGEX_URL);
	$gedcom_id = safe_POST('gedcom_id');
	$username  = safe_POST('username', WT_REGEX_USERNAME);
	$password  = safe_POST('password', WT_REGEX_PASSWORD);

	if (!$serverTitle=="" || !$serverURL=="") {
		$errorServer = '';
		$turl = preg_replace("~^\w+://~", "", $serverURL);
		//-- check the existing server list
		foreach ($remoteServers as $server) {
			if (stristr($server['url'], $turl)) {
				if (empty($gedcom_id) || (strpos($server['gedcom'], "_DBID $gedcom_id")!==false)) {
					$whichFile = $server['name'];
					$errorServer = i18n::translate('This remote database is already in the list as <i>%s</i>', $server['name']);
					break;
				}
			}
		}
		if (empty($errorServer)) {
			$gedcom_string = "0 @new@ SOUR\n";
			$gedcom_string.= "1 TITL ".$serverTitle."\n";
			$gedcom_string.= "1 URL ".$serverURL."\n";
			$gedcom_string.= "1 _DBID ".$gedcom_id."\n";
			$gedcom_string.= "2 _USER ".$username."\n";
			$gedcom_string.= "2 _PASS ".$password."\n";
			//-- only allow admin users to see password
			$gedcom_string.= "3 RESN confidential\n";

			$service = new ServiceClient($gedcom_string);
			$sid = $service->authenticate();
			if (empty($sid) || PEAR::isError($sid)) {
				$errorServer = i18n::translate('Failed to authenticate to remote site');
			} else {
				$serverID = append_gedrec($gedcom_string, WT_GED_ID);
				accept_all_changes($serverID, WT_GED_ID);
				$remoteServers = get_server_list(); // refresh the list
			}
		}
	} else $errorServer = i18n::translate('Please do not leave remote site title or URL blank');

	$action = 'showForm';
}

/**
* Removes a server from the remote linking outbound list
*/
if ($action=='deleteServer') {
	if (!empty($address)) {
		$sid = $address;

		if (count_linked_indi($sid, 'SOUR', WT_GED_ID) || count_linked_fam($sid, 'SOUR', WT_GED_ID)) {
			$errorDelete = i18n::translate('The remote server could not be removed because its Connections list is not empty.');
		} else {
			// No references exist:  it's OK to delete this source
			delete_gedrec($sid, WT_GED_ID);
		}
	}

	$remoteServers = get_server_list(); // refresh the list
	$action = 'showForm';
}

?>

<script language="JavaScript" type="text/javascript">
<!--
function showSite(siteID) {
	buttonShow = document.getElementById("buttonShow_"+siteID);
	siteDetails = document.getElementById("siteDetails_"+siteID);
	if (siteDetails.style.display=='none') {
		buttonShow.innerHTML='<?php echo i18n::translate('Hide Details');?>';
		siteDetails.style.display='block';
	} else {
		buttonShow.innerHTML='<?php echo i18n::translate('Show Details');?>';
		siteDetails.style.display='none';
	}
}
//-->
</script>


<!-- Search Engine IP address table -->
<table class="width66" align="center">
<tr>
	<td colspan="2" class="title" align="center">
	<?php echo i18n::translate('Manage sites');?>
	</td>
</tr>
<tr>
	<td>
	<form name="searchengineform" action="manageservers.php" method="post">
	<table class="width100" align="center">
		<tr>
		<td class="facts_label">
			<b><?php echo i18n::translate('Manually mark Search Engines by IP');?></b>
			<?php echo help_link('help_manual_search_engines'); ?>
		</td>
		</tr>
		<tr>
		<td class="facts_value">
			<table align="center">
<?php
	$sql="SELECT ip_address, comment FROM {$TBLPREFIX}ip_address WHERE category='search-engine' ORDER BY INET_ATON(ip_address)";
	$index=0;
	$search_engines=WT_DB::prepare($sql)->fetchAssoc();
	foreach ($search_engines as $ip_address=>$ip_comment) {
		echo '<tr><td>';
		if (isset($WT_IMAGES["remove"]["other"])) {
			echo '<input type="image" src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["remove"]["other"], '" alt="', i18n::translate('Delete'), '" name="deleteSearch" value="', $ip_address, '">';
		} else {
			echo '<button name="deleteSearch" value="', $ip_address, '" type="submit">', i18n::translate('Remove'), '</button>';
		}
		echo '</td><td><span dir="ltr"><input type="text" name="address', ++$index, '" size="16" value="', $ip_address, '" readonly /></span></td>';
		echo '<td><input type="text" name="comment', ++$index, '" size="60" value="', $ip_comment, '" readonly /></td></tr>';
	}
	echo '<tr><td valign="top"><input name="action" type="hidden" value="addSearch"/>';
	if (isset($WT_IMAGES["add"]["other"])) {
		echo '<input type="image" src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["add"]["other"], '" alt="', i18n::translate('Add'), '">';
	} else {
		echo '<input type="submit" value="', i18n::translate('Add'), '" />';
	}
	echo '</td><td valign="top"><span dir="ltr"><input type="text" id="txtAddIp" name="address" size="16"  value="', empty($errorSearch) ? '':$address, '" /></span></td>';
	echo '<td><input type="text" id="txtAddComment" name="comment" size="60"  value="" />';
	echo '<br />', i18n::translate('You may enter a comment here.'), '</td></tr>';

	if (!empty($errorSearch)) {
		print '<tr><td colspan="2"><span class="warning">';
		print $errorSearch;
		print '</span></td></tr>';
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
			<b><?php echo i18n::translate('Ban Sites by IP');?></b>
			<?php echo help_link('help_banning'); ?>
		</td>
		</tr>
		<tr>
		<td class="facts_value">
			<table align="center">
<?php
	$sql="SELECT ip_address, comment FROM {$TBLPREFIX}ip_address WHERE category='banned' ORDER BY INET_ATON(ip_address)";
	$banned=WT_DB::prepare($sql)->fetchAssoc();
	foreach ($banned as $ip_address=>$ip_comment) {
		echo '<tr><td>';
		if (isset($WT_IMAGES["remove"]["other"])) {
			echo '<input type="image" src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["remove"]["other"], '" alt="', i18n::translate('Delete'), '" name="deleteBanned" value="', $ip_address, '">';
		} else {
			echo '<button name="deleteBanned" value="', $ip_address, '" type="submit">', i18n::translate('Remove'), '</button>';
		}
		echo '</td><td><span dir="ltr"><input type="text" name="address', ++$index, '" size="16" value="', $ip_address, '" readonly /></span></td>';
		echo '<td><input type="text" name="comment', ++$index, '" size="60" value="', $ip_comment, '" readonly /></td></tr>';
	}
	echo '<tr><td valign="top"><input name="action" type="hidden" value="addBanned"/>';
	if (isset($WT_IMAGES["add"]["other"])) {
		echo '<input type="image" src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["add"]["other"], '" alt="', i18n::translate('Add'), '">';
	} else {
		echo '<input type="submit" value="', i18n::translate('Add'), '" />';
	}
	echo '</td><td valign="top"><span dir="ltr"><input type="text" id="txtAddIp" name="address" size="16"  value="', empty($errorBanned) ? '':$address, '" /></span></td>';
	echo '<td><input type="text" id="txtAddComment" name="comment" size="60"  value="" />';
	echo '<br />', i18n::translate('You may enter a comment here.'), '</td></tr>';

	if (!empty($errorBanned)) {
		print '<tr><td colspan="2"><span class="warning">';
		print $errorBanned;
		print '</span></td></tr>';
		$errorBanned = '';
	}
	echo '</table></td></tr></table></form></td></tr></table>';
?>

<!-- remote server list -->
<table class="width66" align="center">
<tr>
	<td>
	<form name="serverlistform" action="manageservers.php" method="post">
	<table class="width100">
		<tr>
		<td class="facts_label">
			<b><?php echo i18n::translate('Remote Servers');?></b>
		</td>
		</tr>
		<tr>
		<td class="facts_value">
			<table>
<?php
	foreach ($remoteServers as $sid=>$server) {
		$serverTitle = $server['name'];
		$serverURL = $server['url'];
		$gedcom_id = get_gedcom_value('_DBID', 1, $server['gedcom']);
		$username = get_gedcom_value('_USER', 2, $server['gedcom']);
?>
			<tr>
				<td>
				<button type="submit" onclick="return (confirm('<?php echo i18n::translate('Are you sure you want to delete this Source?');?>'))" name="deleteServer" value="<?php echo $sid;?>"><?php echo i18n::translate('Remove');?></button>
				&nbsp;&nbsp;
				<button id="buttonShow_<?php echo $sid;?>" type="button" onclick="showSite('<?php echo $sid;?>');"><?php echo i18n::translate('Show Details');?></button>
				&nbsp;&nbsp;
				<button type="button" onclick="window.open('source.php?sid=<?php echo $sid;?>&ged=<?php echo $GEDCOM;?>')"><?php echo i18n::translate('View Connections');?></button>
				&nbsp;&nbsp;
				<?php echo PrintReady($serverTitle); ?>
				<div id="siteDetails_<?php echo $sid;?>" style="display:none">
					<br />
					<table>
					<tr>
						<td class="facts_label width20">
						<?php print i18n::translate('ID');?>
						</td>
						<td class="facts_value">
						<?php echo $sid;?>
						</td>
					</tr>
					<tr>
						<td class="facts_label width20">
						<?php print i18n::translate('Title:');?>
						</td>
						<td class="facts_value">
						<?php echo PrintReady($serverTitle);?>
						</td>
					</tr>
					<tr>
						<td class="facts_label width20">
						<?php print i18n::translate('Site URL/IP');?>
						</td>
						<td class="facts_value">
						<?php echo PrintReady($serverURL);?>
						</td>
					</tr>
					<tr>
						<td class="facts_label width20">
						<?php echo i18n::translate('Database ID:');?>
						</td>
						<td class="facts_value">
						<?php echo PrintReady($gedcom_id);?>
						</td>
					</tr>
					<tr>
						<td class="facts_label width20">
						<?php print i18n::translate('Username');?>
						</td>
						<td class="facts_value">
						<?php echo PrintReady($username);?>
						</td>
					</tr>
					</table>
					<br />
				</div>
				</td>
			</tr>
<?php
			}
	if (!empty($errorDelete)) {
		print '<tr><td colspan="2"><span class="warning">';
		print $errorDelete;
		print '</span></td></tr>';
		$errorDelete = '';
	}
?>
			</table>
		</td>
		</tr>
	</table>
	</form>
	</td>
</tr>
</table>

<!-- Add remote server form -->
<?php
if (empty($errorServer)) {
	$serverTitle = '';
	$serverURL = '';
	$gedcom_id = '';
	$username = '';
}
?>
<form name="addserversform" action="manageservers.php" method="post"">
<table class="width66" align="center">
<tr>
	<td valign="top">
	<table class="width100">
		<tr>
		<td class="facts_label" colspan="2">
			<b><?php print i18n::translate('Add new site');?></b>
			<?php echo help_link('help_remotesites'); ?>
		</td>
		</tr>
		<tr>
		<td class="facts_label width20">
			<?php print i18n::translate('Title:');?>
		</td>
		<td class="facts_value">
			<input type="text" size="66" name="serverTitle" value="<?php echo PrintReady($serverTitle);?>" />
		</td>
		</tr>
		<tr>
		<td class="facts_label width20">
			<?php echo i18n::translate('Site URL/IP'), help_link('link_remote_site'); ?>
		</td>
		<td class="facts_value">
			<input type="text" size="66" name="serverURL" value="<?php echo PrintReady($serverURL);?>" />
			<br /><?php echo i18n::translate('Example:');?>&nbsp;&nbsp;http://www.remotesite.com/phpGedView/genservice.php?wsdl
		</td>
		</tr>
		<tr>
		<td class="facts_label width20">
			<?php echo i18n::translate('Database ID:');?>
		</td>
		<td class="facts_value">
			<input type="text" name="gedcom_id" value="<?php echo PrintReady($gedcom_id);?>" />
		</td>
		</tr>
		<tr>
		<td class="facts_label width20">
			<?php print i18n::translate('Username');?>
		</td>
		<td class="facts_value">
			<input type="text" name="username" value="<?php echo PrintReady($username);?>" />
		</td>
		</tr>
		<tr>
		<td class="facts_label width20">
			<?php print i18n::translate('Password');?>
		</td>
		<td class="facts_value">
			<input type="password" name="password" />
		</td>
		</tr>
		<tr>
		<td class="facts_value" align="center" colspan="2">
			<input type="submit" value="<?php echo i18n::translate('Add');?>" />
			<input name="action" type="hidden" value="addServer"/>
<?php
	if (!empty($errorServer)) {
		print '<br /><br /><span class="warning">';
		print $errorServer;
		print '</span>';
		$errorServer = '';
	}
?>
		</td>
		</tr>
	</table>
	</td>
</tr>
</table>
</form>
<?php
	print_footer();
?>
