<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\PageController;

define('WT_SCRIPT_NAME', 'admin_trees_places.php');
require './includes/session.php';

$search  = Filter::post('search');
$replace = Filter::post('replace');
$confirm = Filter::post('confirm');

$changes = array();

if ($search && $replace) {
	$rows = Database::prepare(
		"SELECT i_id AS xref, i_gedcom AS gedcom" .
		" FROM `##individuals`" .
		" LEFT JOIN `##change` ON (i_id = xref AND i_file=gedcom_id AND status='pending')" .
		" WHERE i_file = ?" .
		" AND COALESCE(new_gedcom, i_gedcom) REGEXP CONCAT('\n2 PLAC ([^\n]*, )*', ?, '(\n|$)')"
	)->execute(array($WT_TREE->getTreeId(), $search))->fetchAll();
	foreach ($rows as $row) {
		$record = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
		foreach ($record->getFacts() as $fact) {
			$old_place = $fact->getAttribute('PLAC');
			if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
				$new_place           = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
				$changes[$old_place] = $new_place;
				if ($confirm == 'update') {
					$gedcom = preg_replace('/(\n2 PLAC (?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', '$1' . $replace . '$2', $fact->getGedcom());
					$record->updateFact($fact->getFactId(), $gedcom, false);
				}
			}
		}
	}
	$rows = Database::prepare(
		"SELECT f_id AS xref, f_gedcom AS gedcom" .
		" FROM `##families`" .
		" LEFT JOIN `##change` ON (f_id = xref AND f_file=gedcom_id AND status='pending')" .
		" WHERE f_file = ?" .
		" AND COALESCE(new_gedcom, f_gedcom) REGEXP CONCAT('\n2 PLAC ([^\n]*, )*', ?, '(\n|$)')"
	)->execute(array($WT_TREE->getTreeId(), $search))->fetchAll();
	foreach ($rows as $row) {
		$record = Family::getInstance($row->xref, $WT_TREE, $row->gedcom);
		foreach ($record->getFacts() as $fact) {
			$old_place = $fact->getAttribute('PLAC');
			if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
				$new_place           = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
				$changes[$old_place] = $new_place;
				if ($confirm == 'update') {
					$gedcom = preg_replace('/(\n2 PLAC (?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', '$1' . $replace . '$2', $fact->getGedcom());
					$record->updateFact($fact->getFactId(), $gedcom, false);
				}
			}
		}
	}
}

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Update all the place names in a family tree') . ' — ' . $WT_TREE->getTitleHtml())
	->pageHeader();
?>

<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li><a href="admin_trees_manage.php"><?php echo I18N::translate('Manage family trees'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<p>
	<?php echo I18N::translate('This will update the highest-level part or parts of the place name.  For example, “Mexico” will match “Quintana Roo, Mexico”, but not “Santa Fe, New Mexico”.'); ?>
</p>

<form method="post">
	<dl>
		<dt><label for="search"><?php echo I18N::translate('Search for'); ?></label></dt>
		<dd><input name="search" id="search" type="text" size="30" value="<?php echo Filter::escapeHtml($search); ?>" required autofocus></dd>
		<dt><label for="replace"><?php echo I18N::translate('Replace with'); ?></label></dt>
		<dd><input name="replace" id="replace" type="text" size="30" value="<?php echo Filter::escapeHtml($replace); ?>" required></dd>
	</dl>
	<button type="submit" value="preview"><?php echo /* I18N: button label */ I18N::translate('preview'); ?></button>
	<button type="submit" value="update" name="confirm"><?php echo /* I18N: button label */ I18N::translate('update'); ?></button>
</form>

<?php if ($search && $replace) { ?>
	<?php if ($changes) { ?>
	<p>
		<?php echo $confirm ? I18N::translate('The following places have been changed:') : I18N::translate('The following places would be changed:'); ?>
	</p>
	<ul>
		<?php foreach ($changes as $old_place => $new_place) { ?>
		<li>
			<?php echo Filter::escapeHtml($old_place); ?>
			&rarr;
			<?php echo Filter::escapeHtml($new_place); ?>
		</li>
		<?php } ?>
	</ul>
	<?php } else { ?>
	<p>
		<?php echo I18N::translate('No places have been found.'); ?>
	</p>
	<?php } ?>
<?php } ?>

