<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Algorithm\ConnectedComponent;
use Fisharebest\Webtrees\Controller\PageController;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Find unrelated individuals') . ' — ' . $WT_TREE->getTitleHtml())
	->pageHeader();

$associates = Filter::getBool('associates');

if ($associates) {
	$sql = "SELECT l_from, l_to FROM `##link` WHERE l_file = :tree_id AND l_type IN ('FAMS', 'FAMC', 'ASSO', '_ASSO')";
} else {
	$sql = "SELECT l_from, l_to FROM `##link` WHERE l_file = :tree_id AND l_type IN ('FAMS', 'FAMC')";
}

$rows = Database::prepare($sql)->execute([
	'tree_id' => $WT_TREE->getTreeId(),
])->fetchAll();
$graph = [];

foreach ($rows as $row) {
	$graph[$row->l_from][$row->l_to] = 1;
	$graph[$row->l_to][$row->l_from] = 1;
}

$algorithm  = new ConnectedComponent($graph);
$components = $algorithm->findConnectedComponents();
$root       = $controller->getSignificantIndividual();
$root_id    = $root->getXref();

/** @var Individual[][] */
$individual_groups = [];
$group_number      = 1;

foreach ($components as $key => $component) {
	if (!in_array($root_id, $component)) {
		$individuals = [];
		foreach ($component as $xref) {
			$individual = Individual::getInstance($xref, $WT_TREE);
			if ($individual instanceof Individual) {
				$individuals[] = $individual;
			}
		}
		$individual_groups[$group_number++] = $individuals;
	}
}

echo Bootstrap4::breadcrumbs([
	'admin.php'              => I18N::translate('Control panel'),
	'admin_trees_manage.php' => I18N::translate('Manage family trees'),
], $controller->getPageTitle());
?>

<h1><?= $controller->getPageTitle() ?></h1>

<form class="form-inline">
	<div class="row form-group">
		<input type="hidden" name="ged" value="<?= $WT_TREE->getNameHtml() ?>">
		<label for="associates"><?= I18N::translate('Include associates') ?></label>
		<input type="checkbox" value="1" id="associates" name="associates" <?= $associates ? 'checked' : '' ?>>
	</div>
	<button type="submit">
		<?= I18N::translate('update') ?>
	</button>
</form>

<p><?= I18N::translate('These groups of individuals are not related to %s.', $root->getFullName()) ?></p>

<?php foreach ($individual_groups as $group): ?>
	<h2><?= I18N::plural('%s individual', '%s individuals', count($group), I18N::number(count($group))) ?></h2>
	<ul>
		<?php foreach ($group as $individual): ?>
			<li>
				<a href="<?= $individual->getHtmlUrl() ?>"><?= $individual->getFullName() ?></a>
			</li>
		<?php endforeach ?>
	</ul>
<?php endforeach ?>
