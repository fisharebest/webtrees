<?php
/**
 * Edit Privacy Settings
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @author PGV Development Team
 * @package webtrees
 * @subpackage Privacy
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'edit_privacy.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_print_facts.php';
require WT_ROOT.'includes/functions/functions_edit.php';

if (!WT_USER_GEDCOM_ADMIN) {
	header('Location: editgedcoms.php');
	exit;
}

switch (safe_POST('action')) {
case 'delete':
	WT_DB::prepare(
		"DELETE FROM `##default_resn` WHERE default_resn_id=?"
	)->execute(array(safe_POST('default_resn_id')));
	break;
case 'add':
	if ((safe_POST('xref') || safe_POST('tag_type')) && safe_POST('resn')) {
		WT_DB::prepare(
			"REPLACE INTO `##default_resn` (gedcom_id, xref, tag_type, resn) VALUES (?, ?, ?, ?)"
		)->execute(array(WT_GED_ID, safe_POST('xref'), safe_POST('tag_type'), safe_POST('resn')));
	}
	break;
case 'update':
	set_gedcom_setting(WT_GED_ID, 'SHOW_DEAD_PEOPLE',           safe_POST('SHOW_DEAD_PEOPLE'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_LIVING_NAMES',          safe_POST('SHOW_LIVING_NAMES'));
	set_gedcom_setting(WT_GED_ID, 'MAX_ALIVE_AGE',              safe_POST('MAX_ALIVE_AGE'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_MULTISITE_SEARCH',      safe_POST('SHOW_MULTISITE_SEARCH'));
	set_gedcom_setting(WT_GED_ID, 'PRIVACY_BY_YEAR',            safe_POST('PRIVACY_BY_YEAR'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_DEAD_PEOPLE',           safe_POST('SHOW_DEAD_PEOPLE'));
	set_gedcom_setting(WT_GED_ID, 'USE_RELATIONSHIP_PRIVACY',   safe_POST('USE_RELATIONSHIP_PRIVACY'));
	set_gedcom_setting(WT_GED_ID, 'MAX_RELATION_PATH_LENGTH',   safe_POST('MAX_RELATION_PATH_LENGTH'));
	set_gedcom_setting(WT_GED_ID, 'SHOW_PRIVATE_RELATIONSHIPS', safe_POST('SHOW_PRIVATE_RELATIONSHIPS'));
	header('Location: editgedcoms.php');
	exit;
}

$PRIVACY_CONSTANTS=array(
	'none'        =>i18n::translate('Show to public'),
	'privacy'     =>i18n::translate('Show only to authenticated users'),
	'confidential'=>i18n::translate('Show only to admin users'),
	'hidden'      =>i18n::translate('Hide even from admin users')
);

$all_tags=array();
$tags=array_unique(array_merge(
	explode(',', $INDI_FACTS_ADD),
	explode(',', $FAM_FACTS_ADD),
	explode(',', $NOTE_FACTS_ADD),
	explode(',', $SOUR_FACTS_ADD),
	explode(',', $REPO_FACTS_ADD),
	array('INDI', 'FAM', 'SOUR', 'REPO', 'OBJE', 'NOTE', 'SUBM', 'SUBN')
));

foreach ($tags as $tag) {
	$all_tags[$tag]=translate_fact($tag);
}

uasort($all_tags, 'utf8_strcasecmp');

print_header(i18n::translate('Edit privacy settings'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';
?>
<table class="facts_table">
	<tr>
		<td class="facts_label"><?php
			print "<h2>".i18n::translate('Edit GEDCOM privacy settings')." - ".WT_GEDCOM. "</h2>";
			print "<a href=\"editgedcoms.php\"><b>";
			print i18n::translate('Return to the GEDCOM management menu');
			print "</b></a><br /><br />"; ?>
		</td>
	</tr>
</table>
<script language="JavaScript" type="text/javascript">
<!--
		var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
//-->
</script>

<form name="editprivacyform" method="post" action="edit_privacy.php">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="ged" value="<?php print $GEDCOM;?>" />

	<table class="facts_table">
		<tr>
			<td class="topbottombar" colspan="2">
				<?php echo i18n::translate('General privacy settings'); ?>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width20">
				<?php echo i18n::translate('Show dead people'), help_link('SHOW_DEAD_PEOPLE'); ?>
			</td>
			<td class="optionbox">
					<?php echo edit_field_access_level("SHOW_DEAD_PEOPLE", get_gedcom_setting(WT_GED_ID, 'SHOW_DEAD_PEOPLE')); ?>
			</td>
		</tr>

		<tr>
			<td class="descriptionbox wrap">
				<?php echo i18n::translate('Show living names'), help_link('SHOW_LIVING_NAMES'); ?>
			</td>
			<td class="optionbox">
					<?php echo edit_field_access_level("SHOW_LIVING_NAMES", get_gedcom_setting(WT_GED_ID, 'SHOW_LIVING_NAMES')); ?>
			</td>
		</tr>

		<tr>
			<td class="descriptionbox wrap">
				<?php echo i18n::translate('Show multi-site search'), help_link('SHOW_MULTISITE_SEARCH'); ?>
			</td>
			<td class="optionbox">
					<?php echo edit_field_access_level("SHOW_MULTISITE_SEARCH", get_gedcom_setting(WT_GED_ID, 'SHOW_MULTISITE_SEARCH')); ?>
			</td>
		</tr>

		<tr>
			<td class="descriptionbox wrap">
				<?php echo i18n::translate('Limit privacy by age of event'), help_link('PRIVACY_BY_YEAR'); ?>
			</td>
			<td class="optionbox">
				<?php echo edit_field_yes_no('PRIVACY_BY_YEAR', get_gedcom_setting(WT_GED_ID, 'PRIVACY_BY_YEAR')); ?>
			</td>
		</tr>

		<tr>
			<td class="descriptionbox wrap">
				<?php echo i18n::translate('Show private relationships'), help_link('SHOW_PRIVATE_RELATIONSHIPS'); ?>
			</td>
			<td class="optionbox">
				<?php echo edit_field_yes_no('SHOW_PRIVATE_RELATIONSHIPS', get_gedcom_setting(WT_GED_ID, 'SHOW_PRIVATE_RELATIONSHIPS')); ?>
			</td>
		</tr>

		<tr>
			<td class="descriptionbox wrap">
				<?php echo i18n::translate('Use relationship privacy'), help_link('USE_RELATIONSHIP_PRIVACY'); ?>
			</td>
			<td class="optionbox">
				<?php echo edit_field_yes_no('USE_RELATIONSHIP_PRIVACY', get_gedcom_setting(WT_GED_ID, 'USE_RELATIONSHIP_PRIVACY')); ?>
			</td>
		</tr>

		<tr>
			<td class="descriptionbox wrap">
				<?php echo i18n::translate('Max. relation path length'), help_link('MAX_RELATION_PATH_LENGTH'); ?>
			</td>
			<td class="optionbox">
				<select size="1" name="MAX_RELATION_PATH_LENGTH"><?php
				for ($y = 1; $y <= 10; $y++) {
					print "<option";
					if (get_gedcom_setting(WT_GED_ID, 'MAX_RELATION_PATH_LENGTH') == $y) print " selected=\"selected\"";
					print ">";
					print $y;
					print "</option>";
				}
				?></select>
			</td>
		</tr>

		<tr>
			<td class="descriptionbox wrap">
				<?php echo i18n::translate('Check marriage relations'), help_link('CHECK_MARRIAGE_RELATIONS'); ?>
			</td>
			<td class="optionbox">
				<?php echo edit_field_yes_no('CHECK_MARRIAGE_RELATIONS', get_gedcom_setting(WT_GED_ID, 'CHECK_MARRIAGE_RELATIONS')); ?>
			</td>
		</tr>

		<tr>
			<td class="descriptionbox wrap">
				<?php echo i18n::translate('Age at which to assume a person is dead'), help_link('MAX_ALIVE_AGE'); ?>
			</td>
			<td class="optionbox">
				<input type="text" name="MAX_ALIVE_AGE" value="<?php print get_gedcom_setting(WT_GED_ID, 'MAX_ALIVE_AGE'); ?>" size="5" />
			</td>
		</tr>
		<tr>
			<td class="topbottombar" colspan="2">
				<input type="submit" value="<?php echo i18n::translate('Save'); ?>" />
			</td>
		</tr>
	</table>
	</form>
	<br />
	<table class="facts_table">
		<tr>
			<td class="topbottombar" colspan="4">
				<?php echo i18n::translate('Privacy restrictions - these apply to records and facts that do not contain a GEDCOM RESN tag'); ?>
			</td>
		</tr>
<?php
$rows=WT_DB::prepare(
	"SELECT default_resn_id, tag_type, xref, resn".
	" FROM `##default_resn`".
	" WHERE gedcom_id=?".
	" ORDER BY xref, tag_type"
)->execute(array(WT_GED_ID))->fetchAll();
foreach ($rows as $row) {
	echo '<form method="post" action="', WT_SCRIPT_NAME, '"><tr><td class="optionbox" width="*">';
	echo '<input type="hidden" name="action" value="delete">';
	echo '<input type="hidden" name="default_resn_id" value="', $row->default_resn_id, '">';
	if ($row->xref) {
		$record=GedcomRecord::getInstance($row->xref);
		if ($record) {
			$name=$record->getFullName();
		} else {
			$name=i18n::translate('this record does not exist');
		}
		// I18N: "Record ID I1234 (John DOE)
		echo i18n::translate('%1$s (%2$s)', $name, $row->xref);
	} else {
		echo '&nbsp;';
	}
	echo '</td><td class="optionbox" width="*">';
	if ($row->tag_type) {
		// I18N: "Record type SOUR (Source)
		echo i18n::translate('%1$s [%2$s]', translate_fact($row->tag_type), $row->tag_type);
	} else {
		echo '&nbsp;';
	}
	echo '</td><td class="optionbox" width="1">';
	echo $PRIVACY_CONSTANTS[$row->resn];
	echo '</td><td class="optionbox" width="1">';
	echo '<input type="submit" value="', i18n::translate('Delete'), '" />';
	echo '</td></tr></form>';
}
echo '<form method="post" action="', WT_SCRIPT_NAME, '"><tr><td class="optionbox" width="*">';
echo '<input type="hidden" name="action" value="add">';
echo '<input type="text" class="pedigree_form" name="xref" id="xref" size="6" />';
print_findindi_link("xref","");
print_findfamily_link("xref");
print_findsource_link("xref");
print_findrepository_link("xref");
print_findmedia_link("xref", "1media");
echo '</td><td class="optionbox" width="*">';
echo select_edit_control('tag_type', $all_tags, '', null, null);
echo '</td><td class="optionbox" width="1">';
echo select_edit_control('resn', $PRIVACY_CONSTANTS, null, 'privacy', null);
echo '</td><td class="optionbox" width="1">';
echo '<input type="submit" value="', i18n::translate('Add'), '" />';
echo '</td></tr></form>';
echo '</table>';
print_footer();
