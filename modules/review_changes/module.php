<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
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
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class review_changes_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Pending Changes');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The Pending Changes block will give users with Edit rights a list of the records that have been changed online and that still need to be reviewed and accepted.  These changes are pending acceptance or rejection.<br /><br />If this block is enabled, users with Accept rights will receive an email once a day notifying them that changes need to be reviewed.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block, $config, $side, $index) {
		// Generate the block contents
	}

	// Implement class WT_Module_Block
	public function configureBlock() {
		// Create an edit form, and respond to the result
	}
}
