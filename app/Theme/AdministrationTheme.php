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
namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * The theme for the control panel.
 */
class AdministrationTheme extends AbstractTheme implements ThemeInterface {
	/**
	 * Where are our CSS, JS and other assets?
	 */
	const THEME_DIR  = '_administration';
	const ASSET_DIR  = 'themes/' . self::THEME_DIR . '/css-2.0.0/';
	const STYLESHEET = self::ASSET_DIR . 'style.css';

	/**
	 * A list of CSS files to include for this page.
	 *
	 * @return string[]
	 */
	protected function stylesheets() {
		return array_merge(parent::stylesheets(), [
			WT_DATATABLES_BOOTSTRAP_CSS_URL,
			WT_BOOTSTRAP_DATETIMEPICKER_CSS_URL,
			self::STYLESHEET,
		]);
	}

	/**
	 * Create the contents of the <footer> tag.
	 *
	 * @return string
	 */
	protected function footerContent() {
		return '';
	}

	/**
	 * Site administration functions.
	 *
	 * @return Menu
	 */
	protected function menuAdminSite() {
		return new Menu(/* I18N: Menu entry*/ I18N::translate('Website'), '#', '', [], [
			new Menu(/* I18N: Menu entry */ I18N::translate('Website preferences'), 'admin_site_config.php?action=site'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Sending email'), 'admin_site_config.php?action=email'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Sign-in and registration'), 'admin_site_config.php?action=login'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Languages'), 'admin_site_config.php?action=languages'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Tracking and analytics'), 'admin_site_config.php?action=tracking'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Website logs'), 'admin_site_logs.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Clean up data folder'), 'admin_site_clean.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Server information'), 'admin_site_info.php'),
		]);
	}

	/**
	 * Tree administration menu.
	 *
	 * @return Menu
	 */
	protected function menuAdminTrees() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Family trees'), '#', '', [], array_filter([
			$this->menuAdminTreesManage(),
			$this->menuAdminTreesSetDefault(),
			$this->menuAdminTreesMerge(),
		]));
	}

	/**
	 * Manage trees menu.
	 *
	 * @return Menu
	 */
	protected function menuAdminTreesManage() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Manage family trees'), 'admin_trees_manage.php');
	}

	/**
	 * Merge trees menu.
	 *
	 * @return Menu|null
	 */
	protected function menuAdminTreesMerge() {
		if (Auth::isAdmin() && count(Tree::getAll()) > 1) {
			return new Menu(/* I18N: Menu entry */ I18N::translate('Merge family trees'), 'admin_trees_merge.php');
		} else {
			return null;
		}
	}

	/**
	 * Set default blocks menu.
	 *
	 * @return Menu|null
	 */
	protected function menuAdminTreesSetDefault() {
		if (Auth::isAdmin() && count(Tree::getAll()) > 1) {
			return new Menu(/* I18N: Menu entry */ I18N::translate('Set the default blocks for new family trees'), route('tree-page-default-edit'));
		} else {
			return null;
		}
	}

	/**
	 * User administration menu.
	 *
	 * @return Menu
	 */
	protected function menuAdminUsers() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Users'), '#', '', [], [
			new Menu(/* I18N: Menu entry */ I18N::translate('User administration'), 'admin_users.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Add a user'), 'admin_users.php?action=edit'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Send broadcast messages'), 'admin_users_bulk.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Delete inactive users'), 'admin_users.php?action=cleanup'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Set the default blocks for new users'), route('user-page-default-edit')),
		]);
	}

	/**
	 * Media administration menu.
	 *
	 * @return Menu
	 */
	protected function menuAdminMedia() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Media'), '#', '', [], [
			new Menu(/* I18N: Menu entry */ I18N::translate('Manage media'), 'admin_media.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Upload media files'), 'admin_media_upload.php'),
		]);
	}

	/**
	 * Module administration menu.
	 *
	 * @return Menu
	 */
	protected function menuAdminModules() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Modules'), '#', '', [], [
			new Menu(/* I18N: Menu entry */ I18N::translate('Module administration'), route('admin-modules')),
			new Menu(/* I18N: Menu entry */ I18N::translate('Menus'), route('admin-menus')),
			new Menu(/* I18N: Menu entry */ I18N::translate('Tabs'), route('admin-tabs')),
			new Menu(/* I18N: Menu entry */ I18N::translate('Blocks'), route('admin-blocks')),
			new Menu(/* I18N: Menu entry */ I18N::translate('Sidebars'), route('admin-sidebars')),
			new Menu(/* I18N: Menu entry */ I18N::translate('Charts'), route('admin-charts')),
			new Menu(/* I18N: Menu entry */ I18N::translate('Reports'), route('admin-reports')),
		]);
	}

	/**
	 * Generate a list of items for the main menu.
	 *
	 * @return Menu[]
	 */
	protected function primaryMenu() {
		if (Auth::isAdmin()) {
			return [
				$this->menuAdminSite(),
				$this->menuAdminTrees(),
				$this->menuAdminUsers(),
				$this->menuAdminMedia(),
				$this->menuAdminModules(),
			];
		} else {
			return [
				$this->menuAdminTrees(),
			];
		}
	}

	/**
	 * Add markup to the primary menu.
	 *
	 * @param Menu[] $menus
	 *
	 * @return string
	 */
	protected function primaryMenuContainer(array $menus) {
		return
			'<nav class="col navbar navbar-expand-md navbar-light bg-light">' .
			'<a class="navbar-brand" href="' . Html::escape(route('admin-control-panel')) . '">' . I18N::translate('Control panel') . '</a>' .
			'<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#primary-navbar" aria-controls="primary-navbar" aria-expanded="false" aria-label="Toggle navigation">' .
			'<span class="navbar-toggler-icon"></span>' .
			'</button> ' .
			'<div class="collapse navbar-collapse" id="primary-navbar">' .
			'<ul class="navbar-nav">' .
			$this->primaryMenuContent($menus) .
			'</ul>' .
			'</div>' .
			'</nav>';
	}

	/**
	 * Generate a list of items for the user menu.
	 *
	 * @return Menu[]
	 */
	protected function secondaryMenu() {
		return array_filter([
			$this->menuPendingChanges(),
			$this->menuMyPage(),
			$this->menuLanguages(),
			$this->menuLogout(),
		]);
	}

	/**
	 * What is this theme called?
	 *
	 * @return string
	 */
	public function themeName() {
		return 'administration';
	}
}
