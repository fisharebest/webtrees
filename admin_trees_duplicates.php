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

define('WT_SCRIPT_NAME', 'admin_trees_duplicates.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Find duplicates') . ' — ' . $WT_TREE->getTitleHtml())
	->pageHeader();

$repositories = Database::prepare(
	"SELECT GROUP_CONCAT(n_id) AS xrefs " .
	" FROM `##other`" .
	" JOIN `##name` ON o_id = n_id AND o_file = n_file" .
	" WHERE o_file = :tree_id AND o_type = 'REPO'" .
	" GROUP BY n_full" .
	" HAVING COUNT(n_id) > 1"
)->execute(array(
	'tree_id' => $WT_TREE->getTreeId(),
))->fetchAll();

$repositories = array_map(
	function (\stdClass $x) use ($WT_TREE) {
		$tmp = explode(',', $x->xrefs);

		return array_map(function ($y) use ($WT_TREE) {
			return Repository::getInstance($y, $WT_TREE);
		}, $tmp);
	}, $repositories
);

$sources = Database::prepare(
	"SELECT GROUP_CONCAT(n_id) AS xrefs " .
	" FROM `##sources`" .
	" JOIN `##name` ON s_id = n_id AND s_file = n_file" .
	" WHERE s_file = :tree_id" .
	" GROUP BY n_full" .
	" HAVING COUNT(n_id) > 1"
)->execute(array(
	'tree_id' => $WT_TREE->getTreeId(),
))->fetchAll();

$sources = array_map(
	function (\stdClass $x) use ($WT_TREE) {
		$tmp = explode(',', $x->xrefs);

		return array_map(function ($y) use ($WT_TREE) {
			return Source::getInstance($y, $WT_TREE);
		}, $tmp);
	}, $sources
);

$individuals = Database::prepare(
	"SELECT DISTINCT GROUP_CONCAT(d_gid ORDER BY d_gid) AS xrefs" .
	" FROM `##dates` AS d" .
	" JOIN `##name` ON d_file = n_file AND d_gid = n_id" .
	" WHERE d_file = :tree_id AND d_fact IN ('BIRT', 'CHR', 'BAPM', 'DEAT', 'BURI')" .
	" GROUP BY d_day, d_month, d_year, d_type, d_fact, n_type, n_full" .
	" HAVING COUNT(DISTINCT d_gid) > 1"
)->execute(array(
	'tree_id' => $WT_TREE->getTreeId(),
))->fetchAll();

$individuals = array_map(
	function (\stdClass $x) use ($WT_TREE) {
		$tmp = explode(',', $x->xrefs);

		return array_map(function ($y) use ($WT_TREE) {
			return Individual::getInstance($y, $WT_TREE);
		}, $tmp);
	}, $individuals
);

$families = Database::prepare(
	"SELECT GROUP_CONCAT(f_id) AS xrefs " .
	" FROM `##families`" .
	" WHERE f_file = :tree_id" .
	" GROUP BY LEAST(f_husb, f_wife), GREATEST(f_husb, f_wife)" .
	" HAVING COUNT(f_id) > 1"
)->execute(array(
	'tree_id' => $WT_TREE->getTreeId(),
))->fetchAll();

$families = array_map(
	function (\stdClass $x) use ($WT_TREE) {
		$tmp = explode(',', $x->xrefs);

		return array_map(function ($y) use ($WT_TREE) {
			return Family::getInstance($y, $WT_TREE);
		}, $tmp);
	}, $families
);

$media = Database::prepare(
	"SELECT GROUP_CONCAT(m_id) AS xrefs " .
	" FROM `##media`" .
	" WHERE m_file = :tree_id" .
	" GROUP BY m_titl" .
	" HAVING COUNT(m_id) > 1"
)->execute(array(
	'tree_id' => $WT_TREE->getTreeId(),
))->fetchAll();

$media = array_map(
	function (\stdClass $x) use ($WT_TREE) {
		$tmp = explode(',', $x->xrefs);

		return array_map(function ($y) use ($WT_TREE) {
			return Media::getInstance($y, $WT_TREE);
		}, $tmp);
	}, $media
);

$all_duplicates = array(
	I18N::translate('Repositories')  => $repositories,
	I18N::translate('Sources')       => $sources,
	I18N::translate('Individuals')   => $individuals,
	I18N::translate('Families')      => $families,
	I18N::translate('Media objects') => $media,
);

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li><a href="admin_trees_manage.php"><?php echo I18N::translate('Manage family trees'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<?php foreach ($all_duplicates as $title => $duplicate_list): ?>

<h2><?php echo $title; ?></h2>

<?php if ($duplicate_list): ?>
<ul>
	<?php foreach ($duplicate_list as $duplicates): ?>
	<li>
		<?php echo $duplicates[0]->getFullName(); ?>
		<?php foreach ($duplicates as $record): ?>
		—
		<a href="<?php echo $record->getHtmlUrl(); ?>">
			<?php echo $record->getXref(); ?>
		</a>
		<?php endforeach; ?>
		<?php if (count($duplicates) === 2): ?>
		—
		<a href="admin_site_merge.php?ged=<?php echo $WT_TREE->getNameHtml(); ?>&amp;gid1=<?php echo $duplicates[0]->getXref(); ?>&amp;gid2=<?php echo $duplicates[1]->getXref(); ?>&amp;url=admin_trees_duplicates.php">
			<?php echo I18N::translate('Merge'); ?>
		</a>
		<?php endif; ?>
	</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p><?php echo I18N::translate('No duplicates have been found.'); ?></p>
<?php endif; ?>

<?php endforeach; ?>
