<?php
/**
*  Add Remote Link Page
*
*  Allow a user the ability to add links to people from other servers and other gedcoms.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
* @subpackage Charts
* @version $Id$
*/

define('WT_SCRIPT_NAME', 'addremotelink.php');
require './includes/session.php';
require WT_ROOT.'includes/controllers/remotelink_ctrl.php';

$controller=new RemoteLinkController();
$controller->init();

print_simple_header(i18n::translate('Add Remote Link'));

$pid=safe_REQUEST($_REQUEST, 'pid', WT_REGEX_XREF);
$action=safe_POST('action', array('addlink'));

//-- only allow gedcom admins to create remote links
if (!$controller->canAccess()) {
	echo '<span class="error">', i18n::translate('<b>Access Denied</b><br />You do not have access to this resource.'), '<br />';
	if (!WT_USER_GEDCOM_ADMIN) {
		echo i18n::translate('This user name cannot edit this GEDCOM.');
	} else if (!$ALLOW_EDIT_GEDCOM) {
		echo i18n::translate('Editing this GEDCOM has been disabled by the administrator.');
	} else {
		echo i18n::translate('Privacy settings prevent you from editing this record.');
		if ($pid) {
			echo '<br />', i18n::translate('You have no access to'), ' ', $pid;
		}
	}
	echo '</span><br /><br /><div class="center"><a href="javascript://', i18n::translate('Close Window'), '" onclick="window.close();">', i18n::translate('Close Window'), '</a></div>';
	print_simple_footer();
	exit;
}

$success=$controller->runAction($action);

echo WT_JS_START;
?>
function sameServer() {
	alert('<?php echo i18n::translate('You have selected the same site.'); ?>');
}
function remoteServer() {
	alert('<?php echo i18n::translate('You have selected a remote site.'); ?>');
}
function swapComponents(btnPressed) {
	var labelSite = document.getElementById('labelSite');
	var existingContent = document.getElementById('existingContent');
	var localContent = document.getElementById('localContent');
	var remoteContent = document.getElementById('remoteContent');
	if (btnPressed=="remote") {
		labelSite.innerHTML = '<?php echo i18n::translate('Site'); ?>';
		existingContent.style.display='none';
		localContent.style.display='none';
		remoteContent.style.display='block';
	} else if (btnPressed=="local") {
		labelSite.innerHTML = '<?php echo i18n::translate('Database ID'); ?>';
		existingContent.style.display='none';
		localContent.style.display='block';
		remoteContent.style.display='none';
	} else {
		labelSite.innerHTML = '<?php echo i18n::translate('Site'); ?>';
		existingContent.style.display='block';
		localContent.style.display='none';
		remoteContent.style.display='none';
	}
}
function edit_close() {
	if (window.opener.showchanges) window.opener.showchanges();
	window.close();
}
function checkform(frm) {
	if (frm.txtPID.value=='') {
		alert('Please enter all fields.');
		return false;
	}
	return true;
}
<?php
echo WT_JS_END;

if (!$success) {
?>
<form method="post" name="addRemoteRelationship" action="addremotelink.php" onsubmit="return checkform(this);">
<input type="hidden" name="action" value="addlink" />
<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
<span class="title">
	<?php echo PrintReady($controller->person->getFullName()), '&nbsp;', PrintReady("(".$controller->person->getXref().")"); ?>
</span><br /><br />
<table class="facts_table">
	<tr>
		<td class="title" colspan="2">
			<?php echo i18n::translate('Add Remote Link'), help_link('link_remote'); ?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
			<?php echo i18n::translate('Relationship to current person'), help_link('link_remote_rel'); ?>
		</td>
		<td class="optionbox">
			<select id="cbRelationship" name="cbRelationship">
				<?php
					foreach (array(
						'current_person'=>i18n::translate('Same as current'),
						'mother'        =>i18n::translate('Mother'),
						'father'        =>i18n::translate('Father'),
						'husband'       =>i18n::translate('Husband'),
						'wife'          =>i18n::translate('Wife'),
						'son'           =>i18n::translate('Son'),
						'daughter'      =>i18n::translate('Daughter')
					) as $rel=>$display) {
					echo '<option value="', $rel, '"';
					if ($rel==$controller->form_cbRelationship) {
						echo ' checked="checked"';
					}
					echo '>', $display, '</option>';
				}
				?>
			</select>
		</td>
	</tr>
	<?php if ($controller->server_list || $controller->gedcom_list) { ?>
	<tr>
		<td class="descriptionbox wrap width20">
			<?php echo i18n::translate('Site location'), help_link('link_remote_location'); ?>
		</td>
		<td class="optionbox">
			<?php
				echo '<input type="radio" id="local" name="location" value="local" onclick="swapComponents(\'local\')"';
				if (!$controller->gedcom_list) {
					echo ' disabled';
				}
				if ($controller->form_location=='local') {
					echo ' checked="checked"';
				}
				echo '/>', i18n::translate('Local site'), '&nbsp;&nbsp;&nbsp';
				echo '<input type="radio" id="existing" name="location" value="existing" onclick="swapComponents(\'existing\');"';
				if (!$controller->server_list) {
					echo ' disabled';
				}
				if ($controller->form_location=='existing') {
					echo ' checked="checked"';
				}
				echo '/>', i18n::translate('Existing remote site'), '&nbsp;&nbsp;&nbsp;';
				echo '<input type="radio" id="remote" name="location" value="remote" onclick="swapComponents(\'remote\');"';
				if ($controller->form_location=='remote') {
					echo ' checked="checked"';
				}
				echo '/>', i18n::translate('New remote site');
			?>
		</td>
	</tr>
	<?php } ?>

	<tr>
		<td class="descriptionbox wrap width20">
			<?php echo i18n::translate('Person ID'), help_link('link_person_id'); ?>
		</td>
		<td class="optionbox">
			<input type="text" id="txtPID" name="txtPID" size="14" value="<?php echo $controller->form_txtPID; ?>" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
			<span id="labelSite"><?php echo i18n::translate('Site'); ?></span><?php echo help_link('link_remote_site'); ?>
		</td>
		<td class="optionbox" id="tdUrlText">
			<div id="existingContent">
				<?php echo i18n::translate('Existing remote site'); ?><br />
				<select id="cbExistingServers" name="cbExistingServers"	style="width: 400px;">
					<?php
						foreach ($controller->server_list as $key=>$server) {
							echo '<option value="', $key, '"';
							if ($key==$controller->form_cbExistingServers) {
								echo ' selected="selected"';
							}
							echo '/>', PrintReady($server['name']), '</option>';
						}
					?>
				</select><br /><br />
			</div>
			<div id="remoteContent">
				<?php echo i18n::translate('Type in a new site.'); ?>
				<table>
					<tr>
						<td ><?php echo i18n::translate('Title:'); ?></td>
						<td><input type="text" id="txtTitle" name="txtTitle" size="66" value="<?php echo $controller->form_txtTitle; ?>" /></td>
					</tr><tr>
						<td valign="top"><?php echo i18n::translate('Site URL:'); ?></td>
						<td><input type="text" id="txtURL" name="txtURL" size="66" value="<?php echo $controller->form_txtURL; ?>" />
					</tr><tr>
						<td>&nbsp;&nbsp;<?php echo i18n::translate('Example:'); ?></td>
						<td>http://www.remotesite.com/phpGedView/genservice.php?wsdl</td>
					</tr><tr>
						<td><?php echo i18n::translate('Database ID:'); ?></td>
						<td><input type="text" id="txtGID" name="txtGID" value="<?php echo $controller->form_txtGID; ?>" /></td>
					</tr><tr>
						<td><?php echo i18n::translate('Username: '); ?></td>
						<td><input type="text" id="txtUsername" name="txtUsername" value="<?php echo $controller->form_txtUsername; ?>" /></td>
					</tr><tr>
						<td><?php echo i18n::translate('Password: '); ?></td>
						<td><input type="password" id="txtPassword" name="txtPassword" value="<?php echo $controller->form_txtPassword; ?>" /></td>
					</tr>
				</table>
			</div>
			<div id="localContent">
			<table><tr>
					<td ><?php echo i18n::translate('Title:'); ?></td>
					<td><input type="text" id="txtCB_Title" name="txtCB_Title" size="66" value="<?php echo $controller->form_txtCB_Title; ?>" /></td>
				</tr><tr>
					<td valign="top"><?php echo i18n::translate('GEDCOM File:'); ?></td>
					<td><select id="txtCB_GID" name="txtCB_GID">
					<?php
						foreach ($controller->gedcom_list as $ged_name) {
							echo '<option value="', $ged_name, '"';
							if ($ged_name==$controller->form_txtCB_GID) {
								echo ' selected="selected"';
							}
							echo '>', $ged_name, '</option>';
						}
					?>
					</select></td>
				</tr></table>
			</div>
		</td>
	</tr>
	<?php
	if (WT_USER_IS_ADMIN) {
		echo "<tr><td class=\"descriptionbox ", $TEXT_DIRECTION, " wrap width25\">";
		echo i18n::translate('Admin Option'), help_link('no_update_CHAN'), '</td><td class="optionbox wrap">';
		if ($NO_UPDATE_CHAN) {
			echo "<input type=\"checkbox\" checked=\"checked\" name=\"preserve_last_changed\" />\n";
		} else {
			echo "<input type=\"checkbox\" name=\"preserve_last_changed\" />\n";
		}
		echo i18n::translate('Do not update the CHAN (Last Change) record'), "<br />\n";
		$event = new Event(get_sub_record(1, "1 CHAN", ""));
		echo format_fact_date($event, false, true);
		echo "</td></tr>\n";
	}
	?>
</table>
<br />
<input type="submit" value="<?php echo i18n::translate('Add Link'); ?>" id="btnSubmit" name="btnSubmit" />
</form>
<?php
	echo WT_JS_START, 'swapComponents("', $controller->form_location, '");', WT_JS_END;
}

// autoclose window when update successful
if ($success && $EDIT_AUTOCLOSE) {
	echo WT_JS_START, 'edit_close();', WT_JS_END;
} else {
	echo '<div class="center"><a href="javascript://', i18n::translate('Close Window'), '" onclick="edit_close();">', i18n::translate('Close Window'), '</a></div>';
	print_simple_footer();
}

?>
