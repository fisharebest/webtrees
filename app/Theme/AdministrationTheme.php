<?php
namespace Fisharebest\Webtrees;

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

/**
 * Class AdministrationTheme - Theme for the control panel.
 */
class AdministrationTheme extends BaseTheme {
	/** {@inheritdoc} */
	protected function stylesheets() {
		$stylesheets = array(
			WT_FONT_AWESOME_CSS_URL,
			WT_BOOTSTRAP_CSS_URL,
			WT_DATATABLES_BOOTSTRAP_CSS_URL,
			WT_BOOTSTRAP_DATETIMEPICKER_CSS_URL,
			$this->assetUrl() . 'style.css',
		);

		if (I18N::scriptDirection(I18N::languageScript(WT_LOCALE)) === 'rtl') {
			$stylesheets[] = WT_BOOTSTRAP_RTL_CSS_URL;
		}

		return $stylesheets;
	}

	/** {@inheritdoc} */
	public function assetUrl() {
		return 'themes/_administration/css-1.7.0/';
	}

	/** {@inheritdoc} */
	protected function footerContent() {
		return '';
	}

	/** {@inheritdoc} */
	protected function headerContent() {
		return
			$this->accessibilityLinks() .
			$this->secondaryMenuContainer($this->secondaryMenu());
	}

	/** {@inheritdoc} */
	public function hookFooterExtraJavascript() {
		return
			'<script src="' . WT_BOOTSTRAP_JS_URL . '"></script>';
	}

	/**
	 * @return Menu
	 */
	protected function menuAdminSite() {
		return new Menu(/* I18N: Menu entry*/ I18N::translate('Website'), '#', '', '', array(
			new Menu(/* I18N: Menu entry */ I18N::translate('Website preferences'), 'admin_site_config.php?action=site'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Sending email'), 'admin_site_config.php?action=email'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Login and registration'), 'admin_site_config.php?action=login'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Tracking and analytics'), 'admin_site_config.php?action=tracking'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Website logs'), 'admin_site_logs.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Website access rules'), 'admin_site_access.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Clean up data folder'), 'admin_site_clean.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Server information'), 'admin_site_info.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('README documentation'), 'admin_site_readme.php'),
		));
	}

	/**
	 * @return Menu
	 */
	protected function menuAdminTrees() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Family trees'), '#', '', '', array_filter(array(
			$this->menuAdminTreesManage(),
			$this->menuAdminTreesSetDefault(),
			$this->menuAdminTreesMerge(),
		)));
	}

	/**
	 * @return Menu
	 */
	protected function menuAdminTreesManage() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Manage family trees'), 'admin_trees_manage.php');
	}

	/**
	 * @return Menu|null
	 */
	protected function menuAdminTreesMerge() {
		if (count(Tree::getAll()) > 1) {
			return new Menu(/* I18N: Menu entry */ I18N::translate('Merge family trees'), 'admin_trees_merge.php');
		} else {
			return null;
		}
	}

	/**
	 * @return Menu|null
	 */
	protected function menuAdminTreesSetDefault() {
		if (count(Tree::getAll()) > 1) {
			return new Menu(/* I18N: Menu entry */ I18N::translate('Set the default blocks for new family trees'), 'index_edit.php?gedcom_id=-1');
		} else {
			return null;
		}
	}

	/**
	 * @return Menu
	 */
	protected function menuAdminUsers() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Users'), '#', '', '', array(
			new Menu(/* I18N: Menu entry */ I18N::translate('User administration'), 'admin_users.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Add a new user'), 'admin_users.php?action=edit'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Send broadcast messages'), 'admin_users_bulk.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Delete inactive users'), 'admin_users.php?action=cleanup'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Set the default blocks for new users'), 'index_edit.php?user_id=-1'),
		));
	}

	/**
	 * @return Menu
	 */
	protected function menuAdminMedia() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Media'), '#', '', '', array(
			new Menu(/* I18N: Menu entry */ I18N::translate('Manage media'), 'admin_media.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Upload media files'), 'admin_media_upload.php'),
		));
	}

	/**
	 * @return Menu
	 */
	protected function menuAdminModules() {
		return new Menu(/* I18N: Menu entry */ I18N::translate('Modules'), '#', '', '', array(
			new Menu(/* I18N: Menu entry */ I18N::translate('Module administration'), 'admin_modules.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Menus'), 'admin_module_menus.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Tabs'), 'admin_module_tabs.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Blocks'), 'admin_module_blocks.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Sidebars'), 'admin_module_sidebar.php'),
			new Menu(/* I18N: Menu entry */ I18N::translate('Reports'), 'admin_module_reports.php'),
		));
	}

	/** {@inheritdoc} */
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

	/** {@inheritdoc} */
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

	/** {@inheritdoc} */
	protected function secondaryMenu() {
		return array_filter(array(
			$this->menuPendingChanges(),
			$this->menuMyPage(),
			$this->menuLanguages(),
			$this->menuLogout(),
		));
	}

	/** {@inheritdoc} */
	protected function secondaryMenuContainer(array $menus) {
		$html = '';
		foreach ($menus as $menu) {
			$html .= $menu->bootstrap();
		}

		return '<div class="clearfix"><ul class="nav nav-pills small pull-right flip" role="menu">' . $html . '</ul></div>';
	}

	/** {@inheritdoc} */
	public function themeId() {
		return '_administration';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return 'administration';
	}
}
