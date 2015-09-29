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
namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * The theme for the control panel.
 */
class AdministrationTheme extends AbstractTheme implements ThemeInterface {
	/**
	 * A list of CSS files to include for this page.
	 *
	 * @return string[]
	 */
	protected function stylesheets() {
		$stylesheets   = parent::stylesheets();
		$stylesheets[] = WT_DATATABLES_BOOTSTRAP_CSS_URL;
		$stylesheets[] = WT_BOOTSTRAP_DATETIMEPICKER_CSS_URL;
		$stylesheets[] = $this->assetUrl() . 'style.css';

		return $stylesheets;
	}

	/**
	 * Where are our CSS, JS and other assets?
	 *
	 * @return string A relative path, such as "themes/foo/"
	 */
	public function assetUrl() {
		return 'themes/_administration/css-1.7.0/';
	}

	/**
	 * HTML link to a "favorites icon".
	 *
	 * @return string
	 */
	protected function favicon() {
		return '<link rel="icon" href="favicon.ico" type="image/x-icon">';
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
	 * Create the contents of the <header> tag.
	 *
	 * @return string
	 */
	protected function headerContent() {
		return
			$this->accessibilityLinks() .
			$this->secondaryMenuContainer($this->secondaryMenu());
	}

	/**
	 * Allow themes to add extra scripts to the page footer.
	 *
	 * @return string
	 */
	public function hookFooterExtraJavascript() {
		return
			'<script src="' . WT_BOOTSTRAP_JS_URL . '"></script>';
	}

	/**
	 * Site administration functions.
	 *
	 * @return Menu
	 */
	protected function menuAdminSite() {
		return new Menu(/* I18N: Menu entry*/ I18N::translate('Website'), '#', '', array(), array(
			new Menu(/* I18N: Menu entry */ I18N::translate('Website preferences'), 'admin_site_config.php?action=site'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Sending email'), 'admin_site_config.php?action=email'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Login and registration'), 'admin_site_config.php?action=login'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Languages'), 'admin_site_config.php?action=languages'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Tracking and analytics'), 'admin_site_config.php?action=tracking'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Website logs'), 'admin_site_logs.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Website access rules'), 'admin_site_access.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Clean up data folder'), 'admin_site_clean.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Server information'), 'admin_site_info.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('README documentation'), 'admin_site_readme.php'),
		));
	}

	/**
	 * Tree administration menu.
	 *
	 * @return Menu
	 */
	protected function menuAdminTrees() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Family trees'), '#', '', array(), array_filter(array(
			$this->menuAdminTreesManage(),
			$this->menuAdminTreesSetDefault(),
			$this->menuAdminTreesMerge(),
		)));
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
			return new Menu(/* I18N: Menu entry */ I18N::translate('Set the default blocks for new family trees'), 'index_edit.php?gedcom_id=-1');
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
		return new Menu(/* I18N: Menu entry */ I18N::translate('Users'), '#', '', array(), array(
			new Menu(/* I18N: Menu entry */ I18N::translate('User administration'), 'admin_users.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Add a new user'), 'admin_users.php?action=edit'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Send broadcast messages'), 'admin_users_bulk.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Delete inactive users'), 'admin_users.php?action=cleanup'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Set the default blocks for new users'), 'index_edit.php?user_id=-1'),
		));
	}

	/**
	 * Media administration menu.
	 *
	 * @return Menu
	 */
	protected function menuAdminMedia() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Media'), '#', '', array(), array(
			new Menu(/* I18N: Menu entry */ I18N::translate('Manage media'), 'admin_media.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Upload media files'), 'admin_media_upload.php'),
		));
	}

	/**
	 * Module administration menu.
	 *
	 * @return Menu
	 */
	protected function menuAdminModules() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Modules'), '#', '', array(), array(
			new Menu(/* I18N: Menu entry */ I18N::translate('Module administration'), 'admin_modules.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Menus'), 'admin_module_menus.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Tabs'), 'admin_module_tabs.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Blocks'), 'admin_module_blocks.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Sidebars'), 'admin_module_sidebar.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Reports'), 'admin_module_reports.php'),
		));
	}

	/**
	 * Generate a list of items for the main menu.
	 *
	 * @return Menu[]
	 */
	protected function primaryMenu() {
		if (Auth::isAdmin()) {
			return array(
				$this->menuAdminSite(),
				$this->menuAdminTrees(),
				$this->menuAdminUsers(),
				$this->menuAdminMedia(),
				$this->menuAdminModules(),
			);
		} else {
			return array(
				$this->menuAdminTrees(),
			);
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
		$html = '';
		foreach ($menus as $menu) {
			$html .= $menu->bootstrap();
		}

		return
			'<nav class="navbar navbar-default">' .
			'<div class="navbar-header">' .
			'<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#primary-navbar-collapse">' .
			'<span class="sr-only">Toggle navigation</span>' .
			'<span class="icon-bar"></span>' .
			'<span class="icon-bar"></span>' .
			'<span class="icon-bar"></span>' .
			'</button>' .
			'<a class="navbar-brand" href="admin.php">' . I18N::translate('Control panel') . '</a>' .
			'</div>' .
			'<div class="collapse navbar-collapse" id="primary-navbar-collapse">' .
			'<ul class="nav navbar-nav">' .
			$html .
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
		return array_filter(array(
			$this->menuPendingChanges(),
			$this->menuMyPage(),
			$this->menuLanguages(),
			$this->menuLogout(),
		));
	}

	/**
	 * Add markup to the secondary menu.
	 *
	 * @param Menu[] $menus
	 *
	 * @return string
	 */
	protected function secondaryMenuContainer(array $menus) {
		return '<div class="clearfix"><ul class="nav nav-pills small pull-right flip" role="menu">' . $this->secondaryMenuContent($menus) . '</ul></div>';
	}

	/**
	 * Format the secondary menu.
	 *
	 * @param Menu[] $menus
	 *
	 * @return string
	 */
	protected function secondaryMenuContent(array $menus) {
		return implode('', array_map(function (Menu $menu) { return $menu->bootstrap(); }, $menus));
	}

	/**
	 * A fixed string to identify this theme, in settings, etc.
	 *
	 * @return string
	 */
	public function themeId() {
		return '_administration';
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
