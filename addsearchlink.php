<?php
/**
 * Allows user to select a person on their server to create a remote link
 * to a person selected from the search results.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * @subpackage Display
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'addsearchlink.php');
require './includes/session.php';

print_simple_header(i18n::translate('Add Local Link'));

//-- only allow users with editing access to this page
if (!WT_USER_CAN_EDIT) {
	print i18n::translate('<b>Access Denied</b><br />You do not have access to this resource.');
	print_simple_footer();
	exit;
}

if (isset($_REQUEST['pid'])) $pid = $_REQUEST['pid'];
if (isset($_REQUEST['server'])) $server = $_REQUEST['server'];
if (isset($_REQUEST['indiName'])) $indiName = $_REQUEST['indiName'];

//To use addsearchlink you should have come from a multisearch result link
if(isset($pid) && isset($server) && isset($indiName))
{
?>

<br/>
<center><font size="4"><?php echo $indiName ?></font><center><br/>
<table align="center">
	<tr>
		<td>
			<form method="post" name="addRemoteRelationship" action="addremotelink.php">
				<input type="hidden" name="action" value="addlink" />
				<input type="hidden" name="location" value="remote" />
				<input type="hidden" name="cbExistingServers" value="<?php print $server; ?>" />
				<input type="hidden" name="txtPID" value="<?php print $pid; ?>" />

				<table class="facts_table" align="center">
					<tr>
						<td class="facts_label03" colspan="3" align="center">
							<?php echo i18n::translate('Add Remote Link'), help_link('link_remote'); ?>
						</td>
					</tr>
					<tr>
						<td class="descriptionbox width20" id="tdId">
							<?php echo i18n::translate('Local Person ID'), help_link('link_person_id'); ?>
						</td>
						<td class="optionbox"><input type="text" id="pid" name="pid" size="14"/></td>
						<td class="optionbox" rowspan="2"><br/>
							<input type="submit" value="<?php echo i18n::translate('Add Link');?>" id="btnSubmit" name="btnSubmit" value="add"/>
						</td>
					</tr>
					<tr>
						<td class="descriptionbox width20">
							<?php echo i18n::translate('Relationship to current person'), help_link('link_remote_rel'); ?>
						</td>
						<td class="optionbox">
							<select id="cbRelationship" name="cbRelationship">
								<option value="self" selected><?php echo i18n::translate('Same as current');?></option>
								<option value="mother"><?php echo i18n::translate('Mother');?></option>
								<option value="father"><?php echo i18n::translate('Father');?></option>
								<option value="husband"><?php echo i18n::translate('Husband');?></option>
								<option value="wife"><?php echo i18n::translate('Wife');?></option>
								<option value="son"><?php echo i18n::translate('Son');?></option>
								<option value="daughter"><?php echo i18n::translate('Daughter');?></option>
							</select>
						</td>
					</tr>
					</table><br/>
				</form>
		</td>
	</tr>
</table>

<?php
}
else {
	print "<br/><center><b><font color=\"red\">Oh, now you're hacking!</font></b></center><br/>";
}
print_footer(); ?>
