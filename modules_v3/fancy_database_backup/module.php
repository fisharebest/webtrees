<?php
/*
 * Fancy Database Backup Module - Version 1.0 - JustCarmen 2013
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2013 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class fancy_database_backup_WT_Module extends WT_Module implements WT_Module_Config {
	
	public function __construct() {
		// Load any local user translations
		if (is_dir(WT_MODULES_DIR.$this->getName().'/language')) {			
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php')) {
				Zend_Registry::get('Zend_Translate')->addTranslation(
					new Zend_Translate('array', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php', WT_LOCALE)
				);
			}
		}
	}
	
	// Extend class WT_Module
	public function getTitle() {
		return /* Name of a module (not translatable) */ 'Fancy Database Backup';
	}

	// Extend class WT_Module
	public function getDescription() {
		return WT_I18N::translate('Provides access to MySQLDumper. A database backup tool.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin':
			$controller=new WT_Controller_Page();
			$controller
				->requireAdminLogin()
				->pageHeader();
				
			echo '<div id="fancy_db">';
			echo '<iframe src="mysqldumper/index.php" width="100%" height="580">'; // Change this src link if you have not installed MySQLDumper in the webtrees root.
			echo '<p>'.WT_I18N::translate('Sorry, your browser does not support iframes.').'</p>';
			echo '</iframe>';
			echo '</div>';
			break;
		default:
			header('HTTP/1.0 404 Not Found');
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin';
	}

}
