<?php
// Search/replace function for PLAC data
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

define('WT_SCRIPT_NAME', 'admin_trees_places.php');

require './includes/session.php';
require WT_ROOT . 'includes/functions/functions_edit.php';

$search  = WT_Filter::post('search');
$replace = WT_Filter::post('replace');
$confirm = WT_Filter::post('confirm');

$changes = array();

if ($search && $replace) {
	$rows = WT_DB::prepare(
		"SELECT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom" .
		" FROM `##individuals`" .
		" LEFT JOIN `##change` ON (i_id = xref AND i_file=gedcom_id AND status='pending')".
		" WHERE i_file = ?" .
		" AND COALESCE(new_gedcom, i_gedcom) REGEXP CONCAT('\n2 PLAC ([^\n]*, )*', ?, '(\n|$)')"
	)->execute(array(WT_GED_ID, $search))->fetchAll();
	foreach ($rows as $row) {
		$record = WT_Individual::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
		foreach ($record->getFacts() as $fact) {
			$old_place = $fact->getAttribute('PLAC');
			if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
				$new_place = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
				$changes[$old_place] = $new_place;
				if ($confirm == 'update') {
					$gedcom = preg_replace('/(\n2 PLAC (?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', '$1' . $replace . '$2', $fact->getGedcom());
					$record->updateFact($fact->getFactId(), $gedcom, false);
				}
			}
		}
	}
	$rows = WT_DB::prepare(
		"SELECT f_id AS xref, f_file AS gedcom_id, f_gedcom AS gedcom".
		" FROM `##families`".
		" LEFT JOIN `##change` ON (f_id = xref AND f_file=gedcom_id AND status='pending')".
		" WHERE COALESCE(new_gedcom, f_gedcom) REGEXP CONCAT('\n2 PLAC ([^\n]*, )*', ?, '(\n|$)')"
	)->execute(array($search))->fetchAll();
	foreach ($rows as $row) {
		$record = WT_Family::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
		foreach ($record->getFacts() as $fact) {
			$old_place = $fact->getAttribute('PLAC');
			if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
				$new_place = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
				$changes[$old_place] = $new_place;
				if ($confirm == 'update') {
					$gedcom = preg_replace('/(\n2 PLAC (?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', '$1' . $replace . '$2', $fact->getGedcom());
					$record->updateFact($fact->getFactId(), $gedcom, false);
				}
			}
		}
	}
}

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isManager())
	->setPageTitle(WT_I18N::translate('Administration - place edit'))
	->pageHeader();
?>

<h2>
	<?php echo WT_I18N::translate('Update all the place names in a family tree'); ?>
	—
	<?php echo WT_Filter::escapeHtml($WT_TREE->tree_title); ?>
</h2>

<p>
	<?php echo WT_I18N::translate('This will update the highest-level part or parts of the place name.  For example, “Mexico” will match “Quintana Roo, Mexico”, but not “Santa Fe, New Mexico”.'); ?>
</p>

<form method="post">
	<dl>
		<dt><?php echo WT_I18N::translate('Family tree'); ?></dt>
		<dd><?php echo select_edit_control('ged', WT_Tree::getNameList(), null, WT_GEDCOM); ?></dd>
		<dt><label for="search"><?php echo WT_I18N::translate('Search for'); ?></label></dt>
		<dd><input name="search" id="search" type="text" size="30" value="<?= WT_Filter::escapeHtml($search) ?>" required autofocus></dd>
		<dt><label for="replace"><?php echo WT_I18N::translate('Replace with'); ?></label></dt>
		<dd><input name="replace" id="replace" type="text" size="30" value="<?= WT_Filter::escapeHtml($replace) ?>" required></dd>
	</dl>
	<button type="submit" value="preview"><?php echo /* I18N: button label */ WT_I18N::translate('preview'); ?></button>
	<button type="submit" value="update" name="confirm"><?php echo /* I18N: button label */ WT_I18N::translate('update'); ?></button>
</form>

<?php if ($search && $replace) { ?>
	<?php if ($changes) { ?>
	<p>
		<?php echo ($confirm) ? WT_I18N::translate('The following places were changed:') : WT_I18N::translate('The following places would be changed:'); ?>
	</p>
	<ul>
		<?php foreach ($changes as $old_place => $new_place) { ?>
		<li>
			<?php echo WT_Filter::escapeHtml($old_place); ?>
			&rarr;
			<?php echo WT_Filter::escapeHtml($new_place); ?>
		</li>
		<?php } ?>
	</ul>
	<?php } else { ?>
	<p>
		<?php echo WT_I18N::translate('No places were found.'); ?>
	</p>
	<?php } ?>
<?php } ?>

