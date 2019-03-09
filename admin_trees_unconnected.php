<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

use Fisharebest\Algorithm\ConnectedComponent;
use Fisharebest\Webtrees\Controller\PageController;

define('WT_SCRIPT_NAME', 'admin_trees_unconnected.php');
require './includes/session.php';

$controller = new PageController;
$controller
    ->restrictAccess(Auth::isManager($WT_TREE))
    ->setPageTitle(I18N::translate('Find unrelated individuals') . ' — ' . $WT_TREE->getTitleHtml())
    ->pageHeader();

$rows = Database::prepare(
    "SELECT l_from, l_to FROM `##link` WHERE l_file = :tree_id AND l_type IN ('FAMS', 'FAMC')"
)->execute(array(
    'tree_id' => $WT_TREE->getTreeId(),
))->fetchAll();
$graph = array();

foreach ($rows as $row) {
    $graph[$row->l_from][$row->l_to] = 1;
    $graph[$row->l_to][$row->l_from] = 1;
}

$algorithm  = new ConnectedComponent($graph);
$components = $algorithm->findConnectedComponents();
$root       = $controller->getSignificantIndividual();
$root_id    = $root->getXref();

/** @var Individual[][] */
$individual_groups = array();
$group_number      = 1;

foreach ($components as $key => $component) {
    if (!in_array($root_id, $component)) {
        $individuals = array();
        foreach ($component as $xref) {
            $individual = Individual::getInstance($xref, $WT_TREE);
            if ($individual instanceof Individual) {
                $individuals[] = $individual;
            }
        }
        $individual_groups[$group_number++] = $individuals;
    }
}

?>
<ol class="breadcrumb small">
    <li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
<li><a href="admin_trees_manage.php"><?php echo I18N::translate('Manage family trees'); ?></a></li>
<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<p><?php echo I18N::translate('These groups of individuals are not related to %s.', $root->getFullName()) ?></p>

<?php foreach ($individual_groups as $group): ?>
    <h2><?php echo I18N::plural('%s individual', '%s individuals', count($group), I18N::number(count($group))) ?></h2>
    <ul>
        <?php foreach ($group as $individual): ?>
            <li>
                <a href="<?php echo $individual->getHtmlUrl() ?>"><?php echo $individual->getFullName() ?></a>
            </li>
        <?php endforeach ?>
    </ul>
<?php endforeach ?>
