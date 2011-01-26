<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
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
 *
 * @package webtrees
 * @subpackage Modules
 * @version $Id: module.php 8218 2010-05-09 07:39:07Z greg $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class custom_js_WT_Module extends WT_Module implements WT_Module_Config {
	private static $sent=false;

	public function __construct () {
		if (!self::$sent) {
			$cjs_footer=get_module_setting('custom_js', 'CJS_FOOTER', '');
			if (strpos($cjs_footer, '#')!==false) {
				# parse for embedded keywords
				$stats = new WT_Stats(WT_GEDCOM);
				$cjs_footer = $stats->embedTags($cjs_footer);
			}
			WT_JS::addInline($cjs_footer, WT_JS::PRIORITY_LOW);
			self::$sent=true;
		}
	}

	// Extend WT_Module
	public function getTitle() {
		return WT_I18N::translate('Custom JavaScript');
	}

	// Extend WT_Module
	public function getDescription() {
		return WT_I18N::translate('Allows you to easily add Custom JavaScript to your webtrees site.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
			// TODO: these files should be methods in this class
			require WT_ROOT.'modules/'.$this->getName().'/'.$mod_action.'.php';
			break;
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}
}

