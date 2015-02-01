<?php
// webtrees: Web based Family History software
// Copyright (C) 2015 webtrees development team.
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

namespace WT\Theme;

use WT\Auth;
use WT_I18N;
use WT_Menu;
use WT_Tree;

/**
 * Class Administration - The admin theme.
 */
class Administration extends BaseTheme {
	/** {@inheritdoc} */
	protected function stylesheets() {
		$stylesheets = array(
			WT_FONT_AWESOME_CSS_URL,
			WT_BOOTSTRAP_CSS_URL,
			WT_DATATABLES_BOOTSTRAP_CSS_URL,
			WT_BOOTSTRAP_DATETIMEPICKER_CSS_URL,
			$this->assetUrl() . 'style.css',
		);

		if (WT_I18N::scriptDirection(WT_I18N::languageScript(WT_LOCALE)) === 'rtl') {
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
	 * @return WT_Menu
	 */
	protected function menuAdminSite() {
		return new WT_Menu(/* I18N: Menu entry*/ WT_I18N::translate('Website'), '#', '', '', array(
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Website preferences'), 'admin_site_config.php?action=site'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Sending email'), 'admin_site_config.php?action=email'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Login and registration'), 'admin_site_config.php?action=login'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Tracking and analytics'), 'admin_site_config.php?action=tracking'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Website logs'), 'admin_site_logs.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Website access rules'), 'admin_site_access.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Clean up data folder'), 'admin_site_clean.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Server information'), 'admin_site_info.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('README documentation'), 'admin_site_readme.php'),
		));
	}

	/**
	 * @return WT_Menu
	 */
	protected function menuAdminTrees() {
		return new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Family trees'), '#', '', '', array_filter(array(
			$this->menuAdminTreesManage(),
			$this->menuAdminTreesSetDefault(),
			$this->menuAdminTreesMerge(),
		)));
	}

	/**
	 * @return WT_Menu
	 */
	protected function menuAdminTreesManage() {
		return new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Manage family trees'), 'admin_trees_manage.php');
	}

	/**
	 * @return WT_Menu|null
	 */
	protected function menuAdminTreesMerge() {
		if (count(WT_Tree::getAll()) > 1) {
			return new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Merge family trees'), 'admin_trees_merge.php');
		} else {
			return null;
		}
	}

	/**
	 * @return WT_Menu|null
	 */
	protected function menuAdminTreesSetDefault() {
		if (count(WT_Tree::getAll()) > 1) {
			return new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Set the default blocks for new family trees'), 'index_edit.php?gedcom_id=-1');
		} else {
			return null;
		}
	}

	/**
	 * @return WT_Menu
	 */
	protected function menuAdminUsers() {
		return new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Users'), '#', '', '', array(
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('User administration'), 'admin_users.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Add a new user'), 'admin_users.php?action=edit'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Send broadcast messages'), 'admin_users_bulk.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Delete inactive users'), 'admin_users.php?action=cleanup'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Set the default blocks for new users'), 'index_edit.php?user_id=-1'),
		));
	}

	/**
	 * @return WT_Menu
	 */
	protected function menuAdminMedia() {
		return new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Media'), '#', '', '', array(
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Manage media'), 'admin_media.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Upload media files'), 'admin_media_upload.php'),
		));
	}

	/**
	 * @return WT_Menu
	 */
	protected function menuAdminModules() {
		return new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Modules'), '#', '', '', array(
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Module administration'), 'admin_modules.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Menus'), 'admin_module_menus.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Tabs'), 'admin_module_tabs.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Blocks'), 'admin_module_blocks.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Sidebars'), 'admin_module_sidebar.php'),
			new WT_Menu(/* I18N: Menu entry */ WT_I18N::translate('Reports'), 'admin_module_reports.php'),
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
			'<a class="navbar-brand" href="admin.php">' . WT_I18N::translate('Control panel') . '</a>' .
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
