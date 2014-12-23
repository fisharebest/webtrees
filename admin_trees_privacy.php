<?php
// UI for online updating of the GEDCOM config file.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

define('WT_SCRIPT_NAME', 'admin_trees_privacy.php');

require './includes/session.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isManager())
	->setPageTitle(WT_I18N::translate('Privacy'));

require WT_ROOT . 'includes/functions/functions_edit.php';

$no_yes = array(
	0 => WT_I18N::translate('no'),
	1 => WT_I18N::translate('yes'),
);

$disable_enable = array(
	0 => WT_I18N::translate('disable'),
	1 => WT_I18N::translate('enable'),
);

$PRIVACY_CONSTANTS = array(
	'none'         => WT_I18N::translate('Show to visitors'),
	'privacy'      => WT_I18N::translate('Show to members'),
	'confidential' => WT_I18N::translate('Show to managers'),
	'hidden'       => WT_I18N::translate('Hide from everyone')
);

$privacy = array(
	WT_PRIV_PUBLIC => WT_I18N::translate('Show to public'),
	WT_PRIV_USER   => WT_I18N::translate('Show to members'),
	WT_PRIV_NONE   => WT_I18N::translate('Show to managers'),
	WT_PRIV_HIDE   => WT_I18N::translate('Hide from everyone'),
);

$tags = array_unique(array_merge(
	explode(',', $WT_TREE->getPreference('INDI_FACTS_ADD')), explode(',', $WT_TREE->getPreference('INDI_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('FAM_FACTS_ADD' )), explode(',', $WT_TREE->getPreference('FAM_FACTS_UNIQUE' )),
	explode(',', $WT_TREE->getPreference('NOTE_FACTS_ADD')), explode(',', $WT_TREE->getPreference('NOTE_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('SOUR_FACTS_ADD')), explode(',', $WT_TREE->getPreference('SOUR_FACTS_UNIQUE')),
	explode(',', $WT_TREE->getPreference('REPO_FACTS_ADD')), explode(',', $WT_TREE->getPreference('REPO_FACTS_UNIQUE')),
	array('SOUR', 'REPO', 'OBJE', '_PRIM', 'NOTE', 'SUBM', 'SUBN', '_UID', 'CHAN')
));

$all_tags = array();
foreach ($tags as $tag) {
	if ($tag) {
		$all_tags[$tag] = WT_Gedcom_Tag::getLabel($tag);
	}
}

uasort($all_tags, array('WT_I18N', 'strcasecmp'));

$resns = WT_DB::prepare(
	"SELECT default_resn_id, tag_type, xref, resn".
	" FROM `##default_resn`".
	" LEFT JOIN `##name` ON (gedcom_id=n_file AND xref=n_id AND n_num=0)".
	" WHERE gedcom_id=?".
	" ORDER BY xref IS NULL, n_sort, xref, tag_type"
)->execute(array(WT_GED_ID))->fetchAll();

foreach ($resns as $resn) {
	$resn->record = WT_GedcomRecord::getInstance($resn->xref);
	if ($resn->tag_type) {
		$resn->tag_label = WT_Gedcom_Tag::getLabel($resn->tag_type);
	} else {
		$resn->tag_label = '&nbsp;';
	}
}

if (WT_Filter::post('action') === 'update' && WT_Filter::checkCsrf()) {
	foreach (WT_Filter::postArray('delete', WT_REGEX_INTEGER) as $delete_resn) {
		WT_DB::prepare(
			"DELETE FROM `##default_resn` WHERE default_resn_id=?"
		)->execute(array($delete_resn));
	}

	$xrefs     = WT_Filter::postArray('xref', WT_REGEX_XREF);
	$tag_types = WT_Filter::postArray('tag_type', WT_REGEX_TAG);
	$resns     = WT_Filter::postArray('resn');

	foreach ($xrefs as $n => $xref) {
		$tag_type = $tag_types[$n];
		$resn     = $resns[$n];

		if ($tag_type || $xref) {
			// Delete any existing data
			if ($xref === '') {
				WT_DB::prepare(
					"DELETE FROM `##default_resn` WHERE gedcom_id=? AND tag_type=? AND xref IS NULL"
				)->execute(array(WT_GED_ID, $tag_type));
			}
			if ($tag_type === '') {
				WT_DB::prepare(
					"DELETE FROM `##default_resn` WHERE gedcom_id=? AND xref=? AND tag_type IS NULL"
				)->execute(array(WT_GED_ID, $xref));
			}
			// Add (or update) the new data
			WT_DB::prepare(
				"REPLACE INTO `##default_resn` (gedcom_id, xref, tag_type, resn) VALUES (?, NULLIF(?, ''), NULLIF(?, ''), ?)"
			)->execute(array(WT_GED_ID, $xref, $tag_type, $resn));
		}
	}

	$WT_TREE->setPreference('HIDE_LIVE_PEOPLE',           WT_Filter::postBool('HIDE_LIVE_PEOPLE'));
	$WT_TREE->setPreference('KEEP_ALIVE_YEARS_BIRTH',     WT_Filter::post('KEEP_ALIVE_YEARS_BIRTH', WT_REGEX_INTEGER, 0));
	$WT_TREE->setPreference('KEEP_ALIVE_YEARS_DEATH',     WT_Filter::post('KEEP_ALIVE_YEARS_DEATH', WT_REGEX_INTEGER, 0));
	$WT_TREE->setPreference('MAX_ALIVE_AGE',              WT_Filter::post('MAX_ALIVE_AGE', WT_REGEX_INTEGER, 100));
	$WT_TREE->setPreference('REQUIRE_AUTHENTICATION',     WT_Filter::postBool('REQUIRE_AUTHENTICATION'));
	$WT_TREE->setPreference('SHOW_DEAD_PEOPLE',           WT_Filter::post('SHOW_DEAD_PEOPLE'));
	$WT_TREE->setPreference('SHOW_LIVING_NAMES',          WT_Filter::post('SHOW_LIVING_NAMES'));
	$WT_TREE->setPreference('SHOW_PRIVATE_RELATIONSHIPS', WT_Filter::post('SHOW_PRIVATE_RELATIONSHIPS'));

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'admin_trees_manage.php?ged=' . $WT_TREE->tree_name);

	return;
}

$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('autocomplete();');

?>

<h2>
	<?php echo WT_I18N::translate('Privacy'); ?>
	—
	<?php echo WT_Filter::escapeHtml($WT_TREE->tree_title); ?>
</h2>

<form
	action="admin_trees_privacy.php"
	class="form-horizontal"
	method="POST"
	role="form"
>
	<?php echo WT_Filter::getCsrf(); ?>
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">

	<!-- REQUIRE_AUTHENTICATION -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Require visitor authentication'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo radio_buttons('REQUIRE_AUTHENTICATION', $no_yes, $WT_TREE->getPreference('REQUIRE_AUTHENTICATION'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Require visitor authentication” configuration setting */ WT_I18N::translate('Enabling this option will force all visitors to login before they can view any data on the site.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- HIDE_LIVE_PEOPLE -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Privacy options'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo radio_buttons('HIDE_LIVE_PEOPLE', $disable_enable, $WT_TREE->getPreference('HIDE_LIVE_PEOPLE'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Privacy options” configuration setting */ WT_I18N::translate('This option will enable all privacy settings and hide the details of living individuals, as defined or modified on the Privacy tab of each GEDCOM’s configuration page.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_DEAD_PEOPLE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="SHOW_DEAD_PEOPLE">
			<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Show dead individuals'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo select_edit_control('SHOW_DEAD_PEOPLE', $privacy, null, $WT_TREE->getPreference('SHOW_DEAD_PEOPLE'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Show dead individuals” configuration setting */ WT_I18N::translate('Set the privacy access level for all dead individuals.'); ?>
			</p>

		</div>
	</div>

	<!-- KEEP_ALIVE_YEARS_BIRTH / KEEP_ALIVE_YEARS_DEATH -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting.  ... [who were] born in the last XX years or died in the last YY years */ WT_I18N::translate('Extend privacy to dead individuals'); ?>
		</legend>
		<div class="col-sm-9">
			<?php
			echo
				/* I18N: Extend privacy to dead people [who were] ... */ WT_I18N::translate(
				'born in the last %1$s years or died in the last %2$s years',
				'<input type="text" name="KEEP_ALIVE_YEARS_BIRTH" value="'.$WT_TREE->getPreference('KEEP_ALIVE_YEARS_BIRTH').'" size="5" maxlength="3">',
				'<input type="text" name="KEEP_ALIVE_YEARS_DEATH" value="'.$WT_TREE->getPreference('KEEP_ALIVE_YEARS_DEATH').'" size="5" maxlength="3">'
			); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Extend privacy to dead individuals” configuration setting */ WT_I18N::translate('In some countries, privacy laws apply not only to living individuals, but also to those who have died recently.  This option will allow you to extend the privacy rules for living individuals to those who were born or died within a specified number of years.  Leave these values empty to disable this feature.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_LIVING_NAMES -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="SHOW_LIVING_NAMES">
			<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Names of private individuals'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo select_edit_control('SHOW_LIVING_NAMES', $privacy, null, $WT_TREE->getPreference('SHOW_LIVING_NAMES'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Names of private individuals” configuration setting */ WT_I18N::translate('This option will show the names (but no other details) of private individuals.  Individuals are private if they are still alive or if a privacy restriction has been added to their individual record.  To hide a specific name, add a privacy restriction to that name record.'); ?>
			</p>

		</div>
	</div>

	<!-- SHOW_PRIVATE_RELATIONSHIPS -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Show private relationships'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo radio_buttons('SHOW_PRIVATE_RELATIONSHIPS', $disable_enable, $WT_TREE->getPreference('SHOW_PRIVATE_RELATIONSHIPS'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Show private relationships” configuration setting */ WT_I18N::translate('This option will retain family links in private records.  This means that you will see empty “private” boxes on the pedigree chart and on other charts with private individuals.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- MAX_ALIVE_AGE -->
	<div class="form-group">
		<label class="control-label col-sm-3" for="MAX_ALIVE_AGE">
			<?php echo WT_I18N::translate('Age at which to assume an individual is dead'); ?>
		</label>
		<div class="col-sm-9">
			<input
				class="form-control"
				id="MAX_ALIVE_AGE"
				maxlength="5"
				name="MAX_ALIVE_AGE"
				type="text"
				value="<?php echo WT_Filter::escapeHtml($WT_TREE->getPreference('MAX_ALIVE_AGE')); ?>"
				>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Age at which to assume an individual is dead” configuration setting */ WT_I18N::translate('If this individual has any events other than death, burial, or cremation more recent than this number of years, he is considered to be “alive”.  Children’s birth dates are considered to be such events for this purpose.'); ?>
			</p>
		</div>
	</div>

	<h2><?php echo WT_I18N::translate('Privacy restrictions - these apply to records and facts that do not contain a GEDCOM RESN tag'); ?></h2>

	<script id="new-resn-template" type="text/html">
		<tr>
			<td>
				<input data-autocomplete-type="IFSRO" id="xref" maxlength="20" name="xref[]" type="text">
			</td>
			<td>
				<?php echo select_edit_control('tag_type[]', $all_tags, '', null, null); ?>
			</td>
			<td>
				<?php echo select_edit_control('resn[]', $PRIVACY_CONSTANTS, null, 'privacy', null); ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
	</script>

	<table class="table table-bordered table-hover" id="default-resn">
		<caption class="sr-only">
			<?php echo WT_I18N::translate('Privacy restrictions - these apply to records and facts that do not contain a GEDCOM RESN tag'); ?>
		</caption>
		<thead>
			<tr>
				<th>
					<?php echo WT_I18N::translate('Record'); ?>
				</th>
				<th>
					<?php echo WT_I18N::translate('Fact or event'); ?>
				</th>
				<th>
					<?php echo WT_I18N::translate('Access level'); ?>
				</th>
				<th>
					<button class="btn btn-primary" id="add-resn" type="button">
						<?php echo WT_I18N::translate('Add'); ?>
					</button>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					&nbsp;
				</td>
				<td>
					<button type="submit" class="btn btn-primary"><?php echo WT_I18N::translate('save'); ?></button>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($resns as $resn): ?>
			<tr>
				<td>
					<?php if ($resn->record): ?>
					<a href="<?php echo $resn->record->getHtmlUrl(); ?>"><?php echo $resn->record->getFullName(); ?></a>
					<?php elseif ($resn->xref): ?>
					<?php echo $resn->xref , ' — ', WT_I18N::translate('this record does not exist'); ?>
					<?php else: ?>
					&nbsp;
					<?php endif; ?>
				</td>
				<td>
					<?php echo $resn->tag_label; ?>
				</td>
				<td>
					<?php echo $PRIVACY_CONSTANTS[$resn->resn]; ?>
				</td>
				<td>
					<label for="delete-<?php echo $resn->default_resn_id; ?>">
						<?php echo WT_I18N::translate('Delete'); ?>
						<input id="delete-<?php echo $resn->default_resn_id; ?>" name="delete[]" type="checkbox" value="<?php echo $resn->default_resn_id; ?>">
					</label>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<script>
		jQuery("#default-resn input[type=checkbox]").on("click", function() {
			if ($(this).prop("checked")) {
				jQuery($(this).closest("tr").addClass("text-muted"));
			} else {
				jQuery($(this).closest("tr").removeClass("text-muted"));
			}
		});
		jQuery("#add-resn").on("click", function() {
			jQuery("#default-resn tbody").prepend(jQuery("#new-resn-template").html());
			autocomplete(); // This also re-applies autocomplete to existing fields
		});
	</script>
</form>
