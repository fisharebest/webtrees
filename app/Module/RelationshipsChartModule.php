<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;

/**
 * Class RelationshipsChartModule
 */
class RelationshipsChartModule extends AbstractModule implements ModuleConfigInterface, ModuleChartInterface {
	const DEFAULT_FIND_ALL_PATHS = '1';

	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module/chart */ I18N::translate('Relationships');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “RelationshipsChart” module */ I18N::translate('A chart displaying relationships between two individuals.');
	}

	/**
	 * What is the default access level for this module?
	 *
	 * Some modules are aimed at admins or managers, and are not generally shown to users.
	 *
	 * @return int
	 */
	public function defaultAccessLevel() {
		return Auth::PRIV_PRIVATE;
	}

	/**
	 * Return a menu item for this chart.
	 *
	 * @return Menu|null
	 */
	public function getChartMenu(Individual $individual) {
		$tree     = $individual->getTree();
		$gedcomid = $tree->getUserPreference(Auth::user(), 'gedcomid');

		if ($gedcomid) {
			return new Menu(
				I18N::translate('Relationship to me'),
				'relationship.php?pid1=' . $gedcomid . '&amp;pid2=' . $individual->getXref() . '&amp;ged=' . $tree->getNameUrl(),
				'menu-chart-relationship',
				array('rel' => 'nofollow')
			);
		} else {
			return new Menu(
				I18N::translate('Relationships'),
				'relationship.php?pid1=' . $individual->getXref() . '&amp;ged=' . $tree->getNameUrl(),
				'menu-chart-relationship',
				array('rel' => 'nofollow')
			);
		}
	}

	/**
	 * Return a menu item for this chart - for use in individual boxes.
	 *
	 * @return Menu|null
	 */
	public function getBoxChartMenu(Individual $individual) {
		return $this->getChartMenu($individual);
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'admin':
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$this->saveConfig();
			} else {
				$this->editConfig();
			}
			break;
		default:
			http_response_code(404);
		}
	}

	/**
	 * Display a form to edit configuration settings.
	 */
	private function editConfig() {
		$controller = new PageController;
		$controller
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(I18N::translate('Chart preferences') . ' — ' . $this->getTitle())
			->pageHeader();

		?>
		<ol class="breadcrumb small">
			<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
			<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
			<li class="active"><?php echo $controller->getPageTitle(); ?></li>
		</ol>
		<h1><?php echo $controller->getPageTitle(); ?></h1>

		<form method="post">
			<?php foreach (Tree::getAll() as $tree): ?>
				<h2><?php echo $tree->getTitleHtml() ?></h2>
				<fieldset class="form-group">
					<legend class="control-label col-sm-3">
						<?php echo I18N::translate('Option to find all relationships'); ?>
					</legend>
					<div class="col-sm-9">
						<?php echo FunctionsEdit::radioButtons('find-all-paths-' . $tree->getTreeId(), array(0 => I18N::translate('hide'), 1 => I18N::translate('show')), $tree->getPreference('FIND_ALL_PATHS', self::DEFAULT_FIND_ALL_PATHS), 'class="radio-inline"'); ?>
						<p class="small text-muted">
							<?php echo I18N::translate('Searching for all possible relationships can take a lot of time in complex trees.') ?>
						</p>
					</div>
				</fieldset>
			<?php endforeach; ?>

			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-check"></i>
						<?php echo I18N::translate('save'); ?>
					</button>
				</div>
			</div>
		</form>
		<?php
	}

	/**
	 * Save updated configuration settings.
	 */
	private function saveConfig() {
		if (Auth::isAdmin()) {
			foreach (Tree::getAll() as $tree) {
				$tree->setPreference('FIND_ALL_PATHS', Filter::post('find-all-paths-' . $tree->getTreeId()));
			}

			FlashMessages::addMessage(I18N::translate('The preferences for the chart “%s” have been updated.', $this->getTitle()), 'success');
		}

		header('Location: ' . WT_BASE_URL . 'module.php?mod=' . $this->getName() . '&mod_action=admin');
	}

	/**
	 * The URL to a page where the user can modify the configuration of this module.
	 *
	 * @return string
	 */
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin';
	}
}
